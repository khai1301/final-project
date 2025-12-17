{{-- Hero Section --}}
<section class="home-hero">
    {{-- Blur Effects --}}
    <div class="home-hero-blur-left"></div>
    <div class="home-hero-blur-right"></div>

    <div class="container home-hero-content">
        <div class="row g-4 align-items-center">
            {{-- Left Column: Text Content --}}
            <div class="col-lg-6">
                <div class="d-flex flex-column gap-4">
                    {{-- Badge --}}
                    <div>
                        <span class="home-badge">
                            <span class="home-badge-pulse">
                                <span class="home-badge-pulse-dot"></span>
                                <span class="home-badge-pulse-core"></span>
                            </span>
                            New: AI Match V2.0 is live
                        </span>
                    </div>

                    {{-- Title --}}
                    <h1 class="home-hero-title">
                        Find the right tutor in minutes with <span class="home-hero-gradient-text">AI matching</span>
                    </h1>

                    {{-- Description --}}
                    <p class="home-hero-description">
                        Stop scrolling through hundreds of profiles. Let our AI analyze your learning style, budget, and goals to connect you with the perfect verified mentor.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="home-hero-buttons d-flex flex-wrap gap-3 pt-2">
                        <button class="btn btn-dark">
                            <span class="material-symbols-outlined">school</span>
                            I am a Student
                        </button>
                        <button class="btn btn-outline-dark">
                            <span class="material-symbols-outlined">cast_for_education</span>
                            I am a Tutor
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Column: Hero Image with Search --}}
            <div class="col-lg-6">
                <div class="home-hero-image" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuB2yl0cS0XnP8jTK2WvMEa_rE5irPLNUEM9N-fifsx3wqc6ALMJOVtWaqcW9LI-sFZWF4B3v6Zwp3ckjHq5UIxwuz6Iw72BYFsDxxAEXFDn2Rs6Z1s6GQG3sLumwt4zkM0TTvqNgylRVeqx4ZUyXuysu8_6PK2YgGUrNmlTTYEcVQaxeCKaaR-nnAmJUBziAvCUz_uM9KBmHf48x1lnrh3OjnGs0Jjf1rIK2DW6E6ix1-xOz8cgRlmNlRYcQVVHn_jDFI77uPv2gM4');">
                    <div class="home-hero-image-overlay"></div>
                    
                    {{-- Search Card --}}
                    <div class="home-search-card">
                        <h3 class="home-search-title">Start Learning</h3>
                        <form action="#" method="GET">
                            <div class="row g-3">
                                {{-- Subject Input --}}
                                <div class="col-md-4 col-12">
                                    <div class="home-search-input-wrapper">
                                        <span class="material-symbols-outlined">search</span>
                                        <input type="text" name="subject" class="home-search-input" placeholder="Subject..." />
                                    </div>
                                </div>

                                {{-- Location Input --}}
                                <div class="col-md-3 col-6">
                                    <div class="home-search-input-wrapper">
                                        <span class="material-symbols-outlined">location_on</span>
                                        <input type="text" name="location" class="home-search-input" placeholder="Zip/Online" />
                                    </div>
                                </div>

                                {{-- Budget Select --}}
                                <div class="col-md-3 col-6">
                                    <div class="home-search-select-wrapper">
                                        <span class="material-symbols-outlined">attach_money</span>
                                        <select name="budget" class="home-search-select">
                                            <option value="" disabled selected>Budget</option>
                                            <option value="1">Under $20/hr</option>
                                            <option value="2">$20 - $50/hr</option>
                                            <option value="3">$50+/hr</option>
                                        </select>
                                        <span class="material-symbols-outlined dropdown-icon">expand_more</span>
                                    </div>
                                </div>

                                {{-- Search Button --}}
                                <div class="col-md-2 col-12">
                                    <button type="submit" class="home-search-btn w-100">
                                        <span class="material-symbols-outlined">arrow_forward</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
