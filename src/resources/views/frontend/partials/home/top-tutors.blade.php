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

        {{-- AI Recommendations Section (For Verified Students with Active Requests) --}}
        @auth
            @if(auth()->user()->isStudent() && auth()->user()->is_verified && isset($latestRequestId) && $latestRequestId)
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
                <div class="card h-100 border-0 shadow-sm hover-shadow" style="transition: all 0.3s;">
                    <div class="card-body">
                        {{-- Avatar & Name --}}
                        <div class="text-center mb-3">
                            @php
                                $avatarUrl = $tutor->avatar ? \Storage::disk('s3')->url($tutor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($tutor->name) . '&size=200&background=3780f6&color=fff';
                            @endphp
                            <img src="{{ $avatarUrl }}" 
                                 class="rounded-circle mb-2 border border-2 border-primary" 
                                 width="80" height="80" 
                                 style="object-fit: cover;"
                                 alt="{{ $tutor->name }}">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <h5 class="fw-bold mb-1">{{ $tutor->name }}</h5>
                                <x-verified-badge :user="$tutor" size="18" />
                            </div>
                            @if($tutor->tutorProfile && $tutor->tutorProfile->education)
                                <small class="text-muted">{{ Str::limit($tutor->tutorProfile->education, 30) }}</small>
                            @endif
                        </div>

                        {{-- Bio --}}
                        @if($tutor->tutorProfile && $tutor->tutorProfile->bio)
                        <p class="text-muted small mb-3" style="min-height: 60px;">
                            {{ Str::limit($tutor->tutorProfile->bio, 100) }}
                        </p>
                        @endif

                        {{-- Subjects --}}
                        @if($tutor->tutorProfile && $tutor->tutorProfile->subjects && $tutor->tutorProfile->subjects->isNotEmpty())
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Môn học:</small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($tutor->tutorProfile->subjects->take(3) as $subject)
                                    <span class="badge bg-primary-light text-primary">{{ $subject->name }}</span>
                                @endforeach
                                @if($tutor->tutorProfile->subjects->count() > 3)
                                    <span class="badge bg-light text-dark">+{{ $tutor->tutorProfile->subjects->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Experience & Rate --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            @if($tutor->tutorProfile && $tutor->tutorProfile->experience_years)
                                <small class="text-muted">
                                    <span class="material-symbols-outlined align-middle" style="font-size: 16px;">work</span>
                                    {{ $tutor->tutorProfile->experience_years }} năm KN
                                </small>
                            @endif
                            @if($tutor->tutorProfile && $tutor->tutorProfile->hourly_rate_min && $tutor->tutorProfile->hourly_rate_max)
                                <strong class="text-primary">{{ number_format($tutor->tutorProfile->hourly_rate_min) }}-{{ number_format($tutor->tutorProfile->hourly_rate_max) }}₫/h</strong>
                            @elseif($tutor->tutorProfile && $tutor->tutorProfile->hourly_rate_min)
                                <strong class="text-primary">{{ number_format($tutor->tutorProfile->hourly_rate_min) }}₫/h</strong>
                            @endif
                        </div>


                        {{-- Action Buttons --}}
                        @auth
                            @if(auth()->user()->isStudent())
                                @if(isset($tutor->connection_status) && $tutor->connection_status)
                                    @if($tutor->connection_status == 'pending')
                                        <button class="btn btn-secondary w-100 mb-2" disabled>
                                            <i class="bi bi-hourglass-split"></i> Đang chờ
                                        </button>
                                    @elseif($tutor->connection_status == 'declined')
                                        <button class="btn btn-danger w-100 mb-2" disabled>
                                            <i class="bi bi-x-circle"></i> Đã từ chối
                                        </button>
                                    @endif
                                    {{-- If accepted, show nothing --}}
                                @else
                                    <form action="{{ route('matching.connect') }}" method="POST" class="w-100 mb-2">
                                        @csrf
                                        <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-person-plus"></i> Kết nối
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endauth

                        <a href="{{ route('tutor.show', $tutor->id) }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye"></i> Xem profile
                        </a>
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
