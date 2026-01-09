@extends('admin.layouts.app')

@section('title', 'Chi tiết hồ sơ gia sư')
@section('subtitle', 'Xem thông tin hồ sơ gia sư')

@section('content')
<div class="container-fluid py-4">
    {{-- Success Message --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ route('admin.tutor-profiles.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <h4 class="mb-1">Hồ sơ: {{ $profile->user->name }}</h4>
                                <small class="text-muted">ID hồ sơ #{{ $profile->id }}</small>
                            </div>
                            <x-verified-badge :user="$profile->user" />
                        </div>
                        @if($profile->is_approved)
                            <span class="badge bg-success">Đã duyệt</span>
                        @else
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    {{-- Subjects --}}
                    @if($profile->subjects && count($profile->subjects) > 0)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Môn học</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($profile->subjects as $subject)
                                <span class="badge bg-primary-light text-primary px-3 py-2">{{ $subject }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Education Background --}}
                    @if($profile->education)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Học vấn</h5>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0 text-break" style="white-space: pre-line;">{{ $profile->education }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Bio --}}
                    @if($profile->bio)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Giới thiệu</h5>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0 text-break">{{ $profile->bio }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Experience & Rates --}}
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Kinh nghiệm & Học phí</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="fw-bold text-muted small">Kinh nghiệm</label>
                                <div class="fw-medium">{{ $profile->experience_years ?? 0 }} năm</div>
                            </div>
                            <div class="col-md-8">
                                <label class="fw-bold text-muted small">Học phí theo giờ</label>
                                <div class="fw-medium">
                                    @if($profile->hourly_rate_min && $profile->hourly_rate_max)
                                        {{ number_format($profile->hourly_rate_min, 0) }} - {{ number_format($profile->hourly_rate_max, 0) }} VNĐ/giờ
                                    @else
                                        <span class="text-muted">Chưa thiết lập</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Teaching Areas --}}
                    @if($profile->teachingAreas && $profile->teachingAreas->count() > 0)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Khu vực dạy ({{ $profile->teachingAreas->count() }})</h5>
                        @foreach($profile->teachingAreas->groupBy('province_id') as $provinceId => $areas)
                            <div class="mb-3">
                                <h6 class="text-primary small fw-semibold mb-2">
                                    <i class="bi bi-map me-1"></i>
                                    {{ $areas->first()->province->name ?? 'Chưa rõ tỉnh/thành' }}
                                </h6>
                                <div class="d-flex flex-wrap gap-2 ms-3">
                                    @foreach($areas as $area)
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            @if($area->ward)
                                                {{ $area->ward->name }}
                                            @else
                                                <em>Toàn tỉnh/thành</em>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Khu vực dạy</h5>
                        <p class="text-muted"><i class="bi bi-info-circle me-1"></i>Chưa cập nhật khu vực dạy</p>
                    </div>
                    @endif

                    {{-- Availability Time Slots --}}
                    @if($profile->availableTimeSlots && $profile->availableTimeSlots->count() > 0)
                    <div class=\"mb-4\">
                        <h5 class=\"text-muted text-uppercase small fw-bold mb-3\">Available Time Slots ({{ $profile->availableTimeSlots->count() }})</h5>
                        @foreach($profile->availableTimeSlots->groupBy('day_of_week') as $dayNum => $slots)
                            <div class=\"mb-3\">
                                <h6 class=\"text-primary small fw-bold\">
                                    <i class=\"bi bi-calendar-day me-1\"></i>
                                    {{ $slots->first()->getDayName() }}
                                </h6>
                                <div class=\"d-flex flex-wrap gap-2\">
                                    @foreach($slots as $slot)
                                        <span class=\"badge bg-light text-dark border px-3 py-2\">
                                            <i class=\"bi bi-clock me-1\"></i>
                                            {{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Certificates --}}
                    @if($profile->certificates && count($profile->certificates) > 0)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Chứng chỉ ({{ count($profile->certificates) }})</h5>
                        <div class="row g-3">
                            @foreach($profile->certificates as $cert)
                            <div class="col-md-6">
                                <div class="border rounded p-2 d-flex align-items-center">
                                    @if(str_contains($cert->file_type, 'image'))
                                        <span class="material-symbols-outlined text-primary fs-2 me-2">image</span>
                                    @elseif(str_contains($cert->file_type, 'pdf'))
                                        <span class="material-symbols-outlined text-danger fs-2 me-2">picture_as_pdf</span>
                                    @else
                                        <span class="material-symbols-outlined text-info fs-2 me-2">description</span>
                                    @endif
                                    
                                    <div class="flex-grow-1 small">
                                        <div class="fw-medium">{{ $cert->name }}</div>
                                        <div class="text-muted small">{{ number_format($cert->file_size / 1024, 2) }} KB</div>
                                    </div>
                                    <a href="{{ $cert->file_url }}" target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- CV --}}
                    @if($profile->cv_path)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">CV / Hồ sơ xin việc</h5>
                        <div class="border rounded p-3 d-flex align-items-center">
                            <i class="bi bi-file-earmark-text fs-2 text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Sơ yếu lý lịch</div>
                                <div class="text-muted small">{{ basename($profile->cv_path) }}</div>
                            </div>
                            <a href="{{ \Storage::disk('s3')->url($profile->cv_path) }}" target="_blank" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Tải xuống
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Metadata --}}
                    <div class="border-top pt-3 mt-4">
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <i class="bi bi-calendar me-1"></i>
                                Ngày tạo: {{ $profile->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-6">
                                <i class="bi bi-clock-history me-1"></i>
                                Cập nhật: {{ $profile->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Tutor Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Thông tin gia sư</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($profile->user->avatar)
                            <img src="{{ \Storage::disk('s3')->url($profile->user->avatar) }}" 
                                 alt="Avatar" class="rounded-circle mb-2" width="80" height="80">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($profile->user->name) }}&size=80&background=3780f6&color=fff" 
                                 alt="Avatar" class="rounded-circle mb-2" width="80" height="80">
                        @endif
                        <h5 class="fw-bold mb-1">{{ $profile->user->name }}</h5>
                        <span class="badge bg-success-light text-success">Gia sư</span>
                    </div>
                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <i class="bi bi-envelope text-muted me-2"></i>
                            <small>{{ $profile->user->email }}</small>
                        </div>
                        @if($profile->user->phone)
                        <div class="mb-2">
                            <i class="bi bi-phone text-muted me-2"></i>
                            <small>{{ $profile->user->phone }}</small>
                        </div>
                        @endif
                        <div>
                            <i class="bi bi-calendar text-muted me-2"></i>
                            <small>Tham gia {{ $profile->user->created_at->format('m/Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    @if(!$profile->is_approved)
                    <form action="{{ route('admin.tutor-profiles.approve', $profile->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i>
                            Duyệt hồ sơ
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.tutor-profiles.unapprove', $profile->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-x-circle me-1"></i>
                            Hủy duyệt hồ sơ
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('admin.tutor-profiles.destroy', $profile->id) }}" 
                          method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-1"></i>
                            Xóa hồ sơ
                        </button>
                    </form>

                    <div class="mt-3 p-2 bg-light rounded small text-muted">
                        <strong>Lưu ý:</strong> Xóa hồ sơ này sẽ xóa vĩnh viễn tất cả chứng chỉ và tệp tin liên quan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
