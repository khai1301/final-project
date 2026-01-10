{{-- Hero Section --}}
<section class="home-hero d-flex align-items-center" style="min-height: calc(100vh - 69px);">
    {{-- Blur Effects --}}
    <div class="home-hero-blur-left"></div>
    <div class="home-hero-blur-right"></div>

    <div class="container home-hero-content">
        <div class="row g-4 align-items-center">
            {{-- Left Column: Text Content --}}
            <div class="col-lg-6">
                <div class="d-flex flex-column gap-4">
                    {{-- Badge --}}

                    {{-- Title --}}
                    <h1 class="home-hero-title">
                        {!! \App\Models\Setting::get('home_hero_title', 'Tìm gia sư phù hợp trong vài phút với <span class="home-hero-gradient-text">AI tự động ghép đôi</span>') !!}
                    </h1>

                    {{-- Description --}}
                    <p class="home-hero-description">
                        {!! \App\Models\Setting::get('home_hero_subtitle', 'Ngừng lướt qua hàng trăm hồ sơ. Để AI phân tích phong cách học tập, ngân sách và mục tiêu của bạn để kết nối với gia sư được xác minh hoàn hảo nhất.') !!}
                    </p>

                    {{-- CTA Buttons --}}
                    @guest
                    <div class="home-hero-buttons d-flex flex-wrap gap-3 pt-2">
                        <a href="{{ route('register') }}?role=student" class="btn btn-dark">
                            <span class="material-symbols-outlined">school</span>
                            Tôi là Học sinh
                        </a>
                        <a href="{{ route('register') }}?role=tutor" class="btn btn-outline-dark">
                            <span class="material-symbols-outlined">cast_for_education</span>
                            Tôi là Gia sư
                        </a>
                    </div>
                    @endguest
                </div>
            </div>

            {{-- Right Column: Hero Image with Search --}}
            <div class="col-lg-6">
                <div class="home-hero-image" style="background-image: url('{{ \App\Models\Setting::get('home_hero_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuB2yl0cS0XnP8jTK2WvMEa_rE5irPLNUEM9N-fifsx3wqc6ALMJOVtWaqcW9LI-sFZWF4B3v6Zwp3ckjHq5UIxwuz6Iw72BYFsDxxAEXFDn2Rs6Z1s6GQG3sLumwt4zkM0TTvqNgylRVeqx4ZUyXuysu8_6PK2YgGUrNmlTTYEcVQaxeCKaaR-nnAmJUBziAvCUz_uM9KBmHf48x1lnrh3OjnGs0Jjf1rIK2DW6E6ix1-xOz8cgRlmNlRYcQVVHn_jDFI77uPv2gM4') }}');">
                    <div class="home-hero-image-overlay"></div>
                </div>
            </div>
        </div>
    </div>
</section>
