<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use App\Models\Batch;
use App\Mail\WelcomeStudentMail; 
use App\Mail\StudentStatusMail;// <-- Add this import
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail; // <-- Add this import
use Illuminate\Http\UploadedFile;

class StudentService
{
    protected $enrollmentService;
    protected $batchService;

public function __construct(EnrollmentService $enrollmentService, BatchService $batchService)
    {
        $this->enrollmentService = $enrollmentService;
        $this->batchService = $batchService;
    }
    public function generateRegistrationNumber(): string
    {
        $latestStudent = Student::withTrashed()->select('registration_number')->latest('id')->first();
        
        $nextId = $latestStudent 
            ? intval(substr($latestStudent->registration_number, 4)) + 1 
            : 1;

        return 'REG-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public function createStudent(array $data, ?UploadedFile $avatar = null): Student
    {
        return DB::transaction(function () use ($data, $avatar) {
            // 1. Create User
            $userData = [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'address'  => $data['address'] ?? null, 
                'password' => Hash::make($data['password']),
                'status'   => true,
            ];

            if ($avatar) {
                $userData['avatar'] = $avatar->store('avatars', 'public');
            }

            $user = User::create($userData);
            $user->assignRole('Student'); 

            // 2. Create Student
            $student = Student::create([
                'user_id'             => $user->id,
                'registration_number' => $this->generateRegistrationNumber(),
                'dob'                 => $data['dob'] ?? null,
                'bio'                 => $data['bio'] ?? null,
                'emergency_contact'   => $data['emergency_contact'] ?? null,
                'permanent_address'   => $data['permanent_address'] ?? null,
                'academic_status'     => 'enrolled', // Force default on create
            ]);

            // 3. Relationships
            $this->saveRelationships($student, $data);
            // 4. Send Welcome Email
          //  Mail::to($user->email)->send(new WelcomeStudentMail($user, $student, $data['password']));

            return $student;
        });
    }

    public function updateStudent(Student $student, array $data, ?UploadedFile $avatar = null): Student
    {
        return DB::transaction(function () use ($student, $data, $avatar) {
            // 1. Update User
            $userData = [
                'name'    => $data['name'],
                'email'   => $data['email'], // Allow email update
                'phone'   => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if ($avatar) {
                if ($student->user->avatar) {
                    Storage::disk('public')->delete($student->user->avatar);
                }
                $userData['avatar'] = $avatar->store('avatars', 'public');
            }

            $student->user->update($userData);

            // 2. Update Student
            $student->update([
                'dob'               => $data['dob'] ?? null,
                'bio'               => $data['bio'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'permanent_address' => $data['permanent_address'] ?? null,
                'academic_status'   => $data['academic_status'],
            ]);

            // 3. Relationships
            $this->syncRelation($student->education(), $data['education'] ?? []);
            $this->syncRelation($student->skills(), $data['skills'] ?? []);
            $this->syncRelation($student->guardians(), $data['guardians'] ?? []);

            return $student;
        });
    }

    /**
     * Common method to handle creation of relations
     */
    private function saveRelationships(Student $student, array $data)
    {
        if (!empty($data['education'])) {
            $student->education()->createMany($data['education']);
        }
        if (!empty($data['skills'])) {
            $student->skills()->createMany($data['skills']);
        }
        if (!empty($data['guardians'])) {
            $student->guardians()->createMany($data['guardians']);
        }
    }

    private function syncRelation($relation, array $items)
    {
        $existingIds = $relation->pluck('id')->toArray();
        $submittedIds = array_filter(array_column($items, 'id'));

        // Delete removed
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if (!empty($idsToDelete)) {
            $relation->whereIn('id', $idsToDelete)->delete();
        }

        // Update or Create
        foreach ($items as $item) {
            if (isset($item['id']) && $item['id']) {
                $relation->where('id', $item['id'])->update($item);
            } else {
                unset($item['id']);
                $relation->create($item);
            }
        }
    }
    public function quickCreateStudent(array $data, ?UploadedFile $avatar = null): Student
    {
        return DB::transaction(function () use ($data, $avatar) {
            // 1. Create the User & Student Profile (Includes relationships like education/guardians)
            $student = $this->createStudent($data, $avatar);

            // 2. Create the Course Enrollment
            $enrollment = $this->enrollmentService->enrollOrResume($student->id, $data['course_id']);

            // 3. Assign the Student to the Batch
            $batch = Batch::findOrFail($data['batch_id']);
            
            $this->batchService->assignStudentToBatch($batch, $enrollment, [
                'is_full_course' => $data['is_full_course'] ?? true,
                'enrolled_at'    => $data['enrolled_at'] ?? now(),
            ]);

            return $student;
        });
    }

    public function notifyStatus(Student $student): bool
    {
        if (!$student->user || !$student->user->email) {
            return false;
        }

        Mail::to($student->user->email)->send(new StudentStatusMail($student));

        return true;
    }
}