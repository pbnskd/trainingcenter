<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class AssignFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Automatically merge batch_id from the route URL
        if ($this->route('batch')) {
            $this->merge([
                'batch_id' => $this->route('batch')->id ?? $this->route('batch'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'exists:batches,id'],
            'faculty_id' => ['required', 'exists:users,id'],
            'unit_id' => ['nullable', 'exists:curriculum_items,id'],
            'is_primary' => ['boolean'], // Checkbox sends 1 or 0
            'instructions' => ['nullable', 'string', 'max:500'],
            'assignable_id' => ['nullable', 'integer'],
            'assignable_type' => ['nullable', 'string'],
        ];
    }
}