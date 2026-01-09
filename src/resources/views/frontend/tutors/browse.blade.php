@extends('frontend.layouts.bootstrap')

@section('title', __('ui.find_tutors_title'))

@section('content')
<div class="container py-5">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-2">{{ __('ui.find_tutors_title') }}</h1>
            <p class="text-muted">{{ __('ui.find_suitable_tutor') }}</p>
        </div>
    </div>

    <div class="row">
        {{-- Filter Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle">filter_list</span>
                        {{ __('ui.filters') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tutors.browse') }}" method="GET" id="filterForm">
                        {{-- Search --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('forms.search_by_name') }}</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="{{ __('forms.enter_tutor_name') }}" 
                                   value="{{ request('search') }}">
                        </div>

                        {{-- Subject Filter --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('forms.subject') }}</label>
                            <select name="subjects[]" class="form-select" multiple size="5">
                                @foreach($allSubjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                        {{ in_array($subject->id, request('subjects', [])) ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('forms.hold_ctrl') }}</small>
                        </div>


                        {{-- Hourly Rate Range --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('forms.price_range') }}</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="rate_min" class="form-control form-control-sm" 
                                           placeholder="{{ __('forms.from') }}" value="{{ request('rate_min') }}" min="0" step="10000">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="rate_max" class="form-control form-control-sm" 
                                           placeholder="{{ __('forms.to') }}" value="{{ request('rate_max') }}" min="0" step="10000">
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <span class="material-symbols-outlined" style="font-size: 18px;">search</span>
                                {{ __('forms.search') }}
                            </button>
                            <a href="{{ route('tutors.browse') }}" class="btn btn-outline-secondary btn-sm">
                                {{ __('ui.reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tutors Grid --}}
        <div class="col-lg-9">
            {{-- Results Count --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">
                    {{ __('ui.found_tutors', ['count' => $tutors->total()]) }}
                </p>
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

            @if($tutors->count() > 0)
                <div class="row g-4">
                    @foreach($tutors as $tutor)
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow" style="transition: all 0.3s;">
                                <div class="card-body">
                                    {{-- Avatar & Name --}}
                                    <div class="text-center mb-3">
                                        @php
                                            $avatarUrl = $tutor->user->avatar ? \Storage::disk('s3')->url($tutor->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($tutor->user->name) . '&size=200&background=3780f6&color=fff';
                                        @endphp
                                        <img src="{{ $avatarUrl }}"
                                             class="rounded-circle mb-2" 
                                             width="80" height="80" 
                                             style="object-fit: cover;"
                                             alt="{{ $tutor->user->name }}">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <h5 class="fw-bold mb-1">{{ $tutor->user->name }}</h5>
                                            <x-verified-badge :user="$tutor->user" size="18" />
                                        </div>
                                        @if($tutor->education)
                                            <small class="text-muted">{{ $tutor->education }}</small>
                                        @endif
                                    </div>

                                    {{-- Bio --}}
                                    <p class="text-muted small mb-3" style="min-height: 60px;">
                                        {{ Str::limit($tutor->bio, 100) }}
                                    </p>

                                    {{-- Subjects --}}
                                    @if($tutor->subjects && $tutor->subjects->isNotEmpty())
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">{{ __('forms.subject') }}:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($tutor->subjects->take(3) as $subject)
                                                    <span class="badge bg-primary-light text-primary">{{ $subject->name }}</span>
                                                @endforeach
                                                @if($tutor->subjects->count() > 3)
                                                    <span class="badge bg-light text-dark">+{{ $tutor->subjects->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Experience & Rate --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        @if($tutor->experience_years)
                                            <small class="text-muted">
                                                <i class="bi bi-briefcase"></i> {{ $tutor->experience_years }} {{ __('ui.years_exp') }}
                                            </small>
                                        @endif
                                        @if($tutor->hourly_rate_min && $tutor->hourly_rate_max)
                                            <strong class="text-primary">{{ number_format($tutor->hourly_rate_min) }}-{{ number_format($tutor->hourly_rate_max) }}{{ __('ui.per_hour_vnd') }}</strong>
                                        @elseif($tutor->hourly_rate_min)
                                            <strong class="text-primary">{{ number_format($tutor->hourly_rate_min) }}{{ __('ui.per_hour_vnd') }}</strong>
                                        @endif
                                    </div>

                                    {{-- View Profile Button --}}
                                    {{-- Connection Status Buttons --}}
                                    @auth
                                        @if(auth()->user()->isStudent())
                                            @php
                                                $status = $matchingStatuses[$tutor->user->id] ?? null;
                                            @endphp

                                            @if(!$status)
                                                <form action="{{ route('matching.connect') }}" method="POST" class="mb-2">
                                                    @csrf
                                                    <input type="hidden" name="tutor_id" value="{{ $tutor->user->id }}">
                                                    <button type="submit" class="btn btn-primary w-100">
                                                        <i class="bi bi-person-plus"></i> Kết nối
                                                    </button>
                                                </form>
                                            @elseif($status == 'pending')
                                                <button class="btn btn-secondary w-100 mb-2" disabled>
                                                    <i class="bi bi-hourglass-split"></i> Đang chờ
                                                </button>
                                            @elseif($status == 'declined')
                                                <button class="btn btn-danger w-100 mb-2" disabled>
                                                    <i class="bi bi-x-circle"></i> Đã từ chối
                                                </button>
                                            @endif
                                        @endif
                                    @endauth

                                    <a href="{{ route('tutor.show', $tutor->user->id) }}" 
                                       class="btn btn-outline-primary w-100">
                                        <i class="bi bi-eye"></i> {{ __('ui.view') }} chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $tutors->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('ui.no_suitable_tutors') }}</h4>
                    <p class="text-muted">{{ __('ui.try_different_filters') }}</p>
                    <a href="{{ route('tutors.browse') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('ui.view_all_tutors') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
