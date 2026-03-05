<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\User;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        // Specifically grab users with the Faculty role
        $facultyMembers = User::role('Faculty')->get();

        if ($courses->isEmpty()) {
            $this->command->warn('Skipping BatchSeeder: No Courses found.');
            return;
        }

        foreach ($courses as $course) {
            for ($i = 1; $i <= 2; $i++) {

                $startDate = Carbon::now()->subMonths(rand(0, 2));
                $endDate = $startDate->copy()->addMonths(6);

                // Unique Batch Code Generation
                $prefix = $course->title ? strtoupper(Str::slug($course->title)) : 'BATCH';
                $prefix = substr(str_replace('-', '', $prefix), 0, 4);
                $batchCode = sprintf('%s-%s-C%d-B%d', $prefix, date('Y'), $course->id, $i);

                $courseName = $course->title ?? 'General Course';

                $batchId = DB::table('batches')->insertGetId([
                    'course_id'             => $course->id,
                    'batch_code'            => $batchCode,
                    'shift'                 => ['Morning', 'Afternoon', 'Evening'][rand(0, 2)],
                    'custom_time_range'     => json_encode(['start' => '09:00', 'end' => '12:00']),
                    'date_range'            => json_encode([
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date'   => $endDate->format('Y-m-d')
                    ]),
                    'description'           => "Official batch for {$courseName} - Cohort {$i}",
                    'total_estimated_hours' => 120,
                    'max_capacity'          => 25,
                    'status' => 'Running',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                // 3. SCHEDULE
                $days = ['Monday', 'Wednesday', 'Friday'];
                foreach ($days as $day) {
                    DB::table('batch_schedules')->insert([
                        'batch_id'      => $batchId,
                        'day_of_week'   => $day,
                        'hours_per_day' => 3.0,
                        'description'   => 'Lecture & Lab',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }

                // 4. FACULTY
                if ($facultyMembers->isNotEmpty()) {
                    DB::table('batch_faculty')->insert([
                        'batch_id'        => $batchId,
                        'faculty_id'      => $facultyMembers->random()->id,
                        'assignable_id'   => $course->id,
                        'assignable_type' => Course::class,
                        'instructions'    => 'Lead Instructor',
                        'is_primary'      => true,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }
        }
    }
}
