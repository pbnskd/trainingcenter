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
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id();

            // Explicitly link to the 'students' table
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');

            $table->string('skill_name'); // e.g., PHP, Python, Communication
            
            // Stores proficiency level. 
            // Note: You could also use an enum here: $table->enum('proficiency', ['Beginner', 'Intermediate', 'Expert']);
            $table->string('proficiency')->default('Beginner'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_skills');
    }
};