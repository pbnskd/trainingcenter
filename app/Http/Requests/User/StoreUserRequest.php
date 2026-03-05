<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],

            // FIX 1: Add these so they pass through $request->validated()
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],

            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status'   => ['required', 'boolean'],

            // FIX 2: Change 'required' to 'sometimes'.
            // In your View, the roles dropdown is wrapped in @can(). 
            // If the user creating the account cannot see that dropdown, 
            // this field won't be sent, and validation would fail if strictly 'required'.
            'roles'    => ['sometimes', 'array'],
        ];
    }
}