@extends('auth.layouts.guest')

@section('title', 'Xác thực Email')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <h2 class="mb-0">Xác thực Email</h2>
        <p class="mb-0 mt-2">Vui lòng xác thực địa chỉ email của bạn</p>
    </div>
    <div class="auth-body">
        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success" role="alert">
                Link xác thực mới đã được gửi đến email của bạn!
            </div>
        @endif

        <div class="text-center mb-4">
            <i class="bi bi-envelope-check text-primary" style="font-size: 4rem;"></i>
        </div>

        <p class="text-center mb-4">
            Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email của bạn 
            bằng cách nhấp vào link chúng tôi vừa gửi đến email. 
            Nếu bạn không nhận được email, chúng tôi sẵn sàng gửi lại.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    Gửi lại email xác thực
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <div class="d-grid">
                <button type="submit" class="btn btn-outline-secondary">
                    Đăng xuất
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
