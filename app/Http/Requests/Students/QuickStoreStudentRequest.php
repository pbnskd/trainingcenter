<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuickStoreStudentRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            // 1. User Account Info
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string',
            'avatar'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // 2. Student Profile Info
            'dob'               => 'nullable|date',
            'bio'               => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string',

            // 3. Course, Batch & Enrollment Info
            'course_id'         => 'required|exists:courses,id',
            'batch_id'          => [
                'required',
                // Ensure the selected batch belongs to the selected course
                Rule::exists('batches', 'id')->where(function ($query) {
                    return $query->where('course_id', $this->course_id)
                                 ->whereNull('deleted_at'); 
                }),
            ],
            'is_full_course'    => 'boolean',
            'enrolled_at'       => 'nullable|date',

            // 4. Education History (Array Validation)
            'education'                       => 'nullable|array',
            'education.*.degree'              => 'required_with:education|string|max:255',
            'education.*.institution'         => 'required_with:education|string|max:255',
            'education.*.passing_year'        => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 5),
            'education.*.grade_or_percentage' => 'nullable|string|max:50',

            // 5. Skills (Array Validation)
            'skills'                 => 'nullable|array',
            'skills.*.skill_name'    => 'required_with:skills|string|max:255',
            'skills.*.proficiency'   => 'nullable|string|max:50',

            // 6. Guardians (Array Validation)
            'guardians'                  => 'nullable|array',
            'guardians.*.name'           => 'required_with:guardians|string|max:255',
            'guardians.*.relationship'   => 'required_with:guardians|string|max:255',
            'guardians.*.phone'          => 'nullable|string|max:20',
            'guardians.*.email'          => 'nullable|email|max:255',
        ];
    }
    
    public function messages()
    {
        return [
            'batch_id.exists' => 'The selected batch is invalid or does not belong to the selected course.',
            
            // Customizing array error messages to be more user-friendly
            'education.*.degree.required_with' => 'The degree name is required if you are adding an education record.',
            'guardians.*.name.required_with'   => 'The guardian name is required if you are adding a guardian.',
        ];
    }
}