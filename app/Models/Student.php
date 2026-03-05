<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'registration_number', // Ensure this is fillable now that Service sets it
        'dob',
        'bio',
        'emergency_contact',
        'permanent_address',
        'academic_status',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    // REMOVED: protected static function booted() ... 
    // Logic moved to StudentService

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(StudentEducation::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(StudentSkill::class);
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(StudentGuardian::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Get active batches through enrollments
    public function activeBatches(): HasManyThrough
    {
        return $this->hasManyThrough(BatchStudent::class, Enrollment::class)
            ->where('batch_students.status', 'active');
    }
}