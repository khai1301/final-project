<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Fetch ALL settings keyed by 'key' to access them easily in view
        $settings = Setting::all()->keyBy('key');
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'contact_unlock_fee' => 'required|numeric|min:0|max:10000000',
            'home_hero_title' => 'nullable|string|max:255',
            'home_hero_subtitle' => 'nullable|string|max:500',
            'student_hero_title' => 'nullable|string|max:255',
            'student_hero_subtitle' => 'nullable|string|max:500',
            'student_hero_image' => 'nullable|image|max:5120', // Max 5MB
            'tutor_hero_title' => 'nullable|string|max:255',
            'tutor_hero_subtitle' => 'nullable|string|max:500',
            'tutor_hero_image' => 'nullable|image|max:5120', // Max 5MB
        ]);

        // --- Payment Settings ---

        // Update unlock fee
        Setting::set(
            'contact_unlock_fee',
            $request->contact_unlock_fee,
            'decimal',
            'payment',
            'Phí mở khóa thông tin liên hệ học sinh (VNĐ)'
        );

        // Update payment toggles
        // Always store as 'true' or 'false' string for consistency
        Setting::set(
            'payment_enabled',
            $request->has('payment_enabled') ? 'true' : 'false',
            'boolean',
            'payment',
            'Bật/tắt tính năng thanh toán'
        );

        // --- Home Page Settings ---
        
        if ($request->has('home_hero_title')) {
            Setting::set(
                'home_hero_title',
                $request->home_hero_title,
                'string',
                'home',
                'Tiêu đề chính trang chủ'
            );
        }

        if ($request->has('home_hero_subtitle')) {
            Setting::set(
                'home_hero_subtitle',
                $request->home_hero_subtitle,
                'string',
                'home',
                'Mô tả ngắn trang chủ'
            );
        }

        // --- Student Hero Settings ---
        if ($request->has('student_hero_title')) {
            Setting::set('student_hero_title', $request->student_hero_title, 'string', 'home', 'Tiêu đề Hero cho Học sinh');
        }
        if ($request->has('student_hero_subtitle')) {
            Setting::set('student_hero_subtitle', $request->student_hero_subtitle, 'string', 'home', 'Mô tả Hero cho Học sinh');
        }
        
        // Handle Student Image Upload
        if ($request->hasFile('student_hero_image')) {
            $path = $request->file('student_hero_image')->store('settings/hero', 's3');
            $url = \Illuminate\Support\Facades\Storage::disk('s3')->url($path);
            Setting::set('student_hero_image', $url, 'string', 'home', 'Ảnh nền Hero cho Học sinh');
        }

        // --- Tutor Hero Settings ---
        if ($request->has('tutor_hero_title')) {
            Setting::set('tutor_hero_title', $request->tutor_hero_title, 'string', 'home', 'Tiêu đề Hero cho Gia sư');
        }
        if ($request->has('tutor_hero_subtitle')) {
            Setting::set('tutor_hero_subtitle', $request->tutor_hero_subtitle, 'string', 'home', 'Mô tả Hero cho Gia sư');
        }

        // Handle Tutor Image Upload
        if ($request->hasFile('tutor_hero_image')) {
             $path = $request->file('tutor_hero_image')->store('settings/hero', 's3');
             $url = \Illuminate\Support\Facades\Storage::disk('s3')->url($path);
             Setting::set('tutor_hero_image', $url, 'string', 'home', 'Ảnh nền Hero cho Gia sư');
        }

        return back()->with('swal', [
            'type' => 'success',
            'title' => 'Thành công',
            'text' => 'Cài đặt đã được cập nhật thành công!'
        ]);
    }
}
