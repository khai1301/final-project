<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Models\TutorProfile;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CVParserController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Upload CV and trigger AI parsing
     */
    public function upload(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        try {
            $user = auth()->user();
            
            // Get or create profile
            $profile = TutorProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'teaching_areas' => [],
                    'is_approved' => false,
                ]
            );

            // Upload CV to S3
            $file = $request->file('cv_file');
            $path = $file->store('cvs', 's3');
            
            // Update profile with CV path
            $profile->update(['cv_path' => $path]);

            // Download CV temporarily for parsing
            $tempPath = storage_path('app/temp/' . uniqid() . '.' . $file->getClientOriginalExtension());
            
            // Ensure temp directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }
            
            // Save temp file
            file_put_contents($tempPath, Storage::disk('s3')->get($path));

            // Parse CV with AI
            $parsedData = $this->aiService->parseCVFile($tempPath);

            // Clean up temp file
            @unlink($tempPath);

            // Map subject names to IDs
            $parsedData['subject_ids'] = $this->mapSubjectNamesToIds($parsedData['subjects'] ?? []);

            return response()->json([
                'success' => true,
                'message' => 'CV đã được phân tích thành công!',
                'data' => $parsedData
            ]);

        } catch (\Exception $e) {
            Log::error('CV upload/parsing failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xử lý CV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply parsed AI data to profile
     */
    public function applyParsedData(Request $request)
    {
        $request->validate([
            'education' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'hourly_rate_min' => 'nullable|integer',
            'hourly_rate_max' => 'nullable|integer',
            'bio' => 'nullable|string|max:500',
            'teaching_areas' => 'nullable|array',
            'subject_ids' => 'nullable|array',
            'skills' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            
            $profile = TutorProfile::where('user_id', $user->id)->first();
            
            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            // Prepare update data - only include fields that are present and not null
            // This preserves existing data if AI returns null/missing fields
            $updateData = [];
            
            if ($request->filled('education')) $updateData['education'] = $request->input('education');
            if ($request->filled('experience_years')) $updateData['experience_years'] = $request->input('experience_years');
            if ($request->filled('hourly_rate_min')) $updateData['hourly_rate_min'] = $request->input('hourly_rate_min');
            if ($request->filled('hourly_rate_max')) $updateData['hourly_rate_max'] = $request->input('hourly_rate_max');
            if ($request->filled('bio')) $updateData['bio'] = $request->input('bio');
            
            // For array fields, only update if provided as valid array
            if ($request->has('teaching_areas') && is_array($request->input('teaching_areas'))) {
                $updateData['teaching_areas'] = $request->input('teaching_areas');
            }

            // Update profile fields if there's anything to update
            if (!empty($updateData)) {
                $profile->update($updateData);
            }

            // Sync subjects only if provided and not empty
            if ($request->has('subject_ids') && is_array($request->input('subject_ids')) && !empty($request->input('subject_ids'))) {
                $profile->subjects()->sync($request->input('subject_ids'));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật hồ sơ thành công!',
                'redirect' => route('tutor.profile')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu hồ sơ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map subject names to IDs
     */
    private function mapSubjectNamesToIds(array $subjectNames): array
    {
        if (empty($subjectNames)) {
            return [];
        }

        $subjectIds = [];
        
        foreach ($subjectNames as $name) {
            // Try to find subject by name (case insensitive)
            $subject = Subject::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($name)) . '%'])
                ->first();
            
            if ($subject) {
                $subjectIds[] = $subject->id;
            }
        }

        return array_unique($subjectIds);
    }
}
