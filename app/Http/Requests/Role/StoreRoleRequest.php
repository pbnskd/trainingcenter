<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'unique:roles,name', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'permission'  => ['nullable', 'array'],
        ];
    }
}