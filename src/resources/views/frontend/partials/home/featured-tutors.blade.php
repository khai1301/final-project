{{-- Featured Tutors Section - Newest Tutors from Database --}}
<section class="featured-tutors py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="home-section-title">{{ __('ui.newest_tutors') }}</h2>
            <p class="home-section-description">{{ __('ui.explore_new_tutors') }}</p>
        </div>

        @if(isset($featuredTutors) && $featuredTutors->count() > 0)
        <div class="row g-4">
            @foreach($featuredTutors as $tutor)
            <div class="col-md-6 col-lg-3">
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

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">{{ __('ui.view_all_tutors') }} →</a>
        </div>
        @else
        <div class="alert alert-info text-center">
            <span class="material-symbols-outlined align-middle me-2">info</span>
            {{ __('ui.no_new_tutors') }}
        </div>
        @endif
    </div>
</section>

<style>
.tutor-card-hover {
    transition: all 0.3s ease;
}

.tutor-card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.bg-primary-light {
    background-color: rgba(13, 110, 253, 0.1);
}
</style>
