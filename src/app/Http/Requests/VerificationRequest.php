<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // All authenticated users can verify
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id_card_image' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png',
                'max:5120', // 5MB
            ],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'id_card_image.required' => 'Vui lòng chọn ảnh CCCD để upload.',
            'id_card_image.file' => 'File upload không hợp lệ.',
            'id_card_image.mimes' => 'Ảnh phải có định dạng: JPEG, JPG hoặc PNG.',
            'id_card_image.max' => 'Kích thước ảnh không được vượt quá 5MB.',
        ];
    }
}
