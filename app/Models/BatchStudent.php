<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchStudent extends Pivot
{
    protected $table = 'batch_students';
    public $incrementing = true; // Set to true because it has its own ID used by batch_student_items

    public function selectedItems(): HasMany
    {
        return $this->hasMany(BatchStudentItem::class, 'batch_student_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    /**
     * Relationship: A BatchStudent record belongs to a specific Enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    /**
     * Relationship: A BatchStudent record belongs to a specific Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function getCompletionPercentageAttribute()
    {
        // Note: This requires knowing the total items in the course. 
        // Ideally calculated in the Service/Controller to avoid heavy logic in Model attributes,
        // but useful if you need quick access.

        $totalCourseItems = $this->batch->course->curriculumItems()->count();
        $completedItems = $this->selectedItems()->count();

        return $totalCourseItems > 0 ? round(($completedItems / $totalCourseItems) * 100) : 0;
    }
    public function certificate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Certificate::class, 'batch_student_id');
    }
}
