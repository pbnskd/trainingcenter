<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = ['student_id', 'course_id', 'status', 'enrolled_at'];

    protected $casts = [
        'enrolled_at' => 'datetime',
    ];

    // Added proper return types to relationships for IDE autocompletion and safety
    public function student(): BelongsTo {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo {
        return $this->belongsTo(Course::class);
    }

    public function batchAssignments(): HasMany {
        return $this->hasMany(BatchStudent::class, 'enrollment_id');
    }

    public function currentBatchAssignment(): HasOne {
        return $this->hasOne(BatchStudent::class, 'enrollment_id')
            ->where('status', 'Active')
            ->latestOfMany(); // Safeguard in case of dirty data creating multiple active states
    }
}