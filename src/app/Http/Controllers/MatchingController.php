<?php

namespace App\Http\Controllers;

use App\Models\Matching;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchingController extends Controller
{
    /**
     * Display user's matchings.
     */
    public function index()
    {
        $user = auth()->user();
        
        $matchings = Matching::forUser($user->id)
            ->with(['student', 'tutor', 'sender'])
            ->latest()
            ->paginate(20);

        return view('frontend.matchings.index', compact('matchings'));
    }

    /**
     * Display my requests page.
     */
    public function myRequests()
    {
        $user = auth()->user();
        
        // Get requests sent by this user
        $sentRequests = Matching::where('sender_id', $user->id)
            ->with(['student', 'tutor'])
            ->latest()
            ->get();
        
        // Get requests received by this user with sender's tutor profile
        $receivedRequests = Matching::forUser($user->id)
            ->where('sender_id', '!=', $user->id)
            ->with(['sender', 'sender.tutorProfile.subjects'])
            ->latest()
            ->get();
        
        return view('frontend.matchings.my-requests', compact('sentRequests', 'receivedRequests'));
    }

    /**
     * Create a new matching request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validate based on user role
        if ($user->isStudent()) {
            $request->validate([
                'tutor_id' => 'required|exists:users,id',
                'message' => 'nullable|string|max:500',
            ]);
            
            // Get student's latest active request
            $latestRequest = \App\Models\Request::where('student_id', $user->id)
                ->where('status', 'open')
                ->latest()
                ->first();
            
            if (!$latestRequest) {
                return back()->with('swal', [
                    'type' => 'warning',
                    'title' => 'Cần tạo yêu cầu học',
                    'text' => 'Bạn cần tạo yêu cầu học trước khi kết nối với gia sư.'
                ]);
            }
            
            $tutorId = $request->tutor_id;
            $studentId = $user->id;
            $requestId = $latestRequest->id;
            
            // CHECK: Tutor must be approved
            $tutor = \App\Models\User::with('tutorProfile')->find($tutorId);
            if (!$tutor || !$tutor->tutorProfile || !$tutor->tutorProfile->is_approved) {
                return back()->withErrors([
                    'error' => __('messages.tutor_not_approved')
                ]);
            }
            
        } elseif ($user->isTutor()) {
            $request->validate([
                'request_id' => 'required|exists:requests,id',
                'message' => 'nullable|string|max:500',
            ]);
            
            // CHECK: Tutor must be approved to send connections
            if (!$user->tutorProfile || !$user->tutorProfile->is_approved) {
                return back()->with('swal', [
                    'type' => 'error',
                    'title' => 'Lỗi',
                    'text' => __('messages.tutor_awaiting_approval')
                ]);
            }
            
            // Get student ID from the learning request
            $learningRequest = \App\Models\Request::findOrFail($request->request_id);
            $studentId = $learningRequest->student_id;
            $tutorId = $user->id;
            $requestId = $request->request_id;
        } else {
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Lỗi phân quyền',
                'text' => 'Vai trò người dùng không hợp lệ.'
            ]);
        }

        // NEW: Check for any active connection FOR THIS REQUEST
        $hasActiveConnection = Matching::where('request_id', $requestId)
            ->where(function($q) use ($user) {
                $q->where('student_id', $user->id)
                  ->orWhere('tutor_id', $user->id);
            })
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
        
        if ($hasActiveConnection) {
            return back()->with('swal', [
                'type' => 'warning',
                'title' => 'Kết nối đang tồn tại',
                'text' => 'Bạn đã có kết nối cho yêu cầu này. Vui lòng kiểm tra lại.'
            ]);
        }
        
        // CHECK: Request must be open and not matched
        $targetRequest = \App\Models\Request::find($requestId);
        if (!$targetRequest || $targetRequest->status !== 'open' || $targetRequest->is_matched) {
             return back()->with('swal', [
                'type' => 'error',
                'title' => 'Yêu cầu không khả dụng',
                'text' => 'Yêu cầu này đã được kết nối hoặc đã đóng.'
            ]);
        }

        // Check for existing active request for this specific learning request
        // Use lockForUpdate to prevent race condition
        DB::beginTransaction();
        try {
            $existing = Matching::where('request_id', $requestId)
                        ->where('tutor_id', $tutorId)
                        ->lockForUpdate()
                        ->first();
            
            if ($existing) {
                DB::rollBack();
                return back()->with('swal', [
                    'type' => 'info',
                    'title' => 'Kết nối đã tồn tại',
                    'text' => 'Bạn đã gửi yêu cầu kết nối cho yêu cầu học tập này rồi.'
                ]);
            }

            // Create matching
            $matching = Matching::create([
                'request_id' => $requestId,
                'student_id' => $studentId,
                'tutor_id' => $tutorId,
                'sender_id' => $user->id,
                'status' => 'pending',
                'message' => $request->message,
            ]);

            // Create notification for receiver
            $receiver = $matching->receiver;
            Notification::create([
                'user_id' => $receiver->id,
                'matching_id' => $matching->id,
                'type' => 'connect_request',
                'title' => 'New Connection Request',
                'message' => $user->name . ' wants to connect with you.',
                'action_url' => route('matching.my-requests'),
            ]);

            DB::commit();

            return back()->with('swal', [
                'type' => 'success',
                'title' => 'Request Sent!',
                'text' => 'Your connection request has been sent successfully.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Matching creation failed', [
                'user_id' => $user->id,
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors([
                'error' => 'Không thể tạo kết nối. Vui lòng thử lại sau.'
            ]);
        }
    }

    /**
     * Accept a matching request.
     */
    public function accept($id)
    {
        $matching = Matching::findOrFail($id);
        $user = auth()->user();

        // Verify user is the receiver (not the sender)
        if ($matching->sender_id == $user->id) {
            return back()->withErrors(['error' => __('messages.cannot_accept_own_request')]);
        }

        // Verify user is part of this matching
        if ($matching->student_id != $user->id && $matching->tutor_id != $user->id) {
            abort(403, 'Bạn không có quyền thực hiện hành động này');
        }

        // NEW: Check matching status - only pending can be accepted
        if ($matching->status !== 'pending') {
            return back()->withErrors([
                'error' => 'Yêu cầu này không còn ở trạng thái chờ và không thể chấp nhận'
            ]);
        }

        // CRITICAL: Check if request is already matched (prevent double acceptance)
        if ($matching->request->is_matched) {
            return back()->withErrors([
                'error' => 'Yêu cầu này đã được kết nối với gia sư khác.'
            ]);
        }

        $matching->accept();

        // NEW: Mark the request as matched
        $matching->request->update(['is_matched' => true]);

        return back()->with('swal', [
            'type' => 'success',
            'title' => 'Đã kết nối!',
            'text' => 'Bạn đã kết nối thành công với người dùng này.'
        ]);
    }

    /**
     * Decline a matching request.
     */
    public function decline(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ]);
        
        $matching = Matching::findOrFail($id);
        $user = auth()->user();

        // Verify user is the receiver (not the sender)
        if ($matching->sender_id == $user->id) {
            return back()->withErrors(['error' => __('messages.cannot_decline_own_request')]);
        }

        // Verify user is part of this matching
        if ($matching->student_id != $user->id && $matching->tutor_id != $user->id) {
            abort(403, 'Bạn không có quyền thực hiện hành động này');
        }

        // NEW: Check matching status - only pending can be declined
        if ($matching->status !== 'pending') {
            return back()->withErrors([
                'error' => 'Yêu cầu này không còn ở trạng thái chờ và không thể từ chối'
            ]);
        }

        $matching->update([
            'status' => 'declined',
            'decline_reason' => $request->reason
        ]);
        
        // Create notification for sender
        \App\Models\Notification::create([
            'user_id' => $matching->sender_id,
            'matching_id' => $matching->id,
            'type' => 'connect_declined',
            'title' => 'Yêu cầu bị từ chối',
            'message' => $matching->receiver->name . ' đã từ chối yêu cầu của bạn. Lý do: ' . $request->reason,
            'action_url' => route('matching.my-requests'),
        ]);

        return back()->with('swal', [
            'type' => 'info',
            'title' => 'Đã từ chối yêu cầu',
            'text' => 'Bạn đã từ chối yêu cầu kết nối này.'
        ]);
    }

    /**
     * Cancel a matching request (by sender).
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ]);
        
        $matching = Matching::findOrFail($id);
        $user = auth()->user();

        // Verify user is the sender
        if ($matching->sender_id != $user->id) {
            return back()->withErrors(['error' => __('messages.can_only_cancel_own')]);
        }

        // NEW: Check matching status - only pending can be cancelled
        if ($matching->status !== 'pending') {
            return back()->withErrors([
                'error' => 'Chỉ có thể hủy yêu cầu đang ở trạng thái chờ'
            ]);
        }

        $matching->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->reason
        ]);
        
        // Notify receiver
        $receiverId = $matching->sender_id == $matching->student_id ? $matching->tutor_id : $matching->student_id;
        \App\Models\Notification::create([
            'user_id' => $receiverId,
            'matching_id' => $matching->id,
            'type' => 'connect_cancelled',
            'title' => 'Yêu cầu đã bị hủy',
            'message' => $user->name . ' đã hủy yêu cầu kết nối. Lý do: ' . $request->reason,
            'action_url' => route('matching.my-requests'),
        ]);

        return back()->with('swal', [
            'type' => 'info',
            'title' => 'Đã hủy yêu cầu',
            'text' => 'Bạn đã hủy yêu cầu kết nối của mình.'
        ]);
    }
}
