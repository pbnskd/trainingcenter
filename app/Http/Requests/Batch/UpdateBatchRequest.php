<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['sometimes', 'exists:courses,id'],
            // Properly extract ID from route and fix syntax error
            'batch_code' => [
                'sometimes', 'string', 'max:255', 
                Rule::unique('batches')->ignore($this->route('batch'))
            ],
            'shift' => ['sometimes', 'string', 'max:50'],
            'max_capacity' => ['sometimes', 'integer', 'min:1'],
            'total_estimated_hours' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:Upcoming,Running,Completed,Cancelled,On_Hold'],
            'date_range' => ['sometimes', 'array'],
            'date_range.start_date' => ['required_with:date_range', 'date'],
            'date_range.end_date' => ['required_with:date_range', 'date', 'after:date_range.start_date'],
            'custom_time_range' => ['nullable', 'array'],
        ];
    }
}