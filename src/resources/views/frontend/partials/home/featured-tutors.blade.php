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
                <div class="home-tutor-card h-100">
                    <div class="home-tutor-header">
                    <div class="home-tutor-avatar-wrapper">
                            <img src="{{ $tutor->avatar_url }}" alt="{{ $tutor->name }}" class="home-tutor-avatar">
                            @if($tutor->tutorProfile && $tutor->tutorProfile->is_approved)
                            <div class="home-tutor-verified">
                                <span class="material-symbols-outlined">check</span>
                            </div>
                            @endif
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
                            @if(auth()->user()->isStudent() && isset($userMatchings))
                                @php
                                    $matching = isset($userMatchings[$tutor->id]) ? $userMatchings[$tutor->id] : null;
                                @endphp
                                
                                @if($matching)
                                    @if($matching->status == 'accepted')
                                        <button class="home-tutor-view-btn" style="background: #10b981; cursor: not-allowed;" disabled>
                                            ✓ {{ __('ui.connected') }}
                                        </button>
                                    @elseif($matching->status == 'declined')
                                        <button class="home-tutor-view-btn" style="background: #ef4444; cursor: not-allowed;" disabled>
                                            ✗ {{ __('ui.declined_status') }}
                                        </button>
                                    @elseif($matching->status == 'pending')
                                        <button class="home-tutor-view-btn" style="background: #f59e0b; cursor: not-allowed;" disabled>
                                            ⏳ {{ __('ui.waiting_confirm') }}
                                        </button>
                                    @endif
                                @else
                                    <form action="{{ route('matching.connect') }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
                                        <button type="submit" class="home-tutor-view-btn">{{ __('ui.connect_now') }}</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('tutor.show', $tutor->id) }}" class="home-tutor-view-btn">Xem profile</a>
                            @endif
                        @else
                            <button class="home-tutor-view-btn" onclick="window.location='{{ route('login') }}'">{{ __('ui.view_profile') }}</button>
                        @endauth
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
