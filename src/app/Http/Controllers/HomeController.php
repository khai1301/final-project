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
            // Load data for Guest Home
            // Featured Tutors
            $featuredTutors = \App\Models\User::where('role', 'tutor')
                ->whereHas('tutorProfile', function($q) {
                    $q->where('is_approved', true);
                })
                ->with(['tutorProfile.subjects', 'province', 'tutorProfile' => function($query) {
                    $query->withCount(['certificates', 'teachingAreas', 'availableTimeSlots']);
                }])
                ->latest()
                ->take(8)
                ->get();
            
            // Top Tutors (Highest completed matchings)
            $topTutors = \App\Models\User::where('role', 'tutor')
                ->whereHas('tutorProfile', function($q) {
                    $q->where('is_approved', true);
                })
                ->with(['tutorProfile.subjects', 'province', 'tutorProfile' => function($query) {
                    $query->withCount(['certificates', 'teachingAreas', 'availableTimeSlots']);
                }])
                ->withCount(['tutorMatchings' => function ($query) {
                    $query->where('status', 'accepted')
                          ->where('contact_unlocked', true);
                }])
                ->orderByDesc('tutor_matchings_count')
                ->take(4)
                ->get();
            
            // Student Requests (Job Feed Preview)
            $studentRequests = \App\Models\Request::with(['student', 'subject', 'educationLevel'])
                ->where('status', 'open')
                ->latest()
                ->take(8)
                ->get();

            // Stats Data
            $totalStudents = \App\Models\User::where('role', 'student')->count();
            $totalTutors = \App\Models\User::where('role', 'tutor')
                ->whereHas('tutorProfile', function($q) {
                    $q->where('is_approved', true);
                })->count();
            $totalSubjects = \App\Models\Subject::count();
            $totalAcceptedMatches = \App\Models\Matching::where('status', 'accepted')->count();

            return view('frontend.home.index', compact(
                'featuredTutors', 
                'topTutors', 
                'studentRequests', 
                'totalStudents', 
                'totalTutors', 
                'totalSubjects',
                'totalAcceptedMatches'
            ));
        }
        
        $myRequests = collect([]);
        $pendingRequests = [];
        $incomingRequests = collect([]);
        $activeClassesCount = 0;
        $activeRequestsCount = 0;
        
        // Initialize collections
        $aiRecommendedTutors = collect([]);
        $aiRecommendedRequests = collect([]);
        $topTutors = collect([]);
        $studentRequests = collect([]);

        // Load data based on role
        if ($user) {
            if ($user->isStudent()) {
                // My Student Data
                $myRequests = \App\Models\Request::where('student_id', $user->id)
                    ->with(['subject'])
                    ->latest()
                    ->get();
                
                $pendingRequests = \App\Models\Matching::where('student_id', $user->id)
                    ->where('status', 'pending')
                    ->pluck('tutor_id')
                    ->toArray();
                
                $incomingRequests = \App\Models\Matching::where('student_id', $user->id)
                    ->where('status', 'pending')
                    ->whereNotIn('sender_id', [$user->id])
                    ->with(['sender', 'tutor'])
                    ->latest()
                    ->get();

                $activeClassesCount = \App\Models\Matching::where('student_id', $user->id)
                    ->where('status', 'accepted')
                    ->count();

                // --- SMART MATCHING LOGIC FOR STUDENT ---
                $latestRequest = \App\Models\Request::where('student_id', $user->id)
                    ->where('status', 'open')
                    ->latest()
                    ->first();

                // 1. AI Recommendations (Real OpenAI Service)
                if ($latestRequest) {
                    try {
                        // Use the MatchingService to get AI recommendations
                        $matchingService = app(\App\Services\MatchingService::class);
                        $aiResult = $matchingService->recommendTutorsForRequest($latestRequest->id);
                        
                        if ($aiResult['success'] && !empty($aiResult['data'])) {
                            // Extract scores mapped by user_id
                            $scores = [];
                            $reasons = [];
                            $userIds = [];
                            
                            foreach ($aiResult['data'] as $item) {
                                $uid = $item['user_id'];
                                $userIds[] = $uid;
                                $scores[$uid] = $item['match_score'];
                                $reasons[$uid] = $item['match_reason'];
                            }
                            
                            // Fetch Eloquent Models to maintain View compatibility
                            $aiRecommendedTutors = \App\Models\User::whereIn('id', $userIds)
                                ->with(['tutorProfile.subjects', 'province', 'tutorProfile.teachingAreas'])
                                ->get()
                                ->map(function($user) use ($scores, $reasons) {
                                    $user->match_score = $scores[$user->id] ?? 0;
                                    $user->match_reason = $reasons[$user->id] ?? '';
                                    return $user;
                                })
                                ->sortByDesc('match_score')
                                ->values();
                        } 
                    } catch (\Exception $e) {
                        \Log::error('Home AI Error: ' . $e->getMessage());
                        $aiRecommendedTutors = collect([]);
                    }
                }

                // 2. Regular List (Sorted by Relevance)
                // Use default logic if AI fails or just as secondary list
                $topTutorsQuery = \App\Models\User::where('role', 'tutor')
                    ->whereHas('tutorProfile', function($q) {
                        $q->where('is_approved', true);
                    })
                    ->with(['tutorProfile.subjects', 'province', 'tutorProfile' => function($query) {
                        $query->withCount(['certificates', 'teachingAreas', 'availableTimeSlots']);
                    }])
                    ->withCount(['tutorMatchings' => function ($query) {
                        $query->where('status', 'accepted')->where('contact_unlocked', true);
                    }]);

                if ($latestRequest) {
                    $subjectId = $latestRequest->subject_id;
                    $provinceId = $latestRequest->province_id;
                    $budgetMin = $latestRequest->budget_min;
                    $budgetMax = $latestRequest->budget_max;

                    // 1. Subject Match
                    if ($subjectId) {
                        $topTutorsQuery->orderByRaw("(
                            SELECT COUNT(*) 
                            FROM tutor_profile_subject 
                            INNER JOIN tutor_profiles ON tutor_profiles.id = tutor_profile_subject.tutor_profile_id 
                            WHERE tutor_profiles.user_id = users.id 
                            AND tutor_profile_subject.subject_id = ?
                        ) DESC", [$subjectId]);
                    }

                    // 2. Level Match (Skipped - No data)

                    // 3. Address Match
                    if ($provinceId) {
                        $topTutorsQuery->orderByRaw("(
                            SELECT COUNT(*) 
                            FROM tutor_teaching_areas 
                            INNER JOIN tutor_profiles ON tutor_profiles.id = tutor_teaching_areas.tutor_profile_id 
                            WHERE tutor_profiles.user_id = users.id 
                            AND tutor_teaching_areas.province_id = ?
                        ) DESC", [$provinceId]);
                    }

                    // 4. Budget Match (Overlap)
                    if ($budgetMin && $budgetMax) {
                        $topTutorsQuery->orderByRaw("(
                            SELECT COUNT(*) 
                            FROM tutor_profiles 
                            WHERE tutor_profiles.user_id = users.id 
                            AND tutor_profiles.hourly_rate_min <= ? 
                            AND tutor_profiles.hourly_rate_max >= ?
                        ) DESC", [$budgetMax, $budgetMin]);
                    }
                    
                    // Fallback
                    $topTutorsQuery->orderByDesc('tutor_matchings_count');
                } else {
                     if ($user->province_id) {
                        $topTutorsQuery->orderByRaw("CASE WHEN province_id = ? THEN 1 ELSE 0 END DESC", [$user->province_id]);
                    }
                    $topTutorsQuery->orderByDesc('tutor_matchings_count');
                }

                $topTutors = $topTutorsQuery->take(8)->get();

                // Append matching status for current student's latest request
                if ($latestRequest) {
                    // Optimized: Eager load specific matchings to avoid N+1 queries
                    $topTutors->load(['tutorMatchings' => function($q) use ($user, $latestRequest) {
                        $q->where('student_id', $user->id)
                          ->where('request_id', $latestRequest->id)
                          ->whereIn('status', ['pending', 'accepted', 'declined']);
                    }]);
                    
                    $topTutors->each(function($tutor) {
                        $tutor->connection_status = $tutor->tutorMatchings->first()?->status;
                    });
                    
                    // Also apply to AI Recommendations
                    if ($aiRecommendedTutors->isNotEmpty()) {
                        $aiRecommendedTutors->load(['tutorMatchings' => function($q) use ($user, $latestRequest) {
                            $q->where('student_id', $user->id)
                              ->where('request_id', $latestRequest->id)
                              ->whereIn('status', ['pending', 'accepted', 'declined']);
                        }]);
                        $aiRecommendedTutors->each(function($tutor) {
                             $tutor->connection_status = $tutor->tutorMatchings->first()?->status;
                        });
                    }
                }


            } elseif ($user->isTutor()) {
                // My Tutor Data
                $pendingRequests = \App\Models\Matching::where('tutor_id', $user->id)
                    ->where('status', 'pending')
                    ->pluck('student_id')
                    ->toArray();
                
                $incomingRequests = \App\Models\Matching::where('tutor_id', $user->id)
                    ->where('status', 'pending')
                    ->whereNotIn('sender_id', [$user->id])
                    ->with(['sender'])
                    ->latest()
                    ->get();

                $activeClassesCount = \App\Models\Matching::where('tutor_id', $user->id)
                    ->where('status', 'accepted')
                    ->count();

                // --- SMART MATCHING LOGIC FOR TUTOR ---
                $tutorProfile = $user->tutorProfile;
                $isApproved = $tutorProfile && $tutorProfile->is_approved;

                // 1. AI Recommendations (Real OpenAI Service)
                if ($isApproved) {
                    try {
                        $matchingService = app(\App\Services\MatchingService::class);
                        $aiResult = $matchingService->recommendRequestsForTutor($tutorProfile->id);

                        if ($aiResult['success'] && !empty($aiResult['data'])) {
                            $scores = [];
                            $reasons = [];
                            $requestIds = [];

                            foreach ($aiResult['data'] as $item) {
                                $rid = $item['request_id'];
                                $requestIds[] = $rid;
                                $scores[$rid] = $item['match_score'];
                                $reasons[$rid] = $item['match_reason'];
                            }

                            $aiRecommendedRequests = \App\Models\Request::with(['student', 'subject', 'educationLevel'])
                                ->whereIn('id', $requestIds)
                                ->get()
                                ->map(function($request) use ($scores, $reasons) {
                                    $request->match_score = $scores[$request->id] ?? 0;
                                    $request->match_reason = $reasons[$request->id] ?? '';
                                    return $request;
                                })
                                ->sortByDesc('match_score')
                                ->values();
                        }
                    } catch (\Exception $e) {
                         \Log::error('Home AI Tutor Error: ' . $e->getMessage());
                         $aiRecommendedRequests = collect([]);
                    }
                }

                // 2. Regular List (Sorted by Relevance: Subject > Address > Budget)
                $studentRequestsQuery = \App\Models\Request::with(['student', 'subject', 'educationLevel'])
                    ->where('status', 'open');

                if ($tutorProfile && $isApproved) {
                    $subjectIds = $tutorProfile->subjects->pluck('id')->toArray();
                    $provinceIds = $tutorProfile->teachingAreas->pluck('province_id')->toArray();
                    $minRate = $tutorProfile->hourly_rate_min;

                    // 1. Subject Match
                    if (!empty($subjectIds)) {
                         $subjectsString = implode(',', $subjectIds);
                         $studentRequestsQuery->orderByRaw("CASE WHEN subject_id IN ($subjectsString) THEN 1 ELSE 0 END DESC");
                    }

                    // 2. Level Match (Skipped)

                    // 3. Address Match
                    if (!empty($provinceIds)) {
                         $provincesString = implode(',', $provinceIds);
                         $studentRequestsQuery->orderByRaw("CASE WHEN province_id IN ($provincesString) THEN 1 ELSE 0 END DESC");
                    }

                    // 4. Budget Match
                    if ($minRate) {
                        $studentRequestsQuery->orderByRaw("CASE WHEN budget_max >= ? THEN 1 ELSE 0 END DESC", [$minRate]);
                    }
                }
                
                $studentRequests = $studentRequestsQuery->latest()->take(10)->get();
            }
        } else {
            // --- GUEST LOGIC ---
            
            // 1. Top Tutors (Highest completed matchings)
            $topTutors = \App\Models\User::where('role', 'tutor')
                ->whereHas('tutorProfile', function($q) {
                    $q->where('is_approved', true);
                })
                ->with(['tutorProfile.subjects', 'province', 'tutorProfile' => function($query) {
                    $query->withCount(['certificates', 'teachingAreas', 'availableTimeSlots']);
                }])
                ->withCount(['tutorMatchings' => function ($query) {
                    $query->where('status', 'accepted')
                          ->where('contact_unlocked', true);
                }])
                ->orderByDesc('tutor_matchings_count')
                ->take(8)
                ->get();

            // 2. Latest Requests
            $studentRequests = \App\Models\Request::with(['student', 'subject', 'educationLevel'])
                ->where('status', 'open')
                ->latest()
                ->take(10)
                ->get();
        }
        
        // Shared Data & Fallbacks
        $totalStudents = \App\Models\User::where('role', 'student')->count();
        $totalTutors = \App\Models\User::where('role', 'tutor')->whereHas('tutorProfile', fn($q) => $q->where('is_approved', true))->count();
        $totalSubjects = \App\Models\Subject::count();
        $totalAcceptedMatches = \App\Models\Matching::where('status', 'accepted')->count();

        return view('frontend.home.index', compact(
            'myRequests', 
            'pendingRequests', 
            'incomingRequests', 
            'activeClassesCount',
            'studentRequests',
            'topTutors',
            'aiRecommendedTutors',
            'aiRecommendedRequests',
            'totalStudents',
            'totalTutors',
            'totalSubjects',
            'totalAcceptedMatches'
        ));
    }
}
