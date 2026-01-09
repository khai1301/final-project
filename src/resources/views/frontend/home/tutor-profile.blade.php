@extends('frontend.layouts.bootstrap')

@section('content')
{{-- Embed location data for JavaScript --}}
<script>
    window.locationData = {
        provinces: @json($provinces),
        wards: @json($wards)
    };
</script>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="material-symbols-outlined me-2">error</i>
                    <strong>Vui lòng sửa các lỗi sau:</strong>
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
                        <h1 class="h2 mb-0 fw-bold">Cập nhật hồ sơ</h1>
                    </div>
                    <p class="mb-0 text-white-50">
                        Hoàn thiện hồ sơ gia sư để bắt đầu nhận yêu cầu từ học sinh. Một hồ sơ đầy đủ giúp học sinh dễ tìm thấy bạn hơn.
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
                            Thông tin cơ bản
                        </h3>
                        
                        <!-- Profile Photo & Basic Info -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-4 text-center">
                                <div class="profile-photo-container">
                                    @php
                                        $avatarUrl = $user->avatar ? \Storage::disk('s3')->url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=200&background=3780f6&color=fff';
                                    @endphp
                                    <img src="{{ $avatarUrl }}" 
                                         alt="Profile Photo" class="profile-photo mb-3" id="profilePhotoPreview">
                                    <input type="file" class="d-none" id="profilePhotoInput" name="avatar" accept="image/*">
                                    <label for="profilePhotoInput" class="btn btn-outline-primary btn-sm">
                                        <span class="material-symbols-outlined me-1" style="font-size: 16px;">upload</span>
                                        Đổi ảnh đại diện
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Họ và tên</label>
                                        <input type="text" class="form-control form-control-lg" name="name" 
                                               value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Email</label>
                                        <input type="email" class="form-control form-control-lg" name="email" 
                                               value="{{ $user->email }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Số điện thoại</label>
                                        <input type="tel" class="form-control form-control-lg" name="phone" 
                                               placeholder="0123 456 789" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Số năm kinh nghiệm</label>
                                        <select class="form-select form-select-lg" name="experience_years" required>
                                            <option value="">Chọn kinh nghiệm</option>
                                            <option value="1" {{ old('experience_years', $profile->experience_years) == 1 ? 'selected' : '' }}>Dưới 1 năm</option>
                                            <option value="2" {{ old('experience_years', $profile->experience_years) == 2 ? 'selected' : '' }}>1-2 năm</option>
                                            <option value="3" {{ old('experience_years', $profile->experience_years) == 3 ? 'selected' : '' }}>3-5 năm</option>
                                            <option value="5" {{ old('experience_years', $profile->experience_years) == 5 ? 'selected' : '' }}>5-10 năm</option>
                                            <option value="10" {{ old('experience_years', $profile->experience_years) == 10 ? 'selected' : '' }}>Trên 10 năm</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Location Fields -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Tỉnh/Thành phố</label>
                                        <select id="tutor_province_id" name="province_id" class="form-select form-select-lg" data-selected="{{ old('province_id', $user->province_id) }}">
                                            <option value="">Chọn Tỉnh/Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Phường/Xã</label>
                                        <select id="tutor_ward_id" name="ward_id" class="form-select form-select-lg" disabled data-selected="{{ old('ward_id', $user->ward_id) }}">
                                            <option value="">Chọn Phường/Xã</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-medium">Địa chỉ chi tiết</label>
                                        <input type="text" class="form-control form-control-lg" name="address_detail" 
                                               placeholder="Số nhà, tên đường, khu phố..." 
                                               value="{{ old('address_detail', $user->address_detail) }}">
                                        <small class="text-muted">Ví dụ: 123 Đường Nguyễn Văn Linh, Phường Tân Phong</small>
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
                            Hồ sơ & Tài liệu
                        </h3>
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-medium mb-0">Tải lên CV (PDF, DOC)</label>
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
                            <!-- <div class="col-md-6">
                                <label class="form-label fw-medium">Identity Document (Optional)</label>
                                <div class="upload-area" id="idUploadArea">
                                    <input type="file" class="d-none" id="idInput" name="identity_doc" accept="image/*,.pdf">
                                    <label for="idInput" class="upload-label">
                                        <span class="material-symbols-outlined fs-1 text-muted mb-2">badge</span>
                                        <span class="text-muted">Upload ID/Passport</span>
                                        <span class="text-muted small">For verification purposes</span>
                                    </label>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 3: Teaching Information -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">3</span>
                            Thông tin giảng dạy
                        </h3>
                        
                        {{-- Subjects --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-medium mb-0">Môn học giảng dạy <span class="text-danger">*</span></label>
                                <span class="badge bg-primary-subtle text-primary">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">star</span>
                                    Nổi bật trên hồ sơ
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
                            <small class="text-muted mt-2 d-block">Chọn tất cả các môn bạn có thể dạy</small>
                        </div>

                        {{-- Education --}}
                        {{-- Education --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Chi tiết học vấn</label>
                            <textarea class="form-control" name="education" rows="3" 
                                      placeholder="Mô tả bằng cấp, trường học, chuyên ngành...">{{ old('education', $profile->education) }}</textarea>
                        </div>

                        {{-- Bio --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Giới thiệu bản thân</label>
                            <textarea class="form-control" name="bio" rows="4" 
                                      placeholder="Giới thiệu về kinh nghiệm, phong cách giảng dạy, và tại sao học sinh nên chọn bạn...">{{ old('bio', $profile->bio) }}</textarea>
                            <small class="text-muted">Thông tin này sẽ hiển thị công khai trên hồ sơ của bạn</small>
                        </div>

                        <!-- Hourly Rate -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Học phí tối thiểu (theo giờ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" name="hourly_rate_min" 
                                           min="100000" max="5000000" value="{{ old('hourly_rate_min', $profile->hourly_rate_min ?? 100000) }}" step="50000" required>
                                    <span class="input-group-text">VNĐ/giờ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Học phí tối đa (theo giờ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" name="hourly_rate_max" 
                                           min="100000" max="5000000" value="{{ old('hourly_rate_max', $profile->hourly_rate_max ?? 500000) }}" step="50000" required>
                                    <span class="input-group-text">VNĐ/giờ</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 4: Skills & Expertise -->
                    <!-- <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">4</span>
                            Kỹ năng & Chuyên môn
                        </h3>
                        
                        <div class="mb-4">
                            <label class="form-label fw-medium">Kỹ năng chuyên sâu / Chủ đề</label>
                            <div class="skills-input-container">
                                <div id="skillsChips" class="skills-chips">
                                    <span class="skill-chip">
                                        Giải tích
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                    <span class="skill-chip">
                                        Đại số tuyến tính
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                    <span class="skill-chip">
                                        Thống kê
                                        <button type="button" class="skill-chip-remove">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                        </button>
                                    </span>
                                </div>
                                <input type="text" id="skillsInput" class="skills-input" 
                                       placeholder="Nhập kỹ năng/chủ đề và nhấn Enter">
                                <input type="hidden" name="skills" id="skillsHidden">
                            </div>
                        </div>
                    </div> -->

                    <hr class="mx-4 my-0">


                    <!-- Section 5: Certificates -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">5</span>
                            Chứng chỉ & Bằng cấp
                        </h3>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-medium mb-0">Chứng chỉ của bạn</label>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                                    <span class="material-symbols-outlined me-1" style="font-size: 16px;">add</span>
                                    Thêm chứng chỉ
                                </button>
                            </div>
                            
                            <!-- Existing Certificates List -->
                            <div id="certificatesList">
                                @if($profile->certificates && $profile->certificates->count() > 0)
                                    @foreach($profile->certificates as $certificate)
                                    <div class="certificate-item card mb-2" data-cert-id="{{ $certificate->id }}">
                                        <div class="card-body p-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if(str_contains($certificate->file_type, 'image'))
                                                            <span class="material-symbols-outlined text-primary">image</span>
                                                        @elseif(str_contains($certificate->file_type, 'pdf'))
                                                            <span class="material-symbols-outlined text-danger">picture_as_pdf</span>
                                                        @else
                                                            <span class="material-symbols-outlined text-info">description</span>
                                                        @endif
                                                        <div>
                                                            <div class="fw-medium">{{ $certificate->name }}</div>
                                                            <small class="text-muted">{{ $certificate->created_at->format('d/m/Y H:i') }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ $certificate->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                                            Xem
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-cert-btn" 
                                                                data-cert-id="{{ $certificate->id }}"
                                                                data-cert-name="{{ $certificate->name }}">
                                                            <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                                            Sửa
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info" id="noCertificatesMessage">
                                        <span class="material-symbols-outlined align-middle me-2">info</span>
                                        Chưa có chứng chỉ nào. Click "Thêm chứng chỉ" để bắt đầu.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 6: Availability -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">6</span>
                            Lịch rảnh
                        </h3>
                        
                        <small class="text-muted d-block mb-3">Chọn tất cả các khung giờ bạn có thể nhận lớp</small>
                        
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
                        <small class="text-muted mt-2 d-block">Học sinh sẽ thấy lịch rảnh của bạn khi đặt lịch.</small>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 7: Teaching Areas -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">7</span>
                            Khu vực dạy
                        </h3>
                        
                        <small class="text-muted d-block mb-3">Bạn có thể dạy ở đâu? Thêm tỉnh/thành phố và phường/xã cụ thể.</small>
                        
                        <div id="teachingAreasList" class="mb-3">
                            @if(isset($profile->teachingAreas) && $profile->teachingAreas->count() > 0)
                                @foreach($profile->teachingAreas as $index => $area)
                                    <div class="teaching-area-item card mb-3 p-3" data-index="{{ $index }}">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label class="form-label small fw-medium">Tỉnh/Thành phố</label>
                                                <select name="teaching_areas[{{ $index }}][province_id]" class="form-select province-select" required data-selected="{{ $area->province_id }}">
                                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-medium">Phường/Xã <span class="text-muted">(Tùy chọn)</span></label>
                                                <select name="teaching_areas[{{ $index }}][ward_id]" class="form-select ward-select" data-selected="{{ $area->ward_id }}">
                                                    <option value="">Toàn tỉnh/thành</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-teaching-area">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info no-areas-message">
                                    <i class="bi bi-info-circle"></i> Chưa có khu vực dạy nào. Nhấn "Thêm khu vực dạy" bên dưới.
                                </div>
                            @endif
                        </div>
                        
                        <button type="button" id="addTeachingArea" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Thêm khu vực dạy
                        </button>
                        
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-lightbulb"></i> 
                            <strong>Mẹo:</strong> Thêm phường/xã cụ thể giúp học sinh tìm thấy bạn dễ dàng hơn.
                        </small>
                    </div>

                    <!-- Submit Section -->
                    <div class="p-4 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">
                                <span class="material-symbols-outlined me-1" style="font-size: 18px;">info</span>
                                Hồ sơ của bạn sẽ được duyệt trước khi kích hoạt
                            </span>
                            <div class="d-flex gap-2">
                                <!-- <button type="button" class="btn btn-outline-secondary px-4">
                                    Save Draft
                                </button> -->
                                <button type="submit" class="btn btn-primary px-4">
                                    Gửi hồ sơ
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



{{-- Add/Edit Certificate Modal --}}
<div class="modal fade" id="addCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="material-symbols-outlined align-middle me-2">workspace_premium</span>
                    <span id="modalTitle">Thêm chứng chỉ mới</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="certificateForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="certId" name="cert_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Tên chứng chỉ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="certName" name="cert_name" required 
                               placeholder="Ví dụ: IELTS 8.0, TOEIC 900, Bằng Cử nhân Sư phạm...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">File chứng chỉ <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="certFileInput" name="cert_file" 
                               accept="image/*,.pdf,.doc,.docx">
                        <small class="text-muted">Chấp nhận: Ảnh (JPG, PNG), PDF, Word. Tối đa 2MB</small>
                        <div id="currentFileName" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto" id="deleteCertBtn" style="display: none;">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">delete</span>
                        Xóa chứng chỉ
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="saveCertBtn">
                        <span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">save</span>
                        Lưu chứng chỉ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection
