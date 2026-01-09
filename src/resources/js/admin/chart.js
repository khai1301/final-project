
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded and dashboard data exists
    if (typeof Chart === 'undefined' || typeof window.dashboardData === 'undefined') {
        return;
    }

    const data = window.dashboardData;

    // User Growth Chart
    const userGrowthCanvas = document.getElementById('userGrowthChart');
    if (userGrowthCanvas) {
        const userGrowthCtx = userGrowthCanvas.getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: data.dates,
                datasets: [{
                    label: 'Người dùng mới',
                    data: data.userGrowthData,
                    borderColor: '#3780f6',
                    backgroundColor: 'rgba(55, 128, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Role Distribution Chart
    const roleCanvas = document.getElementById('roleDistributionChart');
    if (roleCanvas) {
        const roleCtx = roleCanvas.getContext('2d');
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: ['Học viên', 'Gia sư'],
                datasets: [{
                    data: [data.studentCount, data.tutorCount],
                    backgroundColor: [
                        '#3780f6', // Primary Blue for Students
                        '#10b981'  // Success Green for Tutors
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
