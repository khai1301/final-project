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
            'education_level' => 'required|string|in:Elementary,Middle School,High School,Undergraduate,Postgraduate,Professional Certification,Hobby / Casual',
            'skills' => 'nullable|string',
            'mode' => 'required|in:online,offline',
            'address' => 'required_if:mode,offline|nullable|string|max:500',
            'schedule' => 'required|array|min:1',
            'schedule.*' => 'string',
            'budget_min' => 'required|numeric|min:10|max:200',
            'budget_max' => 'required|numeric|min:10|max:200|gte:budget_min',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages()
    {
        return [
            'subject.required' => 'Please enter the subject you want to learn.',
            'education_level.required' => 'Please select your education level.',
            'education_level.in' => 'Please select a valid education level.',
            'mode.required' => 'Please select a learning mode.',
            'mode.in' => 'Please select either online or in-person learning.',
            'address.required_if' => 'Please enter a learning location address for in-person sessions.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'schedule.required' => 'Please select at least one preferred schedule.',
            'schedule.min' => 'Please select at least one preferred schedule.',
            'budget_min.required' => 'Please enter your minimum hourly rate.',
            'budget_min.min' => 'Minimum budget must be at least $10.',
            'budget_min.max' => 'Minimum budget cannot exceed $200.',
            'budget_max.required' => 'Please enter your maximum hourly rate.',
            'budget_max.min' => 'Maximum budget must be at least $10.',
            'budget_max.max' => 'Maximum budget cannot exceed $200.',
            'budget_max.gte' => 'Maximum budget must be greater than or equal to minimum budget.',
            'notes.max' => 'Additional notes cannot exceed 1000 characters.',
        ];
    }
}
