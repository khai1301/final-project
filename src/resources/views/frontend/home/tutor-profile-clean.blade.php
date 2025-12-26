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
                <form method="POST" action="{{ route('tutor.profile.update') }}" class="tutor-profile-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
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
                                    @php
                                        $avatarUrl = $user->avatar 
                                            ? \Storage::disk('s3')->url($user->avatar) 
                                            : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=0d6efd&color=fff';
                                    @endphp
                                    <img src="{{ $avatarUrl }}" 
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
                                               value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Email</label>
                                        <input type="email" class="form-control form-control-lg" name="email" 
                                               value="{{ $user->email }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Phone Number</label>
                                        <input type="tel" class="form-control form-control-lg" name="phone" 
                                               placeholder="0123 456 789" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Years of Experience</label>
                                        <select class="form-select form-select-lg" name="experience_years" required>
                                            <option value="">Select experience</option>
                                            <option value="1" {{ old('experience_years', $profile->experience_years) == 1 ? 'selected' : '' }}>Less than 1 year</option>
                                            <option value="2" {{ old('experience_years', $profile->experience_years) == 2 ? 'selected' : '' }}>1-2 years</option>
                                            <option value="3" {{ old('experience_years', $profile->experience_years) == 3 ? 'selected' : '' }}>3-5 years</option>
                                            <option value="5" {{ old('experience_years', $profile->experience_years) == 5 ? 'selected' : '' }}>5-10 years</option>
                                            <option value="10" {{ old('experience_years', $profile->experience_years) == 10 ? 'selected' : '' }}>10+ years</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Location Fields -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Province/City</label>
                                        <select id="tutor_province_id" name="province_id" class="form-select form-select-lg" data-selected="{{ old('province_id', $user->province_id) }}">
                                            <option value="">Select Province</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Ward/Commune</label>
                                        <select id="tutor_ward_id" name="ward_id" class="form-select form-select-lg" disabled data-selected="{{ old('ward_id', $user->ward_id) }}">
                                            <option value="">Select Ward</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Detailed Address</label>
                                        <input type="text" class="form-control form-control-lg" name="address_detail" 
                                               placeholder="House number, street name, village..." 
                                               value="{{ old('address_detail', $user->address_detail) }}">
                                        <small class="text-muted">e.g., 123 Tran Hung Dao Street, Village 2</small>
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
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-medium mb-0">Upload CV (PDF, DOC)</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="aiAutoFillBtn" disabled>
                                        <span class="material-symbols-outlined me-1" style="font-size: 16px;">auto_awesome</span>
                                        Tự động điền từ CV
                                    </button>
                                </div>
                                
                                @if($profile->cv_path)
                                <div class="alert alert-info mb-2">
                                    <span class="material-symbols-outlined me-2">description</span>
                                    <strong>CV hiện tại:</strong> 
                                    <a href="{{ \Storage::disk('s3')->url($profile->cv_path) }}" target="_blank" class="text-decoration-underline">
                                        Xem CV
                                    </a>
                                </div>
                                @endif
                                
                                <!-- Info about PDF requirements -->
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <span class="material-symbols-outlined me-2">info</span>
                                    <strong>Lưu ý:</strong> Tính năng AI chỉ hoạt động với PDF/DOC có <strong>text layer</strong>. 
                                    Nếu CV của bạn là file scan (hình ảnh), vui lòng nhập thông tin thủ công.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                
                                <div class="upload-area" id="cvUploadArea">
                                    <input type="file" class="d-none" id="cvInput" name="cv" accept=".pdf,.doc,.docx">
                                    <label for="cvInput" class="upload-label">
                                        <span class="material-symbols-outlined fs-1 text-muted mb-2">description</span>
                                        <span class="text-muted">Kéo thả hoặc click để tải CV</span>
                                        <span class="text-muted small">PDF, DOC tối đa 10MB</span>
                                    </label>
                                </div>
                                
                                <div class="uploaded-file d-none mt-2" id="cvPreview">
                                    <span class="material-symbols-outlined me-2">description</span>
                                    <span class="file-name"></span>
                                    <button type="button" class="btn btn-sm btn-link text-danger ms-auto" id="cvRemoveBtn">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                
                                <!-- AI Processing Status -->
                                <div class="alert alert-primary d-none mt-3" id="aiProcessingAlert">
                                    <div class="d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span id="aiStatusText">Đang xử lý CV với AI...</span>
                                    </div>
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
                        
                        {{-- Subjects --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-medium mb-0">Subjects You Teach <span class="text-danger">*</span></label>
                                <span class="badge bg-primary-subtle text-primary">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                    Featured on Profile
                                </span>
                            </div>
                            @php
                                // WORKAROUND: Use manual join since Eloquent relationship not loading
                                $manualSubjects = DB::table('tutor_profile_subject')
                                    ->where('tutor_profile_id', $profile->id)
                                    ->pluck('subject_id')
                                    ->toArray();
                                
                                // Get selected subject IDs as array for easier checking
                                $selectedSubjectIds = [];
                                
                                // First try old() for validation errors
                                if (old('subjects')) {
                                    $selectedSubjectIds = old('subjects');
                                } else {
                                    // Use manual query result
                                    $selectedSubjectIds = $manualSubjects;
                                }
                            @endphp
                            
                            {{-- Debug: Remove this after testing --}}
                            @if(config('app.debug'))
                                @php
                                    // Check pivot table data
                                    $pivotData = DB::table('tutor_profile_subject')
                                        ->where('tutor_profile_id', $profile->id)
                                        ->get();
                                    
                                    // Debug model info
                                    $modelClass = get_class($profile);
                                    $tableName = $profile->getTable();
                                    
                                    // Test: Check if profile exists in DB
                                    $rawProfile = DB::table('tutor_profiles')->where('id', $profile->id)->first();
                                    
                                    // Test: Manually load subjects WITHOUT relationship
                                    $manualSubjects = DB::table('tutor_profile_subject')
                                        ->join('subjects', 'subjects.id', '=', 'tutor_profile_subject.subject_id')
                                        ->where('tutor_profile_subject.tutor_profile_id', $profile->id)
                                        ->select('subjects.*')
                                        ->get();
                                @endphp
                            @endif
                            
                            <div class="row g-2">
                                @foreach($allSubjects as $subject)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="subjects[]" 
                                               value="{{ $subject->id }}" id="subject_{{ $subject->id }}"
                                               {{ in_array($subject->id, $selectedSubjectIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="subject_{{ $subject->id }}">
                                            {{ $subject->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <small class="text-muted mt-2 d-block">Select all subjects you can teach</small>
                        </div>

                        {{-- Education --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Education Background</label>
                            <textarea class="form-control" name="education" rows="3" 
                                      placeholder="Describe your educational qualifications, degrees, institutions...">{{ old('education', $profile->education) }}</textarea>
                        </div>

                        {{-- Bio --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">About Me / Bio</label>
                            <textarea class="form-control" name="bio" rows="4" 
                                      placeholder="Tell students about yourself, your teaching style, and what makes you a great tutor...">{{ old('bio', $profile->bio) }}</textarea>
                            <small class="text-muted">This will be displayed on your public profile</small>
                        </div>

                        <!-- Hourly Rate -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Minimum Hourly Rate (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" name="hourly_rate_min" 
                                           min="100000" max="5000000" value="{{ old('hourly_rate_min', $profile->hourly_rate_min ?? 100000) }}" step="50000" required>
                                    <span class="input-group-text">VNĐ/hr</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Maximum Hourly Rate (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" name="hourly_rate_max" 
                                           min="100000" max="5000000" value="{{ old('hourly_rate_max', $profile->hourly_rate_max ?? 500000) }}" step="50000" required>
                                    <span class="input-group-text">VNĐ/hr</span>
                                </div>
                            </div>
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
                            
                            <!-- Uploaded Certificates Preview -->
                            <div class="row g-3 mt-3" id="certificatesPreview">
                                @if($profile->certificates && $profile->certificates->count() > 0)
                                    @foreach($profile->certificates as $certificate)
                                    <div class="col-md-4">
                                        <div class="certificate-card">
                                            @if(str_contains($certificate->file_type, 'image'))
                                                <img src="{{ $certificate->file_url }}" 
                                                     alt="{{ $certificate->name }}" class="certificate-img">
                                            @else
                                                <div class="certificate-img d-flex align-items-center justify-content-center bg-light">
                                                    <span class="material-symbols-outlined fs-1 text-muted">description</span>
                                                </div>
                                            @endif
                                            <div class="certificate-info">
                                                <span class="certificate-name">{{ $certificate->name }}</span>
                                                <form action="{{ route('tutor.certificate.delete', $certificate->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" 
                                                            onclick="return confirm('Delete this certificate?')">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
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
                        
                        <small class="text-muted d-block mb-3">Select all time slots when you're available to teach</small>
                        
                        <div class="time-slots-grid">
                            @foreach($timeSlots->groupBy('day_of_week') as $dayNum => $slots)
                                <div class="day-slot-group mb-4">
                                    <h6 class="fw-semibold text-primary mb-3 d-flex align-items-center gap-2">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">today</span>
                                        {{ $slots->first()->getDayName() }}
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($slots as $slot)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check time-slot-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="time_slots[]" 
                                                           value="{{ $slot->id }}" 
                                                           id="tutor_slot{{ $slot->id }}"
                                                           @checked(in_array($slot->id, $selectedTimeSlots))>
                                                    <label class="form-check-label" for="tutor_slot{{ $slot->id }}">
                                                        <strong>{{ date('H:i', strtotime($slot->start_time)) }}</strong> - {{ date('H:i', strtotime($slot->end_time)) }}
                                                        <span class="text-muted small d-block">{{ $slot->duration_minutes }} phút</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted mt-2 d-block">Students will see your available time slots when booking.</small>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 7: Teaching Areas -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">7</span>
                            Teaching Areas
                        </h3>
                        
                        <small class="text-muted d-block mb-3">Where are you willing to teach? Add provinces and specific wards/communes.</small>
                        
                        <div id="teachingAreasList" class="mb-3">
                            @if(isset($profile->teachingAreas) && $profile->teachingAreas->count() > 0)
                                @foreach($profile->teachingAreas as $index => $area)
                                    <div class="teaching-area-item card mb-3 p-3" data-index="{{ $index }}">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label small fw-medium">Province/City</label>
                                                <select name="teaching_areas[{{ $index }}][province_id]" class="form-select province-select" required data-selected="{{ $area->province_id }}">
                                                    <option value="">Select Province</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-medium">Ward/Commune <span class="text-muted">(Optional)</span></label>
                                                <select name="teaching_areas[{{ $index }}][ward_id]" class="form-select ward-select" data-selected="{{ $area->ward_id }}">
                                                    <option value="">Entire Province</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-teaching-area">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info no-areas-message">
                                    <i class="bi bi-info-circle"></i> No teaching areas added yet. Click "Add Teaching Area" below.
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" id="addTeachingArea" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Add Teaching Area
                        </button>
                        
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-lightbulb"></i> 
                            <strong>Tip:</strong> Adding specific wards helps students find you more easily.
                        </small>
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

<!-- AI Preview Modal -->
<div class="modal fade" id="aiPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <span class="material-symbols-outlined me-2">auto_awesome</span>
                    Kết quả phân tích CV bởi AI
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <span class="material-symbols-outlined me-2">info</span>
                    <strong>Hướng dẫn:</strong> Xem lại thông tin đã trích xuất, chỉnh sửa nếu cần, sau đó nhấn "Áp dụng" để điền vào form.
                </div>
                
                <div class="row g-3" id="aiPreviewContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="applyAIDataBtn">
                    <span class="material-symbols-outlined me-1" style="font-size: 18px;">check_circle</span>
                    Áp dụng vào Form
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div data-success-message="{{ session('success') }}" style="display: none;"></div>
@endif


@endsection
