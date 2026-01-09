@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-5">
    {{-- Profile Header Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                    @php
                        $avatarUrl = $tutor->avatar ? \Storage::disk('s3')->url($tutor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($tutor->name) . '&size=200&background=3780f6&color=fff';
                    @endphp
                    <img src="{{ $avatarUrl }}" alt="{{ $tutor->name }}" 
                         class="rounded-circle border avatar-img" 
                         width="120" height="120" loading="lazy">
                </div>
                <div class="col-md">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h1 class="h3 mb-0">{{ $tutor->name }}</h1>
                        @if($tutor->is_verified)
                            <x-verified-badge :user="$tutor" />
                        @endif
                    </div>
                    
                    <div class="text-muted mb-3">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">work</span>
                        {{ $tutor->tutorProfile->experience_years ?? 0 }} năm kinh nghiệm
                        @if($tutor->province)
                        <span class="ms-3">
                            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">location_on</span>
                            {{ $tutor->province->name }}
                        </span>
                        @endif
                    </div>
                    
                    @if($tutor->tutorProfile->subjects && $tutor->tutorProfile->subjects->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($tutor->tutorProfile->subjects as $subject)
                        <span class="badge bg-light text-dark border">{{ $subject->name }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                {{-- Connect Button in Header --}}
                <div class="col-md-auto text-center text-md-end mt-3 mt-md-0">
                    @auth
                        @if(auth()->user()->isStudent())
                            @php
                                $existingMatching = \App\Models\Matching::where('student_id', auth()->id())
                                    ->where('tutor_id', $tutor->id)
                                    ->whereIn('status', ['pending', 'accepted'])
                                    ->first();
                            @endphp

                            @if($existingMatching)
                                @if($existingMatching->status === 'accepted')
                                    <button class="btn btn-success px-4" disabled>
                                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">check_circle</span>
                                        Đã kết nối
                                    </button>
                                @else
                                    <button class="btn btn-secondary px-4" disabled>
                                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">hourglass_empty</span>
                                        Đã gửi yêu cầu
                                    </button>
                                @endif
                            @else
                                <form action="{{ route('matching.connect') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">person_add</span>
                                        Kết nối
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary px-4">
                            <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">login</span>
                            Đăng nhập để kết nối
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- About Section --}}
            @if($tutor->tutorProfile->bio)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">person</span>
                        Giới thiệu
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $tutor->tutorProfile->bio }}</p>
                </div>
            </div>
            @endif

            {{-- Education --}}
            @if($tutor->tutorProfile->education)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">school</span>
                        Học vấn
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $tutor->tutorProfile->education }}</p>
                </div>
            </div>
            @endif

            {{-- Certificates --}}
            @if($tutor->tutorProfile->certificates && $tutor->tutorProfile->certificates->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">workspace_premium</span>
                        Chứng chỉ
                    </h5>
                    <span class="badge bg-primary">{{ $tutor->tutorProfile->certificates->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        @foreach($tutor->tutorProfile->certificates as $cert)
                        <div class="border rounded p-3 d-flex align-items-center">
                            @if(str_contains($cert->file_type, 'image'))
                                <span class="material-symbols-outlined text-primary fs-2 me-3" style="font-size: 32px;">image</span>
                            @else
                                <span class="material-symbols-outlined text-danger fs-2 me-3" style="font-size: 32px;">picture_as_pdf</span>
                            @endif
                            
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $cert->name }}</div>
                                <div class="text-muted small">
                                    @if(str_contains($cert->file_type, 'image'))
                                        Hình ảnh
                                    @else
                                        Tài liệu PDF
                                    @endif
                                </div>
                            </div>
                            
                            <a href="{{ \Storage::disk('s3')->url($cert->file_path) }}" target="_blank" 
                               class="btn btn-outline-primary">
                                <span class="material-symbols-outlined align-middle me-1">visibility</span>
                                Xem
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- CV --}}
            @if($tutor->tutorProfile->cv_path)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">description</span>
                        CV / Hồ sơ xin việc
                    </h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3 d-flex align-items-center">
                        <span class="material-symbols-outlined text-primary fs-2 me-3" style="font-size: 32px;">article</span>
                        <div class="flex-grow-1">
                            <div class="fw-medium">Sơ yếu lý lịch</div>
                            <div class="text-muted small">Tải xuống để xem chi tiết</div>
                        </div>
                        <a href="{{ \Storage::disk('s3')->url($tutor->tutorProfile->cv_path) }}" target="_blank" 
                           class="btn btn-outline-primary">
                            <span class="material-symbols-outlined align-middle me-1">download</span>
                            Xem CV
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Pricing Card --}}
            @if($tutor->tutorProfile->hourly_rate_min && $tutor->tutorProfile->hourly_rate_max)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">payments</span>
                        Học phí
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    <div class="display-6 fw-bold text-primary mb-1">
                        {{ number_format($tutor->tutorProfile->hourly_rate_min / 1000) }}k - {{ number_format($tutor->tutorProfile->hourly_rate_max / 1000) }}k
                    </div>
                    <div class="text-muted">VNĐ / giờ</div>
                </div>
            </div>
            @endif

            {{-- Contact Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">contact_mail</span>
                        Liên hệ
                    </h5>
                </div>
                <div class="card-body">
                    @php
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
                        @if($tutor->email)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-primary">email</span>
                            <a href="mailto:{{ $tutor->email }}" class="text-decoration-none">{{ $tutor->email }}</a>
                        </div>
                        @endif
                        @if($tutor->phone)
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-primary">phone</span>
                            <a href="tel:{{ $tutor->phone }}" class="text-decoration-none">{{ $tutor->phone }}</a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <span class="material-symbols-outlined text-muted mb-3" style="font-size: 48px;">lock</span>
                            <p class="text-muted mb-2"><small>Thông tin liên hệ đang bị khóa</small></p>
                            <p class="text-muted mb-0"><small>Kết nối và mở khóa để xem</small></p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Teaching Areas --}}
            @if($tutor->tutorProfile->teaching_areas && count($tutor->tutorProfile->teaching_areas) > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <span class="material-symbols-outlined align-middle me-2" style="font-size: 20px;">location_on</span>
                        Khu vực dạy
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($tutor->tutorProfile->teaching_areas as $area)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-success" style="font-size: 16px;">check_circle</span>
                        <span>{{ $area }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>



<style>
/* Simple hover effect for certificate items */
.cert-item {
    transition: all 0.2s ease;
    background: #fff;
}

.cert-item:hover {
    background: #f8f9fa;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

/* Tooltip styling */
[data-bs-toggle="tooltip"] {
    cursor: help;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
