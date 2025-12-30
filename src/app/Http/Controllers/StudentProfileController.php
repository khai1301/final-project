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
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'province_id' => 'nullable|exists:provinces,id',
            'ward_id' => 'nullable|exists:wards,id',
            'address_detail' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB
        ]);
        
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
                ->with('success', __('messages.profile_updated'));
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Lỗi khi cập nhật: ' . $e->getMessage()]);
        }
    }
}
