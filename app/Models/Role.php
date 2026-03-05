<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // This allows the description to be saved to the database
    protected $fillable = [
        'name', 
        'guard_name', 
        'description'
    ];
}