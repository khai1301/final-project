<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body style="font-family: 'Inter', sans-serif; background-color: #f5f7f8;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
        <div class="container-lg">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home.index') }}">
                <svg class="text-primary" width="24" height="24" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4H17.3334V17.3334H30.6666V30.6666H44V44H4V4Z" fill="currentColor"></path>
                </svg>
                <span class="fw-bold">{{ config('app.name') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home.index') }}">Trang chủ</a>
                    </li>
                    @if(auth()->user()->isStudent())
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tìm gia sư</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Lịch học</a>
                    </li>
                    @endif
                    @if(auth()->user()->isTutor())
                    <li class="nav-item">
                        <a class="nav-link" href="#">Lịch dạy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Học sinh</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Thu nhập</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tin nhắn</a>
                    </li>
                </ul>
                <div class="dropdown ms-lg-3">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="https://i.pravatar.cc/40?img={{ auth()->id() }}" alt="{{ auth()->user()->name }}" class="rounded-circle me-2" width="32" height="32">
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
