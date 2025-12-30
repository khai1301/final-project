<?php

namespace App\Http\Controllers;

use App\Models\Request as StudentRequest;
use Illuminate\Http\Request;

class RequestBrowseController extends Controller
{
    /**
     * Browse all open student requests (for tutors)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $subjectId = $request->input('subject_id');
        $educationLevelId = $request->input('education_level_id');
        $learningModeId = $request->input('learning_mode_id');
        $provinceId = $request->input('province_id');
        
        $requests = StudentRequest::with(['student', 'subject', 'educationLevel', 'learningMode', 'province', 'ward'])
            ->where('status', 'open')
            
            // Search by title or description
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            
            // Filter by subject
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            
            // Filter by education level
            ->when($educationLevelId, fn($q) => $q->where('education_level_id', $educationLevelId))
            
            // Filter by learning mode
            ->when($learningModeId, fn($q) => $q->where('learning_mode_id', $learningModeId))
            
            // Filter by province
            ->when($provinceId, fn($q) => $q->where('province_id', $provinceId))
            
            ->latest()
            ->paginate(12)
            ->withQueryString();
        
        // Get filter options
        $subjects = \App\Models\Subject::active()->orderBy('name')->get();
        $educationLevels = \App\Models\EducationLevel::active()->orderBy('order')->get();
        $learningModes = \App\Models\LearningMode::active()->get();
        $provinces = \App\Models\Province::orderBy('name')->get();
        
        return view('frontend.requests.browse', compact(
            'requests', 
            'subjects', 
            'educationLevels', 
            'learningModes',
            'provinces'
        ));
    }
}
