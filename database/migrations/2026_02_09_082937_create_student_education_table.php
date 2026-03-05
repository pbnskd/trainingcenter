<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_education', function (Blueprint $table) {
            $table->id();

            // Explicitly linking to the 'students' table created in the previous step
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');

            $table->string('degree');       // e.g., High School, Bachelors
            $table->string('institution');  // e.g., ABC University
            $table->string('passing_year'); // Kept as string to allow ranges (e.g., "2020-2024")
            $table->string('grade_or_percentage')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_education');
    }
};