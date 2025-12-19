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
        return auth()->check() && auth()->user()->role === 'tutor';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic Info
            'phone' => 'nullable|string|max:20',
            'experience_years' => 'required|integer|min:0|max:50',
            
            // Teaching Info
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'string|max:255',
            'education' => 'nullable|string|max:2000',
            'bio' => 'nullable|string|max:1000',
            
            // Rates
            'hourly_rate_min' => 'required|numeric|min:100000|max:5000000',
            'hourly_rate_max' => 'required|numeric|min:100000|max:5000000|gte:hourly_rate_min',
            
            // Teaching Areas
            'teaching_areas' => 'required|array|min:1',
            'teaching_areas.*' => 'string|max:255',
            
            // File Uploads
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB
            'certificates' => 'nullable|array|max:10',
            'certificates.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB each
            
            // Availability
            'availability' => 'nullable|array',
            'availability.*' => 'array',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'subjects.required' => 'Please add at least one subject you teach.',
            'subjects.min' => 'Please add at least one subject you teach.',
            'experience_years.required' => 'Please select your years of experience.',
            'hourly_rate_min.required' => 'Please enter your minimum hourly rate.',
            'hourly_rate_min.min' => 'Minimum rate must be at least 100,000 VNĐ.',
            'hourly_rate_max.required' => 'Please enter your maximum hourly rate.',
            'hourly_rate_max.gte' => 'Maximum rate must be greater than or equal to minimum rate.',
            'teaching_areas.required' => 'Please add at least one teaching area/location.',
            'teaching_areas.min' => 'Please add at least one teaching area/location.',
            'avatar.image' => 'Profile photo must be an image.',
            'avatar.max' => 'Profile photo must not exceed 5MB.',
            'cv.mimes' => 'CV must be a PDF or DOC file.',
            'cv.max' => 'CV file must not exceed 10MB.',
            'certificates.*.max' => 'Each certificate file must not exceed 10MB.',
            'certificates.max' => 'You can upload maximum 10 certificates.',
        ];
    }
}
