{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home.index') }}">
                    <div class="logo-container">
            <div class="logo-icon">
                <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
                </svg>
            </div>
            <h2 class="logo-text">SmartTutor</h2>
        </div>
        </a>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Navigation --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                @auth
                    {{-- Common for all authenticated --}}
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">{{ __('ui.home') }}</a>
                    </li>

                    @if(auth()->user()->role === 'tutor')
                        <!-- <li class="nav-item">
                            <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">{{ __('ui.find_students') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">{{ __('ui.my_classes') }}</a>
                        </li> -->
                    @elseif(auth()->user()->role === 'student')
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('tutors.browse') }}" style="color: #4b5563 !important;">{{ __('ui.find_tutors') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.request.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm px-3" style="padding: 0.5rem 1rem;">
                                <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">add</span>
                                {{ __('ui.create_request') }}
                            </a>
                        </li>
                    @elseif(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('admin.dashboard') }}" style="color: #4b5563 !important;">{{ __('ui.admin_panel') }}</a>
                        </li>
                    @endif
                @else
                    {{-- Guest Menu --}}
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="{{ route('tutors.browse') }}" style="color: #4b5563 !important;">{{ __('ui.find_tutors') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">{{ __('pages.become_tutor') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">{{ __('pages.how_it_works') }}</a>
                    </li>
                @endauth
            </ul>

            {{-- Auth Buttons --}}
            <div class="d-flex gap-2 ms-lg-4 mt-3 mt-lg-0">
                @auth
                    {{-- Notification Bell --}}
                    <div class="dropdown me-2">
                        <a href="#" class="position-relative d-flex align-items-center justify-content-center text-decoration-none" id="notificationDropdown" data-bs-toggle="dropdown" style="color: #4b5563; width: 40px; height: 40px;">
                            <span class="material-symbols-outlined">notifications</span>
                            @php
                                $unreadCount = auth()->user()->unreadNotificationsCount();
                            @endphp
                            @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 300px;" aria-labelledby="notificationDropdown">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>{{ __('ui.notifications') }}</span>
                                @if($unreadCount > 0)
                                <a href="{{ route('notifications.index') }}" class="text-primary small">{{ __('ui.view_all') }}</a>
                                @endif
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @php
                                $recentNotifications = auth()->user()->notifications()->unread()->take(5)->get();
                            @endphp
                            @forelse($recentNotifications as $notification)
                            <li>
                                <a class="dropdown-item py-2 {{ !$notification->is_read ? 'bg-light' : '' }}" href="{{ route('notifications.index') }}">
                                    <div class="d-flex align-items-start">
                                        <span class="material-symbols-outlined text-primary me-2" style="font-size: 18px;">{{ $notification->type == 'connect_request' ? 'person_add' : ($notification->type == 'connect_accepted' ? 'check_circle' : 'cancel') }}</span>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small">{{ $notification->title }}</div>
                                            <div class="text-muted" style="font-size: 12px;">{{ Str::limit($notification->message, 50) }}</div>
                                            <div class="text-muted" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @empty
                            <li>
                                <div class="dropdown-item text-center text-muted py-3">
                                    <span class="material-symbols-outlined d-block mb-1">notifications_off</span>
                                    {{ __('ui.no_data') }}
                                </div>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                    
                    {{-- User Dropdown / Profile --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: #4b5563;">
                            <span class="fw-bold me-2">{{ auth()->user()->name }}</span>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                            @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @elseif(auth()->user()->role === 'tutor')
                            <li><a class="dropdown-item" href="{{ route('tutor.profile') }}">Hồ Sơ</a></li>
                            <li><a class="dropdown-item" href="{{ route('matching.my-requests') }}">Yêu Cầu Của Tôi</a></li>
                            <!-- <li><a class="dropdown-item" href="#">Cài Đặt</a></li> -->
                            @else
                            <li><a class="dropdown-item" href="#">Hồ Sơ</a></li>
                            <li><a class="dropdown-item" href="{{ route('matching.my-requests') }}">Yêu Cầu Của Tôi</a></li>
                            <!-- <li><a class="dropdown-item" href="#">Cài Đặt</a></li> -->
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">{{ __('auth.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm fw-bold" style="color: var(--gray-600);">{{ __('auth.login') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary fw-bold shadow-sm">{{ __('auth.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
