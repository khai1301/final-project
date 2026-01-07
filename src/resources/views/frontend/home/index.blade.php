@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container-fluid py-4">
    {{-- CCCD Verification Banner for Unverified Users --}}
    @auth
        @if(!auth()->user()->is_verified)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <span class="material-symbols-outlined me-3" style="font-size: 32px;">verified_user</span>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">Xác thực CCCD để sử dụng đầy đủ tính năng</h5>
                    <p class="mb-2">Bạn cần xác thực CCCD để tạo yêu cầu học tập và kết nối với gia sư/học sinh.</p>
                    <a href="{{ route('id-verification.show') }}" class="btn btn-sm btn-warning">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">upload</span>
                        Xác thực ngay
                    </a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
    @endauth

    {{-- Landing Page Sections - Shown to ALL users (guest + logged in) --}}
    @include('frontend.partials.home.hero')
    @include('frontend.partials.home.stats')
    @include('frontend.partials.home.features')
    @include('frontend.partials.home.top-tutors')
    @include('frontend.partials.home.how-it-works')
    @include('frontend.partials.home.tutor-requests')
    @include('frontend.partials.home.testimonials')
    <!-- @include('frontend.partials.home.cta-section') -->
</div>

<style>
.tutor-card, .student-card {
    transition: transform 0.2s;
}
.tutor-card:hover, .student-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
