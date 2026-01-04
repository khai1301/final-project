<?php

namespace App\Services;

use App\Models\Matching;
use App\Models\Payment;
use App\Models\Setting;
use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private PayOS $payOS;

    public function __construct()
    {
        $this->payOS = new PayOS(
            clientId: config('services.payos.client_id'),
            apiKey: config('services.payos.api_key'),
            checksumKey: config('services.payos.checksum_key')
        );
    }

    /**
     * Create payment link for contact unlock
     */
    public function createUnlockPayment(Matching $matching, int $userId): array
    {
        // Get unlock fee from settings
        $unlockFee = (int) Setting::get('contact_unlock_fee', 10000);

        // Create unique order code
        $orderCode = (int) (time() . rand(100, 999));
        
        // Create payment data
        $paymentData = new CreatePaymentLinkRequest(
            orderCode: $orderCode,
            amount: $unlockFee,
            description: "Unlock contact - Matching #{$matching->id}",
            returnUrl: config('services.payos.return_url'),
            cancelUrl: config('services.payos.cancel_url')
        );

        $result = $this->payOS->paymentRequests->create($paymentData);

        // Store order code in matching for verification
        $matching->update([
            'transaction_id' => (string) $orderCode,
            'unlock_fee' => $unlockFee,
            'payment_status' => 'pending',
            'payment_method' => 'payos',
        ]);

        // Create payment record for tracking
        Payment::create([
            'matching_id' => $matching->id,
            'user_id' => $userId,
            'transaction_id' => (string) $orderCode,
            'amount' => $unlockFee,
            'currency' => 'VND',
            'payment_method' => 'payos',
            'status' => 'pending',
            'description' => "Unlock contact - Matching #{$matching->id}",
            'payment_data' => [
                'checkout_url' => $result->checkoutUrl,
                'order_code' => $orderCode,
            ],
        ]);

        Log::info('PayOS Payment Created', [
            'matching_id' => $matching->id,
            'order_code' => $orderCode,
            'amount' => $unlockFee,
        ]);

        return [
            'success' => true,
            'checkout_url' => $result->checkoutUrl,
            'order_code' => $orderCode,
        ];
    }

    /**
     * Verify and complete payment
     */
    public function verifyAndCompletePayment(string $orderCode): bool
    {
        try {
            $paymentInfo = $this->payOS->paymentRequests->get((int) $orderCode);
            
            if ($paymentInfo->status === 'PAID') {
                $this->completePayment($orderCode, (array) $paymentInfo);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('PayOS Payment Verification Error', [
                'order_code' => $orderCode,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Complete payment and unlock contact
     */
    public function completePayment(string $orderCode, ?array $paymentInfo = null): void
    {
        $matching = Matching::where('transaction_id', $orderCode)->first();

        if (!$matching) {
            return;
        }

        $matching->update([
            'contact_unlocked' => true,
            'unlocked_at' => now(),
            'payment_status' => 'completed',
        ]);

        // Update payment record
        Payment::where('transaction_id', $orderCode)->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_data' => $paymentInfo ? ['payment_info' => $paymentInfo] : null,
        ]);

        Log::info('Contact Unlocked', [
            'matching_id' => $matching->id,
            'order_code' => $orderCode,
        ]);
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(string $orderCode): void
    {
        $matching = Matching::where('transaction_id', $orderCode)->first();

        if ($matching) {
            $matching->update(['payment_status' => 'cancelled']);
        }

        Payment::where('transaction_id', $orderCode)->update([
            'status' => 'cancelled',
        ]);

        Log::info('Payment Cancelled', ['order_code' => $orderCode]);
    }

    /**
     * Process webhook
     */
    public function processWebhook(array $webhookPayload): array
    {
        // Verify webhook signature
        $verified = $this->payOS->webhooks->verify($webhookPayload);

        // Extract data from webhook
        $data = $verified->data ?? null;
        if (!$data) {
            return ['success' => false, 'error' => 'Invalid webhook data'];
        }

        $orderCode = $data->orderCode ?? null;
        $status = $data->status ?? null;

        if (!$orderCode) {
            return ['success' => false, 'error' => 'Missing order code'];
        }

        $matching = Matching::where('transaction_id', $orderCode)->first();

        if (!$matching) {
            Log::warning('PayOS Webhook - Matching not found', ['order_code' => $orderCode]);
            return ['success' => false, 'error' => 'Matching not found'];
        }

        // Update based on payment status
        if ($status === 'PAID') {
            $matching->update([
                'contact_unlocked' => true,
                'unlocked_at' => now(),
                'payment_status' => 'completed',
            ]);

            Payment::where('transaction_id', $orderCode)->update([
                'status' => 'completed',
                'paid_at' => now(),
                'webhook_data' => $webhookPayload,
            ]);

            Log::info('Contact Unlocked via Webhook', [
                'matching_id' => $matching->id,
                'order_code' => $orderCode,
            ]);
        } elseif ($status === 'CANCELLED') {
            $matching->update(['payment_status' => 'cancelled']);

            Payment::where('transaction_id', $orderCode)->update([
                'status' => 'cancelled',
            ]);
        }

        return ['success' => true];
    }

    /**
     * Get payment history for a matching
     */
    public function getPaymentHistory(int $matchingId)
    {
        return Payment::where('matching_id', $matchingId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get payment statistics for a user
     */
    public function getUserPaymentStats(int $userId): array
    {
        $payments = Payment::where('user_id', $userId)->get();

        return [
            'total_payments' => $payments->count(),
            'completed_payments' => $payments->where('status', 'completed')->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            'pending_payments' => $payments->where('status', 'pending')->count(),
        ];
    }
}
