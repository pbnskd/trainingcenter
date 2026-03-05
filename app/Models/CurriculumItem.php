<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CurriculumItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id', 'parent_id', 'type', 'title', 
        'slug', 'content', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getBodyAttribute(): ?string
    {
        // FIX: Prevent returning array to Blade views if data is malformed
        if (is_array($this->content)) {
            return $this->content['html'] ?? null;
        }
        return is_string($this->content) ? $this->content : null;
    }

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CurriculumItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CurriculumItem::class, 'parent_id')->orderBy('sort_order');
    }
    
    public function scopeRoots(Builder $query): void
    {
        $query->whereNull('parent_id')->orderBy('sort_order');
    }

    public function isUnit(): bool
    {
        return $this->type === 'unit';
    }

    public function isLesson(): bool
    {
        return $this->type !== 'unit';
    }
}