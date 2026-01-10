<?php

namespace App\Services;

use App\Models\Request;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatchingService
{
    /**
     * Recommend tutors for a student request
     * Uses caching to avoid unnecessary AI API calls
     * 
     * @param int $requestId
     * @param bool $forceRefresh Force refresh cache
     * @return array
     */
    public function recommendTutorsForRequest(int $requestId, bool $forceRefresh = false): array
    {
        $startTime = microtime(true);
        $request = Request::with([
            'subject',
            'educationLevel',
            'learningMode',
            'province',
            'ward',
            'timeSlots'
        ])->findOrFail($requestId);

        // Phase 1: Pre-filtering with database
        $preFilterStart = microtime(true);
        $filteredTutors = $this->preFilterTutors($request);
        $preFilterTime = round((microtime(true) - $preFilterStart) * 1000, 2);

        // --- SMART CACHING STRATEGY ---
        $candidatesHash = $filteredTutors->map(function($t) {
            return $t->id . '_' . $t->updated_at;
        })->implode('|');
        
        // Include Request updated_at in signature so if Request changes, cache invalidates
        $candidateSignature = md5($request->updated_at . '|' . $candidatesHash);
        
        $cacheKeyData = "ai_recs_tutors_data_{$requestId}";
        $cacheKeySig = "ai_recs_tutors_sig_{$requestId}";
        $cacheDuration = 604800; // 7 days

        if (!$forceRefresh && 
            \Illuminate\Support\Facades\Cache::has($cacheKeyData) && 
            \Illuminate\Support\Facades\Cache::get($cacheKeySig) === $candidateSignature) {
            
            $result = \Illuminate\Support\Facades\Cache::get($cacheKeyData);
            $result['cached'] = true;
            $result['execution_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);
            $result['performance']['pre_filter_ms'] = $preFilterTime;
            // Keep original AI time or set to 0? Set to 0 to show it didn't run.
            $result['performance']['ai_ranking_ms'] = 0; 
            return $result;
        }

        if ($filteredTutors->isEmpty()) {
            $result = [
                'success' => true,
                'data' => [],
                'message' => 'Không tìm thấy gia sư phù hợp với yêu cầu của bạn.',
                'cached' => false,
                'cache_expires_at' => now()->addSeconds($cacheDuration)->toIso8601String(),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'performance' => ['pre_filter_ms' => $preFilterTime, 'ai_ranking_ms' => 0]
            ];
            // Cache the empty result too
            \Illuminate\Support\Facades\Cache::put($cacheKeyData, $result, $cacheDuration);
            \Illuminate\Support\Facades\Cache::put($cacheKeySig, $candidateSignature, $cacheDuration);
            
            return $result;
        }

        // Phase 2: AI ranking (Only if signature changed and not empty)
        $aiStart = microtime(true);
        $rankedTutors = $this->rankTutorsWithAI($filteredTutors, $request);
        $aiTime = round((microtime(true) - $aiStart) * 1000, 2);
        
        $totalTime = round((microtime(true) - $startTime) * 1000, 2);

        $result = [
            'success' => true,
            'data' => $rankedTutors,
            'message' => 'Tìm thấy ' . count($rankedTutors) . ' gia sư phù hợp.',
            'cached' => false,
            'cache_expires_at' => now()->addSeconds($cacheDuration)->toIso8601String(),
            'execution_time_ms' => $totalTime,
            'performance' => [
                'pre_filter_ms' => $preFilterTime,
                'ai_ranking_ms' => $aiTime,
                'total_ms' => $totalTime,
                'tutors_filtered' => $filteredTutors->count(),
                'tutors_ranked' => count($rankedTutors)
            ]
        ];

        // Log performance for monitoring
        Log::info('AI Matching Performance - Tutors (Fresh Run)', [
            'request_id' => $requestId,
            'pre_filter_ms' => $preFilterTime,
            'ai_ranking_ms' => $aiTime,
            'total_ms' => $totalTime
        ]);

        // Cache the result AND the signature
        \Illuminate\Support\Facades\Cache::put($cacheKeyData, $result, $cacheDuration);
        \Illuminate\Support\Facades\Cache::put($cacheKeySig, $candidateSignature, $cacheDuration);

        return $result;
    }

    /**
     * Recommend requests for a tutor
     * Uses Smart Caching (Signature-based)
     * 
     * @param int $tutorProfileId
     * @param bool $forceRefresh Force refresh cache
     * @return array
     */
    public function recommendRequestsForTutor(int $tutorProfileId, bool $forceRefresh = false): array
    {
        $startTime = microtime(true);
        $tutorProfile = TutorProfile::with([
            'user',
            'subjects',
            'teachingAreas.ward',
            'availableTimeSlots'
        ])->findOrFail($tutorProfileId);

        // Phase 1: Pre-filtering with database
        $preFilterStart = microtime(true);
        $filteredRequests = $this->preFilterRequests($tutorProfile);
        $preFilterTime = round((microtime(true) - $preFilterStart) * 1000, 2);

        // --- SMART CACHING STRATEGY ---
        $candidatesHash = $filteredRequests->map(function($r) {
            return $r->id . '_' . $r->updated_at;
        })->implode('|');
        
        // Include TutorProfile updated_at in signature
        $candidateSignature = md5($tutorProfile->updated_at . '|' . $candidatesHash);
        
        $cacheKeyData = "ai_recs_requests_data_v2_{$tutorProfileId}";
        $cacheKeySig = "ai_recs_requests_sig_v2_{$tutorProfileId}";
        $cacheDuration = 604800; // 7 days

        if (!$forceRefresh && 
            \Illuminate\Support\Facades\Cache::has($cacheKeyData) && 
            \Illuminate\Support\Facades\Cache::get($cacheKeySig) === $candidateSignature) {
            
            $result = \Illuminate\Support\Facades\Cache::get($cacheKeyData);
            $result['cached'] = true;
            $result['execution_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);
            $result['performance']['pre_filter_ms'] = $preFilterTime;
            $result['performance']['ai_ranking_ms'] = 0;
            return $result;
        }

        if ($filteredRequests->isEmpty()) {
             $result = [
                'success' => true,
                'data' => [],
                'message' => 'Không tìm thấy yêu cầu nào phù hợp với profile của bạn.',
                'cached' => false,
                'cache_expires_at' => now()->addSeconds($cacheDuration)->toIso8601String(),
                'execution_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'performance' => ['pre_filter_ms' => $preFilterTime, 'ai_ranking_ms' => 0]
            ];
            // Cache Empty
            \Illuminate\Support\Facades\Cache::put($cacheKeyData, $result, $cacheDuration);
            \Illuminate\Support\Facades\Cache::put($cacheKeySig, $candidateSignature, $cacheDuration);

            return $result;
        }

        // Phase 2: AI ranking (Fresh)
        $aiStart = microtime(true);
        $rankedRequests = $this->rankRequestsWithAI($filteredRequests, $tutorProfile);
        $aiTime = round((microtime(true) - $aiStart) * 1000, 2);
        
        $totalTime = round((microtime(true) - $startTime) * 1000, 2);

        $result = [
            'success' => true,
            'data' => $rankedRequests,
            'message' => 'Tìm thấy ' . count($rankedRequests) . ' yêu cầu phù hợp.',
            'cached' => false,
            'cache_expires_at' => now()->addSeconds($cacheDuration)->toIso8601String(),
            'execution_time_ms' => $totalTime,
            'performance' => [
                'pre_filter_ms' => $preFilterTime,
                'ai_ranking_ms' => $aiTime,
                'total_ms' => $totalTime,
                'requests_filtered' => $filteredRequests->count(),
                'requests_ranked' => count($rankedRequests)
            ]
        ];

        // Log performance
        Log::info('AI Matching Performance - Requests (Fresh Run)', [
            'tutor_profile_id' => $tutorProfileId,
            'pre_filter_ms' => $preFilterTime,
            'ai_ranking_ms' => $aiTime,
            'total_ms' => $totalTime
        ]);

        // Cache Data & Signature
        \Illuminate\Support\Facades\Cache::put($cacheKeyData, $result, $cacheDuration);
        \Illuminate\Support\Facades\Cache::put($cacheKeySig, $candidateSignature, $cacheDuration);

        return $result;
    }

    /**
     * Phase 1: Pre-filter tutors based on basic criteria
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function preFilterTutors(Request $request)
    {
        $subjectId = $request->subject_id;
        $budgetMax = $request->budget_max;
        $provinceId = $request->province_id;
        $isOnline = $request->learningMode && $request->learningMode->slug === 'online';

        return TutorProfile::query()
            // Only approved tutors
            ->where('is_approved', true)
            // Prevent self-matching (Student sees their own Tutor profile)
            ->where('user_id', '!=', $request->student_id)
            // Exclude tutors who are already connected (pending or accepted) with this request
            ->whereNotIn('user_id', function($query) use ($request) {
                $query->select('tutor_id')
                      ->from('matchings')
                      ->where('request_id', $request->id)
                      ->whereIn('status', ['pending', 'accepted']);
            })
            // Must teach the required subject
            ->whereHas('subjects', function ($query) use ($subjectId) {
                $query->where('subjects.id', $subjectId);
            })
            // Budget must be within range (approximate)
            ->where('hourly_rate_min', '<=', $budgetMax)
            
            // Location Filter: Relaxed if Online
            ->when(!$isOnline && $provinceId, function ($query) use ($provinceId) {
                // If NOT online, enforce Province match
                $query->whereHas('teachingAreas', function ($q) use ($provinceId) {
                    $q->where('province_id', $provinceId);
                });
            })
            
            // Load relationships for AI analysis
            ->with([
                'user',
                'subjects',
                'teachingAreas.ward.province',
                'availableTimeSlots'
            ])
            // Limit to top 30 candidates for AI
            ->limit(30)
            ->get();
    }

    /**
     * Phase 1: Pre-filter requests based on tutor profile
     * 
     * @param TutorProfile $tutorProfile
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function preFilterRequests(TutorProfile $tutorProfile)
    {
        $subjectIds = $tutorProfile->subjects ? $tutorProfile->subjects->pluck('id')->toArray() : [];
        $provinceIds = $tutorProfile->teachingAreas ? $tutorProfile->teachingAreas->pluck('province_id')->unique()->toArray() : [];
        $minRate = $tutorProfile->hourly_rate_min;
        $tutorUserId = $tutorProfile->user_id;

        return Request::query()
            // Only open requests
            ->where('status', 'open')
            // Prevent self-matching (Tutor sees their own Student request)
            ->where('student_id', '!=', $tutorUserId)
            // Exclude requests this tutor is already connected with (pending or accepted)
            ->whereNotIn('id', function($query) use ($tutorUserId) {
                $query->select('request_id')
                      ->from('matchings')
                      ->where('tutor_id', $tutorUserId)
                      ->whereIn('status', ['pending', 'accepted']);
            })
            // Subject must match
            ->when(!empty($subjectIds), function ($query) use ($subjectIds) {
                $query->whereIn('subject_id', $subjectIds);
            })
            // Budget must be acceptable
            ->where('budget_max', '>=', $minRate)
            
            // Location Filter: Province Match OR Request is Online
            ->where(function($query) use ($provinceIds) {
                $query->when(!empty($provinceIds), function ($q) use ($provinceIds) {
                    $q->whereIn('province_id', $provinceIds);
                })
                ->orWhereHas('learningMode', function ($q) {
                    $q->where('slug', 'online');
                });
            })

            // Load relationships for AI analysis
            ->with([
                'student',
                'subject',
                'educationLevel',
                'learningMode',
                'province',
                'ward',
                'timeSlots'
            ])
            // Limit to top 30 candidates for AI
            ->limit(30)
            ->get();
    }

    /**
     * Phase 2: Rank tutors using AI
     * 
     * @param \Illuminate\Database\Eloquent\Collection $tutors
     * @param Request $request
     * @return array
     */
    private function rankTutorsWithAI($tutors, Request $request): array
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            if (!$apiKey) {
                Log::warning('OpenAI API key not configured. Returning tutors without AI ranking.');
                return $this->fallbackTutorRanking($tutors);
            }

            // Prepare request data for AI
            $requestData = [
                'subject' => $request->subject->name ?? 'N/A',
                'education_level' => $request->educationLevel->name ?? 'N/A',
                'learning_mode' => $request->learningMode->name ?? 'N/A',
                'budget_range' => $request->budget_min . ' - ' . $request->budget_max . ' VNĐ/giờ',
                'location' => ($request->ward->name ?? '') . ', ' . ($request->province->name ?? ''),
                'description' => $request->description ?? '',
                'time_slots' => $request->timeSlots ? $request->timeSlots->pluck('name')->implode(', ') : 'Chưa chọn',
            ];

            // Prepare tutors data for AI
            $tutorsData = $tutors->map(function ($tutor, $index) {
                return [
                    'id' => $tutor->id,
                    'user_id' => $tutor->user_id, // For profile links
                    'index' => $index,
                    'name' => $tutor->user->name ?? 'Unknown',
                    'education' => $tutor->education ?? 'N/A',
                    'experience_years' => $tutor->experience_years ?? 0,
                    'hourly_rate' => $tutor->hourly_rate_min . ' - ' . $tutor->hourly_rate_max . ' VNĐ/giờ',
                    'rating' => $tutor->rating_avg ?? 'Chưa có đánh giá',
                    'review_count' => $tutor->review_count ?? 0,
                    'bio' => $tutor->bio ?? '',
                    'subjects' => $tutor->subjects ? $tutor->subjects->pluck('name')->implode(', ') : 'N/A',
                    'teaching_areas' => $tutor->teachingAreas ? $tutor->teachingAreas->map(function ($area) {
                        if ($area->ward) {
                            $province = $area->ward->province->name ?? '';
                            return $area->ward->name . ($province ? ', ' . $province : '');
                        }
                        return 'N/A';
                    })->unique()->implode('; ') : 'N/A',
                    'available_time_slots' => $tutor->availableTimeSlots ? $tutor->availableTimeSlots->pluck('name')->implode(', ') : 'N/A',
                ];
            })->toArray();

            // Create AI prompt
            $prompt = $this->createTutorRankingPrompt($requestData, $tutorsData);

            // Call OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Bạn là một hệ thống tư vấn giáo dục thông minh. Trả lời chỉ bằng JSON, không thêm markdown hoặc giải thích.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.2,
                'max_tokens' => 2048,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['choices'][0]['message']['content'] ?? '';
                
                // Parse AI response and create ranked list
                return $this->parseAITutorResponse($aiResponse, $tutors);
            } else {
                Log::error('OpenAI API error: ' . $response->body());
                return $this->fallbackTutorRanking($tutors);
            }

        } catch (\Exception $e) {
            Log::error('Error in AI ranking: ' . $e->getMessage());
            return $this->fallbackTutorRanking($tutors);
        }
    }

    /**
     * Phase 2: Rank requests using AI
     * 
     * @param \Illuminate\Database\Eloquent\Collection $requests
     * @param TutorProfile $tutorProfile
     * @return array
     */
    private function rankRequestsWithAI($requests, TutorProfile $tutorProfile): array
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            if (!$apiKey) {
                Log::warning('OpenAI API key not configured. Returning requests without AI ranking.');
                return $this->fallbackRequestRanking($requests);
            }

            // Prepare tutor data for AI
            $tutorData = [
                'name' => $tutorProfile->user->name ?? 'Unknown',
                'education' => $tutorProfile->education ?? 'N/A',
                'experience_years' => $tutorProfile->experience_years ?? 0,
                'hourly_rate' => $tutorProfile->hourly_rate_min . ' - ' . $tutorProfile->hourly_rate_max . ' VNĐ/giờ',
                'rating' => $tutorProfile->rating_avg ?? 'Chưa có đánh giá',
                'bio' => $tutorProfile->bio ?? '',
                'subjects' => $tutorProfile->subjects ? $tutorProfile->subjects->pluck('name')->implode(', ') : 'N/A',
                'teaching_areas' => $tutorProfile->teachingAreas ? $tutorProfile->teachingAreas->map(function ($area) {
                    if ($area->ward) {
                        $province = $area->ward->province->name ?? '';
                        return $area->ward->name . ($province ? ', ' . $province : '');
                    }
                    return 'N/A';
                })->unique()->implode('; ') : 'N/A',
                'available_time_slots' => $tutorProfile->availableTimeSlots ? $tutorProfile->availableTimeSlots->pluck('name')->implode(', ') : 'N/A',
            ];

            // Prepare requests data for AI
            $requestsData = $requests->map(function ($request, $index) {
                return [
                    'id' => $request->id,
                    'index' => $index,
                    'title' => $request->title ?? 'N/A',
                    'subject' => $request->subject->name ?? 'N/A',
                    'education_level' => $request->educationLevel->name ?? 'N/A',
                    'learning_mode' => $request->learningMode->name ?? 'N/A',
                    'budget_range' => $request->budget_min . ' - ' . $request->budget_max . ' VNĐ/giờ',
                    'location' => ($request->ward->name ?? '') . ', ' . ($request->province->name ?? ''),
                    'description' => $request->description ?? '',
                    'time_slots' => $request->timeSlots ? $request->timeSlots->pluck('name')->implode(', ') : 'Chưa chọn',
                    'student_name' => $request->student->name ?? 'Unknown',
                ];
            })->toArray();

            // Create AI prompt
            $prompt = $this->createRequestRankingPrompt($tutorData, $requestsData);

            // Call OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Bạn là một hệ thống tư vấn giáo dục thông minh. Trả lời chỉ bằng JSON, không thêm markdown hoặc giải thích.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.2,
                'max_tokens' => 2048,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['choices'][0]['message']['content'] ?? '';
                
                // Parse AI response and create ranked list
                return $this->parseAIRequestResponse($aiResponse, $requests);
            } else {
                Log::error('OpenAI API error: ' . $response->body());
                return $this->fallbackRequestRanking($requests);
            }

        } catch (\Exception $e) {
            Log::error('Error in AI ranking: ' . $e->getMessage());
            return $this->fallbackRequestRanking($requests);
        }
    }

    /**
     * Create prompt for AI to rank tutors
     */
    private function createTutorRankingPrompt(array $requestData, array $tutorsData): string
    {
        $tutorsList = '';
        foreach ($tutorsData as $tutor) {
            $tutorsList .= "Tutor #{$tutor['index']}:\n";
            $tutorsList .= "- Tên: {$tutor['name']}\n";
            $tutorsList .= "- Học vấn: {$tutor['education']}\n";
            $tutorsList .= "- Kinh nghiệm: {$tutor['experience_years']} năm\n";
            $tutorsList .= "- Giá/giờ: {$tutor['hourly_rate']}\n";
            $tutorsList .= "- Rating: {$tutor['rating']} ({$tutor['review_count']} đánh giá)\n";
            $tutorsList .= "- Môn dạy: {$tutor['subjects']}\n";
            $tutorsList .= "- Khu vực: {$tutor['teaching_areas']}\n";
            $tutorsList .= "- Lịch rảnh: {$tutor['available_time_slots']}\n";
            $tutorsList .= "- Giới thiệu: {$tutor['bio']}\n\n";
        }

        return <<<PROMPT
Bạn là một hệ thống tư vấn giáo dục thông minh. Nhiệm vụ của bạn là xếp hạng các gia sư phù hợp nhất cho yêu cầu của học sinh.

YÊU CẦU CỦA HỌC SINH:
- Môn học: {$requestData['subject']}
- Trình độ: {$requestData['education_level']}
- Hình thức: {$requestData['learning_mode']}
- Ngân sách: {$requestData['budget_range']}
- Khu vực: {$requestData['location']}
- Lịch mong muốn: {$requestData['time_slots']}
- Mô tả: {$requestData['description']}

DANH SÁCH GIA SƯ ĐÃ ĐƯỢC LỌC SƠ BỘ:
{$tutorsList}

Hãy phân tích và xếp hạng các gia sư theo độ phù hợp (từ cao đến thấp). 
Xem xét các yếu tố: kinh nghiệm, rating, lịch rảnh khớp với yêu cầu, khu vực, và sự phù hợp về giá.

Trả về kết quả theo format JSON SAU ĐÂY (KHÔNG thêm markdown code block):
{
  "rankings": [
    {
      "index": 0,
      "match_score": 95,
      "reason": "Lý do ngắn gọn tại sao gia sư này phù hợp nhất"
    }
  ]
}

Chỉ trả về JSON, không giải thích thêm.
PROMPT;
    }

    /**
     * Create prompt for AI to rank requests
     */
    private function createRequestRankingPrompt(array $tutorData, array $requestsData): string
    {
        $requestsList = '';
        foreach ($requestsData as $req) {
            $requestsList .= "Request #{$req['index']}:\n";
            $requestsList .= "- Tiêu đề: {$req['title']}\n";
            $requestsList .= "- Môn học: {$req['subject']}\n";
            $requestsList .= "- Trình độ: {$req['education_level']}\n";
            $requestsList .= "- Hình thức: {$req['learning_mode']}\n";
            $requestsList .= "- Ngân sách: {$req['budget_range']}\n";
            $requestsList .= "- Khu vực: {$req['location']}\n";
            $requestsList .= "- Lịch: {$req['time_slots']}\n";
            $requestsList .= "- Mô tả: {$req['description']}\n\n";
        }

        return <<<PROMPT
Bạn là một hệ thống tư vấn giáo dục thông minh. Nhiệm vụ của bạn là xếp hạng các yêu cầu học phù hợp nhất cho gia sư.

THÔNG TIN GIA SƯ:
- Tên: {$tutorData['name']}
- Học vấn: {$tutorData['education']}
- Kinh nghiệm: {$tutorData['experience_years']} năm
- Giá/giờ: {$tutorData['hourly_rate']}
- Rating: {$tutorData['rating']}
- Môn dạy: {$tutorData['subjects']}
- Khu vực dạy: {$tutorData['teaching_areas']}
- Lịch rảnh: {$tutorData['available_time_slots']}
- Giới thiệu: {$tutorData['bio']}

DANH SÁCH YÊU CẦU ĐÃ ĐƯỢC LỌC SƠ BỘ:
{$requestsList}

Hãy phân tích và xếp hạng các yêu cầu theo độ phù hợp với gia sư (từ cao đến thấp).
Xem xét: môn học, trình độ phù hợp, lịch khớp, khu vực, giá cả.

Trả về kết quả theo format JSON SAU ĐÂY (KHÔNG thêm markdown code block):
{
  "rankings": [
    {
      "index": 0,
      "match_score": 95,
      "reason": "Lý do ngắn gọn tại sao request này phù hợp nhất"
    }
  ]
}

Chỉ trả về JSON, không giải thích thêm.
PROMPT;
    }

    /**
     * Parse AI response for tutor ranking
     */
    private function parseAITutorResponse(string $aiResponse, $tutors): array
    {
        try {
            // Remove markdown code blocks if present
            $aiResponse = preg_replace('/```json\s*|\s*```/', '', $aiResponse);
            $aiResponse = trim($aiResponse);
            
            $data = json_decode($aiResponse, true);
            
            if (!isset($data['rankings']) || !is_array($data['rankings'])) {
                throw new \Exception('Invalid AI response format');
            }

            $result = [];
            foreach ($data['rankings'] as $ranking) {
                $tutor = $tutors[$ranking['index']] ?? null;
                if ($tutor) {
                    $result[] = [
                        'tutor_id' => $tutor->id,
                        'user_id' => $tutor->user_id,
                        'tutor_name' => $tutor->user->name ?? 'Unknown',
                        'education' => $tutor->education,
                        'experience_years' => $tutor->experience_years,
                        'hourly_rate_min' => $tutor->hourly_rate_min,
                        'hourly_rate_max' => $tutor->hourly_rate_max,
                        'rating_avg' => $tutor->rating_avg,
                        'review_count' => $tutor->review_count,
                        'bio' => $tutor->bio,
                        'match_score' => $ranking['match_score'],
                        'match_reason' => $ranking['reason'],
                    ];
                }
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error parsing AI response: ' . $e->getMessage());
            return $this->fallbackTutorRanking($tutors);
        }
    }

    /**
     * Parse AI response for request ranking
     */
    private function parseAIRequestResponse(string $aiResponse, $requests): array
    {
        try {
            // Remove markdown code blocks if present
            $aiResponse = preg_replace('/```json\s*|\s*```/', '', $aiResponse);
            $aiResponse = trim($aiResponse);
            
            $data = json_decode($aiResponse, true);
            
            if (!isset($data['rankings']) || !is_array($data['rankings'])) {
                throw new \Exception('Invalid AI response format');
            }

            $result = [];
            foreach ($data['rankings'] as $ranking) {
                $request = $requests[$ranking['index']] ?? null;
                if ($request) {
                    $result[] = [
                        'request_id' => $request->id,
                        'title' => $request->title,
                        'subject' => $request->subject->name ?? 'N/A',
                        'education_level' => $request->educationLevel->name ?? 'N/A',
                        'budget_min' => $request->budget_min,
                        'budget_max' => $request->budget_max,
                        'location' => ($request->ward->name ?? '') . ', ' . ($request->province->name ?? ''),
                        'description' => $request->description,
                        'student_name' => $request->student->name ?? 'Unknown',
                        'student_is_verified' => (bool) ($request->student->email_verified_at ?? false),
                        'learning_mode' => $request->learningMode->name ?? 'N/A',
                        'created_at' => $request->created_at->toIso8601String(),
                        'match_score' => $ranking['match_score'],
                        'match_reason' => $ranking['reason'],
                    ];
                }
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error parsing AI response: ' . $e->getMessage());
            return $this->fallbackRequestRanking($requests);
        }
    }

    /**
     * Fallback ranking for tutors (when AI is not available)
     */
    private function fallbackTutorRanking($tutors): array
    {
        return $tutors->sortByDesc(function ($tutor) {
            // Simple scoring: rating * 10 + experience * 2
            $rating = $tutor->rating_avg ?? 0;
            $experience = $tutor->experience_years ?? 0;
            return ($rating * 10) + ($experience * 2);
        })->map(function ($tutor) {
            return [
                'tutor_id' => $tutor->id,
                'tutor_name' => $tutor->user->name ?? 'Unknown',
                'education' => $tutor->education,
                'experience_years' => $tutor->experience_years,
                'hourly_rate_min' => $tutor->hourly_rate_min,
                'hourly_rate_max' => $tutor->hourly_rate_max,
                'rating_avg' => $tutor->rating_avg,
                'review_count' => $tutor->review_count,
                'bio' => $tutor->bio,
                'match_score' => 0,
                'match_reason' => 'Sắp xếp theo kinh nghiệm và đánh giá',
            ];
        })->values()->toArray();
    }

    /**
     * Fallback ranking for requests (when AI is not available)
     */
    private function fallbackRequestRanking($requests): array
    {
        return $requests->sortByDesc('budget_max')->map(function ($request) {
            return [
                'request_id' => $request->id,
                'title' => $request->title,
                'subject' => $request->subject->name ?? 'N/A',
                'education_level' => $request->educationLevel->name ?? 'N/A',
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'location' => ($request->ward->name ?? '') . ', ' . ($request->province->name ?? ''),
                'description' => $request->description,
                'student_name' => $request->student->name ?? 'Unknown',
                'student_is_verified' => (bool) ($request->student->email_verified_at ?? false),
                'learning_mode' => $request->learningMode->name ?? 'N/A',
                'created_at' => $request->created_at->toIso8601String(),
                'match_score' => 0,
                'match_reason' => 'Sắp xếp theo ngân sách',
            ];
        })->values()->toArray();
    }
}
