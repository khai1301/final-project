<?php

namespace App\Http\Controllers;

use App\Models\Matching;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactUnlockController extends Controller
{
    /**
     * Unlock student contact information
     */
    public function unlock(Request $request, $matchingId)
    {
        $matching = Matching::with(['student', 'tutor'])->findOrFail($matchingId);
        
        // Verify tutor owns this matching
        if ($matching->tutor_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền truy cập kết nối này.');
        }
        
        // Verify connection is accepted
        if ($matching->status !== 'accepted') {
            return back()->withErrors(['error' => __('messages.connection_must_accepted')]);
        }
        
        // Check if already unlocked
        if ($matching->contact_unlocked) {
            return back()->with('swal', [
                'type' => 'info',
                'title' => 'Thông báo',
                'text' => __('messages.contact_already_unlocked')
            ]);
        }

        // Get unlock fee from settings
        $unlockFee = Setting::get('contact_unlock_fee', 50000);
        $paymentEnabled = Setting::get('payment_enabled', false);

        DB::beginTransaction();
        try {
            if ($paymentEnabled) {
                // TODO: Integrate with payment gateway (VNPay/MoMo)
                // For now, redirect to payment page
                return $this->initiatePayment($matching, $unlockFee);
            } else {
                // Dev mode: unlock without payment
                $matching->update([
                    'contact_unlocked' => true,
                    'unlocked_at' => now(),
                    'unlock_fee' => $unlockFee,
                    'payment_status' => 'completed',
                    'payment_method' => 'dev_mode',
                ]);

                DB::commit();

                return back()->with('swal', [
                    'type' => 'success',
                    'title' => 'Thành công',
                    'text' => __('messages.contact_unlocked_dev_mode')
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => __('messages.unlock_error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Initiate payment with gateway
     */
    private function initiatePayment(Matching $matching, $amount)
    {
        // TODO: Implement VNPay/MoMo integration
        // This is a placeholder for future payment gateway integration
        
        return back()->with('swal', [
            'type' => 'info',
            'title' => 'Thông báo',
            'text' => __('messages.payment_dev_mode')
        ]);
    }

    /**
     * Handle payment callback
     */
    public function paymentCallback(Request $request)
    {
        // TODO: Handle VNPay/MoMo callback
        // Verify payment signature
        // Update matching record
        // Unlock contact info
        
        return redirect()->route('matching.my-requests')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Thành công',
                'text' => 'Thanh toán thành công! Thông tin liên hệ đã được mở khóa.'
            ]);
    }
}
