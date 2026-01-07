<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User must be authenticated to reach this
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'province_id' => 'nullable|exists:provinces,id',
            'ward_id' => 'nullable|exists:wards,id',
            'address_detail' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'phone.max' => 'Số điện thoại không hợp lệ.',
            'avatar.image' => 'File phải là ảnh.',
            'avatar.mimes' => 'Ảnh phải có định dạng: JPG, JPEG hoặc PNG.',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'province_id' => 'Tỉnh/Thành phố',
            'ward_id' => 'Quận/Huyện',
            'address_detail' => 'Địa chỉ cụ thể',
            'avatar' => 'Ảnh đại diện',
        ];
    }
}
