@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="material-symbols-outlined me-2">error</i>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 overflow-hidden student-request-card">
                <!-- Header Section -->
                <div class="card-header bg-gradient-primary text-white p-4 border-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="material-symbols-outlined fs-1">auto_awesome</span>
                        <h1 class="h2 mb-0 fw-bold">New Learning Request</h1>
                    </div>
                    <p class="mb-0 text-white-50">
                        Tell us what you want to learn. Our AI engine will analyze your needs, schedule, and learning style to find the top 3 tutors for you instantly.
                    </p>
                </div>

                <!-- Form Body -->
                <form method="POST" action="{{ route('student.request.store') }}" class="student-request-form">
                    @csrf
                    
                    <!-- Section 1: The Basics -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">1</span>
                            The Basics
                        </h3>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Subject</label>
                                <select name="subject" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Select subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Education Level</label>
                                <select name="education_level" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Select level</option>
                                    @foreach($educationLevels as $level)
                                        <option value="{{ $level->name }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-medium mb-0">Specific Skills or Topics</label>
                                <span class="badge bg-primary-subtle text-primary">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">bolt</span>
                                    AI Matching Priority
                                </span>
                            </div>
                            <div class="skills-input-container">
                                <div id="skillsChips" class="skills-chips"></div>
                                <input type="text" id="skillsInput" class="skills-input" 
                                       placeholder="Type topics and press enter (e.g. Limits, Derivatives)">
                                <input type="hidden" name="skills" id="skillsHidden">
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 2: Logistics -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">2</span>
                            Logistics
                        </h3>
                        
                        <div class="row g-4 mb-4">
                            <!-- Learning Mode -->
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Learning Mode</label>
                                <div class="row g-3">
                                    @foreach($learningModes as $index => $modeItem)
                                    <div class="col-{{ count($learningModes) <= 2 ? '6' : '4' }}">
                                        <input type="radio" class="btn-check" name="mode" 
                                               id="mode{{ $modeItem->slug }}" 
                                               value="{{ strtolower($modeItem->name) }}" 
                                               {{ $index === 0 ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100 mode-card" for="mode{{ $modeItem->slug }}">
                                            @if($modeItem->icon)
                                                <i class="{{ $modeItem->icon }} fs-2 d-block mb-2"></i>
                                            @else
                                                <span class="material-symbols-outlined fs-2 d-block mb-2">
                                                    {{ $index === 0 ? 'laptop_chromebook' : 'person_pin_circle' }}
                                                </span>
                                            @endif
                                            <span class="d-block">{{ $modeItem->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Schedule -->
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Preferred Schedule</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-check schedule-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[]" value="weekdays_am" id="schedWeekdaysAM">
                                            <label class="form-check-label" for="schedWeekdaysAM">Weekdays (AM)</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check schedule-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[]" value="weekdays_pm" id="schedWeekdaysPM" checked>
                                            <label class="form-check-label" for="schedWeekdaysPM">Weekdays (PM)</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check schedule-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[]" value="weekends" id="schedWeekends" checked>
                                            <label class="form-check-label" for="schedWeekends">Weekends</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check schedule-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[]" value="flexible" id="schedFlexible">
                                            <label class="form-check-label" for="schedFlexible">Flexible</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Field (shown only for offline mode) -->
                        <div class="mb-3 d-none" id="addressField">
                            <label class="form-label fw-medium">
                                Learning Location Address
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <span class="material-symbols-outlined text-muted">location_on</span>
                                </span>
                                <input type="text" name="address" id="addressInput" class="form-control form-control-lg" 
                                       placeholder="Enter your preferred learning location">
                            </div>
                            <small class="text-muted">Provide the address where you'd like to have in-person sessions</small>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 3: Budget -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">3</span>
                            Budget
                        </h3>
                        
                        <div class="budget-container p-4 rounded-3 bg-light">
                            <label class="form-label fw-medium mb-3">Hourly Rate Range (VNĐ)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Minimum Rate</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="budget_min" 
                                               id="budgetMinInput" min="100000" max="5000000" value="500000" step="50000" required>
                                        <span class="input-group-text">VNĐ/hr</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Maximum Rate</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="budget_max" 
                                               id="budgetMaxInput" min="100000" max="5000000" value="1000000" step="50000" required>
                                        <span class="input-group-text">VNĐ/hr</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Notes -->
                    <div class="px-4 pb-4">
                        <label class="form-label fw-medium">Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="4" 
                                  placeholder="e.g. I'm preparing for the SATs next month and specifically need help with Geometry problems."></textarea>
                    </div>

                    <!-- Submit Area -->
                    <div class="card-footer bg-light p-4 border-0 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <span class="material-symbols-outlined">info</span>
                            <span>Requests are usually matched within 24 hours.</span>
                        </div>
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-outline-secondary px-4">Save Draft</button>
                            <button type="submit" class="btn btn-primary px-4">
                                Find My Match
                                <span class="material-symbols-outlined ms-1" style="font-size: 18px;">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div data-success-message="{{ session('success') }}" style="display: none;"></div>
@endif
@endsection
