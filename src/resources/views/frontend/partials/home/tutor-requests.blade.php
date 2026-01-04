{{-- Student Requests Section - Latest Open Requests --}}
@if(isset($studentRequests) && $studentRequests->count() > 0)
<section class="home-requests">
    <div class="container">
        {{-- Section Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4 gap-3">
            <div>
                <h2 class="home-section-title mb-2">{{ __('ui.student_requests_title') }}</h2>
                <p class="home-section-description mb-0">{{ __('ui.see_what_students_need') }}</p>
            </div>
            <div>
                @auth
                    @if(auth()->user()->isTutor())
                        <a href="#" class="btn btn-outline-secondary">{{ __('ui.view_all') }}</a>
                    @else
                        <a href="{{ route('student.request.create') }}" class="btn btn-primary">{{ __('ui.post_request') }}</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">{{ __('ui.login_button') }}</a>
                @endauth
            </div>
        </div>

        {{-- AI Recommendations Section (For Tutors) --}}
        @auth
            @if(auth()->user()->isTutor() && isset($tutorProfileId) && $tutorProfileId)
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="ai-icon-wrapper">
                        <span class="material-symbols-outlined">psychology</span>
                    </div>
                    <div>
                        <h3 class="mb-0 h5">{{ __('ui.requests_match_you') }}</h3>
                        <small class="text-muted">{{ __('ui.ai_analyzed_profile_requests') }}</small>
                    </div>
                </div>
                
                <div class="row g-4" 
                     data-ai-requests 
                     data-tutor-profile-id="{{ $tutorProfileId }}">
                    {{-- AI will auto-load recommendations here --}}
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="text-muted">Đang phân tích với AI để tìm yêu cầu phù hợp...</p>
                    </div>
                </div>
            </div>
            @endif
        @endauth

        {{-- Request Cards Grid --}}
        <div class="row g-4">
            @foreach($studentRequests as $request)
            <div class="col-md-6 col-lg-4">
                <div class="home-request-card">
                    <div class="home-request-header">
                        <span class="home-request-badge {{ strtolower($request->subject->name ?? 'other') }}">
                            {{ $request->subject->name ?? 'Chung' }}
                        </span>
                        <span class="home-request-time">
                            <span class="material-symbols-outlined">schedule</span> 
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div>
                        <h3 class="home-request-title">{{ Str::limit($request->title, 50) }}</h3>
                        <p class="home-request-description">
                            {{ Str::limit($request->description, 100) }}
                        </p>
                    </div>
                    <div class="home-request-footer">
                        @if($request->budget_min && $request->budget_max)
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">payments</span> 
                            {{ number_format($request->budget_min / 1000) }}k-{{ number_format($request->budget_max / 1000) }}k₫/giờ
                        </div>
                        @endif
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">
                                {{ $request->location_type == 'online' ? 'videocam' : ($request->location_type == 'offline' ? 'location_on' : 'wifi') }}
                            </span> 
                            @if($request->location_type == 'online')
                                Trực tuyến
                            @elseif($request->location_type == 'offline')
                                Trực tiếp
                            @else
                                Linh hoạt
                            @endif
                        </div>
                    </div>
                    @auth
                        @if(auth()->user()->isTutor())
                        <div class="mt-3">
                            <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#requestDetailModal{{ $request->id }}">
                                <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                Xem Chi Tiết
                            </button>
                        </div>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Request Detail Modal --}}
            @auth
                @if(auth()->user()->isTutor())
                <div class="modal fade" id="requestDetailModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <span class="material-symbols-outlined align-middle me-2">description</span>
                                    Chi tiết yêu cầu học
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Student Info --}}
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    @php
                                        $studentAvatar = $request->student->avatar 
                                            ? \Storage::disk('s3')->url($request->student->avatar) 
                                            : 'https://ui-avatars.com/api/?name='.urlencode($request->student->name).'&size=80';
                                    @endphp
                                    <img src="{{ $studentAvatar }}" class="rounded-circle me-3" width="80" height="80">
                                    <div>
                                        <h5 class="mb-1">{{ $request->student->name }}</h5>
                                        <p class="text-muted mb-0">
                                            <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                                            Thông tin liên hệ bị khóa
                                        </p>
                                        <small class="text-muted">Kết nối và thanh toán để xem</small>
                                    </div>
                                </div>

                                {{-- Request Details --}}
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">title</span>
                                        Tiêu đề
                                    </h6>
                                    <p>{{ $request->title }}</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">description</span>
                                        Mô tả chi tiết
                                    </h6>
                                    <p class="text-muted">{{ $request->description }}</p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">school</span>
                                            Môn học
                                        </h6>
                                        <span class="badge bg-primary">{{ $request->subject->name ?? 'Chung' }}</span>
                                    </div>
                                    @if($request->education_level)
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">stairs</span>
                                            Trình độ
                                        </h6>
                                        <p>
                                            @if(is_object($request->educationLevel ?? null))
                                                {{ $request->educationLevel->name }}
                                            @elseif(is_string($request->education_level))
                                                {{ $request->education_level }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                    @endif
                                </div>

                                @if($request->budget_min && $request->budget_max)
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">payments</span>
                                        Ngân sách
                                    </h6>
                                    <p class="text-success fw-bold">
                                        {{ number_format($request->budget_min / 1000) }}k - {{ number_format($request->budget_max / 1000) }}k ₫/giờ
                                    </p>
                                </div>
                                @endif

                                @if($request->schedule)
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">calendar_month</span>
                                        Lịch học mong muốn
                                    </h6>
                                    <p>
                                        @if(is_array($request->schedule))
                                            {{ implode(', ', $request->schedule) }}
                                        @else
                                            {{ $request->schedule }}
                                        @endif
                                    </p>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <h6 class="fw-bold mb-2">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">
                                            {{ $request->location_type == 'online' ? 'videocam' : 'location_on' }}
                                        </span>
                                        Hình thức học
                                    </h6>
                                    <p>
                                        @if($request->location_type == 'online')
                                            <span class="badge bg-info">Trực tuyến</span>
                                        @elseif($request->location_type == 'offline')
                                            <span class="badge bg-warning">Trực tiếp</span>
                                        @else
                                            <span class="badge bg-secondary">Linh hoạt</span>
                                        @endif
                                        @if($request->location && $request->location_type != 'online')
                                            - {{ $request->location }}
                                        @endif
                                    </p>
                                </div>

                                <div class="border-top pt-3">
                                    <small class="text-muted">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 14px;">schedule</span>
                                        Đăng {{ $request->created_at->diffForHumans() }} ({{ $request->created_at->format('d/m/Y H:i') }})
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                
                                {{-- Check if already connected or has pending request --}}
                                @php
                                    $existingMatching = \App\Models\Matching::where(function($query) use ($request) {
                                        $query->where('student_id', $request->student_id)
                                              ->where('tutor_id', auth()->id());
                                    })
                                    ->whereIn('status', ['pending', 'accepted'])
                                    ->first();
                                @endphp
                                
                                @if($existingMatching)
                                    @if($existingMatching->status === 'accepted')
                                        <button type="button" class="btn btn-success" disabled>
                                            <span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span>
                                            Đã Kết Nối
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-warning" disabled>
                                            <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                                            Đang Chờ Phản Hồi
                                        </button>
                                    @endif
                                @else
                                    <form action="{{ route('matching.connect') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{ $request->id }}">
                                        <input type="hidden" name="message" value="Tôi quan tâm đến yêu cầu học của bạn: {{ $request->title }}">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">person_add</span>
                                            Kết Nối Với Học Sinh
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endauth
            @endforeach
        </div>
    </div>
</section>
@endif
