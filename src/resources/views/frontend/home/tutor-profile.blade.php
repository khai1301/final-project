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

            <div class="card shadow-sm border-0 overflow-hidden tutor-profile-card">
                <!-- Header Section -->
                <div class="card-header bg-gradient-tutor text-white p-4 border-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="material-symbols-outlined fs-1">person_edit</span>
                        <h1 class="h2 mb-0 fw-bold">Update Profile</h1>
                    </div>
                    <p class="mb-0 text-white-50">
                        Complete your tutor profile to start receiving student requests. A complete profile helps students find you.
                    </p>
                </div>

                <!-- Form Body -->
                <form method="POST" action="#" class="tutor-profile-form" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Section 1: Basic Information -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">1</span>
                            Basic Information
                        </h3>
                        
                        <!-- Profile Photo & Basic Info -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-4 text-center">
                                <div class="profile-photo-container">
                                    <img src="https://ui-avatars.com/api/?name=Tutor+Name&size=150&background=0d6efd&color=fff" 
                                         alt="Profile Photo" class="profile-photo mb-3" id="profilePhotoPreview">
                                    <input type="file" class="d-none" id="profilePhotoInput" name="avatar" accept="image/*">
                                    <label for="profilePhotoInput" class="btn btn-outline-primary btn-sm">
                                        <span class="material-symbols-outlined me-1" style="font-size: 16px;">upload</span>
                                        Change Photo
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Full Name</label>
                                        <input type="text" class="form-control form-control-lg" name="name" 
                                               value="Nguyễn Văn A" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Email</label>
                                        <input type="email" class="form-control form-control-lg" name="email" 
                                               value="tutor@example.com" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Phone Number</label>
                                        <input type="tel" class="form-control form-control-lg" name="phone" 
                                               placeholder="0123 456 789" value="0912 345 678">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Years of Experience</label>
                                        <select class="form-select form-select-lg" name="experience_years" required>
                                            <option value="">Select experience</option>
                                            <option value="1">Less than 1 year</option>
                                            <option value="2" selected>1-2 years</option>
                                            <option value="3">3-5 years</option>
                                            <option value="5">5-10 years</option>
                                            <option value="10">10+ years</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 2: CV & Documents -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">2</span>
                            CV & Documents
                        </h3>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Upload CV (PDF, DOC)</label>
                                <div class="upload-area" id="cvUploadArea">
                                    <input type="file" class="d-none" id="cvInput" name="cv" accept=".pdf,.doc,.docx">
                                    <label for="cvInput" class="upload-label">
                                        <span class="material-symbols-outlined fs-1 text-muted mb-2">description</span>
                                        <span class="text-muted">Drag & drop or click to upload</span>
                                        <span class="text-muted small">PDF, DOC up to 10MB</span>
                                    </label>
                                </div>
                                <div class="uploaded-file d-none mt-2" id="cvPreview">
                                    <span class="material-symbols-outlined me-2">description</span>
                                    <span class="file-name">CV_NguyenVanA.pdf</span>
                                    <button type="button" class="btn btn-sm btn-link text-danger ms-auto">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Identity Document (Optional)</label>
                                <div class="upload-area" id="idUploadArea">
                                    <input type="file" class="d-none" id="idInput" name="identity_doc" accept="image/*,.pdf">
                                    <label for="idInput" class="upload-label">
                                        <span class="material-symbols-outlined fs-1 text-muted mb-2">badge</span>
                                        <span class="text-muted">Upload ID/Passport</span>
                                        <span class="text-muted small">For verification purposes</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 3: Teaching Information -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">3</span>
                            Teaching Information
                        </h3>
                        
                        <!-- Subjects -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-medium mb-0">Subjects You Teach</label>
                                <span class="badge bg-primary-subtle text-primary">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                    Featured on Profile
                                </span>
                            </div>
                            <div class="skills-input-container">
                                <div id="subjectsChips" class="skills-chips">
                                    <span class="skill-chip">
                                        Mathematics
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                    <span class="skill-chip">
                                        Physics
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                </div>
                                <input type="text" id="subjectsInput" class="skills-input" 
                                       placeholder="Type subject and press enter">
                                <input type="hidden" name="subjects" id="subjectsHidden">
                            </div>
                        </div>

                        <!-- Education -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Education Background</label>
                            <textarea class="form-control" name="education" rows="3" 
                                      placeholder="Describe your educational qualifications, degrees, institutions...">Bachelor's Degree in Mathematics - Vietnam National University
