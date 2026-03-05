<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_type_id', 'code', 'title', 'slug', 'summary', 
        'description', 'thumbnail_url', 'level', 'price', 
        'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'price' => 'integer',
    ];

    // Relationships
    public function type(): BelongsTo
    {
        return $this->belongsTo(CourseType::class, 'course_type_id');
    }

    public function curriculumItems(): HasMany
    {
        return $this->hasMany(CurriculumItem::class)->orderBy('sort_order');
    }

    // Helper for Controller editing
    public function loadSyllabusForEditing(): self
    {
        return $this->load(['curriculumItems' => function($query) {
            $query->whereNull('parent_id')->with('children')->orderBy('sort_order');
        }]);
    }

    public function getSyllabusAttribute()
    {
        return $this->curriculumItems()
            ->roots()
            ->with('children')
            ->get();
    }

    // Accessors
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price !== null ? number_format($this->price / 100, 2) : '0.00',
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (!$value) return null;
                if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
                // FIX: Standardize storage link resolution
                return Storage::url($value);
            }
        );
    }
}