{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home.index') }}">
            <div class="d-flex align-items-center justify-content-center rounded-3 bg-gradient text-white shadow-sm" 
                 style="width: 2.5rem; height: 2.5rem; background: linear-gradient(135deg, var(--primary) 0%, #2d6bd9 100%);">
                <span class="material-symbols-outlined">school</span>
            </div>
            <span class="fw-bold fs-5">SmartTutor</span>
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
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">Home</a>
                    </li>

                    @if(auth()->user()->role === 'tutor')
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">Find Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">My Classes</a>
                        </li>
                    @elseif(auth()->user()->role === 'student')
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">Find Tutors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('student.request.create') }}" style="color: #4b5563 !important;">My Requests</a>
                        </li>
                    @elseif(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link fw-medium" href="{{ route('admin.dashboard') }}" style="color: #4b5563 !important;">Admin Panel</a>
                        </li>
                    @endif
                @else
                    {{-- Guest Menu --}}
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">Find a Tutor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">Become a Tutor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#" style="color: #4b5563 !important;">How it Works</a>
                    </li>
                @endauth
            </ul>

            {{-- Auth Buttons --}}
            <div class="d-flex gap-2 ms-lg-4 mt-3 mt-lg-0">
                @auth
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
                            <li><a class="dropdown-item" href="{{ route('tutor.profile') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            @else
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm fw-bold" style="color: var(--gray-600);">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary fw-bold shadow-sm">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
