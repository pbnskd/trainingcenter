<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Batches Table
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            // Link to 'courses' table. Ensure the courses table migration runs before this.
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            $table->string('batch_code')->unique();

            // Enforced shifts for consistency
            $table->enum('shift', ['Morning', 'Afternoon', 'Evening']);

            // Using JSON for flexibility in time/date ranges as requested
            $table->json('custom_time_range')->nullable(); // e.g., {"start": "13:00", "end": "15:00"}
            $table->json('date_range'); // e.g., {"start": "2023-10-01", "end": "2024-03-01"}

            $table->text('description')->nullable();
            $table->integer('total_estimated_hours')->default(0);
            $table->integer('max_capacity');

            // Status tracking with 'Upcoming' as the default
            $table->enum('status', ['Upcoming', 'Running', 'Completed', 'Cancelled', 'On_Hold'])->default('Upcoming');

            $table->softDeletes();
            $table->timestamps();

            // Indexing status for faster filtering in the dashboard
            $table->index('status');
        });

        // 2. Batch Schedules
        Schema::create('batch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->enum('day_of_week', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->decimal('hours_per_day', 4, 2);
            $table->text('description')->nullable(); // Standardized to lowercase 'description'
            $table->timestamps();

            // Ensures a batch doesn't have two different schedules for the same day
            $table->unique(['batch_id', 'day_of_week']);
        });

        Schema::create('batch_students', function (Blueprint $table) {
            $table->id();

            // The logistical assignment
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');

            // The specific student (kept for Eloquent simplicity and fast queries)
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // The academic record (links this batch assignment to the parent enrollment)
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');

            // Toggle for full course vs partial curriculum items
            $table->boolean('is_full_course')->default(true);
            $table->date('enrolled_at');

            // Status handles the history tracking
            $table->enum('status', ['Active', 'Completed', 'Dropped', 'Transferred_Out'])->default('Active');

            $table->text('remark')->nullable();
            $table->timestamps();

            // REMOVED: $table->unique(['batch_id', 'student_id']); 
            // We remove this so a student can have an 'Active' row and a 'Transferred_Out' row for the same batch if they bounce around.
        });

        // 3.1 Batch Student Items (Partial Enrollment Details)
        Schema::create('batch_student_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_student_id')->constrained('batch_students')->onDelete('cascade');

            // Linked to 'curriculum_items' table as per your CurriculumItem model
            $table->foreignId('curriculum_item_id')->constrained('curriculum_items')->onDelete('cascade');

            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique(['batch_student_id', 'curriculum_item_id'], 'unique_student_item');
        });

        // 4. Batch Faculty Assignments (Polymorphic)
        Schema::create('batch_faculty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');

            // Assuming Faculty are 'Users' with specific roles
            $table->foreignId('faculty_id')->constrained('users')->onDelete('cascade');

            // Polymorphic link: can point to Course, CurriculumItem (Unit/Lesson)
            $table->morphs('assignable');

            $table->text('instructions')->nullable();
            $table->boolean('is_primary')->default(false); // To designate the main instructor
            $table->timestamps();

            // Custom index name used to avoid "identifier too long" errors in some DB engines
            $table->unique(['batch_id', 'faculty_id', 'assignable_id', 'assignable_type'], 'batch_faculty_unique');
        });

        // 5. Batch Transfer History
        Schema::create('batch_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('from_batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('to_batch_id')->constrained('batches')->onDelete('cascade');

            // Tracking who authorized the transfer (User)
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->text('reason')->nullable();
            $table->timestamp('transferred_at')->useCurrent();
            $table->timestamps();
        });

        // 6. Attendance
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_present')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Prevents double entry for the same student in the same batch on the same day
            $table->unique(['batch_id', 'student_id', 'date']);
        });
    }

    public function down(): void
    {
        // Dropping in reverse order of creation to avoid constraint violations
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('batch_transfers');
        Schema::dropIfExists('batch_faculty');
        Schema::dropIfExists('batch_student_items');
        Schema::dropIfExists('batch_students');
        Schema::dropIfExists('batch_schedules');
        Schema::dropIfExists('batches');
    }
};
