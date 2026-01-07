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

            <div class="card shadow-sm border-0 overflow-hidden student-request-card">
                <!-- Header Section -->
                <div class="card-header bg-gradient-primary text-white p-4 border-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="material-symbols-outlined fs-1">edit_note</span>
                        <h1 class="h2 mb-0 fw-bold">Chỉnh Sửa Yêu Cầu</h1>
                    </div>
                    <p class="mb-0 text-white-50">
                        Cập nhật thông tin yêu cầu học tập của bạn.
                    </p>
                </div>

                <!-- Form Body -->
                <form method="POST" action="{{ route('student.request.update', $request->id) }}" class="student-request-form">
                    @csrf
                    @method('PUT')
                    
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
                                    <option value="" disabled>Chọn môn học</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->name }}" {{ ($request->subject && $request->subject->name == $subject->name) ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Trình độ học vấn</label>
                                <select name="education_level" class="form-select form-select-lg" required>
                                    <option value="" disabled>Chọn trình độ</option>
                                    @foreach($educationLevels as $level)
                                        <option value="{{ $level->name }}" {{ ($request->educationLevel && $request->educationLevel->name == $level->name) ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
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
                                <input type="hidden" name="skills" id="skillsHidden" value="{{ json_encode($request->skills ?? []) }}">
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
                                        <input type="radio" class="btn-check" name="learning_mode_id" 
                                               id="mode{{ $modeItem->slug }}" 
                                               value="{{ $modeItem->id }}" 
                                               {{ $request->learning_mode_id == $modeItem->id ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary w-100 mode-card" for="mode{{ $modeItem->slug }}">
                                            @if($modeItem->icon)
                                                <i class="{{ $modeItem->icon }} fs-2 d-block mb-2"></i>
                                            @else
                                                <span class="material-symbols-outlined fs-2 d-block mb-2">
                                                    @if($index === 0) laptop_chromebook @elseif($index === 1) person_pin_circle @else home @endif
                                                </span>
                                            @endif
                                            <span class="d-block">{{ $modeItem->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Time Slots -->
                            <div class="col-md-12">
                                <label class="form-label fw-medium mb-3">Lịch học ưu tiên</label>
                                <small class="text-muted d-block mb-3">Chọn các khung giờ bạn có thể học (có thể chọn nhiều)</small>
                                
                                <div class="time-slots-grid">
                                    @php
                                        $selectedSlots = $request->timeSlots->pluck('id')->toArray();
                                    @endphp
                                    @foreach($timeSlots->groupBy('day_of_week') as $dayNum => $slots)
                                        <div class="day-slot-group mb-3">
                                            <h6 class="fw-semibold text-primary mb-2">
                                                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">today</span>
                                                {{ $slots->first()->getDayName() }}
                                            </h6>
                                            <div class="row g-2">
                                                @foreach($slots as $slot)
                                                    <div class="col-md-4 col-lg-3">
                                                        <div class="form-check time-slot-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="time_slots[]" 
                                                                   value="{{ $slot->id }}" 
                                                                   id="slot{{ $slot->id }}"
                                                                   {{ in_array($slot->id, $selectedSlots) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="slot{{ $slot->id }}">
                                                                {{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                        <!-- User Location Notice -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Địa điểm học</label>
                            
                            @php
                                // Check if request location differs from user profile location
                                $isDifferent = false;
                                if (auth()->user()->province_id) {
                                    if ($request->province_id != auth()->user()->province_id || 
                                        $request->ward_id != auth()->user()->ward_id) {
                                        $isDifferent = true;
                                    }
                                }
                            @endphp

                            @if(auth()->user()->province_id)
                                <!-- User has location - show and allow override -->
                                <div class="alert alert-info d-flex align-items-start mb-3">
                                    <span class="material-symbols-outlined me-2 mt-1">info</span>
                                    <div class="flex-grow-1">
                                        <strong>Địa chỉ trong hồ sơ của bạn:</strong>
                                        <div class="mt-2">
                                            <i class="bi bi-geo-alt-fill text-primary"></i>
                                            <strong>{{ auth()->user()->province->name ?? 'N/A' }}</strong>
                                            @if(auth()->user()->ward_id)
                                                → {{ auth()->user()->ward->name ?? '' }}
                                            @endif
                                            @if(auth()->user()->address_detail)
                                                <br><small class="text-muted ms-4">{{ auth()->user()->address_detail }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="use_different_location" {{ $isDifferent ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_different_location">
                                        Sử dụng địa chỉ khác cho yêu cầu này
                                    </label>
                                </div>
                                
                                <div id="custom_location_fields" class="{{ $isDifferent ? '' : 'd-none' }}">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="request_province_id" class="form-label">Tỉnh/Thành phố</label>
                                            <select id="request_province_id" name="province_id" class="form-select">
                                                <option value="">Chọn tỉnh/thành phố</option>
                                                <!-- Populate via JS if checked, but pre-fill via blade if active -->
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="request_ward_id" class="form-label">Phường/Xã</label>
                                            <select id="request_ward_id" name="ward_id" class="form-select" disabled>
                                                <option value="">Chọn phường/xã</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="request_address_detail" class="form-label">Địa chỉ chi tiết</label>
                                            <input type="text" class="form-control" id="request_address_detail" name="address_detail" 
                                                   placeholder="Số nhà, tên đường..." value="{{ $request->address_detail }}">
                                        </div>
                                    </div>
                                    {{-- Hidden inputs to pass initial values to JS --}}
                                    <input type="hidden" id="initial_province_id" value="{{ $request->province_id }}">
                                    <input type="hidden" id="initial_ward_id" value="{{ $request->ward_id }}">
                                </div>
                            @else
                                <!-- User has NO location - require input -->
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Bạn chưa cập nhật địa chỉ trong hồ sơ. Vui lòng nhập địa chỉ cho yêu cầu này.
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="request_province_id" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                        <select id="request_province_id" name="province_id" class="form-select" required>
                                            <option value="">Chọn tỉnh/thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="request_ward_id" class="form-label">Phường/Xã</label>
                                        <select id="request_ward_id" name="ward_id" class="form-select" disabled>
                                            <option value="">Chọn phường/xã</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="request_address_detail" class="form-label">Địa chỉ chi tiết</label>
                                        <input type="text" class="form-control" id="request_address_detail" name="address_detail" 
                                               placeholder="Số nhà, tên đường..." value="{{ $request->address_detail }}">
                                    </div>
                                </div>
                                <input type="hidden" id="initial_province_id" value="{{ $request->province_id }}">
                                <input type="hidden" id="initial_ward_id" value="{{ $request->ward_id }}">
                            @endif
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
                                               id="budgetMinInput" min="100000" max="5000000" value="{{ $request->budget_min }}" step="50000" required>
                                        <span class="input-group-text">VNĐ/giờ</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Giá tối đa</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg" name="budget_max" 
                                               id="budgetMaxInput" min="100000" max="5000000" value="{{ $request->budget_max }}" step="50000" required>
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
                                  placeholder="vd: Tôi đang chuẩn bị cho kỳ thi SAT vào tháng tới và cần giúp đỡ cụ thể về các bài toán Hình học.">{{ $request->description }}</textarea>
                    </div>

                    <!-- Submit Area -->
                    <div class="card-footer bg-light p-4 border-0 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <span class="material-symbols-outlined">info</span>
                            <span>Yêu cầu sẽ được cập nhật ngay lập tức.</span>
                        </div>
                        <div class="d-flex gap-3">
                            <a href="{{ route('student.requests.index') }}" class="btn btn-outline-secondary px-4">Hủy</a>
                            <button type="submit" class="btn btn-primary px-4">
                                Cập Nhật Yêu Cầu
                                <span class="material-symbols-outlined ms-1" style="font-size: 18px;">save</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('swal'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '{{ session('swal.type') }}',
            title: '{{ session('swal.title') }}',
            text: '{{ session('swal.text') }}',
            confirmButtonColor: '#0a2647'
        });
    });
</script>
@endif
@endsection
