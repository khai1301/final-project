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
        
        // Get fresh profile data with subjects relationship
        $profile = TutorProfile::with(['subjects', 'certificates'])
            ->where('user_id', $user->id)
            ->first();
        
        // If no profile exists, redirect to create one
        if (!$profile) {
            return redirect()->route('tutor.profile.edit')
                ->with('info', 'Please complete your profile first.');
        }
        
        // Get all active subjects for reference
        $allSubjects = \App\Models\Subject::active()->orderBy('name')->get();
        
        return view('frontend.home.tutor-profile', compact('profile', 'user', 'allSubjects'));
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
                'availability' => [],
                'is_approved' => false,
            ]
        );
        
        // Reload profile with relationships (fresh() doesn't always work with eager loading)
        $profile = TutorProfile::with(['subjects', 'certificates'])
            ->where('id', $profile->id)
            ->first();

        // Get all active subjects for dropdown/selection
        $allSubjects = \App\Models\Subject::active()->orderBy('name')->get();

        return view('frontend.home.tutor-profile', compact('profile', 'user', 'allSubjects'));
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

            // Handle phone update on user
            if ($request->has('phone')) {
                $user->update(['phone' => $request->input('phone')]);
            }

            // Parse teaching_areas from JSON string to array
            $teachingAreas = [];
            if ($request->has('teaching_areas')) {
                $areasJson = $request->input('teaching_areas');
                $teachingAreas = is_string($areasJson) ? json_decode($areasJson, true) : $areasJson;
                $teachingAreas = $teachingAreas ?: [];
            }

            // Update profile fields (removed subjects as it's now a pivot)
            $profile->fill([
                'education' => $request->input('education'),
                'experience_years' => $request->input('experience_years'),
                'hourly_rate_min' => $request->input('hourly_rate_min'),
                'hourly_rate_max' => $request->input('hourly_rate_max'),
                'teaching_areas' => $teachingAreas,
                'bio' => $request->input('bio'),
                'availability' => $request->input('availability', []),
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
}
