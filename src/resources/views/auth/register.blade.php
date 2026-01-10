<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.register_button') }} - SmartTutor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    {{-- Embed location data for JavaScript --}}
    <script>
        window.locationData = {
            provinces: @json($provinces),
            wards: @json($wards)
        };
    </script>
    
    @include('frontend.partials.navbar')


    <main>
        <div class="login-container register-container">
            <div class="login-card">
                <h1 class="login-title">{{ __('ui.create_new_account') }}</h1>

                <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
                    @csrf
                    
                    <div class="form-grid">
                        {{-- Left Column: Account Info --}}
                        <div class="form-column">
                            <h5 class="form-section-title">{{ __('ui.account_info') }}</h5>
                            
                            <div class="form-group mb-3">
                                <label for="name">{{ __('forms.full_name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="{{ __('ui.enter_name') }}" 
                                       required autofocus>
                                @error('name')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="{{ __('ui.enter_email') }}" 
                                       required>
                                @error('email')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">{{ __('forms.password') }} <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="password" id="password" name="password" 
                                           placeholder="{{ __('ui.enter_password') }}" required>
                                    <button type="button" class="eye-btn">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                </div>
                                <small class="text-muted" style="font-size: 0.8em;">{{ __('ui.password_requirement') }}</small>
                                @error('password')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation">{{ __('forms.confirm_password') }} <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           placeholder="{{ __('ui.reenter_password') }}" required>
                                    <button type="button" class="eye-btn">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Personal Info --}}
                        <div class="form-column">
                            <h5 class="form-section-title">{{ __('ui.personal_info') }}</h5>

                            <div class="form-group mb-3">
                                <label for="phone">{{ __('forms.phone') }} {{ __('ui.optional') }}</label>
                                <input type="text" id="phone" name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="{{ __('ui.enter_phone') }}">
                                @error('phone')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="province_id">{{ __('forms.province') }} <span class="text-danger">*</span></label>
                                <select id="province_id" name="province_id" required data-old-value="{{ old('province_id') }}">
                                    <option value="">{{ __('ui.select_province') }}</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('province_id')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="ward_id">{{ __('forms.ward') }} {{ __('ui.optional') }}</label>
                                <select id="ward_id" name="ward_id" disabled data-old-value="{{ old('ward_id') }}">
                                    <option value="">{{ __('ui.select_ward') }}</option>
                                </select>
                                @error('ward_id')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="address_detail">{{ __('forms.address_detail') }} {{ __('ui.optional') }}</label>
                                <input type="text" id="address_detail" name="address_detail" 
                                       value="{{ old('address_detail') }}" 
                                       placeholder="{{ __('ui.address_placeholder') }}">
                                <small class="text-muted" style="font-size: 0.8em;">{{ __('ui.address_example') }}</small>
                                @error('address_detail')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Full Width: Role & Submit --}}
                    <div class="mt-4">
                        <div class="form-group mb-4">
                            <label class="d-block mb-2 font-weight-bold">{{ __('ui.you_are') }}</label>
                            <div class="role-selection d-flex gap-3">
                                <label class="role-option" style="flex: 1;">
                                    <input type="radio" name="role" value="student" 
                                           {{ old('role', request('role', 'student')) == 'student' ? 'checked' : '' }}>
                                    <span class="role-card w-100 justify-content-center">
                                        <span class="material-symbols-outlined">school</span>
                                        <span class="role-label ms-2">{{ __('ui.student') }}</span>
                                    </span>
                                </label>
                                <label class="role-option" style="flex: 1;">
                                    <input type="radio" name="role" value="tutor" 
                                           {{ old('role', request('role')) == 'tutor' ? 'checked' : '' }}>
                                    <span class="role-card w-100 justify-content-center">
                                        <span class="material-symbols-outlined">person</span>
                                        <span class="role-label ms-2">{{ __('ui.tutor') }}</span>
                                    </span>
                                </label>
                            </div>
                            @error('role')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="login-btn w-100" id="submitBtn">{{ __('ui.register_button') }}</button>
                    </div>
                </form>
            </div>
            <p class="signup-text">
                {{ __('ui.have_account') }} <a href="{{ route('login') }}" class="signup-link">{{ __('ui.login_button') }}</a>
            </p>
        </div>
    </main>
    <script>
        // Prevent double submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            if (btn.disabled) {
                e.preventDefault();
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '{{ __('ui.processing') }}...';
        });

        // Toggle password visibility
        document.querySelectorAll('.eye-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.querySelector('span').textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    this.querySelector('span').textContent = 'visibility';
                }
            });
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
