<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_full_course' => ['boolean'],
            'enrolled_at' => ['date'],
            'status' => ['required', 'in:enrolled,dropped,completed,on_hold'],
        ];
    }
}