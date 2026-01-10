<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.register_button') }} - SmartTutor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
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
    
    <header>
        <div class="logo-container">
            <div class="logo-icon">
                <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
                </svg>
            </div>
            <h2 class="logo-text">SmartTutor</h2>
        </div>
        <a href="{{ route('login') }}" class="signup-btn">{{ __('ui.login_button') }}</a>
    </header>

    <main>
        <div class="login-container">
            <div class="login-card">
                <h1 class="login-title">{{ __('ui.create_new_account') }}</h1>

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="name">{{ __('forms.full_name') }} <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" 
                               value="{{ old('name') }}" 
                               placeholder="{{ __('ui.enter_name') }}" 
                               required autofocus>
                        @error('name')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" 
                               value="{{ old('email') }}" 
                               placeholder="{{ __('ui.enter_email') }}" 
                               required>
                        @error('email')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('forms.phone') }} {{ __('ui.optional') }}</label>
                        <input type="text" id="phone" name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="{{ __('ui.enter_phone') }}">
                        @error('phone')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
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

                    <div class="form-group">
                        <label for="ward_id">{{ __('forms.ward') }} {{ __('ui.optional') }}</label>
                        <select id="ward_id" name="ward_id" disabled data-old-value="{{ old('ward_id') }}">
                            <option value="">{{ __('ui.select_ward') }}</option>
                        </select>
                        @error('ward_id')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address_detail">{{ __('forms.address_detail') }} {{ __('ui.optional') }}</label>
                        <input type="text" id="address_detail" name="address_detail" 
                               value="{{ old('address_detail') }}" 
                               placeholder="{{ __('ui.address_placeholder') }}">
                        <small class="text-muted">{{ __('ui.address_example') }}</small>
                        @error('address_detail')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>{{ __('ui.you_are') }}</label>
                        <div class="role-selection">
                            <label class="role-option">
                                <input type="radio" name="role" value="student" 
                                       {{ old('role', request('role', 'student')) == 'student' ? 'checked' : '' }}>
                                <span class="role-card">
                                    <span class="material-symbols-outlined">school</span>
                                    <span class="role-label">{{ __('ui.student') }}</span>
                                </span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="tutor" 
                                       {{ old('role', request('role')) == 'tutor' ? 'checked' : '' }}>
                                <span class="role-card">
                                    <span class="material-symbols-outlined">person</span>
                                    <span class="role-label">{{ __('ui.tutor') }}</span>
                                </span>
                            </label>
                        </div>
                        @error('role')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('forms.password') }} <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" 
                                   placeholder="{{ __('ui.enter_password') }}" required>
                            <button type="button" class="eye-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        <small class="text-muted">{{ __('ui.password_requirement') }}</small>
                        @error('password')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ __('forms.confirm_password') }} <span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   placeholder="{{ __('ui.reenter_password') }}" required>
                            <button type="button" class="eye-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">{{ __('ui.register_button') }}</button>
                </form>
            </div>
            <p class="signup-text">
                {{ __('ui.have_account') }} <a href="{{ route('login') }}" class="signup-link">{{ __('ui.login_button') }}</a>
            </p>
        </div>
    </main>
</body>
</html>
