<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    /**
     * Show the form for editing student profile
     */
    public function edit()
    {
        $user = auth()->user();
        
        // Load location data for dropdowns
        $provinces = \App\Models\Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name', 'type', 'code', 'province_code']);
        
        return view('frontend.student.edit-profile', compact('user', 'provinces', 'wards'));
    }
    
    /**
     * Update student profile
     */
    public function update(\App\Http\Requests\UpdateStudentProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();
        
        try {
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar from S3
                if ($user->avatar && Storage::disk('s3')->exists($user->avatar)) {
                    Storage::disk('s3')->delete($user->avatar);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 's3');
                $validated['avatar'] = $avatarPath;
            }
            
            $user->update($validated);
            
            return redirect()->route('student.profile.edit')
                ->with('swal', [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'text' => 'Cập nhật hồ sơ thành công!'
                ]);
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Lỗi cập nhật',
                    'text' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
        }
    }
}
