<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class EnrollmentService
{
    public function __construct(protected BatchService $batchService) {}

    /**
     * Orchestrates creating a new enrollment and assigning to a batch.
     */
    public function processNewEnrollment(array $data): Enrollment
    {
        return DB::transaction(function () use ($data) {
            $enrollment = $this->enrollOrResume($data['student_id'], $data['course_id']);

            if (!empty($data['batch_id'])) {
                $batch = Batch::findOrFail($data['batch_id']);
                $this->batchService->processStudentEnrollment($batch, $data['student_id'], [
                    'is_full_course' => $data['is_full_course'] ?? true,
                    'enrolled_at'    => $data['enrolled_at'] ?? now(),
                ]);
            }

            return $enrollment;
        });
    }

    /**
     * Orchestrates status updates and batch transfers.
     */
    public function processEnrollmentUpdate(Enrollment $enrollment, array $data): void
    {
        DB::transaction(function () use ($enrollment, $data) {
            // 1. Handle Status Change
            if (isset($data['status']) && $data['status'] !== $enrollment->status) {
                $this->updateStatus($enrollment, $data['status']);
            }

            // 2. Handle Batch Transfer / Assignment
            if (!empty($data['transfer_to_batch_id'])) {
                $currentAssignment = $enrollment->currentBatchAssignment;
                $newBatchId = $data['transfer_to_batch_id'];

                if ($currentAssignment && $currentAssignment->batch_id != $newBatchId) {
                    $this->batchService->processStudentTransfer([
                        'from_batch_id' => $currentAssignment->batch_id,
                        'to_batch_id'   => $newBatchId,
                        'student_id'    => $enrollment->student_id,
                        'transfer_date' => $data['transfer_date'] ?? now()->toDateString(),
                        'reason'        => $data['transfer_reason'] ?? null,
                    ]);
                } elseif (!$currentAssignment) {
                    // Edge case: resuming into a new batch
                    $batch = Batch::findOrFail($newBatchId);
                    $this->batchService->processStudentEnrollment($batch, $enrollment->student_id, [
                        'enrolled_at' => $data['transfer_date'] ?? now()
                    ]);
                }
            }
        });
    }

    protected function enrollOrResume(int $studentId, int $courseId): Enrollment
    {
        $activeEnrollment = Enrollment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->first();

        if ($activeEnrollment) {
            throw new Exception("Student is already actively enrolled in this course.");
        }

        $pausedEnrollment = Enrollment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereIn('status', ['paused', 'dropped'])
            ->latest()
            ->first();

        if ($pausedEnrollment) {
            $pausedEnrollment->update(['status' => 'in_progress']);
            return $pausedEnrollment;
        }

        return Enrollment::create([
            'student_id'  => $studentId,
            'course_id'   => $courseId,
            'status'      => 'enrolled',
            'enrolled_at' => Carbon::now(),
        ]);
    }

    protected function updateStatus(Enrollment $enrollment, string $newStatus): void
    {
        $enrollment->update(['status' => $newStatus]);
        
        if (in_array($newStatus, ['paused', 'dropped', 'completed'])) {
            $pivotStatus = match($newStatus) {
                'completed' => 'Completed',
                default     => 'Dropped'
            };
            
            if ($activeAssignment = $enrollment->currentBatchAssignment) {
                $activeAssignment->update([
                    'status' => $pivotStatus, 
                    'remark' => "Enrollment status updated to {$newStatus} on " . now()->toDateString()
                ]);
            }
        }
    }
}