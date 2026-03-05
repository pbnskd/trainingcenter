<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Student;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');
        return $this->user()->can('update', $student);
    }

    public function rules(): array
    {
        // Get the Student Model from the Route
        $student = $this->route('student');
        
        // Safety check: ensure we have the user_id to ignore in unique check
        $userId = $student->user_id;

        return [
            // 1. User Account
            'name'    => ['required', 'string', 'max:255'],
            
            // CRITICAL: Ignore the current user's ID for email uniqueness
            'email'   => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId)
            ],

            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Nullable on update
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            // 2. Student Profile
            'dob'               => ['nullable', 'date', 'before:today'],
            'bio'               => ['nullable', 'string', 'max:1000'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'permanent_address' => ['nullable', 'string', 'max:1000'],
            'academic_status'   => ['required', Rule::in(['enrolled', 'graduated', 'suspended', 'alumni'])],

            // 3. Arrays (Allow ID for updates)
            'education'    => ['nullable', 'array'],
            'education.*.id' => ['nullable', 'integer'], 
            'education.*.degree' => ['required', 'string', 'max:255'],
            
            'skills'       => ['nullable', 'array'],
            'skills.*.id'    => ['nullable', 'integer'],
            'skills.*.skill_name' => ['required', 'string', 'max:255'],
            
            'guardians'    => ['nullable', 'array'],
            'guardians.*.id' => ['nullable', 'integer'],
            'guardians.*.name' => ['required', 'string', 'max:255'],
        ];
    }
}