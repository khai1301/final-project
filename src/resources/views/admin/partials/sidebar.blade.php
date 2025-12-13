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
                <span>Dashboard</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>User Management</span>
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-person-badge"></i>
                <span>Tutor Management</span>
            </a>
        </div>
        <div class="nav-item">
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
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
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
                    <span>Logout</span>
                </a>
            </form>
        </div>
    </div>
</aside>
