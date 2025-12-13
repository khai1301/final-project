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

    // --- SweetAlert2 Configuration & Global Handlers ---

    // 1. Toast Notification Config
    // Check if Swal is defined (it should be loaded via CDN or import)
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // 2. Handle Session Flashes (passed via window.sessionMessages)
        if (window.sessionMessages) {
            if (window.sessionMessages.success) Toast.fire({ icon: 'success', title: window.sessionMessages.success });
            if (window.sessionMessages.error) Toast.fire({ icon: 'error', title: window.sessionMessages.error });
            if (window.sessionMessages.status) Toast.fire({ icon: 'info', title: window.sessionMessages.status });
            if (window.sessionMessages.warning) Toast.fire({ icon: 'warning', title: window.sessionMessages.warning });
        }

        // 3. Handle Confirmations (Delete, Ban, Approve)

        // Delete Logic
        document.querySelectorAll('.delete-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Ban/Unban Logic
        document.querySelectorAll('.ban-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const isBanned = this.dataset.banned === '1';
                const title = isBanned ? 'Unban User?' : 'Ban User?';
                const text = isBanned ? "User will regain access to the system." : "User will be forbidden from logging in.";
                const btnText = isBanned ? 'Yes, unban!' : 'Yes, ban!';
                const btnColor = isBanned ? '#10b981' : '#f59e0b';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: btnColor,
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: btnText
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Approve Logic
        document.querySelectorAll('.approve-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Approve Tutor?',
                    text: "This user will become an approved tutor.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    }
});
