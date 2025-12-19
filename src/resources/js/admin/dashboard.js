document.addEventListener('DOMContentLoaded', function () {
    // Export button
    const exportBtn = document.querySelector('.export-report-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Report exported successfully!',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }
});
