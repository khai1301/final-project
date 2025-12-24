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
            // Basic Info - all optional for partial updates
            'phone' => 'nullable|string|max:20',
            'experience_years' => 'nullable|integer|min:0|max:50',
            
            // Teaching Info - optional for partial updates
            'subjects' => 'nullable|array|min:1',
            'subjects.*' => 'required_with:subjects|exists:subjects,id',
            'education' => 'nullable|string|max:2000',
            'bio' => 'nullable|string|max:1000',
            
            // Rates - optional for partial updates
            'hourly_rate_min' => 'nullable|numeric|min:100000|max:5000000',
            'hourly_rate_max' => 'nullable|numeric|min:100000|max:5000000|gte:hourly_rate_min',
            
            // Teaching Areas - optional
            'teaching_areas' => 'nullable|string',
            
            // File Uploads - always optional
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB
            'certificates' => 'nullable|array|max:10',
            'certificates.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB each
            
            // Availability - optional
            'availability' => 'nullable|array',
            'availability.*' => 'array',
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation()
    {
        // Keep teaching_areas as JSON string for validation
        // Controller will parse it when saving
        
        if ($this->has('skills') && is_string($this->skills)) {
            $skills = json_decode($this->skills, true);
            $this->merge(['skills' => $skills ?: []]);
        }
    }


    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'subjects.required' => 'Vui lòng thêm ít nhất một môn học bạn dạy.',
            'subjects.min' => 'Vui lòng thêm ít nhất một môn học bạn dạy.',
            'experience_years.required' => 'Vui lòng chọn số năm kinh nghiệm của bạn.',
            'hourly_rate_min.required' => 'Vui lòng nhập mức giá tối thiểu mỗi giờ.',
            'hourly_rate_min.min' => 'Mức giá tối thiểu phải ít nhất 100.000 VNĐ.',
            'hourly_rate_max.required' => 'Vui lòng nhập mức giá tối đa mỗi giờ.',
            'hourly_rate_max.gte' => 'Mức giá tối đa phải lớn hơn hoặc bằng mức giá tối thiểu.',
            'teaching_areas.required' => 'Vui lòng thêm ít nhất một khu vực/địa điểm dạy học.',
            'teaching_areas.min' => 'Vui lòng thêm ít nhất một khu vực/địa điểm dạy học.',
            'avatar.image' => 'Ảnh đại diện phải là một hình ảnh.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 5MB.',
            'cv.mimes' => 'CV phải là file PDF hoặc DOC.',
            'cv.max' => 'File CV không được vượt quá 10MB.',
            'certificates.*.max' => 'Mỗi file chứng chỉ không được vượt quá 10MB.',
            'certificates.max' => 'Bạn có thể tải lên tối đa 10 chứng chỉ.',
        ];
    }
}
