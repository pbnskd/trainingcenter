<?php

namespace App\Http\Requests\Batch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
 public function authorize(): bool
{
    return true; // CHANGED FROM false
}

public function rules(): array
{
    return [
        'day_of_week' => ['required', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
        'hours_per_day' => ['required', 'numeric', 'min:0.5', 'max:24'],
        'description' => ['nullable', 'string', 'max:255'],
    ];
}
}
