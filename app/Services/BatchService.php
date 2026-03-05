<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchFaculty;
use App\Models\BatchSchedule;
use App\Models\CurriculumItem;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\BatchTransfer;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;

class BatchService
{
    public function __construct(protected BatchNotificationService $notifier)
    {
    }

    // ==========================================
    // DATA RETRIEVAL METHODS (Read Operations)
    // ==========================================

    public function getPaginatedBatches(int $perPage = 10)
    {
        // Optimized: Select only necessary course columns to reduce memory overhead
        return Batch::with(['course:id,title,code'])
            ->withCount('students')
            ->latest()
            ->paginate($perPage);
    }

    public function getAvailableCoursesForDropdown()
    {
        return Course::pluck('title', 'id');
    }

    public function getBatchDetailsForShow(Batch $batch): array
    {
        $batch->load([
            'course.curriculumItems' => fn($q) => $q->whereNull('parent_id'),
            'schedules',
            'students.user:id,name,email', // Optimized memory usage
            'faculty.faculty',
            'faculty.assignable'
        ]);

        return [
            'batch' => $batch,
            'availableFaculty' => User::role('Faculty')->orderBy('name')->get(),
            'units' => $batch->course->curriculumItems,
        ];
    }

    public function getEligibleStudentsForEnrollment(Batch $batch)
    {
        return Student::with('user:id,name')
            ->whereHas('enrollments', function ($query) use ($batch) {
                $query->where('course_id', $batch->course_id)
                      ->whereIn('status', ['enrolled', 'in_progress']);
            })
            ->whereDoesntHave('batches', function ($query) use ($batch) {
                $query->where('batch_id', $batch->id)
                      ->where('batch_students.status', 'Active'); // Allow re-enrollment if previously dropped
            })
            ->get()
            ->pluck('user.name', 'id');
    }

    public function getAvailableBatchesForCourse(int $courseId)
    {
        return Batch::withCount(['students' => function ($q) {
            // Count only active students to gauge true capacity
            $q->where('batch_students.status', 'Active');
        }])
        ->where('course_id', $courseId)
        ->whereIn('status', ['Upcoming', 'Running'])
        ->get();
    }

    // ==========================================
    // MUTATION METHODS (Write Operations)
    // ==========================================

    public function createBatch(array $data): Batch
    {
        return DB::transaction(function () use ($data) {
            $course = Course::findOrFail($data['course_id']);
            
            $prefix = strtoupper(substr(Str::slug($course->title ?? $course->code), 0, 4));
            $year = date('Y');

            // Prevent race conditions during creation using pessimistic locking
            $count = Batch::where('course_id', $course->id)->lockForUpdate()->count() + 1;
            
            $data['batch_code'] = sprintf('%s-%s-B%02d', $prefix, $year, $count);

            return Batch::create($data);
        });
    }

    public function updateBatch(Batch $batch, array $data): Batch
    {
        return DB::transaction(function () use ($batch, $data) {
            $oldStatus = $batch->status;
            
            $batch->update($data);

            if (isset($data['status']) && $data['status'] !== $oldStatus) {
                $this->notifier->notifyStatusChange($batch);
            }

            return $batch;
        });
    }

    public function deleteBatch(Batch $batch): bool
    {
        return DB::transaction(function () use ($batch) {
            $activeStudents = $batch->students()->wherePivot('status', 'Active')->exists();
                
            if ($activeStudents) {
                throw new Exception("Cannot delete batch because it has active students.");
            }

            $batch->schedules()->delete();
            return $batch->delete();
        });
    }

    public function addSchedule(Batch $batch, array $data): BatchSchedule
    {
        return $batch->schedules()->create($data);
    }

