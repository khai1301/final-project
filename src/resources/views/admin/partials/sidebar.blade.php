<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="#" class="logo">
            <i class="bi bi-mortarboard-fill me-2"></i>SmartTutor
        </a>
    </div>
    
    <nav class="sidebar-nav d-flex flex-column">
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>{{ __('admin.dashboard') }}</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>{{ __('admin.user_management') }}</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.requests.index') }}" class="nav-link {{ request()->routeIs('admin.requests*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>{{ __('admin.requests') }}</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.tutor-profiles.index') }}" class="nav-link {{ request()->routeIs('admin.tutor-profiles*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>{{ __('admin.tutors') }}</span>
                @php
                    $pendingCount = \App\Models\TutorProfile::where('is_approved', false)->count();
                @endphp
                @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.matchings.index') }}" class="nav-link {{ request()->routeIs('admin.matchings*') ? 'active' : '' }}">
                <i class="bi bi-link-45deg"></i>
                <span>{{ __('admin.matchings') }}</span>
                @php
                    $pendingMatchings = \App\Models\Matching::where('status', 'pending')->count();
                @endphp
                @if($pendingMatchings > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingMatchings }}</span>
                @endif
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Thanh toán</span>
                @php
                    $pendingPayments = \App\Models\Payment::where('status', 'pending')->count();
                @endphp
                @if($pendingPayments > 0)
                    <span class="badge bg-danger text-white ms-auto">{{ $pendingPayments }}</span>
                @endif
            </a>
        </div>
        <!-- <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-wallet2"></i>
                <span>Financial Center</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Reports & Analytics</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-robot"></i>
                <span>AI Configuration</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-chat-left-text"></i>
                <span>Dispute Resolution</span>
            </a>
        </div> -->
        
        <!-- System Configuration Section with Dropdown -->
        <div class="nav-item">
            <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#systemConfigMenu" 
               aria-expanded="{{ request()->routeIs('admin.subjects*') || request()->routeIs('admin.education-levels*') || request()->routeIs('admin.learning-modes*') ? 'true' : 'false' }}">
                <i class="bi bi-gear-fill"></i>
                <span>{{ __('admin.settings') }}</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.subjects*') || request()->routeIs('admin.education-levels*') || request()->routeIs('admin.learning-modes*') ? 'show' : '' }}" id="systemConfigMenu">
                <div class="submenu">
                    <a href="{{ route('admin.subjects.index') }}" class="submenu-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i>
                        <span>{{ __('admin.subjects') }}</span>
                    </a>
                    <a href="{{ route('admin.education-levels.index') }}" class="submenu-link {{ request()->routeIs('admin.education-levels*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i>
                        <span>{{ __('admin.education_levels') }}</span>
                    </a>
                    <a href="{{ route('admin.learning-modes.index') }}" class="submenu-link {{ request()->routeIs('admin.learning-modes*') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <span>{{ __('admin.learning_modes') }}</span>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i>
                <span>{{ __('admin.settings') }}</span>
            </a>
        </div>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3780f6&color=fff" 
                 alt="{{ auth()->user()->name }}" class="user-avatar">
            <div>
                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
            </div>
        </div>
        <div class="mt-3">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>{{ __('auth.logout') }}</span>
                </a>
            </form>
        </div>
    </div>
</aside>
