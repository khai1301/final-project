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

                <form action="{{ route('admin.settings.update') }}" method="POST">
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

                        {{-- Payment Settings Section --}}
                        <h4 class="mb-3">
                            <span class="material-symbols-outlined align-middle">payments</span>
                            Cài Đặt Thanh Toán
                        </h4>

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
                                           value="{{ old('contact_unlock_fee', $settings['contact_unlock_fee']->value ?? 50000) }}"
                                           min="0"
                                           max="10000000"
                                           step="1000"
                                           required>
                                    @error('contact_unlock_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Gia sư phải thanh toán số tiền này để xem thông tin liên hệ của học sinh sau khi kết nối được chấp nhận.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Tùy Chọn Thanh Toán</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="payment_enabled" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="payment_enabled" 
                                           name="payment_enabled"
                                           value="1"
                                           {{ ($settings['payment_enabled']->value ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="payment_enabled">
                                        Bật Tính Năng Thanh Toán
                                    </label>
                                    <div class="form-text">
                                        Nếu tắt, gia sư có thể mở khóa thông tin miễn phí (chế độ phát triển)
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="vnpay_enabled" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="vnpay_enabled" 
                                           name="vnpay_enabled"
                                           value="1"
                                           {{ ($settings['vnpay_enabled']->value ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vnpay_enabled">
                                        Kích Hoạt VNPay
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="momo_enabled" value="0">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="momo_enabled" 
                                           name="momo_enabled"
                                           value="1"
                                           {{ ($settings['momo_enabled']->value ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="momo_enabled">
                                        Kích Hoạt MoMo
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <span class="material-symbols-outlined align-middle">info</span>
                                        Lưu Ý
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li>Tích hợp VNPay và MoMo cần cấu hình thêm trong file .env</li>
                                        <li>Trong quá trình phát triển, hãy tắt "Bật Tính Năng Thanh Toán"</li>
                                        <li>Phí mở khóa được tính theo VNĐ</li>
                                    </ul>
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
