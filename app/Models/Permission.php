<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    // This allows the description to be saved to the database
    protected $fillable = [
        'name', 
        'guard_name', 
        'description'
    ];
}