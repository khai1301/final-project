@extends('frontend.layouts.bootstrap')

@section('title', 'Tìm Gia Sư')

@section('content')
<div class="container py-5">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-2">Tìm Gia Sư</h1>
            <p class="text-muted">Tìm kiếm gia sư phù hợp với nhu cầu học tập của bạn</p>
        </div>
    </div>

    <div class="row">
        {{-- Filter Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Bộ Lọc</h5>
                    
                    <form action="{{ route('tutors.browse') }}" method="GET" id="filterForm">
                        {{-- Search --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tìm kiếm theo tên</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Nhập tên gia sư..." 
                                   value="{{ request('search') }}">
                        </div>

                        {{-- Subject Filter --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Môn học</label>
                            <select name="subjects[]" class="form-select" multiple size="5">
                                @foreach($allSubjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                        {{ in_array($subject->id, request('subjects', [])) ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Giữ Ctrl để chọn nhiều</small>
                        </div>


                        {{-- Hourly Rate Range --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mức giá (VNĐ/giờ)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="rate_min" class="form-control form-control-sm" 
                                           placeholder="Từ" value="{{ request('rate_min') }}" min="0" step="10000">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="rate_max" class="form-control form-control-sm" 
                                           placeholder="Đến" value="{{ request('rate_max') }}" min="0" step="10000">
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('tutors.browse') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Đặt lại
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
                    Tìm thấy <strong>{{ $tutors->total() }}</strong> gia sư
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
                                            <small class="text-muted d-block mb-1">Môn học:</small>
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
                                                <i class="bi bi-briefcase"></i> {{ $tutor->experience_years }} năm KN
                                            </small>
                                        @endif
                                        @if($tutor->hourly_rate_min && $tutor->hourly_rate_max)
                                            <strong class="text-primary">{{ number_format($tutor->hourly_rate_min) }}-{{ number_format($tutor->hourly_rate_max) }}đ/h</strong>
                                        @elseif($tutor->hourly_rate_min)
                                            <strong class="text-primary">{{ number_format($tutor->hourly_rate_min) }}đ/h</strong>
                                        @endif
                                    </div>

                                    {{-- View Profile Button --}}
                                    <a href="{{ route('tutor.show', $tutor->user->id) }}" 
                                       class="btn btn-outline-primary w-100">
                                        <i class="bi bi-eye"></i> Xem chi tiết
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
                    <h4 class="text-muted">Không tìm thấy gia sư phù hợp</h4>
                    <p class="text-muted">Thử thay đổi bộ lọc để xem thêm kết quả</p>
                    <a href="{{ route('tutors.browse') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Xem tất cả gia sư
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
