<?php

namespace App\Http\Controllers;

use App\Models\Matching;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display payment history for the authenticated user
     */
    public function history()
    {
        $payments = \App\Models\Payment::where('user_id', auth()->id())
            ->with(['matching.student', 'matching.tutor', 'matching.request'])
            ->latest()
            ->paginate(20);
        
        $stats = [
            'total_spent' => \App\Models\Payment::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->sum('amount'),
            'total_transactions' => \App\Models\Payment::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->count(),
            'pending_payments' => \App\Models\Payment::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->count(),
        ];
        
        return view('frontend.payment.history', compact('payments', 'stats'));
    }


    /**
     * Create payment link for contact unlock
     */
    public function createUnlockPayment(Matching $matching)
    {
        // Verify user is tutor and part of this matching
        if (!auth()->user()->isTutor()) {
            return back()->withErrors(['error' => 'Only tutors can unlock contact']);
        }

        if ($matching->tutor_id !== auth()->id()) {
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        // Check if already unlocked
        if ($matching->contact_unlocked) {
            return back()->with('swal', [
                'type' => 'info',
                'title' => 'Thông báo',
                'text' => 'Contact already unlocked'
            ]);
        }

        // Check if accepted
        if ($matching->status !== 'accepted') {
            return back()->withErrors(['error' => 'Please accept the request first']);
        }

        try {
            $result = $this->paymentService->createUnlockPayment($matching, auth()->id());
            
            // Redirect to payment page
            return redirect($result['checkout_url']);

        } catch (\Exception $e) {
            Log::error('PayOS Payment Creation Error', [
                'matching_id' => $matching->id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => 'Payment creation failed. Please try again.']);
        }
    }

    /**
     * Handle payment return (success/cancel)
     */
    public function paymentReturn(Request $request)
    {
        $orderCode = $request->query('orderCode');
        $status = $request->query('status'); // PAID, CANCELLED
        $cancel = $request->query('cancel'); // true if cancelled

        Log::info('PayOS Payment Return', [
            'orderCode' => $orderCode,
            'status' => $status,
            'cancel' => $cancel,
            'all_params' => $request->all(),
        ]);

        if (!$orderCode) {
            return redirect()->route('matching.my-requests')
                ->withErrors(['error' => 'Invalid payment response']);
        }

        // Find matching by transaction_id
        $matching = Matching::where('transaction_id', $orderCode)->first();

        if (!$matching) {
            return redirect()->route('matching.my-requests')
                ->withErrors(['error' => 'Matching not found']);
        }

        // Handle cancellation
        if ($cancel === 'true' || $status === 'CANCELLED') {
            $this->paymentService->cancelPayment($orderCode);

            return redirect()->route('matching.my-requests')
                ->with('swal', [
                    'type' => 'info',
                    'title' => 'Thông báo',
                    'text' => 'Payment cancelled'
                ]);
        }

        // Handle success
        if ($status === 'PAID') {
            // VERIFY payment with PayOS API to prevent spoofing
            $verified = $this->paymentService->verifyAndCompletePayment($orderCode);
            
            if ($verified) {
                return redirect()->route('matching.my-requests')
                    ->with('swal', [
                        'type' => 'success',
                        'title' => 'Thanh toán thành công',
                        'text' => 'Thông tin liên hệ đã được mở khóa!'
                    ]);
            } else {
                // Verification failed (API Error or Fake Request)
                Log::warning('Payment verification failed for order: ' . $orderCode);
                
                return redirect()->route('matching.my-requests')
                    ->with('swal', [
                        'type' => 'warning',
                        'title' => 'Đang xử lý thanh toán',
                        'text' => 'Chúng tôi đã ghi nhận thanh toán nhưng chưa thể xác thực ngay lập tức. Hệ thống sẽ tự động cập nhật trong ít phút.'
                    ]);
            }
        }

        // Default fallback
        return redirect()->route('matching.my-requests')
            ->withErrors(['error' => 'Trạng thái thanh toán không xác định.']);
    }

    /**
     * Handle payment cancellation
     */
    public function paymentCancel(Request $request)
    {
        $orderCode = $request->query('orderCode');
        
        Log::info('PayOS Payment Cancelled via Dedicated Route', [
            'orderCode' => $orderCode,
        ]);

        if ($orderCode) {
            $this->paymentService->cancelPayment($orderCode);
        }

        return redirect()->route('matching.my-requests')
            ->with('swal', [
                'type' => 'info',
                'title' => 'Thông báo',
                'text' => 'Bạn đã hủy thanh toán.'
            ]);
    }

    /**
     * Handle PayOS webhook
     */
    public function webhook(Request $request)
    {
        try {
            $webhookPayload = $request->all();
            
            Log::info('PayOS Webhook Received', ['payload' => $webhookPayload]);

            $result = $this->paymentService->processWebhook($webhookPayload);

            if (!$result['success']) {
                return response()->json(['error' => $result['error'] ?? 'Unknown error'], 400);
            }

            return response()->json(['success' => true]);

        } catch (\PayOS\Exceptions\WebhookException $e) {
            Log::error('PayOS Webhook Verification Failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);
            return response()->json(['error' => 'Invalid webhook signature'], 401);
        } catch (\Exception $e) {
            Log::error('PayOS Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}
