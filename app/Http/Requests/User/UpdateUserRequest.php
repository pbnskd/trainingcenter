<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the user ID safely from the route
        $user = $this->route('user');
        $userId = $user ? $user->id : null;

        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            
            // FIX 1: Add these fields so they are included in validated()
            'phone'   => ['nullable', 'string', 'max:20'], 
            'address' => ['nullable', 'string', 'max:255'],
            
            'avatar'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status'  => ['required', 'boolean'],
            
            // FIX 2: Change 'required' to 'sometimes' or 'nullable'. 
            // If the user can't see the roles dropdown (due to @can), this field won't be sent, 
            // causing validation to fail with "required".
            'roles'   => ['sometimes', 'array'], 
            
            // FIX 3: Simplify password logic. Validate it as nullable here, 
            // and handle the empty check in the Controller/Service.
            'password'=> ['nullable', 'confirmed', 'min:8'],
        ];
    }
}