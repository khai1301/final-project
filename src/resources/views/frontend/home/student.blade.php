@extends('frontend.layouts.bootstrap')

@section('content')
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

            <div class="card shadow-sm border-0 overflow-hidden student-request-card">
                <!-- Header Section -->
                <div class="card-header bg-gradient-primary text-white p-4 border-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="material-symbols-outlined fs-1">auto_awesome</span>
                        <h1 class="h2 mb-0 fw-bold">Yêu Cầu Học Mới</h1>
                    </div>
                    <p class="mb-0 text-white-50">
                        Cho chúng tôi biết bạn muốn học gì. Hệ thống AI sẽ phân tích nhu cầu, lịch trình và phong cách học của bạn để tìm ra 3 gia sư phù hợp nhất ngay lập tức.
                    </p>
                </div>

                <!-- Form Body -->
                <form method="POST" action="{{ route('student.request.store') }}" class="student-request-form">
                    @csrf
                    
                    <!-- Section 1: The Basics -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">1</span>
                            Thông Tin Cơ Bản
                        </h3>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Môn học</label>
                                <select name="subject" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Chọn môn học</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Trình độ học vấn</label>
                                <select name="education_level" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Chọn trình độ</option>
                                    @foreach($educationLevels as $level)
                                        <option value="{{ $level->name }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-medium mb-0">Kỹ năng hoặc chủ đề cụ thể</label>
                                <span class="badge bg-primary-subtle text-primary">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">bolt</span>
                                    Ưu tiên ghép đôi AI
                                </span>
                            </div>
                            <div class="skills-input-container">
                                <div id="skillsChips" class="skills-chips"></div>
                                <input type="text" id="skillsInput" class="skills-input" 
                                       placeholder="Nhập chủ đề và nhấn enter (vd: Giới hạn, Đạo hàm)">
                                <input type="hidden" name="skills" id="skillsHidden">
                            </div>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 2: Logistics -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">2</span>
                            Thông Tin Học Tập
                        </h3>
                        
                        <div class="row g-4 mb-4">
                            <!-- Learning Mode -->
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Hình thức học</label>
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
                                <label class="form-label fw-medium">Lịch học ưu tiên</label>
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
                                Địa chỉ học
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <span class="material-symbols-outlined text-muted">location_on</span>
                                </span>
                                <input type="text" name="address" id="addressInput" class="form-control form-control-lg" 
                                       placeholder="Nhập địa điểm học ưa thích của bạn">
                            </div>
                            <small class="text-muted">Cung cấp địa chỉ nơi bạn muốn học trực tiếp</small>
                        </div>
                    </div>

                    <hr class="mx-4 my-0">

                    <!-- Section 3: Budget -->
                    <div class="p-4 pb-3">
                        <h3 class="section-title mb-4">
                            <span class="section-number">3</span>
                            Ngân Sách
                        </h3>
                        
                        <div class="budget-container p-4 rounded-3 bg-light">
                            <label class="form-label fw-medium mb-3">Khoảng giá theo giờ (VNĐ)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Giá tối thiểu</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="budget_min" 
                                               id="budgetMinInput" min="100000" max="5000000" value="500000" step="50000" required>
                                        <span class="input-group-text">VNĐ/giờ</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Giá tối đa</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="budget_max" 
                                               id="budgetMaxInput" min="100000" max="5000000" value="600000" step="50000" required>
                                        <span class="input-group-text">VNĐ/giờ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Notes -->
                    <div class="px-4 pb-4">
                        <label class="form-label fw-medium">Ghi chú thêm</label>
                        <textarea name="notes" class="form-control" rows="4" 
                                  placeholder="vd: Tôi đang chuẩn bị cho kỳ thi SAT vào tháng tới và cần giúp đỡ cụ thể về các bài toán Hình học."></textarea>
                    </div>

                    <!-- Submit Area -->
                    <div class="card-footer bg-light p-4 border-0 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <span class="material-symbols-outlined">info</span>
                            <span>Yêu cầu thường được ghép đôi trong vòng 24 giờ.</span>
                        </div>
                        <div class="d-flex gap-3">
                            <!-- <button type="button" class="btn btn-outline-secondary px-4">Lưu Nháp</button> -->
                            <button type="submit" class="btn btn-primary px-4">
                                Tìm Gia Sư Phù Hợp
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
