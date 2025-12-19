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
     * Display the tutor's profile (or redirect to edit if profile doesn't exist).
     */
    public function show()
    {
        $user = auth()->user();
        
        // Get or create profile
        $profile = TutorProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'subjects' => [],
                'teaching_areas' => [],
                'availability' => [],
                'is_approved' => false,
            ]
        );

        // If profile is empty, redirect to edit
        if (!$profile->bio && !$profile->education) {
            return redirect()->route('tutor.profile.edit');
        }

        return view('frontend.tutor.show', compact('profile', 'user'));
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
                'subjects' => [],
                'teaching_areas' => [],
                'availability' => [],
                'is_approved' => false,
            ]
        );

        return view('frontend.home.tutor-profile', compact('profile', 'user'));
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
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->update(['avatar' => $avatarPath]);
            }

            // Handle CV upload
            if ($request->hasFile('cv')) {
                // Delete old CV
                if ($profile->cv_path && Storage::disk('public')->exists($profile->cv_path)) {
                    Storage::disk('public')->delete($profile->cv_path);
                }
                
                $cvPath = $request->file('cv')->store('cvs', 'public');
                $profile->cv_path = $cvPath;
            }

            // Handle phone update on user
            if ($request->has('phone')) {
                $user->update(['phone' => $request->input('phone')]);
            }

            // Update profile fields
            $profile->fill([
                'subjects' => $request->input('subjects', []),
                'education' => $request->input('education'),
                'experience_years' => $request->input('experience_years'),
                'hourly_rate_min' => $request->input('hourly_rate_min'),
                'hourly_rate_max' => $request->input('hourly_rate_max'),
                'teaching_areas' => $request->input('teaching_areas', []),
                'bio' => $request->input('bio'),
                'availability' => $request->input('availability', []),
            ]);
            
            $profile->save();

            // Handle certificate uploads
            if ($request->hasFile('certificates')) {
                foreach ($request->file('certificates') as $certificate) {
                    $path = $certificate->store('certificates', 'public');
                    
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
                ->with('success', 'Profile updated successfully!');

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

        // Delete file
        if (Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return back()->with('success', 'Certificate deleted successfully!');
    }
}
