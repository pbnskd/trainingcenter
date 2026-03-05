<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'exists:batches,id'],
            'date' => ['required', 'date'],
            
            // Validating the array of student attendance
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'exists:students,id'],
            'attendances.*.is_present' => ['required', 'boolean'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}