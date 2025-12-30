{{-- Testimonials Section --}}
<section class="home-testimonials">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mx-auto mb-5" style="max-width: 48rem;">
            <h2 class="home-section-title">{{ __('ui.testimonials_title') }}</h2>
            <p class="home-section-description">{{ __('ui.why_users_trust') }}</p>
        </div>

        {{-- Testimonial Cards Grid --}}
        <div class="row g-4">
            {{-- Testimonial 1: Student --}}
            <div class="col-md-4">
                <div class="home-testimonial-card">
                    <span class="home-testimonial-quote material-symbols-outlined">format_quote</span>
                    <div class="home-testimonial-header">
                        <div class="home-testimonial-avatar blue">AS</div>
                        <div>
                            <h4 class="home-testimonial-name">Alex S.</h4>
                            <p class="home-testimonial-role">Student</p>
                        </div>
                    </div>
                    <p class="home-testimonial-text">
                        "I found a calculus tutor in less than 5 minutes. The AI matching was surprisingly accurate to my learning style."
                    </p>
                    <div class="home-testimonial-stars">
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                    </div>
                </div>
            </div>

            {{-- Testimonial 2: Tutor --}}
            <div class="col-md-4">
                <div class="home-testimonial-card">
                    <span class="home-testimonial-quote material-symbols-outlined">format_quote</span>
                    <div class="home-testimonial-header">
                        <div class="home-testimonial-avatar purple">MJ</div>
                        <div>
                            <h4 class="home-testimonial-name">Maria J.</h4>
                            <p class="home-testimonial-role">Tutor</p>
                        </div>
                    </div>
                    <p class="home-testimonial-text">
                        "SmartTutor helps me fill my schedule with motivated students without any marketing effort. The dashboard is super intuitive."
                    </p>
                    <div class="home-testimonial-stars">
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                    </div>
                </div>
            </div>

            {{-- Testimonial 3: Parent --}}
            <div class="col-md-4">
                <div class="home-testimonial-card">
                    <span class="home-testimonial-quote material-symbols-outlined">format_quote</span>
                    <div class="home-testimonial-header">
                        <div class="home-testimonial-avatar green">RL</div>
                        <div>
                            <h4 class="home-testimonial-name">Robert L.</h4>
                            <p class="home-testimonial-role">Parent</p>
                        </div>
                    </div>
                    <p class="home-testimonial-text">
                        "Safety was my main concern. Knowing all tutors are verified gives me peace of mind when my daughter has her lessons."
                    </p>
                    <div class="home-testimonial-stars">
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                        <span class="material-symbols-outlined">star</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
