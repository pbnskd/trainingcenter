<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation()
{
    if (!$this->has('transfer_date')) {
        $this->merge(['transfer_date' => now()->format('Y-m-d')]);
    }
}

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'from_batch_id' => ['required', 'exists:batches,id'],
            'to_batch_id' => [
                'required', 
                'exists:batches,id', 
                'different:from_batch_id' // Cannot transfer to same batch
            ],
            'transfer_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}