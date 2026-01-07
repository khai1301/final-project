<?php
namespace App\Http\Controllers;

use App\Models\Request as LearningRequest;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\LearningMode;
use App\Http\Requests\StoreStudentRequest;
use Illuminate\Support\Facades\Auth;

class StudentRequestController extends Controller
{
    /**
     * Display a listing of the student's learning requests.
     */
    public function index()
    {
        $requests = LearningRequest::where('student_id', auth()->id())
            ->with(['subject', 'educationLevel', 'learningMode', 'province', 'ward', 'matchings'])
            ->withCount(['matchings as pending_connections' => function($q) {
                $q->where('status', 'pending');
            }])
            ->withCount(['matchings as accepted_connections' => function($q) {
                $q->where('status', 'accepted');
            }])
            ->latest()
            ->paginate(10);
        
        return view('frontend.student.my-learning-requests', compact('requests'));
    }

    /**
     * Show the request creation form.
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $educationLevels = EducationLevel::active()->ordered()->get();
        $learningModes = LearningMode::active()->get();
        $timeSlots = \App\Models\TimeSlot::active()->orderBy('day_of_week')->orderBy('start_time')->get();
        
        // Load location data for client-side filtering
        $provinces = \App\Models\Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name', 'type', 'code', 'province_code']);
        
        return view('frontend.home.student', compact('subjects', 'educationLevels', 'learningModes', 'timeSlots', 'provinces', 'wards'));
    }

    /**
     * Store a new learning request.
     */
    public function store(StoreStudentRequest $request)
    {
        // NEW: Check for existing open/pending request
        $existingRequest = LearningRequest::where('student_id', Auth::id())
            ->whereIn('status', ['open', 'pending'])
            ->exists();
        
        if ($existingRequest) {
            return back()->withErrors([
                'error' => 'Bạn đã có yêu cầu học tập đang mở. Vui lòng hoàn thành hoặc đóng yêu cầu hiện tại trước khi tạo yêu cầu mới.'
            ])->withInput();
        }

        // Parse skills from JSON string if provided
        $skills = null;
        if ($request->filled('skills')) {
            $skillsData = json_decode($request->input('skills'), true);
            $skills = is_array($skillsData) ? $skillsData : null;
        }

        // Lookup foreign keys by ID (database-driven)
        $subject = Subject::where('name', $request->input('subject'))->first();
        $educationLevel = EducationLevel::where('name', $request->input('education_level'))->first();
        $learningMode = LearningMode::find($request->input('learning_mode_id'));

        // Determine location: custom (if provided) OR user profile
        $provinceId = $request->filled('province_id') ? $request->input('province_id') : auth()->user()->province_id;
        $wardId = $request->filled('ward_id') ? $request->input('ward_id') : auth()->user()->ward_id;
        $addressDetail = $request->filled('address_detail') ? $request->input('address_detail') : auth()->user()->address_detail;
        
        // Create the learning request (only FK columns + core data)
        $learningRequest = LearningRequest::create([
            'student_id' => Auth::id(),
            'title' => "Learning request for " . $request->input('subject'),
            'subject_id' => $subject ? $subject->id : null,
            'education_level_id' => $educationLevel ? $educationLevel->id : null,
            'learning_mode_id' => $learningMode ? $learningMode->id : null,
            'skills' => $skills,
            'province_id' => $provinceId,
            'ward_id' => $wardId,
            'address_detail' => $addressDetail,
            'budget_min' => $request->input('budget_min'),
            'budget_max' => $request->input('budget_max'),
            'description' => $request->input('notes'),
            'status' => 'open',
        ]);

        // Attach time slots if provided
        if ($request->filled('time_slots') && is_array($request->input('time_slots'))) {
            $learningRequest->timeSlots()->attach($request->input('time_slots'));
        }

        return redirect()
            ->route('student.request.create')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Thành công',
                'text' => 'Yêu cầu học tập của bạn đã được gửi thành công! Hệ thống AI sẽ ghép đôi bạn với gia sư phù hợp nhất.'
            ]);
    }

    /**
     * Show the form for editing a learning request.
     */
    public function edit($id)
    {
        $request = LearningRequest::findOrFail($id);
        
        // Authorization check
        if ($request->student_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Can only edit active or open requests
        if (!in_array($request->status, ['active', 'open'])) {
            return redirect()->route('student.requests.index')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Không thể sửa',
                    'text' => 'Chỉ có thể sửa yêu cầu đang hoạt động hoặc đang mở'
                ]);
        }
        
        $subjects = Subject::active()->orderBy('name')->get();
        $educationLevels = EducationLevel::active()->ordered()->get();
        $learningModes = LearningMode::active()->get();
        $timeSlots = \App\Models\TimeSlot::active()->orderBy('day_of_week')->orderBy('start_time')->get();
        
        // Load location data
        $provinces = \App\Models\Province::orderBy('name')->get(['id', 'name', 'type', 'code']);
        $wards = \App\Models\Ward::orderBy('name')->get(['id', 'name', 'type', 'code', 'province_code']);
        
        return view('frontend.student.edit-request', compact('request', 'subjects', 'educationLevels', 'learningModes', 'timeSlots', 'provinces', 'wards'));
    }

    /**
     * Update a learning request.
     */
    public function update(StoreStudentRequest $updateRequest, $id)
    {
        $learningRequest = LearningRequest::findOrFail($id);
        
        // Authorization check
        if ($learningRequest->student_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        

        // Can only update active or open requests
        if (!in_array($learningRequest->status, ['active', 'open'])) {
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Không thể cập nhật',
                'text' => 'Chỉ có thể cập nhật yêu cầu đang hoạt động hoặc đang mở'
            ]);
        }

        // Parse skills
        $skills = null;
        if ($updateRequest->filled('skills')) {
            $skillsData = json_decode($updateRequest->input('skills'), true);
            $skills = is_array($skillsData) ? $skillsData : null;
        }

        // Lookup foreign keys
        $subject = Subject::where('name', $updateRequest->input('subject'))->first();
        $educationLevel = EducationLevel::where('name', $updateRequest->input('education_level'))->first();
        $learningMode = LearningMode::find($updateRequest->input('learning_mode_id'));

        // Update location
        $provinceId = $updateRequest->filled('province_id') ? $updateRequest->input('province_id') : auth()->user()->province_id;
        $wardId = $updateRequest->filled('ward_id') ? $updateRequest->input('ward_id') : auth()->user()->ward_id;
        $addressDetail = $updateRequest->filled('address_detail') ? $updateRequest->input('address_detail') : auth()->user()->address_detail;
        
        // Update the request
        $learningRequest->update([
            'title' => "Learning request for " . $updateRequest->input('subject'),
            'subject_id' => $subject ? $subject->id : null,
            'education_level_id' => $educationLevel ? $educationLevel->id : null,
            'learning_mode_id' => $learningMode ? $learningMode->id : null,
            'skills' => $skills,
            'province_id' => $provinceId,
            'ward_id' => $wardId,
            'address_detail' => $addressDetail,
            'budget_min' => $updateRequest->input('budget_min'),
            'budget_max' => $updateRequest->input('budget_max'),
            'description' => $updateRequest->input('notes'),
        ]);

        // Sync time slots
        if ($updateRequest->filled('time_slots') && is_array($updateRequest->input('time_slots'))) {
            $learningRequest->timeSlots()->sync($updateRequest->input('time_slots'));
        } else {
            $learningRequest->timeSlots()->detach();
        }

        return redirect()->route('student.requests.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Thành công',
                'text' => 'Yêu cầu học tập đã được cập nhật!'
            ]);
    }

    /**
     * Delete a learning request.
     */
    public function destroy($id)
    {
        $request = LearningRequest::findOrFail($id);
        
        // Authorization check
        if ($request->student_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        

        // Can only delete active or open requests
        if (!in_array($request->status, ['active', 'open'])) {
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Không thể xóa',
                'text' => 'Chỉ có thể xóa yêu cầu đang hoạt động hoặc đang mở'
            ]);
        }
        
        // Check for accepted matchings
        $hasAcceptedMatchings = $request->matchings()->where('status', 'accepted')->exists();
        if ($hasAcceptedMatchings) {
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Không thể xóa',
                'text' => 'Không thể xóa yêu cầu đã có kết nối được chấp nhận'
            ]);
        }
        
        // Delete the request (cascade will handle time slots)
        $request->delete();
        
        return redirect()->route('student.requests.index')
            ->with('swal', [
                'type' => 'success',
                'title' => 'Đã xóa',
                'text' => 'Yêu cầu học tập đã được xóa'
            ]);
    }
}
