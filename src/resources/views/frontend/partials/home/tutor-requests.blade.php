{{-- Tutor Requests Section --}}
<section class="home-requests">
    <div class="container">
        {{-- Section Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4 gap-3">
            <div>
                <h2 class="home-section-title mb-2">Open Tutor Requests</h2>
                <p class="home-section-description mb-0">See what students are looking for right now</p>
            </div>
            <div>
                <button class="btn btn-outline-secondary">View All Requests</button>
            </div>
        </div>

        {{-- Request Cards Grid --}}
        <div class="row g-4">
            {{-- Request 1: Math --}}
            <div class="col-md-6 col-lg-4">
                <div class="home-request-card">
                    <div class="home-request-header">
                        <span class="home-request-badge math">Math</span>
                        <span class="home-request-time">
                            <span class="material-symbols-outlined">schedule</span> 2m ago
                        </span>
                    </div>
                    <div>
                        <h3 class="home-request-title">Help with Calculus II Integration</h3>
                        <p class="home-request-description">
                            I need help understanding integration by parts and trigonometric substitution for my upcoming midterm.
                        </p>
                    </div>
                    <div class="home-request-footer">
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">payments</span> $30-40/hr
                        </div>
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">videocam</span> Online
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request 2: Language --}}
            <div class="col-md-6 col-lg-4">
                <div class="home-request-card">
                    <div class="home-request-header">
                        <span class="home-request-badge language">Language</span>
                        <span class="home-request-time">
                            <span class="material-symbols-outlined">schedule</span> 15m ago
                        </span>
                    </div>
                    <div>
                        <h3 class="home-request-title">Conversational Spanish Tutor</h3>
                        <p class="home-request-description">
                            Looking for a native speaker to practice conversational Spanish twice a week. Intermediate level.
                        </p>
                    </div>
                    <div class="home-request-footer">
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">payments</span> $25/hr
                        </div>
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">wifi</span> Remote
                        </div>
                    </div>
                </div>
            </div>

            {{-- Request 3: Science --}}
            <div class="col-md-6 col-lg-4">
                <div class="home-request-card">
                    <div class="home-request-header">
                        <span class="home-request-badge science">Science</span>
                        <span class="home-request-time">
                            <span class="material-symbols-outlined">schedule</span> 1h ago
                        </span>
                    </div>
                    <div>
                        <h3 class="home-request-title">High School Chemistry Help</h3>
                        <p class="home-request-description">
                            My son needs help with stoichiometry and balancing equations. Looking for a patient tutor.
                        </p>
                    </div>
                    <div class="home-request-footer">
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">payments</span> $35-50/hr
                        </div>
                        <div class="home-request-info">
                            <span class="material-symbols-outlined">location_on</span> Austin, TX
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
