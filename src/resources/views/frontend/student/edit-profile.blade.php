@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0">
                        <span class="material-symbols-outlined align-middle">person</span>
                        {{ __('ui.edit_profile') }}
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

                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                        @csrf
                        @method('PUT')

                        {{-- Avatar --}}
                        <div class="mb-4 text-center">
                            @php
                                $avatarUrl = $user->avatar 
                                    ? Storage::disk('s3')->url($user->avatar) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=200';
                            @endphp
                            <img id="avatarPreview" 
                                 src="{{ $avatarUrl }}" 
                                 alt="Avatar" 
                                 class="rounded-circle mb-3" 
                                 width="150" 
                                 height="150" 
                                 style="object-fit: cover;">
                            <div>
                                <label for="avatar" class="btn btn-outline-primary btn-sm">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">photo_camera</span>
                                    {{ __('ui.change_avatar') }}
                                </label>
                                <input type="file" id="avatar" name="avatar" class="d-none" 
                                       accept="image/jpeg,image/png,image/jpg">
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        {{-- Phone --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone', $user->phone) }}">
                        </div>

                        {{-- Province --}}
                        <div class="mb-3">
                            <label for="province_id" class="form-label">Tỉnh/Thành phố</label>
                            <select class="form-select" id="province_id" name="province_id">
                                <option value="">-- Chọn tỉnh/thành --</option>
                                @foreach($provinces as $province)
                                <option value="{{ $province->id }}" 
                                    {{ old('province_id', $user->province_id) == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ward --}}
                        <div class="mb-3">
                            <label for="ward_id" class="form-label">Quận/Huyện</label>
                            <select class="form-select" id="ward_id" name="ward_id">
                                <option value="">-- Chọn quận/huyện --</option>
                                @if($user->province_id)
                                    @foreach($wards->where('province_code', $provinces->firstWhere('id', $user->province_id)?->code ?? '') as $ward)
                                    <option value="{{ $ward->id }}" 
                                        {{ old('ward_id', $user->ward_id) == $ward->id ? 'selected' : '' }}>
                                        {{ $ward->name }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Address Detail --}}
                        <div class="mb-4">
                            <label for="address_detail" class="form-label">Địa chỉ cụ thể</label>
                            <input type="text" class="form-control" id="address_detail" name="address_detail" 
                                   value="{{ old('address_detail', $user->address_detail) }}"
                                   placeholder="Số nhà, tên đường...">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
                                {{ __('ui.save') }}
                            </button>
                            <a href="{{ route('home.index') }}" class="btn btn-secondary">{{ __('ui.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Password Change Link --}}
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title">{{ __('ui.security') }}</h5>
                    <p class="text-muted mb-3">Thay đổi mật khẩu để bảo vệ tài khoản</p>
                    <a href="{{ route('password.edit') }}" class="btn btn-outline-danger">
                        <span class="material-symbols-outlined" style="font-size: 18px;">lock</span>
                        {{ __('ui.change_password') }}
                    </a>
                </div>
            </div>
            
            {{-- CCCD Verification Link --}}
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <span class="material-symbols-outlined align-middle">verified_user</span>
                                Xác thực CCCD
                            </h5>
                            @if(auth()->user()->is_verified)
                                <p class="text-success mb-0">
                                    <span class="material-symbols-outlined align-middle" style="font-size: 18px;">check_circle</span>
                                    Đã xác thực
                                </p>
                            @else
                                <p class="text-muted mb-0">Xác thực để tăng độ tin cậy</p>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('id-verification.show') }}" class="btn btn-outline-primary">
                                @if(auth()->user()->is_verified)
                                    <span class="material-symbols-outlined" style="font-size: 18px;">verified</span>
                                    Xem chi tiết
                                @else
                                    <span class="material-symbols-outlined" style="font-size: 18px;">badge</span>
                                    Xác thực ngay
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Pass data to JS module
window.wardsData = @json($wards);
window.provincesData = @json($provinces);
</script>
@endpush
@endsection
