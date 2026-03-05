<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Enrollment;

class StoreStudentEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Actively check the policy instead of blind 'true'
        return $this->user()->can('create', Enrollment::class);
    }

    public function rules(): array
    {
        return [
            'student_id'     => ['required', 'exists:students,id'],
            'course_id'      => ['required', 'exists:courses,id'],
            'batch_id'       => ['nullable', 'exists:batches,id'],
            'is_full_course' => ['boolean'],
            'enrolled_at'    => ['nullable', 'date'],
        ];
    }
}