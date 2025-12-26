<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTutorProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // If user has base location (province_id), teaching areas are optional
        // If no base location, at least 1 teaching area is required
        $hasBaseLocation = $this->filled('province_id');
        
        return [
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'address_detail' => ['nullable', 'string', 'max:500'],
            
            // Profile fields
            'education' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'hourly_rate_min' => ['required', 'numeric', 'min:0'],
            'hourly_rate_max' => ['required', 'numeric', 'min:0', 'gte:hourly_rate_min'],
            'bio' => ['nullable', 'string', 'max:2000'],
            
            // Subjects (required, at least one)
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['exists:subjects,id'],
            
            // Skills
            'skills' => ['nullable', 'string'],
            
            // Teaching areas - conditional: optional if base location exists, required if not
            'teaching_areas' => [$hasBaseLocation ? 'nullable' : 'required', 'array'],
            'teaching_areas.*.province_id' => ['required', 'exists:provinces,id'],
            'teaching_areas.*.ward_id' => ['nullable', 'exists:wards,id'],
            
            // Available time slots
            'time_slots' => ['nullable', 'array'],
            'time_slots.*' => ['exists:time_slots,id'],
            
            // File uploads
            'avatar' => ['nullable', 'image', 'max:5120'], // 5MB
            'cv' => ['nullable', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB
            'certificates' => ['nullable', 'array'],
            'certificates.*' => ['image', 'max:5120'], // 5MB per certificate
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'subjects.required' => 'Please select at least one subject you teach.',
            'subjects.min' => 'Please select at least one subject you teach.',
            'teaching_areas.required' => 'Please add at least one teaching area or set your base location above.',
            'teaching_areas.*.province_id.required' => 'Province is required for each teaching area.',
            'teaching_areas.*.province_id.exists' => 'Selected province is invalid.',
            'teaching_areas.*.ward_id.exists' => 'Selected ward is invalid.',
            'hourly_rate_max.gte' => 'Maximum rate must be greater than or equal to minimum rate.',
        ];
    }
}
