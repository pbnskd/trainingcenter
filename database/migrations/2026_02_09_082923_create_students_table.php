<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // 1. Link to User Table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 2. Academic Identification
            $table->string('registration_number')->unique();

            // 3. Personal Details (Not in User table)
            $table->date('dob')->nullable();

            $table->text('bio')->nullable();

            // 4. Extended Contact Info
            $table->string('emergency_contact')->nullable();
            $table->text('permanent_address')->nullable(); // Separate from User 'address'

            // 5. Academic Status (Different from User Login Status)
            $table->enum('academic_status', ['enrolled', 'graduated', 'suspended', 'alumni'])
                ->default('enrolled');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