    public function processStudentEnrollment(Batch $batch, int $studentId, array $data): void
    {
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('course_id', $batch->course_id)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->latest()
            ->first();

        if (!$enrollment) {
            throw new Exception('No active enrollment found for this course.');
        }

        if ($enrollment->currentBatchAssignment()->exists()) {
            throw new Exception("Student is already assigned to an active batch. Use the transfer process instead.");
        }

        $activeStudentsCount = $batch->students()->wherePivot('status', 'Active')->count();
        if ($activeStudentsCount >= $batch->max_capacity) {
            throw new Exception("This batch has reached its maximum capacity of {$batch->max_capacity} students.");
        }

        DB::transaction(function () use ($batch, $enrollment, $data) {
            // FIX: Using syncWithoutDetaching prevents SQL duplicate entry errors
            // if a student previously dropped this batch and is now re-joining it.
            $batch->students()->syncWithoutDetaching([
                $enrollment->student_id => [
                    'enrollment_id'  => $enrollment->id,
                    'is_full_course' => $data['is_full_course'] ?? true,
                    'status'         => 'Active', 
                    'enrolled_at'    => Carbon::parse($data['enrolled_at'] ?? now())->toDateString(),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]
            ]);
        });

        $this->notifier->notifyStudent($batch, $enrollment->student_id);
    }

    public function unenrollStudent(Batch $batch, Student $student): void
    {
        // FIX: Never use detach() here. It deletes the pivot row, wiping out historical 
        // financial and attendance data. We update the status instead.
        $batch->students()->updateExistingPivot($student->id, [
            'status' => 'Dropped',
            'remark' => 'Manually unenrolled on ' . now()->toDateString()
        ]);
    }

    public function assignFaculty(Batch $batch, array $data): BatchFaculty
    {
        $assignableId = $data['unit_id'] ?? $batch->course_id;
        $assignableType = !empty($data['unit_id']) ? CurriculumItem::class : Course::class;

        $assignment = BatchFaculty::updateOrCreate(
            [
                'batch_id'        => $batch->id,
                'faculty_id'      => $data['faculty_id'],
                'assignable_id'   => $assignableId,
                'assignable_type' => $assignableType,
            ],
            [
                'is_primary'   => $data['is_primary'] ?? false,
                'instructions' => $data['instructions'] ?? null,
            ]
        );

        $this->notifier->notifyFaculty($batch, $data['faculty_id']);

        return $assignment;
    }

    public function processStudentTransfer(array $data): void
    {
        $fromBatchId = $data['from_batch_id'];
        $toBatchId = $data['to_batch_id'];
        $studentId = $data['student_id'];
        $transferDate = $data['transfer_date'];
        $reason = $data['reason'] ?? null;

        $fromBatch = Batch::findOrFail($fromBatchId);
        $toBatch = Batch::findOrFail($toBatchId);

        if ($fromBatch->course_id !== $toBatch->course_id) {
            throw new Exception("Cannot transfer: Course mismatch between batches.");
        }

        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('course_id', $toBatch->course_id)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->firstOrFail();

        $activeStudentsCount = $toBatch->students()->wherePivot('status', 'Active')->count();
        if ($activeStudentsCount >= $toBatch->max_capacity) {
            throw new Exception("Cannot transfer: The target batch is at maximum capacity.");
        }

        $currentAssignment = $enrollment->batchAssignments()
            ->where('batch_id', $fromBatchId)
            ->where('status', 'Active')
            ->first();

        if (!$currentAssignment) {
            throw new Exception("Cannot transfer: Student is not actively assigned to the source batch.");
        }

        DB::transaction(function () use ($enrollment, $fromBatchId, $toBatchId, $transferDate, $reason, $currentAssignment) {
            
            // 1. Mark current assignment as transferred out
            $currentAssignment->update([
                'status' => 'Transferred_Out',
                'remark' => "Transferred to Batch ID {$toBatchId} on {$transferDate}"
            ]);

            // 2. Create the new active assignment in the target batch
            $newAssignment = $enrollment->batchAssignments()->create([
                'batch_id'       => $toBatchId,
                'student_id'     => $enrollment->student_id,
                'is_full_course' => $currentAssignment->is_full_course,
                'enrolled_at'    => $transferDate,
                'status'         => 'Active'
            ]);

            // 3. Log the transfer
            BatchTransfer::create([
                'student_id'    => $enrollment->student_id,
                'from_batch_id' => $fromBatchId,
                'to_batch_id'   => $toBatchId,
                'reason'        => $reason,
                'approved_by'   => Auth::id()
            ]);

            // 4. Move selected items if they are doing a partial course
            if (!$currentAssignment->is_full_course) {
                $currentAssignment->selectedItems()->update([
                    'batch_student_id' => $newAssignment->id
                ]);
            }
        });
    }
}