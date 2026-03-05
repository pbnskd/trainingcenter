<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Secure the update endpoint
        $enrollment = $this->route('enrollment');
        return $enrollment ? $this->user()->can('update', $enrollment) : false;
    }

    public function rules(): array
    {
        return [
            'status'               => ['sometimes', 'in:enrolled,in_progress,paused,completed,dropped'],
            'transfer_to_batch_id' => ['nullable', 'exists:batches,id'],
            'transfer_reason'      => ['nullable', 'string', 'max:255', 'required_with:transfer_to_batch_id'],
            'transfer_date'        => ['nullable', 'date', 'required_with:transfer_to_batch_id'],
        ];
    }
}