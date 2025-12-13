<nav class="navbar-top">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="bi bi-list"></i>
                </button>
                <div class="page-title">
                    <h1>@yield('title', 'Admin Dashboard')</h1>
                    <p>@yield('subtitle', 'Platform Overview & Analytics')</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="search-box d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Search anything...">
                </div>
                
                <button class="btn btn-light position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </button>
                
                @yield('navbar-actions')
                
                <button class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Add New Tutor
                </button>
            </div>
        </div>
    </div>
</nav>
