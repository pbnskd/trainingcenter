<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'batch_code', 'shift', 'custom_time_range', 
        'date_range', 'description', 'total_estimated_hours', 
        'max_capacity', 'status'
    ];

    protected $casts = [
        'custom_time_range' => 'array',
        'date_range' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BatchSchedule::class);
    }

   public function students(): BelongsToMany
{
    return $this->belongsToMany(Student::class, 'batch_students')
                ->withPivot(['is_full_course', 'enrolled_at', 'status'])
                ->withTimestamps();
}
    public function faculty(): HasMany
    {
        // This links Batch -> BatchFaculty
        return $this->hasMany(BatchFaculty::class);
    }
   
}