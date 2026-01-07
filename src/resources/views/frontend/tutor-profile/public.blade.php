@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- Main Profile Section --}}
        <div class="col-lg-8">
            {{-- Profile Header --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img src="{{ $tutor->avatar_url }}" alt="{{ $tutor->name }}" 
                                 class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                        </div>
                        <div class="col-md-9">
                            <h1 class="h2 mb-2">
                                {{ $tutor->name }}
                                @if($tutor->tutorProfile->is_approved)
                                <span class="badge bg-primary ms-2">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">verified</span>
                                    {{ __('ui.verified') }}
                                </span>
                                @endif
                            </h1>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div>
                                    <span class="material-symbols-outlined">work</span>
                                    <strong>{{ $tutor->tutorProfile->experience_years ?? 0 }}</strong> năm kinh nghiệm
                                </div>
                            </div>
                            
                            @if($tutor->tutorProfile->subjects && $tutor->tutorProfile->subjects->count() > 0)
                            <div class="mb-3">
                                <strong class="d-block mb-2">{{ __('ui.subjects') }}:</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($tutor->tutorProfile->subjects as $subject)
                                    <span class="badge bg-light text-dark">{{ $subject->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @auth
                                @if(auth()->user()->isStudent())
                                    <form action="{{ route('matching.connect') }}" method="POST" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">person_add</span>
                                            {{ __('ui.connect_with') }} {{ $tutor->name }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary mt-3">
                                    {{ __('ui.login_to_connect') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- About Section --}}
            @if($tutor->tutorProfile->bio)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">{{ __('ui.about_me') }}</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tutor->tutorProfile->bio }}</p>
                </div>
            </div>
            @endif

            {{-- Education --}}
            @if($tutor->tutorProfile->education)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <span class="material-symbols-outlined align-middle me-2">school</span>
                        {{ __('ui.education') }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $tutor->tutorProfile->education }}</p>
                </div>
            </div>
            @endif

            {{-- Certificates --}}
            @if($tutor->tutorProfile->certificates && $tutor->tutorProfile->certificates->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <span class="material-symbols-outlined align-middle me-2">workspace_premium</span>
                        {{ __('ui.certificates') }} ({{ $tutor->tutorProfile->certificates->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($tutor->tutorProfile->certificates as $cert)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <a href="{{ $cert->file_url }}" target="_blank" class="text-decoration-none">
                                    <span class="material-symbols-outlined me-2">description</span>
                                    {{ $cert->name }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Pricing Card --}}
            @if($tutor->tutorProfile->hourly_rate_min && $tutor->tutorProfile->hourly_rate_max)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <span class="material-symbols-outlined align-middle me-2">payments</span>
                        {{ __('ui.pricing') }}
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-primary mb-2">
                        {{ number_format($tutor->tutorProfile->hourly_rate_min / 1000) }}k 
                        - 
                        {{ number_format($tutor->tutorProfile->hourly_rate_max / 1000) }}k ₫
                    </div>
                    <p class="text-muted mb-0">per hour</p>
                </div>
            </div>
            @endif

            {{-- Contact Info --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <span class="material-symbols-outlined align-middle me-2">contact_mail</span>
                        {{ __('ui.contact') }}
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        // Check if current user has unlocked contact with this tutor
                        $hasUnlockedContact = false;
                        if (auth()->check() && auth()->user()->isStudent()) {
                            $matching = \App\Models\Matching::where('student_id', auth()->id())
                                ->where('tutor_id', $tutor->id)
                                ->where('contact_unlocked', true)
                                ->first();
                            $hasUnlockedContact = $matching !== null;
                        }
                    @endphp
                    
                    @if($hasUnlockedContact)
                        {{-- Unlocked: Show real contact --}}
                        @if($tutor->email)
                        <div class="mb-3">
                            <span class="material-symbols-outlined align-middle me-2">email</span>
                            <a href="mailto:{{ $tutor->email }}">{{ $tutor->email }}</a>
                        </div>
                        @endif
                        @if($tutor->phone)
                        <div>
                            <span class="material-symbols-outlined align-middle me-2">phone</span>
                            <a href="tel:{{ $tutor->phone }}">{{ $tutor->phone }}</a>
                        </div>
                        @endif
                    @else
                        {{-- Locked: Hide contact info --}}
                        <div class="text-center py-3">
                            <span class="material-symbols-outlined text-muted d-block mb-2" style="font-size: 48px;">lock</span>
                            <p class="text-muted mb-2">{{ __('ui.contact_locked') }}</p>
                            <small class="text-muted">{{ __('ui.unlock_contact') }}</small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Teaching Areas --}}
            @if($tutor->tutorProfile->teaching_areas && count($tutor->tutorProfile->teaching_areas) > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">
                        <span class="material-symbols-outlined align-middle me-2">location_on</span>
                        {{ __('ui.teaching_locations') }}
                    </h3>
                </div>
                <div class="card-body">
                    @foreach($tutor->tutorProfile->teaching_areas as $area)
                    <div class="mb-2">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 16px;">check_circle</span>
                        {{ $area }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
