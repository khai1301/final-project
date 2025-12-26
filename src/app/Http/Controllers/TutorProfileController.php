<?php

namespace App\Http\Controllers;

use App\Models\TutorProfile;
use App\Models\TutorCertificate;
use App\Http\Requests\UpdateTutorProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TutorProfileController extends Controller
{
    /**
     * Display the tutor's profile.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Get fresh profile data with relationships
        $profile = TutorProfile::with(['subjects', 'certificates', 'availableTimeSlots', 'teachingAreas'])
            ->where('user_id', $user->id)
            ->first();
        
        // If no profile exists, redirect to create one
        if (!$profile) {
            return redirect()->route('tutor.profile.edit')
                ->with('info', 'Please complete your profile first.');
        }
        
        // Get all active subjects for reference
        $allSubjects = \App\Models\Subject::active()->orderBy('name')->get();
        
        
        // Get all time slots grouped by day
        $timeSlots = \App\Models\TimeSlot::active()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
        // Get selected time slot IDs
        $selectedTimeSlots = $profile->availableTimeSlots->pluck('id')->toArray();
        
        // Load location data for client-side filtering
        $provinces = \App\Models\Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name', 'type', 'code', 'province_code']);
        
        return view('frontend.home.tutor-profile', compact('profile', 'user', 'allSubjects', 'timeSlots', 'selectedTimeSlots', 'provinces', 'wards'));
    }

    /**
     * Show the form for editing the tutor's profile.
     */
    public function edit()
    {
        $user = auth()->user();
        
        // Get or create profile
        $profile = TutorProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'teaching_areas' => [],
                'is_approved' => false,
            ]
        );
        
        // Reload profile with relationships (fresh() doesn't always work with eager loading)
        $profile = TutorProfile::with(['subjects', 'certificates', 'availableTimeSlots'])
            ->where('id', $profile->id)
            ->first();

        // Get all active subjects for dropdown/selection
        $allSubjects = \App\Models\Subject::active()->orderBy('name')->get();
        
        // Get all time slots grouped by day
        $timeSlots = \App\Models\TimeSlot::active()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
        // Get selected time slot IDs
        $selectedTimeSlots = $profile->availableTimeSlots->pluck('id')->toArray();
        
        // Load location data for client-side filtering
        $provinces = \App\Models\Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name', 'type', 'code', 'province_code']);

        return view('frontend.home.tutor-profile', compact('profile', 'user', 'allSubjects', 'timeSlots', 'selectedTimeSlots', 'provinces', 'wards'));
    }

    /**
     * Update the tutor's profile.
     */
    public function update(UpdateTutorProfileRequest $request)
    {
        $user = auth()->user();
        $profile = TutorProfile::firstOrCreate(['user_id' => $user->id]);

        DB::beginTransaction();
        try {
            // Handle avatar upload to S3
            if ($request->hasFile('avatar')) {
                // Delete old avatar from S3
                if ($user->avatar && Storage::disk('s3')->exists($user->avatar)) {
                    Storage::disk('s3')->delete($user->avatar);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 's3');
                $user->update(['avatar' => $avatarPath]);
            }

            // Handle CV upload to S3
            if ($request->hasFile('cv')) {
                // Delete old CV from S3
                if ($profile->cv_path && Storage::disk('s3')->exists($profile->cv_path)) {
                    Storage::disk('s3')->delete($profile->cv_path);
                }
                
                $cvPath = $request->file('cv')->store('cvs', 's3');
                $profile->cv_path = $cvPath;
            }

            // Handle name, phone, and location updates on user
            $user->update([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'province_id' => $request->input('province_id'),
                'ward_id' => $request->input('ward_id'),
                'address_detail' => $request->input('address_detail'),
            ]);

            // Update profile fields
            $profile->fill([
                'education' => $request->input('education'),
                'experience_years' => $request->input('experience_years'),
                'hourly_rate_min' => $request->input('hourly_rate_min'),
                'hourly_rate_max' => $request->input('hourly_rate_max'),
                'bio' => $request->input('bio'),
            ]);
            
            $profile->save();

            // Sync subjects relationship (many-to-many)
            // Always sync if subjects field is present (even if empty array to clear all)
            if ($request->has('subjects')) {
                $subjectIds = $request->input('subjects', []);
                // Ensure it's an array
                if (!is_array($subjectIds)) {
                    $subjectIds = [];
                }
                $profile->subjects()->sync($subjectIds);
            }
            
            // Sync teaching areas with base location auto-prepend
            // Teaching areas array may be empty if user has base location
            $teachingAreasData = $request->input('teaching_areas', []);
            
            // If user has base location, always ensure it's first
            if ($user->province_id) {
                $baseArea = [
                    'province_id' => $user->province_id,
                    'ward_id' => $user->ward_id,
                ];
                
                // Remove duplicates if user added base location manually
                $teachingAreasData = array_filter($teachingAreasData, function($area) use ($baseArea) {
                    return !(
                        $area['province_id'] == $baseArea['province_id'] && 
                        ($area['ward_id'] ?? null) == ($baseArea['ward_id'] ?? null)
                    );
                });
                
                // Prepend base location
                array_unshift($teachingAreasData, $baseArea);
            }
            
            // Delete existing teaching areas
            $profile->teachingAreas()->delete();
            
            // Create new teaching areas
            foreach ($teachingAreasData as $area) {
                if (!empty($area['province_id'])) {
                    $profile->teachingAreas()->create([
                        'province_id' => $area['province_id'],
                        'ward_id' => $area['ward_id'] ?? null,
                    ]);
                }
            }

            
            // Sync available time slots (many-to-many)
            if ($request->has('time_slots')) {
                $timeSlotIds = $request->input('time_slots', []);
                // Ensure it's an array
                if (!is_array($timeSlotIds)) {
                    $timeSlotIds = [];
                }
                $profile->availableTimeSlots()->sync($timeSlotIds);
            }

            // Handle certificate uploads to S3
            if ($request->hasFile('certificates')) {
                foreach ($request->file('certificates') as $certificate) {
                    $path = $certificate->store('certificates', 's3');
                    
                    TutorCertificate::create([
                        'tutor_profile_id' => $profile->id,
                        'name' => $certificate->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $certificate->getMimeType(),
                        'file_size' => $certificate->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('tutor.profile')
                ->with('success', 'Cập nhật hồ sơ thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a certificate.
     */
    public function deleteCertificate($id)
    {
        $certificate = TutorCertificate::findOrFail($id);
        
        // Check ownership
        if ($certificate->tutorProfile->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete file from S3
        if (Storage::disk('s3')->exists($certificate->file_path)) {
            Storage::disk('s3')->delete($certificate->file_path);
        }

        $certificate->delete();

        return back()->with('success', 'Đã xóa chứng chỉ thành công!');
    }

    /**
     * Browse all approved tutors with filters (public page)
     */
    public function browse()
    {
        $search = request('search');
        $subjects = request('subjects', []);
        $rateMin = request('rate_min');
        $rateMax = request('rate_max');

        $tutors = TutorProfile::with(['user', 'subjects'])
            ->where('is_approved', true)
            
            // Search by tutor name
            ->when($search, function($query) use ($search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            
            // Filter by subjects (OR logic - at least one subject matches)
            ->when(!empty($subjects), function($query) use ($subjects) {
                $query->whereHas('subjects', function($q) use ($subjects) {
                    $q->whereIn('subjects.id', $subjects);
                });
            })
            
            // Filter by hourly rate range (using min/max fields)
            ->when($rateMin, fn($q) => $q->where('hourly_rate_min', '>=', $rateMin))
            ->when($rateMax, fn($q) => $q->where('hourly_rate_max', '<=', $rateMax))
            
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Get filter options
        $allSubjects = \App\Models\Subject::active()->orderBy('name')->get();

        return view('frontend.tutors.browse', compact('tutors', 'allSubjects'));
    }

    /**
     * Show public tutor profile (viewable by anyone)
     */
    public function showPublic($id)
    {
        $tutor = \App\Models\User::with(['tutorProfile.subjects', 'tutorProfile.certificates'])
            ->where('role', 'tutor')
            ->findOrFail($id);
        
        // Check if profile exists and is approved
        if (!$tutor->tutorProfile || !$tutor->tutorProfile->is_approved) {
            abort(404, 'Tutor profile not found or not approved');
        }
        
        return view('frontend.tutor-profile.public', compact('tutor'));
    }

    /**
     * Parse CV file and extract data
     */
    public function parseCV(\Illuminate\Http\Request $request)
    {
        try {
            $request->validate([
                'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            ]);

            $file = $request->file('cv_file');
            
            // TODO: Implement AI CV parsing logic here
            // For now, return mock data
            $mockData = [
                'name' => 'Nguyễn Văn A',
                'phone' => '0123456789',
                'education' => 'Đại học',
                'experience_years' => 3,
                'hourly_rate_min' => 150000,
                'hourly_rate_max' => 300000,
                'bio' => 'Giáo viên với nhiều năm kinh nghiệm giảng dạy',
                'subject_ids' => [], // Will be extracted by AI later
            ];

            return response()->json([
                'success' => true,
                'message' => 'CV parsed successfully',
                'data' => $mockData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error parsing CV: ' . $e->getMessage()
            ], 500);
        }
    }
}
