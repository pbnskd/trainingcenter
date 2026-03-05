<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseType;
use App\Models\CurriculumItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CourseSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Disable Foreign Keys
        Schema::disableForeignKeyConstraints();

        // 2. Truncate tables
        CurriculumItem::truncate();
        Course::truncate();
        CourseType::truncate();

        // 3. Re-enable Foreign Keys
        Schema::enableForeignKeyConstraints();

        // --- Seeding Logic ---

        // 1. Create Types
        $longType = CourseType::create([
            'name' => 'Long Course', 
            'slug' => 'long-course',
        ]);
        
        $shortType = CourseType::create([
            'name' => 'Short Course', 
            'slug' => 'short-course',
        ]);
        
        $specialType = CourseType::create([
            'name' => 'Special Training', 
            'slug' => 'special-training',
        ]);

        // 2. Create 15 Short/Special Courses
        for ($i = 1; $i <= 15; $i++) {
            $isSpecial = ($i % 5 == 0);
            $typeId = $isSpecial ? $specialType->id : $shortType->id;
            
            $title = "Skill Course $i: " . fake()->jobTitle();
            $durationWeeks = rand(2, 8);
            
            // A. Create Course
            $course = Course::create([
                'course_type_id'        => $typeId,
                'code'                  => 'SKILL-' . strtoupper(Str::random(5)) . '-' . $i,
                'title'                 => $title,
                'slug'                  => Str::slug($title) . '-' . Str::random(5),
                'summary'               => fake()->sentence(),
                'description'           => fake()->paragraphs(3, true), 
                'thumbnail_url'         => 'https://picsum.photos/640/480?random=' . $i,
                'level'                 => 'all_levels',
                
                // Price in cents
                'price'                 => rand(4900, 19900), 
                
                'published_at'          => now(), 
                'status'                => 'published',
            ]);

            // B. Create Curriculum (Units & Lessons)
            for ($u = 1; $u <= 2; $u++) {
                $unitTitle = "Unit $u: " . fake()->words(3, true);
                
                // 1. Create Unit (Root Item)
                $unit = CurriculumItem::create([
                    'course_id'   => $course->id,
                    'parent_id'   => null,
                    'type'        => 'section',
                    'title'       => $unitTitle,
                    'slug'        => Str::slug($unitTitle) . '-' . Str::random(5),
                    'sort_order'  => $u,
                    'is_active'   => true,
                ]);
                
                // 2. Create Lessons (Children)
                
                // Lesson 1: Video
                CurriculumItem::create([
                    'course_id'   => $course->id,
                    'parent_id'   => $unit->id, 
                    'type'        => 'lesson',
                    'title'       => 'Introduction Video',
                    'slug'        => Str::slug('Introduction Video ' . Str::random(5)),
                    // FIX: Use json_encode here
                    'content'     => json_encode([
                        'html' => '<p>Welcome to this unit.</p>',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration' => '10:00'
                    ]),
                    'sort_order'  => 1,
                    'is_active'   => true,
                ]);

                // Lesson 2: Text
                CurriculumItem::create([
                    'course_id'   => $course->id,
                    'parent_id'   => $unit->id,
                    'type'        => 'lesson',
                    'title'       => 'Core Concepts',
                    'slug'        => Str::slug('Core Concepts ' . Str::random(5)),
                    // FIX: Use json_encode here
                    'content'     => json_encode([
                        'html' => '<p>' . fake()->paragraphs(2, true) . '</p>',
                        'read_time' => '5 min'
                    ]),
                    'sort_order'  => 2,
                    'is_active'   => true,
                ]);
            }
        }

        // 3. Create Long Courses (Diplomas)
        $diplomaNames = [
            'Diploma in Computer Science', 
            'Advanced Graphic Design', 
            'Business Admin Masterclass', 
            'Full Stack Development Bootcamp', 
            'Cyber Security Expert'
        ];

        foreach ($diplomaNames as $index => $name) {
            $longCourse = Course::create([
                'course_type_id'        => $longType->id,
                'code'                  => 'DIP-' . strtoupper(Str::random(5)),
                'title'                 => $name,
                'slug'                  => Str::slug($name) . '-' . Str::random(3),
                'summary'               => "Start your career in " . $name,
                'description'           => "Comprehensive program for $name",
                'published_at'          => now(),
                'status'                => 'published',
                'level'                 => 'advanced',
                'price'                 => rand(49900, 99900),
            ]);
            
            // Add a sample unit for diplomas too
            CurriculumItem::create([
                'course_id'   => $longCourse->id,
                'parent_id'   => null,
                'type'        => 'section',
                'title'       => 'Semester 1',
                'slug'        => 'semester-1-' . $longCourse->id,
                'sort_order'  => 1,
                'is_active'   => true
            ]);
        }
    }
}