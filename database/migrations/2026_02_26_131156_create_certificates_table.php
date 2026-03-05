<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_student_id')->constrained('batch_students')->cascadeOnDelete();
            $table->string('certificate_number')->unique()->nullable();
            
            // Core Validation
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            
            // Faculty Workflow
            $table->foreignId('faculty_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('faculty_approved_at')->nullable();
            $table->text('faculty_remarks')->nullable();
            
            // Admin Workflow
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('admin_remarks')->nullable();
            
            // Status & Storage
            $table->enum('status', [
                'pending',          // Awaiting Faculty Approval
                'faculty_approved', // Awaiting Admin Approval
                'generated',        // Finalized & PDF created
                'rejected'          // Rejected by either Faculty or Admin
            ])->default('pending');
            $table->string('file_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};