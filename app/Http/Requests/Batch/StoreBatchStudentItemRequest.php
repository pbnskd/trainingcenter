<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchStudentItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_student_id' => ['required', 'exists:batch_students,id'],
            'curriculum_item_id' => ['required', 'exists:curriculum_items,id'], // Assuming table name
            'remark' => ['nullable', 'string', 'max:1000'],
        ];
    }
}