<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Certificate extends Model
{
    use SoftDeletes;

    // Define constants to avoid magic strings and prevent typos
    public const STATUS_PENDING = 'pending';
    public const STATUS_FACULTY_APPROVED = 'faculty_approved';
    public const STATUS_GENERATED = 'generated';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'batch_student_id', 'certificate_number', 'attendance_percentage',
        'faculty_id', 'faculty_approved_at', 'faculty_remarks',
        'admin_id', 'admin_approved_at', 'admin_remarks',
        'status', 'file_path'
    ];

    protected $casts = [
        'faculty_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'attendance_percentage' => 'float',
    ];

    // Relationships
    public function batchStudent(): BelongsTo
    {
        return $this->belongsTo(BatchStudent::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes for cleaner Controller queries
    public function scopePendingForFaculty(Builder $query, int $facultyId): Builder
    {
        return $query->where('status', self::STATUS_PENDING)
            ->whereHas('batchStudent.batch', fn($q) => $q->where('faculty_id', $facultyId));
    }
}