<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchFaculty extends Model
{
    protected $table = 'batch_faculty';
    protected $fillable = ['batch_id', 'faculty_id', 'assignable_id', 'assignable_type', 'instructions', 'is_primary'];

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
