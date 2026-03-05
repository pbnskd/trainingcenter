<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchTransfer extends Model
{
    protected $fillable = [
        'student_id',
        'from_batch_id',
        'to_batch_id',
        'transfer_date',
        'reason'
    ];
}
