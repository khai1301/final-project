@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle">filter_list</span>
                        {{ __('ui.filters') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('requests.browse') }}" method="GET">
                        {{-- Search --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tìm kiếm</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Tiêu đề, mô tả...">
                        </div>

                        {{-- Subject --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Môn học</label>
                            <select class="form-select" name="subject_id">
                                <option value="">Tất cả</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Education Level --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Cấp độ</label>
                            <select class="form-select" name="education_level_id">
                                <option value="">Tất cả</option>
                                @foreach($educationLevels as $level)
                                <option value="{{ $level->id }}" {{ request('education_level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Learning Mode --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Hình thức</label>
                            <select class="form-select" name="learning_mode_id">
                                <option value="">Tất cả</option>
                                @foreach($learningModes as $mode)
                                <option value="{{ $mode->id }}" {{ request('learning_mode_id') == $mode->id ? 'selected' : '' }}>
                                    {{ $mode->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Province --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Khu vực</label>
                            <select class="form-select" name="province_id">
                                <option value="">Tất cả</option>
                                @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ request('province_id') == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <span class="material-symbols-outlined" style="font-size: 18px;">search</span>
                                {{ __('ui.apply') }}
                            </button>
                            <a href="{{ route('requests.browse') }}" class="btn btn-outline-secondary btn-sm">
                                {{ __('ui.clear_filters') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">{{ __('ui.browse_requests') }}</h3>
                <span class="badge bg-light text-dark">{{ $requests->total() }} {{ __('ui.results') }}</span>
            </div>

            @if($requests->isEmpty())
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <span class="material-symbols-outlined d-block mb-3 text-muted" style="font-size: 64px;">search_off</span>
                        <h5>{{ __('ui.no_requests_found') }}</h5>
                        <p class="text-muted">Thử thay đổi bộ lọc hoặc quay lại sau</p>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($requests as $req)
                    <div class="col-12">
                        <div class="card shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="card-title">{{ $req->title }}</h5>
                                        
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if($req->subject)
                                            <span class="badge bg-primary">{{ $req->subject->name }}</span>
                                            @endif
                                            @if($req->educationLevel)
                                            <span class="badge bg-info">{{ $req->educationLevel->name }}</span>
                                            @endif
                                            @if($req->learningMode)
                                            <span class="badge bg-success">{{ $req->learningMode->name }}</span>
                                            @endif
                                        </div>

                                        <p class="text-muted mb-3">
                                            {{ Str::limit($req->description, 150) }}
                                        </p>

                                        <div class="d-flex flex-wrap gap-3 small text-muted">
                                            @if($req->province)
                                            <div>
                                                <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                                {{ $req->ward?->name }}{{ $req->ward ? ', ' : '' }}{{ $req->province->name }}
                                            </div>
                                            @endif
                                            <div>
                                                <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                                                {{ $req->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 d-flex flex-column justify-content-between align-items-end">
                                        <div class="text-end mb-3">
                                            <div class="h4 text-primary mb-0">
                                                {{ number_format($req->budget_min / 1000) }}k - {{ number_format($req->budget_max / 1000) }}k ₫
                                            </div>
                                            <small class="text-muted">/ giờ</small>
                                        </div>

                                        @auth
                                            @if(auth()->user()->isTutor())
                                                @if($req->connection_status === 'pending')
                                                    <button class="btn btn-secondary" disabled>
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">pending</span>
                                                        Đang chờ
                                                    </button>
                                                @elseif($req->connection_status === 'accepted')
                                                    <button class="btn btn-success" disabled>
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                                                        Đã kết nối
                                                    </button>
                                                @else
                                                    <form action="{{ route('matching.connect') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="request_id" value="{{ $req->id }}">
                                                        <button type="submit" class="btn btn-primary">
                                                            <span class="material-symbols-outlined" style="font-size: 18px;">send</span>
                                                            {{ __('ui.send_request') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                                Đăng nhập để kết nối
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endpush
@endsection
