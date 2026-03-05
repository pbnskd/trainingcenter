<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // 1. Merge the batch route parameter
        if ($this->route('batch')) {
            $this->merge([
                'batch_id' => $this->route('batch')->id ?? $this->route('batch'),
            ]);
        }

        // 2. FIX: Set default status to 'Active' (Title Case) matches your DB constraint
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'Active', 
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'batch_id' => [
                'required', 
                'exists:batches,id',
                Rule::unique('batch_students')->where(function ($query) {
                    return $query->where('student_id', $this->student_id);
                })
            ],
            'is_full_course' => ['boolean'],
            'enrolled_at' => ['nullable', 'date'],
            
            // FIX: Validate against allowed DB values (Title Case)
            'status' => ['required', 'in:Active,Waitlist'], 
        ];
    }
}