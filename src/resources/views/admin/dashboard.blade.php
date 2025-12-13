@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('subtitle', 'Platform Overview & Analytics')

@section('content')
<div class="container-fluid py-4">
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">Last 7 days</button>
                    <button class="btn btn-light">Last 30 days</button>
                    <button class="btn btn-light">Custom Range</button>
                </div>
                <button class="btn btn-outline-secondary" id="exportReportBtn">
                    <i class="bi bi-download me-1"></i>
                    Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h4 class="fw-bold">Quick Insights</h4>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Users</p>
                        <h2 class="stat-card-value">12,480</h2>
                        <div class="stat-card-change change-positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>+12.5% vs last month</span>
                        </div>
                    </div>
                    <div class="stat-card-icon bg-primary-light text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Active Sessions</p>
                        <h2 class="stat-card-value">852</h2>
                        <div class="stat-card-change change-positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>+5.2% vs yesterday</span>
                        </div>
                    </div>
                    <div class="stat-card-icon bg-warning-light text-warning">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Revenue</p>
                        <h2 class="stat-card-value">$25,670</h2>
                        <div class="stat-card-change change-negative">
                            <i class="bi bi-arrow-down"></i>
                            <span>-1.8% vs last month</span>
                        </div>
                    </div>
                    <div class="stat-card-icon bg-success-light text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">New Tutor Requests</p>
                        <h2 class="stat-card-value">45</h2>
                        <div class="stat-card-change change-positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>+2 in the last hour</span>
                        </div>
                    </div>
                    <div class="stat-card-icon bg-purple-light text-purple">
                        <i class="bi bi-person-add"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>User Growth</h3>
                    <p>Signups over the last 30 days</p>
                </div>
                <div class="chart-placeholder" style="height: 300px;">
                    <div class="text-center">
                        <i class="bi bi-bar-chart-line display-4 text-muted mb-3"></i>
                        <p>Line chart showing steady upward trend</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-3">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>User Role Distribution</h3>
                    <p>Tutor vs. Student ratio</p>
                </div>
                <div class="chart-placeholder" style="height: 300px;">
                    <div class="text-center">
                        <i class="bi bi-pie-chart display-4 text-muted mb-3"></i>
                        <p>Donut chart: Students 70%, Tutors 30%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-12">
            <div class="data-table mb-4">
                <div class="table-header">
                    <h3>Latest Pending Requests</h3>
                    <p>Recent student requests needing admin approval or matching</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Subject</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Olivia+Martin&background=3780f6&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <span>Olivia Martin</span>
                                    </div>
                                </td>
                                <td>Calculus II</td>
                                <td>2023-10-26</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Liam+Johnson&background=10b981&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <span>Liam Johnson</span>
                                    </div>
                                </td>
                                <td>AP Physics</td>
                                <td>2023-10-26</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Noah+Williams&background=f59e0b&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <span>Noah Williams</span>
                                    </div>
                                </td>
                                <td>Organic Chemistry</td>
                                <td>2023-10-25</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=Emma+Brown&background=9333ea&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <span>Emma Brown</span>
                                    </div>
                                </td>
                                <td>English Literature</td>
                                <td>2023-10-25</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Tables for Missing Features -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="data-table">
                <div class="table-header">
                    <h3>Pending Tutor Verifications</h3>
                    <p>Tutors awaiting document approval</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tutor Name</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Dr. Sarah Chen</td>
                                <td>2 hours ago</td>
                                <td><span class="status-badge status-pending">Review Needed</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">Review</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Prof. James Wilson</td>
                                <td>1 day ago</td>
                                <td><span class="status-badge status-pending">Documents Pending</span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">Review</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="data-table">
                <div class="table-header">
                    <h3>Recent Ticket Transactions</h3>
                    <p>Latest tutor ticket purchases</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tutor</th>
                                <th>Tickets</th>
                                <th>Amount</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Michael Brown</td>
                                <td>5 tickets</td>
                                <td>$25.00</td>
                                <td class="text-end"><span class="status-badge status-approved">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Lisa Wong</td>
                                <td>10 tickets</td>
                                <td>$45.00</td>
                                <td class="text-end"><span class="status-badge status-approved">Completed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export report button functionality
        const exportBtn = document.getElementById('exportReportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                alert('Report exported successfully!');
            });
        }
    });
</script>
@endpush