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
        
        return view('frontend.home.student', compact('subjects', 'educationLevels', 'learningModes'));
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

        // Create the learning request
        $learningRequest = LearningRequest::create([
            'student_id' => Auth::id(),
            'title' => "Learning request for " . $request->input('subject'),
            'subject' => $request->input('subject'),
            'subject_id' => $subject ? $subject->id : null,
            'education_level' => $request->input('education_level'),
            'education_level_id' => $educationLevel ? $educationLevel->id : null,
            'grade' => $request->input('education_level'),
            'skills' => $skills,
            'mode' => $learningMode ? $learningMode->name : null,
            'learning_mode_id' => $learningMode ? $learningMode->id : null,
            'location_type' => $learningMode ? $learningMode->slug : null,
            'address' => $request->input('address'),
            'schedule' => $request->input('schedule'),
            'budget_min' => $request->input('budget_min'),
            'budget_max' => $request->input('budget_max'),
            'description' => $request->input('notes'),
            'status' => 'open',
        ]);

        return redirect()
            ->route('student.request.create')
            ->with('success', 'Yêu cầu học tập của bạn đã được gửi thành công! Hệ thống AI sẽ ghép đôi bạn với gia sư phù hợp nhất.');
    }
}
