{{-- CTA Section --}}
<section class="home-cta">
    {{-- Background Effects --}}
    <div class="home-cta-pattern"></div>
    <div class="home-cta-blur-right"></div>
    <div class="home-cta-blur-left"></div>

    <div class="container home-cta-content">
        <h2 class="home-cta-title">Bắt đầu ngay</h2>
        <p class="home-cta-description">
            {{ __('ui.cta_description') }}
        </p>
        <div class="home-cta-buttons">
            <a href="{{ route('register') }}?role=student" class="btn home-cta-btn home-cta-btn-primary">
                <span class="material-symbols-outlined align-middle me-2">search</span>
                Tìm kiếm gia sư
            </a>
            <a href="{{ route('register') }}?role=tutor" class="btn home-cta-btn home-cta-btn-secondary">
                <span class="material-symbols-outlined align-middle me-2">person_add</span>
                Trở thành gia sư
            </a>
        </div>
        <p class="home-cta-note">{{ __('ui.no_credit_card') }}</p>
    </div>
</section>
