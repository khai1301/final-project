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
        $settings = Setting::where('group', 'payment')->get()->keyBy('key');
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'contact_unlock_fee' => 'required|numeric|min:0|max:10000000',
            'payment_enabled' => 'nullable|boolean',
            'vnpay_enabled' => 'nullable|boolean',
            'momo_enabled' => 'nullable|boolean',
        ]);

        // Update unlock fee
        Setting::set(
            'contact_unlock_fee',
            $request->contact_unlock_fee,
            'decimal',
            'payment',
            'Phí mở khóa thông tin liên hệ học sinh (VNĐ)'
        );

        // Update payment toggles
        Setting::set(
            'payment_enabled',
            $request->has('payment_enabled') ? 'true' : 'false',
            'boolean',
            'payment',
            'Bật/tắt tính năng thanh toán'
        );

        Setting::set(
            'vnpay_enabled',
            $request->has('vnpay_enabled') ? 'true' : 'false',
            'boolean',
            'payment',
            'Kích hoạt VNPay'
        );

        Setting::set(
            'momo_enabled',
            $request->has('momo_enabled') ? 'true' : 'false',
            'boolean',
            'payment',
            'Kích hoạt MoMo'
        );

        return back()->with('success', 'Cài đặt đã được cập nhật thành công!');
    }
}
