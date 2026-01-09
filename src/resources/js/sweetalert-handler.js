document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 is not loaded');
        return;
    }

    const flash = window.Laravel?.flash || {};

    if (flash.swal) {
        Swal.fire({
            icon: flash.swal.type,
            title: flash.swal.title,
            text: flash.swal.text,
            timer: 3000,
            showConfirmButton: true
        });
    }

    if (flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: flash.success,
            timer: 3000
        });
    }

    if (flash.error) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: flash.error,
            timer: 3000
        });
    }
});
