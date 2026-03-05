<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchSchedule extends Model
{
    protected $fillable = ['batch_id', 'day_of_week', 'hours_per_day', 'description'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
}