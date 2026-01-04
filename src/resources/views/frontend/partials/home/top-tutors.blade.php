{{-- Top Tutors Section - By Rating (High to Low) --}}
<section class="home-tutors">
    <div class="container">
        {{-- Section Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4 gap-3">
            <div>
                <h2 class="home-section-title mb-2">{{ __('ui.top_tutors') }}</h2>
                <p class="home-section-description mb-0">{{ __('ui.learn_from_top_rated') }}</p>
            </div>
            @guest
            <div class="d-none d-md-block">
                <a href="{{ route('login') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none fw-bold" style="color: var(--primary);">
                    {{ __('ui.view_all') }}
                    <span class="material-symbols-outlined" style="font-size: 0.875rem;">arrow_forward</span>
                </a>
            </div>
            @endguest
        </div>

        {{-- AI Recommendations Section (For Students with Requests) --}}
        @auth
            @if(auth()->user()->isStudent() && isset($latestRequestId) && $latestRequestId)
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="ai-icon-wrapper">
                        <span class="material-symbols-outlined">psychology</span>
                    </div>
                    <div>
                        <h3 class="mb-0 h5">{{ __('ui.personalized_recommendations') }}</h3>
                        <small class="text-muted">{{ __('ui.ai_analyzed_best_match') }}</small>
                    </div>
                </div>
                
                <div class="row g-4" 
                     data-ai-tutors 
                     data-request-id="{{ $latestRequestId }}">
                    {{-- AI will auto-load recommendations here --}}
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="text-muted">Đang phân tích với AI để tìm gia sư phù hợp nhất...</p>
                    </div>
                </div>
            </div>
            @endif
        @endauth

        {{-- Tutor Cards Grid --}}
        @if(isset($topTutors) && $topTutors->count() > 0)
        <div class="row g-4">
            @foreach($topTutors as $tutor)
            <div class="col-sm-6 col-lg-3">
                <div class="home-tutor-card">
                    <div class="home-tutor-header">
                        <div class="home-tutor-avatar-wrapper">
                            @php
                                $avatarUrl = $tutor->avatar 
                                    ? \Storage::disk('s3')->url($tutor->avatar) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($tutor->name).'&size=100';
                            @endphp
                            <img src="{{ $avatarUrl }}" alt="{{ $tutor->name }}" class="home-tutor-avatar">
                            @if($tutor->tutorProfile && $tutor->tutorProfile->is_approved)
                            <div class="home-tutor-verified">
                                <span class="material-symbols-outlined">check</span>
                            </div>
                            @endif
                        </div>
                        <div class="home-tutor-rating">
                            <span class="material-symbols-outlined">star</span> 
                            {{ $tutor->tutorProfile->rating_avg ?? '5.0' }}
                        </div>
                    </div>
                    <div>
                        <h3 class="home-tutor-name">{{ $tutor->name }}</h3>
                        <p class="home-tutor-subject">
                            @if($tutor->tutorProfile && $tutor->tutorProfile->subjects && $tutor->tutorProfile->subjects->count() > 0)
                                {{ $tutor->tutorProfile->subjects->first()->name }} 
                                @if($tutor->tutorProfile->subjects->count() > 1)
                                    +{{ $tutor->tutorProfile->subjects->count() - 1 }}
                                @endif
                            @else
                                Gia sư
                            @endif
                        </p>
                    </div>
                    @if($tutor->tutorProfile && $tutor->tutorProfile->subjects && $tutor->tutorProfile->subjects->count() > 0)
                    <div class="home-tutor-tags">
                        @foreach($tutor->tutorProfile->subjects->take(2) as $subject)
                        <span class="home-tutor-tag">{{ $subject->name }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="home-tutor-footer">
                        @if($tutor->tutorProfile && $tutor->tutorProfile->hourly_rate_min)
                        <span class="home-tutor-price">
                            {{ number_format($tutor->tutorProfile->hourly_rate_min / 1000) }}k-{{ number_format($tutor->tutorProfile->hourly_rate_max / 1000) }}k
                            <span class="home-tutor-price-unit">₫/giờ</span>
                        </span>
                        @endif

                        @auth
                            @if(auth()->user()->isStudent())
                                @if(isset($latestRequest) && $latestRequest)
                                    {{-- Student has active request - show connection status --}}
                                    @if(isset($tutor->connection_status))
                                        @if($tutor->connection_status == 'accepted')
                                            <button class="home-tutor-view-btn" style="background: #10b981; cursor: not-allowed;" disabled>
                                                ✓ Đã kết nối
                                            </button>
                                        @elseif($tutor->connection_status == 'pending')
                                            <button class="home-tutor-view-btn" style="background: #f59e0b; cursor: not-allowed;" disabled>
                                                ⏳ Chờ xác nhận
                                            </button>
                                        @endif
                                    @else
                                        <form action="{{ route('matching.connect') }}" method="POST" class="w-100">
                                            @csrf
                                            <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
                                            <button type="submit" class="home-tutor-view-btn">
                                                Kết nối ngay
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    {{-- Student has no active request --}}
                                    <a href="{{ route('student.request.create') }}" class="home-tutor-view-btn">
                                        Tạo yêu cầu học
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('tutor.show', $tutor->id) }}" class="home-tutor-view-btn">Xem profile</a>
                            @endif
                        @else
                            <button class="home-tutor-view-btn" onclick="window.location='{{ route('login') }}'">
                                Xem profile
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-info text-center">
            <span class="material-symbols-outlined align-middle me-2">info</span>
            Chưa có gia sư nào. Vui lòng quay lại sau!
        </div>
        @endif

        {{-- Mobile View All Button --}}
        @guest
        <div class="text-center mt-4 d-md-none">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                Xem tất cả gia sư
                <span class="material-symbols-outlined" style="font-size: 0.875rem;">arrow_forward</span>
            </a>
        </div>
        @endguest
    </div>
</section>
