<?php
namespace App\Http\Controllers;

use App\Models\Request as LearningRequest;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\LearningMode;
use App\Http\Requests\StoreStudentRequest;
use Illuminate\Support\Facades\Auth;

class StudentRequestController extends Controller
{
    /**
     * Show the request creation form.
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $educationLevels = EducationLevel::active()->ordered()->get();
        $learningModes = LearningMode::active()->get();
        $timeSlots = \App\Models\TimeSlot::active()->orderBy('day_of_week')->orderBy('start_time')->get();
        
        // Load location data for client-side filtering
        $provinces = \App\Models\Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name', 'type', 'code', 'province_code']);
        
        return view('frontend.home.student', compact('subjects', 'educationLevels', 'learningModes', 'timeSlots', 'provinces', 'wards'));
    }

    /**
     * Store a new learning request.
     */
    public function store(StoreStudentRequest $request)
    {
        // Parse skills from JSON string if provided
        $skills = null;
        if ($request->filled('skills')) {
            $skillsData = json_decode($request->input('skills'), true);
            $skills = is_array($skillsData) ? $skillsData : null;
        }

        // Lookup foreign keys by ID (database-driven)
        $subject = Subject::where('name', $request->input('subject'))->first();
        $educationLevel = EducationLevel::where('name', $request->input('education_level'))->first();
        $learningMode = LearningMode::find($request->input('learning_mode_id'));

        // Determine location: custom (if provided) OR user profile
        $provinceId = $request->filled('province_id') ? $request->input('province_id') : auth()->user()->province_id;
        $wardId = $request->filled('ward_id') ? $request->input('ward_id') : auth()->user()->ward_id;
        $addressDetail = $request->filled('address_detail') ? $request->input('address_detail') : auth()->user()->address_detail;
        
        // Create the learning request (only FK columns + core data)
        $learningRequest = LearningRequest::create([
            'student_id' => Auth::id(),
            'title' => "Learning request for " . $request->input('subject'),
            'subject_id' => $subject ? $subject->id : null,
            'education_level_id' => $educationLevel ? $educationLevel->id : null,
            'learning_mode_id' => $learningMode ? $learningMode->id : null,
            'skills' => $skills,
            'province_id' => $provinceId,
            'ward_id' => $wardId,
            'address_detail' => $addressDetail,
            'budget_min' => $request->input('budget_min'),
            'budget_max' => $request->input('budget_max'),
            'description' => $request->input('notes'),
            'status' => 'open',
        ]);

        // Attach time slots if provided
        if ($request->filled('time_slots') && is_array($request->input('time_slots'))) {
            $learningRequest->timeSlots()->attach($request->input('time_slots'));
        }

        return redirect()
            ->route('student.request.create')
            ->with('success', 'Yêu cầu học tập của bạn đã được gửi thành công! Hệ thống AI sẽ ghép đôi bạn với gia sư phù hợp nhất.');
    }
}
