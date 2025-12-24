<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'subject' => 'required|string|max:255',
            'education_level' => 'required|string|max:255',
            'skills' => 'nullable|string',
            'learning_mode_id' => 'required|integer|exists:learning_modes,id',
            'address' => 'nullable|string|max:500',
            'schedule' => 'required|array|min:1',
            'schedule.*' => 'string',
            'budget_min' => 'required|numeric|min:100000|max:5000000',
            'budget_max' => 'required|numeric|min:100000|max:5000000|gte:budget_min',
            'notes' => 'nullable|string|max:1000',
        ];
    }



    /**
     * Get custom validation messages.
     */
    public function messages()
    {
        return [
            'subject.required' => 'Vui lòng nhập môn học bạn muốn học.',
            'education_level.required' => 'Vui lòng chọn trình độ học vấn của bạn.',
            'education_level.in' => 'Vui lòng chọn một trình độ học vấn hợp lệ.',
            'learning_mode_id.required' => 'Vui lòng chọn hình thức học.',
            'learning_mode_id.exists' => 'Hình thức học không hợp lệ.',
            'address.required_if' => 'Vui lòng nhập địa chỉ học cho các buổi học trực tiếp.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
            'schedule.required' => 'Vui lòng chọn ít nhất một lịch học ưa thích.',
            'schedule.min' => 'Vui lòng chọn ít nhất một lịch học ưa thích.',
            'budget_min.required' => 'Vui lòng nhập mức giá tối thiểu mỗi giờ.',
            'budget_min.min' => 'Ngân sách tối thiểu phải ít nhất 100.000 VNĐ.',
            'budget_min.max' => 'Ngân sách tối thiểu không được vượt quá 5.000.000 VNĐ.',
            'budget_max.required' => 'Vui lòng nhập mức giá tối đa mỗi giờ.',
            'budget_max.min' => 'Ngân sách tối đa phải ít nhất 100.000 VNĐ.',
            'budget_max.max' => 'Ngân sách tối đa không được vượt quá 5.000.000 VNĐ.',
            'budget_max.gte' => 'Ngân sách tối đa phải lớn hơn hoặc bằng ngân sách tối thiểu.',
            'notes.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ];
    }
}
