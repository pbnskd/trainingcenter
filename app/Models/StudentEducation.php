<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEducation extends Model
{
    use HasFactory;

    // Since the table name is 'student_education' (singular 'education'), 
    // Laravel might look for 'student_educations'. It's safer to define it:
    protected $table = 'student_education';

    // Allow these fields to be filled via StudentEducation::create([...])
    protected $fillable = [
        'student_id',
        'degree',
        'institution',
        'passing_year',
        'grade_or_percentage',
    ];

    /**
     * Get the student that owns the education record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}