<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'shift' => ['required', 'string', 'max:50'], 
            'max_capacity' => ['required', 'integer', 'min:1'],
            'total_estimated_hours' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Upcoming,Running,Completed,Cancelled,On_Hold'],
            'date_range' => ['required', 'array'], 
            'date_range.start_date' => ['required', 'date'],
            'date_range.end_date' => ['required', 'date', 'after:date_range.start_date'],
            'custom_time_range' => ['nullable', 'array'],
            'custom_time_range.start_time' => ['nullable', 'date_format:H:i'],
            'custom_time_range.end_time' => ['nullable', 'date_format:H:i', 'after:custom_time_range.start_time'],
        ];
    }
}