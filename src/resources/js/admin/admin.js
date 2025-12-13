// Import admin CSS
document.addEventListener('DOMContentLoaded', function () {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // Active nav link handling
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            if (!this.classList.contains('active')) {
                // This logic might need to be removed if active state is handled by backend
                // But for now keeping it to match original behavior
                // navLinks.forEach(l => l.classList.remove('active'));
                // this.classList.add('active');
            }
        });
    });

    // Add New Tutor button (Global)
    const addTutorBtn = document.querySelector('.navbar-top .btn-primary');
    if (addTutorBtn) {
        addTutorBtn.addEventListener('click', function () {
            alert('Redirecting to Add New Tutor form...');
        });
    }
});
