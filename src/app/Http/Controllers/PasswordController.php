<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show the password change form
     */
    public function edit()
    {
        return view('frontend.profile.change-password');
    }
    
    /**
     * Update user password
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        try {
            auth()->user()->update([
                'password' => Hash::make($validated['password'])
            ]);
            
            return back()->with('swal', [
                'type' => 'success',
                'title' => 'Thành công',
                'text' => __('messages.password_updated')
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi khi đổi mật khẩu: ' . $e->getMessage()]);
        }
    }
}
