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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">{{ __('ui.filter_section') }}</h5>
                    
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
                                <i class="bi bi-search"></i> {{ __('forms.search') }}
                            </button>
                            <a href="{{ route('tutors.browse') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> {{ __('ui.reset') }}
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

            @if($tutors->count() > 0)
                <div class="row g-4">
                    @foreach($tutors as $tutor)
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow" style="transition: all 0.3s;">
                                <div class="card-body">
                                    {{-- Avatar & Name --}}
                                    <div class="text-center mb-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($tutor->user->name) }}&background=random&color=fff" 
                                             class="rounded-circle mb-2" 
                                             width="80" height="80" 
                                             alt="{{ $tutor->user->name }}">
                                        <h5 class="fw-bold mb-1">{{ $tutor->user->name }}</h5>
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
