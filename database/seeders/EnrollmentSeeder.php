<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\BatchStudent;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $courses = Course::all();

        if ($students->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('Skipping EnrollmentSeeder: Missing students or courses.');
            return;
        }

        foreach ($students as $student) {
            // Enroll each student in 1 to 2 random courses
            $selectedCourses = $courses->random(rand(1, 2));

            foreach ($selectedCourses as $course) {
                
                $enrolledDate = Carbon::now()->subDays(rand(5, 60));

                // 1. Create the Master Enrollment Record
                $enrollment = Enrollment::create([
                    'student_id'  => $student->id,
                    'course_id'   => $course->id,
                    'status'      => 'in_progress',
                    'enrolled_at' => $enrolledDate,
                ]);

                // 2. Find a batch for this course to assign them to
                $batch = Batch::where('course_id', $course->id)->first();

                if ($batch) {
                    // Create the Batch Assignment using the new enrollment_id
                    BatchStudent::create([
                        'enrollment_id'  => $enrollment->id,
                        'student_id'     => $student->id,
                        'batch_id'       => $batch->id,
                        'status'         => 'active',
                        'is_full_course' => true,
                        'enrolled_at'    => $enrolledDate,
                    ]);

                    // 3. Generate Fake Attendance
                    for ($d = 0; $d < 5; $d++) {
                        $date = Carbon::now()->subDays($d);
                        if ($date->isWeekend()) continue;

                        DB::table('attendances')->insertOrIgnore([
                            'batch_id'   => $batch->id,
                            'student_id' => $student->id,
                            'date'       => $date->format('Y-m-d'),
                            'is_present' => (rand(0, 10) > 2), // 80% chance present
                            'remarks'    => (rand(0, 10) === 0) ? 'Late arrival' : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('Enrollments and Batch Assignments seeded successfully.');
    }
}