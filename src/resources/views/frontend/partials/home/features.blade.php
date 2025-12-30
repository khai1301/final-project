{{-- Features Section --}}
<section class="home-features">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mx-auto mb-5" style="max-width: 48rem;">
            <span class="home-section-badge">{{ __('ui.smartmatch_tech') }}</span>
            <h2 class="home-section-title">{{ __('ui.we_search_for_you') }}</h2>
            <p class="home-section-description">
                {{ __('ui.ai_analyzes_needs') }}
            </p>
        </div>

        {{-- Feature Cards --}}
        <div class="row g-4">
            {{-- Feature 1: Analyze Needs --}}
            <div class="col-md-4">
                <div class="home-feature-card">
                    <div class="home-feature-icon blue">
                        <span class="material-symbols-outlined">psychology</span>
                    </div>
                    <h3 class="home-feature-title">{{ __('ui.analyze_needs') }}</h3>
                    <p class="home-feature-description">
                        {{ __('ui.analyze_needs_desc') }}
                    </p>
                </div>
            </div>

            {{-- Feature 2: Instant Match --}}
            <div class="col-md-4">
                <div class="home-feature-card">
                    <div class="home-feature-icon purple">
                        <span class="material-symbols-outlined">hub</span>
                    </div>
                    <h3 class="home-feature-title">{{ __('ui.instant_match') }}</h3>
                    <p class="home-feature-description">
                        {{ __('ui.instant_match_desc') }}
                    </p>
                </div>
            </div>

            {{-- Feature 3: Save Time & Cost --}}
            <div class="col-md-4">
                <div class="home-feature-card">
                    <div class="home-feature-icon green">
                        <span class="material-symbols-outlined">savings</span>
                    </div>
                    <h3 class="home-feature-title">{{ __('ui.save_time_cost') }}</h3>
                    <p class="home-feature-description">
                        {{ __('ui.save_time_cost_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
