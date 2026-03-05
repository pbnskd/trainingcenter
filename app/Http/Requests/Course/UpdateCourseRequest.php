<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Safely extract the course ID from the route for the unique rule exception
        $courseId = $this->route('course')?->id;

        return [
            // --- Basic Info ---
            'course_type_id' => ['sometimes', 'integer', 'exists:course_types,id'],
            'title'          => ['sometimes', 'string', 'max:255'],
            // Unique check that ignores the current course ID
            'slug'           => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('courses', 'slug')->ignore($courseId)],
            'summary'        => ['nullable', 'string', 'max:1000'],
            'description'    => ['nullable', 'string'],
            'thumbnail'      => ['nullable', 'image', 'max:2048'],

            // --- Settings ---
            'level'          => ['sometimes', 'string', Rule::in(['beginner', 'intermediate', 'advanced', 'all_levels'])],
            'price'          => ['nullable', 'numeric', 'min:0'], 
            'status'         => ['sometimes', 'string', Rule::in(['draft', 'published', 'archived'])],
            'published_at'   => ['nullable', 'date'],

            // --- Curriculum Builder ---
            'items'                              => ['nullable', 'array'],
            
            // Roots
            'items.*.id'                         => ['nullable', 'integer'],
            'items.*.title'                      => ['required_with:items', 'string', 'max:255'],
            'items.*.type'                       => ['required_with:items', 'string'],
            'items.*.description'                => ['nullable', 'string'],
            'items.*.html_content'               => ['nullable', 'string'],

            // Children
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