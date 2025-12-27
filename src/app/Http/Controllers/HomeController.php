<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     * Redirects authenticated users to role-specific dashboards.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Guest → public home
        if (!$user) {
            return view('frontend.home.index');
        }
        
        $tutors = collect([]);
        $students = collect([]);
        $pendingRequests = [];
        $incomingRequests = collect([]);
        $userMatchings = collect([]);
        
        // Load data based on role
        if ($user->isStudent()) {
            // Load approved tutors for students
            $tutors = \App\Models\User::where('role', 'tutor')
                ->whereHas('tutorProfile', function($q) {
                    $q->where('is_approved', true);
                })
                ->with('tutorProfile.subjects')
                ->latest()
                ->get();
            
            // Get pending requests sent by this student
            $pendingRequests = \App\Models\Matching::where('student_id', $user->id)
                ->where('status', 'pending')
                ->pluck('tutor_id')
                ->toArray();
            
            // Get incoming requests (from tutors)
            $incomingRequests = \App\Models\Matching::where('student_id', $user->id)
                ->where('status', 'pending')
                ->whereNotIn('sender_id', [$user->id])
                ->with(['sender', 'tutor'])
                ->latest()
                ->get();
        } elseif ($user->isTutor()) {
            // Load students for tutors
            $students = \App\Models\User::where('role', 'student')
                ->with('studentProfile')
                ->latest()
                ->get();
            
            // Get pending requests sent by this tutor
            $pendingRequests = \App\Models\Matching::where('tutor_id', $user->id)
                ->where('status', 'pending')
                ->pluck('student_id')
                ->toArray();
            
            // Get incoming requests (from students)
            $incomingRequests = \App\Models\Matching::where('tutor_id', $user->id)
                ->where('status', 'pending')
                ->whereNotIn('sender_id', [$user->id])
                ->with(['sender'])
                ->latest()
                ->get();
        }
        
        // Load all matchings for authenticated users to check status
        if ($user && ($user->isStudent() || $user->isTutor())) {
            $userMatchings = \App\Models\Matching::forUser($user->id)
                ->whereIn('status', ['pending', 'accepted', 'declined'])
                ->get()
                ->mapWithKeys(function($matching) use ($user) {
                    $otherId = $matching->getOtherUser($user->id)->id;
                    return [$otherId => $matching];
                });
        }
        
        // Load data for ALL users (guest + authenticated) for landing page partials
        
        // Featured Tutors: Newest tutors (for featured-tutors.blade.php)
        $featuredTutors = \App\Models\User::where('role', 'tutor')
            ->whereHas('tutorProfile', function($q) {
                $q->where('is_approved', true);
            })
            ->with('tutorProfile.subjects')
            ->latest()
            ->take(8)
            ->get();
        
        // Top Tutors: By rating (for top-tutors.blade.php)
        $topTutors = \App\Models\User::where('role', 'tutor')
            ->whereHas('tutorProfile', function($q) {
                $q->where('is_approved', true);
            })
            ->with('tutorProfile.subjects')
            ->leftJoin('tutor_profiles', 'users.id', '=', 'tutor_profiles.user_id')
            ->orderByDesc('tutor_profiles.rating_avg')
            ->orderByDesc('users.created_at')
            ->select('users.*')
            ->take(8)
            ->get();
        
        // Student Requests: Latest student requests (for tutor-requests.blade.php)
        $studentRequests = \App\Models\Request::with(['student', 'subjectRelation', 'educationLevelRelation'])
            ->where('status', 'open')
            ->latest()
            ->take(6)
            ->get();
        
        // For AI recommendations
        $latestRequestId = null;
        $tutorProfileId = null;
        
        if ($user && $user->isStudent()) {
            // Get student's latest open request for AI recommendations
            $latestRequest = \App\Models\Request::where('student_id', $user->id)
                ->where('status', 'open')
                ->latest()
                ->first();
            $latestRequestId = $latestRequest->id ?? null;
        } elseif ($user && $user->isTutor()) {
            // Get tutor's profile ID for AI recommendations
            $tutorProfileId = $user->tutorProfile->id ?? null;
        }
        
        return view('frontend.home.index', compact(
            'tutors', 
            'students', 
            'pendingRequests', 
            'incomingRequests', 
            'userMatchings',
            'featuredTutors',
            'topTutors',
            'studentRequests',
            'latestRequestId',
            'tutorProfileId'
        ));
    }
}
