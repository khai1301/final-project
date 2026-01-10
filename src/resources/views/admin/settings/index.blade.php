@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="material-symbols-outlined align-middle">settings</span>
                        Cài Đặt Hệ Thống
                    </h3>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- Success Message --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="material-symbols-outlined">check_circle</i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Tabs Navigation --}}
                        <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                                    <span class="material-symbols-outlined align-middle" style="font-size: 18px;">payments</span>
                                    Thanh Toán
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab">
                                    <span class="material-symbols-outlined align-middle" style="font-size: 18px;">home</span>
                                    Trang Chủ
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="settingsTabContent">
                            {{-- Tab 1: Payment Settings --}}
                            <div class="tab-pane fade show active" id="payment" role="tabpanel">
                                <h4 class="mb-3">Cấu Hình Phí & Cổng Thanh Toán</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_unlock_fee" class="form-label fw-bold">
                                                Phí Mở Khóa Thông Tin Liên Hệ (VNĐ)
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('contact_unlock_fee') is-invalid @enderror" 
                                                   id="contact_unlock_fee" 
                                                   name="contact_unlock_fee" 
                                                   value="{{ old('contact_unlock_fee', data_get($settings, 'contact_unlock_fee.value', 50000)) }}"
                                                   min="0"
                                                   max="10000000"
                                                   step="1000"
                                                   required>
                                            @error('contact_unlock_fee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                Gia sư phải thanh toán số tiền này để xem thông tin liên hệ của học sinh.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Tùy Chọn Kích Hoạt</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input type="hidden" name="payment_enabled" value="0">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="payment_enabled" 
                                                   name="payment_enabled"
                                                   value="1"
                                                   {{ filter_var(data_get($settings, 'payment_enabled.value'), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="payment_enabled">
                                                Bật Tính Năng Thanh Toán
                                            </label>
                                            <div class="form-text">Nếu tắt, tính năng mở khóa sẽ miễn phí (Dev mode).</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Home Page Settings --}}
                            <div class="tab-pane fade" id="home" role="tabpanel">
                                <h4 class="mb-3">Cấu Hình Trang Chủ (Guest)</h4>
                                <div class="row mb-4">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="home_hero_title" class="form-label fw-bold">Tiêu Đề Chính (Hero Title)</label>
                                            <input type="text" class="form-control" id="home_hero_title" name="home_hero_title" 
                                                   value="{{ old('home_hero_title', data_get($settings, 'home_hero_title.value', 'Kết nối với Gia sư giỏi nhất')) }}"
                                                   placeholder="VD: Kết nối với Gia sư giỏi nhất">
                                        </div>

                                        <div class="mb-3">
                                            <label for="home_hero_subtitle" class="form-label fw-bold">Mô Tả Ngắn (Subtitle)</label>
                                            <textarea class="form-control" id="home_hero_subtitle" name="home_hero_subtitle" rows="2"
                                                      placeholder="VD: Nền tảng tìm kiếm gia sư uy tín...">{{ old('home_hero_subtitle', data_get($settings, 'home_hero_subtitle.value', 'Nền tảng tìm kiếm gia sư uy tín hàng đầu...')) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-info">
                                            <h6><i class="bi bi-info-circle me-2"></i>Guest</h6>
                                            <p class="small">Hiển thị cho khách chưa đăng nhập. Buttons đăng ký được cố định.</p>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h4 class="mb-3">Cấu hình Hero (Học sinh)</h4>
                                <div class="row mb-4">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="student_hero_title" class="form-label fw-bold">Tiêu Đề (Hỗ trợ @{{name}} để hiện tên)</label>
                                            <input type="text" class="form-control" id="student_hero_title" name="student_hero_title" 
                                                   value="{{ old('student_hero_title', data_get($settings, 'student_hero_title.value', 'Chào mừng trở lại, ' . '{' . '{name}' . '}! 👋')) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="student_hero_subtitle" class="form-label fw-bold">Mô Tả Ngắn</label>
                                            <textarea class="form-control" id="student_hero_subtitle" name="student_hero_subtitle" rows="2">{{ old('student_hero_subtitle', data_get($settings, 'student_hero_subtitle.value', 'Quản lý việc học và tìm kiếm gia sư phù hợp nhất cho bạn.')) }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="student_hero_image" class="form-label fw-bold">Ảnh Nền</label>
                                            <input type="file" class="form-control" id="student_hero_image" name="student_hero_image" accept="image/*">
                                            @if($img = data_get($settings, 'student_hero_image.value'))
                                                <div class="mt-2">
                                                    <small class="text-muted">Hiện tại:</small><br>
                                                    <img src="{{ $img }}" alt="Student Hero" class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-success">
                                            <h6><i class="bi bi-person-check me-2"></i>Học sinh</h6>
                                            <p class="small">Hiển thị sau khi học sinh đăng nhập.</p>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h4 class="mb-3">Cấu hình Hero (Gia sư)</h4>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="tutor_hero_title" class="form-label fw-bold">Tiêu Đề (Hỗ trợ @{{name}} để hiện tên)</label>
                                            <input type="text" class="form-control" id="tutor_hero_title" name="tutor_hero_title" 
                                                   value="{{ old('tutor_hero_title', data_get($settings, 'tutor_hero_title.value', 'Chào mừng trở lại, ' . '{' . '{name}' . '}! 👋')) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="tutor_hero_subtitle" class="form-label fw-bold">Mô Tả Ngắn</label>
                                            <textarea class="form-control" id="tutor_hero_subtitle" name="tutor_hero_subtitle" rows="2">{{ old('tutor_hero_subtitle', data_get($settings, 'tutor_hero_subtitle.value', 'Kết nối với học viên mới và quản lý lịch dạy của bạn.')) }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tutor_hero_image" class="form-label fw-bold">Ảnh Nền</label>
                                            <input type="file" class="form-control" id="tutor_hero_image" name="tutor_hero_image" accept="image/*">
                                            @if($img = data_get($settings, 'tutor_hero_image.value'))
                                                <div class="mt-2">
                                                    <small class="text-muted">Hiện tại:</small><br>
                                                    <img src="{{ $img }}" alt="Tutor Hero" class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-primary">
                                            <h6><i class="bi bi-briefcase me-2"></i>Gia sư</h6>
                                            <p class="small">Hiển thị sau khi gia sư đăng nhập.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-outlined align-middle">save</span>
                            Lưu Cài Đặt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
