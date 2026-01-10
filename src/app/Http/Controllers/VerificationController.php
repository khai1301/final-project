<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Show verification form
     */
    public function show()
    {
        return view('frontend.profile.verification');
    }

    /**
     * Verify ID card using FPT.AI API
     */
    public function verify(\App\Http\Requests\VerificationRequest $request)
    {
        try {
            // Get uploaded file
            if (!$request->hasFile('id_card_image')) {
                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'Thiếu ảnh',
                    'text' => 'Vui lòng chọn ảnh để upload.'
                ]);
            }
            
            $file = $request->file('id_card_image');
            
            // Check if file is valid
            if (!$file->isValid()) {
                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'File không hợp lệ',
                    'text' => 'File upload không hợp lệ. Vui lòng thử lại.'
                ]);
            }
            
            $filePath = $file->getRealPath();
            
            // Get API key from config
            $apiKey = config('services.kyc.api_key');
            // dd($apiKey);
            
            if (!$apiKey) {
                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'Lỗi cấu hình',
                    'text' => 'API key chưa được cấu hình. Vui lòng liên hệ quản trị viên.'
                ]);
            }

            // Prepare cURL request
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            $fileSize = $file->getSize();
            Log::info('Starting KYC Verification', [
                'user_id' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $fileSize,
                'mime_type' => $mimeType
            ]);

            // Use Laravel HTTP Client instead of raw cURL for better compatibility
            try {
                $response = Http::withHeaders([
                    'api-key' => $apiKey
                ])
                ->timeout(60) // 60 seconds timeout
                ->attach(
                    'image', 
                    fopen($filePath, 'r'), 
                    $file->getClientOriginalName(),
                    ['Content-Type' => $mimeType]
                )
                ->post('https://api.fpt.ai/vision/idr/vnm');

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('KYC API Connection Error: ' . $e->getMessage());
                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'Lỗi kết nối',
                    'text' => 'Kết nối đến AI bị gián đoạn hoặc quá thời gian. Vui lòng thử lại.'
                ]);
            }

            if ($response->failed()) {
                Log::error('KYC API Error Response: ' . $response->status(), ['body' => $response->body()]);
                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'Lỗi dịch vụ',
                    'text' => 'Dịch vụ AI không phản hồi hoặc gặp lỗi. Mã: ' . $response->status()
                ]);
            }

            // Parse response
            $result = $response->json();
            
            // Log for debugging
            Log::info('KYC API Response', ['response' => $result]);

            // Check if verification was successful
            if (isset($result['data']) && count($result['data']) > 0) {
                $idData = $result['data'][0];
                
                // Extract info from CCCD
                $idNumber = $idData['id'] ?? null;
                $name = $idData['name'] ?? null;
                $dob = $idData['dob'] ?? null;
                
                // Validate name matches user profile
                $user = auth()->user();
                if ($name) {
                    // Normalize both names for comparison (remove extra spaces, convert to uppercase)
                    $normalizedCccdName = mb_strtoupper(trim(preg_replace('/\s+/', ' ', $name)));
                    $normalizedUserName = mb_strtoupper(trim(preg_replace('/\s+/', ' ', $user->name)));
                    
                    // Check if names match
                    if ($normalizedCccdName !== $normalizedUserName) {
                        Log::warning('CCCD Name Mismatch', [
                            'user_name' => $user->name,
                            'cccd_name' => $name,
                            'normalized_user' => $normalizedUserName,
                            'normalized_cccd' => $normalizedCccdName
                        ]);
                        
                        return back()->with('swal', [
                            'type' => 'error',
                            'title' => 'Tên không khớp',
                            'text' => "Tên trên CCCD ({$name}) không khớp với tên trong hồ sơ ({$user->name}). Vui lòng cập nhật hồ sơ hoặc sử dụng CCCD đúng."
                        ]);
                    }
                }
                
                // Mark user as verified
                $user->is_verified = true;
                $user->verified_at = now();
                $user->save();

                return back()->with('swal', [
                    'type' => 'success',
                    'title' => 'Xác thực thành công',
                    'text' => 'Xác thực CCCD thành công! Tài khoản của bạn đã được xác minh.'
                ]);
            } else {
                // Verification failed
                $errorCode = $result['errorCode'] ?? 'unknown';
                $errorMessage = $result['errorMessage'] ?? 'Không thể xác thực CCCD';
                
                Log::warning('KYC Verification Failed', [
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'response' => $result
                ]);

                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'Xác thực thất bại',
                    'text' => 'Không thể xác thực CCCD. Vui lòng kiểm tra ảnh và thử lại.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('KYC Verification Exception: ' . $e->getMessage());
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Lỗi',
                'text' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ]);
        }
    }
}
