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
                return back()->withErrors(['error' => 'Bạn cần tạo yêu cầu học trước khi kết nối với gia sư']);
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
                return back()->withErrors([
                    'error' => __('messages.tutor_awaiting_approval')
                ]);
            }
            
            // Get student ID from the learning request
            $learningRequest = \App\Models\Request::findOrFail($request->request_id);
            $studentId = $learningRequest->student_id;
            $tutorId = $user->id;
            $requestId = $request->request_id;
        } else {
            return back()->withErrors(['error' => 'Invalid user role.']);
        }

        // Check for existing active request for this specific learning request
        if (Matching::where('request_id', $requestId)
                    ->where('tutor_id', $tutorId)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists()) {
            return back()->withErrors(['error' => 'You have already sent a connection request for this learning request.']);
        }

        DB::beginTransaction();
        try {
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
            ]);

            DB::commit();

            return back()->with('swal', [
                'type' => 'success',
                'title' => 'Request Sent!',
                'text' => 'Your connection request has been sent successfully.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to send request: ' . $e->getMessage()]);
        }
    }

    /**
     * Accept a matching request.
     */
    public function accept($id)
    {
        $matching = Matching::findOrFail($id);
        $user = auth()->user();

        // Verify user is the receiver
        if ($matching->sender_id == $user->id) {
            return back()->withErrors(['error' => __('messages.cannot_accept_own_request')]);
        }

        // Verify user is part of this matching
        if ($matching->student_id != $user->id && $matching->tutor_id != $user->id) {
            abort(403);
        }

        $matching->accept();

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

        // Verify user is the receiver
        if ($matching->sender_id == $user->id) {
            return back()->withErrors(['error' => __('messages.cannot_decline_own_request')]);
        }

        // Verify user is part of this matching
        if ($matching->student_id != $user->id && $matching->tutor_id != $user->id) {
            abort(403);
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
        ]);

        return back()->with('swal', [
            'type' => 'info',
            'title' => 'Đã hủy yêu cầu',
            'text' => 'Bạn đã hủy yêu cầu kết nối của mình.'
        ]);
    }
}
