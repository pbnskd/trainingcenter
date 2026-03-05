<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // --- Basic Info ---
            'course_type_id' => ['required', 'integer', 'exists:course_types,id'],
            'title'          => ['required', 'string', 'max:255'],
            // Unique check for new course creation
            'slug'           => ['nullable', 'string', 'max:255', 'unique:courses,slug', 'alpha_dash'],
            'summary'        => ['nullable', 'string', 'max:1000'],
            'description'    => ['nullable', 'string'],
            'thumbnail'      => ['nullable', 'image', 'max:2048'],

            // --- Settings ---
            'level'          => ['required', 'string', Rule::in(['beginner', 'intermediate', 'advanced', 'all_levels'])],
            'price'          => ['nullable', 'numeric', 'min:0'], 
            'status'         => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'published_at'   => ['nullable', 'date'],

            // --- Curriculum Builder ---
            'items'                              => ['nullable', 'array'],
            
            // Roots (Sections & Standalone Lessons)
            'items.*.id'                         => ['nullable', 'integer'],
            'items.*.title'                      => ['required', 'string', 'max:255'],
            'items.*.type'                       => ['required', 'string'],
            'items.*.description'                => ['nullable', 'string'],
            'items.*.html_content'               => ['nullable', 'string'],

            // Children (Lessons inside Units)
            'items.*.children'                   => ['nullable', 'array'],
            'items.*.children.*.id'              => ['nullable', 'integer'],
            'items.*.children.*.title'           => ['required_with:items.*.children', 'string', 'max:255'],
            'items.*.children.*.type'            => ['required_with:items.*.children', 'string'],
            'items.*.children.*.description'     => ['nullable', 'string'], 
            'items.*.children.*.html_content'    => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation(): void
    {
        if (!$this->slug && $this->title) {
            $this->merge(['slug' => Str::slug($this->title)]);
        }
    }
}