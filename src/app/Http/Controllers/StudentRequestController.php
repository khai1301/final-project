<?php
namespace App\Http\Controllers;

use App\Models\Request as LearningRequest;
use App\Http\Requests\StoreStudentRequest;
use Illuminate\Support\Facades\Auth;

class StudentRequestController extends Controller
{
    /**
     * Show the request creation form.
     */
    public function create()
    {
        return view('frontend.home.student');
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

        // Create the learning request
        $learningRequest = LearningRequest::create([
            'student_id' => Auth::id(),
            'title' => "Learning request for " . $request->input('subject'),
            'subject' => $request->input('subject'),
            'education_level' => $request->input('education_level'),
            'grade' => $request->input('education_level'),
            'skills' => $skills,
            'mode' => $request->input('mode'),
            'location_type' => $request->input('mode'),
            'address' => $request->input('address'),
            'schedule' => $request->input('schedule'),
            'budget_min' => $request->input('budget_min'),
            'budget_max' => $request->input('budget_max'),
            'description' => $request->input('notes'),
            'status' => 'open',
        ]);

        return redirect()
            ->route('student.request.create')
            ->with('success', 'Your learning request has been submitted successfully! Our AI will match you with the best tutors.');
    }
}
