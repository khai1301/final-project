{{-- How It Works Section --}}
<section class="home-how-it-works">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mx-auto mb-5" style="max-width: 48rem;">
            <h2 class="home-section-title">{{ __('ui.how_it_works') }}</h2>
            <p class="home-section-description">{{ __('ui.three_simple_steps') }}</p>
        </div>

        {{-- Process Steps --}}
        <div class="position-relative">
            {{-- Connecting Line (Desktop Only) --}}
            <div class="home-process-line d-none d-md-block"></div>

            <div class="row g-5">
                {{-- Step 1: Request a Tutor --}}
                <div class="col-md-4">
                    <div class="home-process-step">
                        <div class="home-process-icon-wrapper blue">
                            <span class="material-symbols-outlined">edit_note</span>
                        </div>
                        <h3 class="home-process-title">{{ __('ui.step') }} 1. {{ __('ui.step_1_title') }}</h3>
                        <p class="home-process-description">
                            {{ __('ui.step_1_desc') }}
                        </p>
                    </div>
                </div>

                {{-- Step 2: Get Matched --}}
                <div class="col-md-4">
                    <div class="home-process-step">
                        <div class="home-process-icon-wrapper purple">
                            <span class="material-symbols-outlined">auto_awesome</span>
                        </div>
                        <h3 class="home-process-title">{{ __('ui.step') }} 2. {{ __('ui.step_2_title') }}</h3>
                        <p class="home-process-description">
                            {{ __('ui.step_2_desc') }}
                        </p>
                    </div>
                </div>

                {{-- Step 3: Start Learning --}}
                <div class="col-md-4">
                    <div class="home-process-step">
                        <div class="home-process-icon-wrapper green">
                            <span class="material-symbols-outlined">video_chat</span>
                        </div>
                        <h3 class="home-process-title">{{ __('ui.step') }} 3. {{ __('ui.step_3_title') }}</h3>
                        <p class="home-process-description">
                            {{ __('ui.step_3_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
