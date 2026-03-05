<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Course Types
        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_type_id')->constrained();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->nullable()->unique(); 
            $table->longText('description')->nullable(); 
            $table->text('summary')->nullable(); 
            $table->string('thumbnail_url')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
            $table->unsignedBigInteger('price')->default(0);   
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->timestamp('published_at')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Curriculum Items
        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('curriculum_items')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('type')->index(); 
            $table->json('content')->nullable(); 
            $table->integer('sort_order')->default(0); 
            $table->boolean('is_active')->default(true);         
            $table->timestamps();
            $table->softDeletes();
            $table->index(['course_id', 'parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_items');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_types');
    }
};