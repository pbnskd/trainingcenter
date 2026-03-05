<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Student;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Student::class);
    }

    public function rules(): array
    {
        return [
            // 1. User Account Details
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'], // Unique check
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            // 2. Student Profile
            'dob'               => ['nullable', 'date', 'before:today'],
            'bio'               => ['nullable', 'string', 'max:1000'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'permanent_address' => ['nullable', 'string', 'max:1000'],
            // academic_status is ignored here because Service forces it to 'enrolled'

            // 3. Nested Array Validation
            'education'   => ['nullable', 'array'],
            'education.*.degree' => ['required_with:education', 'string', 'max:255'],
            'education.*.institution' => ['required_with:education', 'string', 'max:255'],
            'education.*.passing_year' => ['required_with:education', 'string', 'max:20'],

            'skills'      => ['nullable', 'array'],
            'skills.*.skill_name' => ['required_with:skills', 'string', 'max:255'],
            'skills.*.proficiency' => ['required_with:skills', Rule::in(['Beginner', 'Intermediate', 'Expert'])],

            'guardians'   => ['nullable', 'array'],
            'guardians.*.name' => ['required_with:guardians', 'string', 'max:255'],
            'guardians.*.relationship' => ['required_with:guardians', 'string', 'max:100'],
            'guardians.*.phone' => ['required_with:guardians', 'string', 'max:20'],
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'This email is already registered in the system.',
            'education.*.degree.required_with' => 'Degree is required for education entries.',
        ];
    }
}