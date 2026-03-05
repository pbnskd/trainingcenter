<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\User;
use App\Models\Student;
use App\Mail\BatchAssignedToFaculty;
use App\Mail\StudentAddedToBatch;
use App\Mail\BatchStatusChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BatchNotificationService
{
    /**
     * Notify Faculty Member
     */
    public function notifyFaculty(Batch $batch, int $facultyId)
    {
        $faculty = User::find($facultyId);
        
        if ($faculty && $faculty->email) {
            Mail::to($faculty->email)->queue(new BatchAssignedToFaculty($batch, $faculty));
        }
    }

    /**
     * Notify Student
     */
    public function notifyStudent(Batch $batch, int $studentId)
    {
        // Assuming Student model has a relationship to User for email
        $student = Student::with('user')->find($studentId);

        if ($student && $student->user && $student->user->email) {
            Mail::to($student->user->email)->queue(new StudentAddedToBatch($batch, $student));
        }
    }

    /**
     * Notify All Stakeholders of Status Change
     */
    public function notifyStatusChange(Batch $batch)
    {
        // 1. Notify Assigned Faculty
        $facultyMembers = $batch->faculty()->with('faculty')->get();
        foreach ($facultyMembers as $record) {
            if ($record->faculty && $record->faculty->email) {
                Mail::to($record->faculty->email)
                    ->queue(new BatchStatusChanged($batch, 'Faculty'));
            }
        }

        // 2. Notify Active Students
        // Using chunking for scalability
        $batch->students()->wherePivot('status', 'Active')->chunk(50, function ($students) use ($batch) {
            foreach ($students as $student) {
                if ($student->user && $student->user->email) {
                    Mail::to($student->user->email)
                        ->queue(new BatchStatusChanged($batch, 'Student'));
                }
            }
        });

        // 3. Notify Admin (Optional hardcoded or from config)
        $adminEmail = config('mail.admin_address');
        if ($adminEmail) {
             Mail::to($adminEmail)->queue(new BatchStatusChanged($batch, 'Admin'));
        }
    }
}