Teaching Certificate - Ministry of Education</textarea>
                        </div>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">About Me / Bio</label>
                            <textarea class="form-control" name="bio" rows="4" 
                                      placeholder="Tell students about yourself, your teaching style, and what makes you a great tutor...">Passionate mathematics tutor with over 5 years of experience helping students excel in their studies. I specialize in making complex concepts easy to understand through practical examples and patient guidance.</textarea>
                            <small class="text-muted">This will be displayed on your public profile</small>
                        </div>

                        <!-- Hourly Rate -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Minimum Hourly Rate (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" name="hourly_rate_min" 
                                           min="100000" max="5000000" value="300000" step="50000" required>
                                    <span class="input-group-text">VNĐ/hr</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Maximum Hourly Rate (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" name="hourly_rate_max" 
                                           min="100000" max="5000000" value="500000" step="50000" required>
                                    <span class="input-group-text">VNĐ/hr</span>
                                </div>
                            </div>
                        </div>

                        <!-- Teaching Areas -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Teaching Areas / Locations</label>
                            <div class="skills-input-container">
                                <div id="areasChips" class="skills-chips">
                                    <span class="skill-chip">
                                        Quận 1, HCM
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                    <span class="skill-chip">
                                        Online
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                </div>
                                <input type="text" id="areasInput" class="skills-input" 
                                       placeholder="Add teaching location and press enter">
                                <input type="hidden" name="teaching_areas" id="areasHidden">
                            </div>
                            <small class="text-muted">Add areas where you're willing to teach (include "Online" if applicable)</small>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 4: Skills & Expertise -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">4</span>
                            Skills & Expertise
                        </h3>
                        
                        <div class="mb-4">
                            <label class="form-label fw-medium">Specific Topics & Skills</label>
                            <div class="skills-input-container">
                                <div id="skillsChips" class="skills-chips">
                                    <span class="skill-chip">
                                        Calculus
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                    <span class="skill-chip">
                                        Linear Algebra
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                    <span class="skill-chip">
                                        Statistics
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                </div>
                                <input type="text" id="skillsInput" class="skills-input" 
                                       placeholder="Type skill/topic and press enter">
                                <input type="hidden" name="skills" id="skillsHidden">
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 5: Certificates -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">5</span>
                            Certificates & Qualifications
                        </h3>
                        
                        <div class="mb-4">
                            <label class="form-label fw-medium">Upload Certificates (Images/PDF)</label>
                            <div class="upload-area upload-area-lg" id="certUploadArea">
                                <input type="file" class="d-none" id="certInput" name="certificates[]" 
                                       accept="image/*,.pdf" multiple>
                                <label for="certInput" class="upload-label">
                                    <span class="material-symbols-outlined fs-1 text-muted mb-2">workspace_premium</span>
                                    <span class="text-muted">Drag & drop or click to upload certificates</span>
                                    <span class="text-muted small">PNG, JPG, PDF up to 10MB each. Multiple files allowed.</span>
                                </label>
                            </div>
                            
                            <!-- Uploaded Certificates Preview -->
                            <div class="row g-3 mt-3">
                                <div class="col-md-4">
                                    <div class="certificate-card">
                                        <img src="https://via.placeholder.com/200x150/e8f4f8/0d6efd?text=Certificate" 
                                             alt="Certificate" class="certificate-img">
                                        <div class="certificate-info">
                                            <span class="certificate-name">Teaching Certificate.pdf</span>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="certificate-card">
                                        <img src="https://via.placeholder.com/200x150/f8f4e8/d4a900?text=IELTS" 
                                             alt="Certificate" class="certificate-img">
                                        <div class="certificate-info">
                                            <span class="certificate-name">IELTS_8.0.jpg</span>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 6: Availability -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">6</span>
                            Availability Schedule
                        </h3>
                        
                        <div class="availability-grid">
                            <div class="table-responsive">
                                <table class="table table-bordered availability-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">Day</th>
                                            <th>Morning (8AM - 12PM)</th>
                                            <th>Afternoon (12PM - 5PM)</th>
                                            <th>Evening (5PM - 9PM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                        <tr>
                                            <td class="fw-medium">{{ $day }}</td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input availability-check" 
                                                       name="availability[{{ strtolower($day) }}][]" value="morning"
                                                       {{ in_array($day, ['Monday', 'Wednesday', 'Friday']) ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input availability-check" 
                                                       name="availability[{{ strtolower($day) }}][]" value="afternoon"
                                                       {{ in_array($day, ['Tuesday', 'Thursday', 'Saturday']) ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input availability-check" 
                                                       name="availability[{{ strtolower($day) }}][]" value="evening"
                                                       {{ $day === 'Sunday' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">Select your available time slots. Students will see this when booking.</small>
                    </div>

                    <!-- Submit Section -->
                    <div class="p-4 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <span class="material-symbols-outlined me-1" style="font-size: 18px;">info</span>
                                Your profile will be reviewed before activation
                            </span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary px-4">
                                    Save Draft
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    Submit for Review
                                    <span class="material-symbols-outlined ms-1" style="font-size: 18px;">arrow_forward</span>
                                </button>
                            </div>
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
