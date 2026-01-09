@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0">
                        <span class="material-symbols-outlined align-middle">lock</span>
                        {{ __('ui.change_password') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form action="{{ route('password.updates') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Current Password --}}
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-outlined">lock</span>
                                </span>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                        </div>

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-outlined">lock_open</span>
                                </span>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                            </div>
                            <small class="text-muted">Tối thiểu 8 ký tự</small>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </span>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger">
                                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                                {{ __('ui.change_password') }}
                            </button>
                            <a href="{{ route('student.profile.edit') }}" class="btn btn-secondary">{{ __('ui.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security Tips --}}
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title">💡 Mẹo bảo mật</h6>
                    <ul class="small text-muted mb-0">
                        <li>Sử dụng mật khẩu mạnh có chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                        <li>Không sử dụng mật khẩu giống với các tài khoản khác</li>
                        <li>Thay đổi mật khẩu định kỳ mỗi 3-6 tháng</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
