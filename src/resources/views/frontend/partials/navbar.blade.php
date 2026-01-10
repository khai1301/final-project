{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home.index') }}">
            <i class="bi bi-mortarboard-fill text-primary" style="font-size: 1.5rem;"></i>
            <span class="fw-bold" style="font-size: 1.25rem;">SmartTutor</span>
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
                        <a class="nav-link fw-medium" href="{{ route('home.index') }}" style="color: #4b5563 !important;">{{ __('ui.home') }}</a>
                    </li>

                    @if(auth()->user()->role === 'tutor')
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('requests.browse') }}" style="color: #4b5563 !important;">Yêu cầu học sinh</a>
                        </li>
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
                            @if(auth()->user()->avatar)
                                <img src="{{ \Storage::disk('s3')->url(auth()->user()->avatar) }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="rounded-circle" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">
                            @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ __('ui.dashboard') }}</a></li>
                            @elseif(auth()->user()->role === 'tutor')
                            <li><a class="dropdown-item" href="{{ route('tutor.profile') }}">{{ __('ui.profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('matching.my-requests') }}">{{ __('ui.my_requests') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('payment.history') }}">Lịch sử giao dịch</a></li>
                            <!-- <li><a class="dropdown-item" href="#">Cài Đặt</a></li> -->
                            @else
                            <li><a class="dropdown-item" href="{{ route('student.profile.edit') }}">{{ __('ui.profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('matching.my-requests') }}">{{ __('ui.my_requests') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('student.requests.index') }}">Yêu cầu học tập của tôi</a></li>
                            <li><a class="dropdown-item" href="{{ route('password.edit') }}">{{ __('ui.change_password') }}</a></li>
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
