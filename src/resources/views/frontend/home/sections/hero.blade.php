{{-- Guest Hero (Original Partial) --}}
@guest
    @include('frontend.partials.home.hero')
@endguest

{{-- Auth Hero (Full Screen + Configurable) --}}
@auth
    @php
        $role = auth()->user()->role;
        // Default fallbacks match the Seeder/Controller defaults
        $bgImage = \App\Models\Setting::get($role . '_hero_image', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
        $title = \App\Models\Setting::get($role . '_hero_title', 'Chào mừng trở lại, {{name}}! 👋');
        $subtitle = \App\Models\Setting::get($role . '_hero_subtitle', 'Quản lý việc học và giảng dạy hiệu quả.');
        
        // Replace placeholders
        $title = str_replace('{{name}}', auth()->user()->name, $title);
    @endphp

<section class="hero-section text-center position-relative d-flex align-items-center justify-content-center" 
    style="min-height: calc(100vh - 69px); background-image: url('{{ $bgImage }}'); background-size: cover; background-position: center; color: white;">
    
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.6;"></div> {{-- Overlay --}}
    
    <div class="container position-relative z-1">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown text-white">
                    {{ $title }}
                </h1>
                <p class="lead mb-5 animate__animated animate__fadeInUp animate__delay-1s text-white-50 fs-3">
                    {{ $subtitle }}
                </p>
                <div class="d-flex justify-content-center gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                    @if($role === 'student')
                        <a href="{{ route('tutors.browse') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg border-0 py-3">
                            <i class="bi bi-search me-2"></i>Tìm gia sư mới
                        </a>
                        <a href="{{ route('student.requests.index') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill shadow-lg backdrop-blur py-3">
                            <i class="bi bi-list-ul me-2"></i>Yêu cầu của tôi
                        </a>
                    @elseif($role === 'tutor')
                        <a href="{{ route('requests.browse') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg border-0 py-3">
                            <i class="bi bi-briefcase me-2"></i>Xem yêu cầu mới
                        </a>
                        <a href="{{ route('tutor.profile') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill shadow-lg backdrop-blur py-3">
                            <i class="bi bi-person-badge me-2"></i>Hồ sơ cá nhân
                        </a>
                    @else
                         <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg border-0 py-3">
                            <i class="bi bi-speedometer2 me-2"></i>Trang quản trị
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .backdrop-blur {
        backdrop-filter: blur(5px);
        background: rgba(255, 255, 255, 0.1);
    }
    .backdrop-blur:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    /* Ensure navbar is above hero if it's fixed */
    .navbar {
        z-index: 1000;
    }
</style>
@endauth
