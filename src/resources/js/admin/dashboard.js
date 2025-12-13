document.addEventListener('DOMContentLoaded', function () {
    // Export report button functionality
    const exportBtn = document.getElementById('exportReportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            alert('Report exported successfully!');
        });
    }
});
