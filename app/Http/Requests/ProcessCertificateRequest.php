<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approve,reject'],
            'remarks'  => ['nullable', 'string', 'max:1000', 'required_if:decision,reject'],
        ];
    }

    public function messages(): array
    {
        return [
            'remarks.required_if' => 'You must provide a reason when rejecting a certificate.',
        ];
    }
}