<?php

namespace App\Http\Controllers;

use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Get tutor recommendations for a student request
     * 
     * @param int $requestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTutorRecommendations($requestId)
    {
        try {
            $result = $this->matchingService->recommendTutorsForRequest($requestId);
            
            return response()->json($result);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu này.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get request recommendations for a tutor
     * 
     * @param int $tutorProfileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRequestRecommendations($tutorProfileId)
    {
        try {
            $result = $this->matchingService->recommendRequestsForTutor($tutorProfileId);
            
            return response()->json($result);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy profile gia sư này.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show tutor recommendations page for student
     */
    public function showTutorRecommendations($requestId)
    {
        return view('student.recommendations', compact('requestId'));
    }

    /**
     * Show request recommendations page for tutor
     */
    public function showRequestRecommendations()
    {
        $user = Auth::user();
        $tutorProfile = $user->tutorProfile;

        if (!$tutorProfile) {
            return redirect()->route('tutor.profile.create')
                ->with('error', 'Vui lòng tạo profile gia sư trước.');
        }

        return view('tutor.recommendations', [
            'tutorProfileId' => $tutorProfile->id
        ]);
    }
}
