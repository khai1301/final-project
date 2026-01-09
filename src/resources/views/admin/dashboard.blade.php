@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển Admin')
@section('subtitle', 'Tổng quan & Thống kê hệ thống')

@section('content')
<div class="container-fluid py-4">
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">7 ngày qua</button>
                    <button class="btn btn-light">30 ngày qua</button>
                    <button class="btn btn-light">Tùy chọn</button>
                </div>
                <button class="btn btn-outline-secondary" id="exportReportBtn">
                    <i class="bi bi-download me-1"></i>
                    Xuất báo cáo
                </button>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h4 class="fw-bold">Thông số nhanh</h4>
        </div>
        
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Tổng thành viên</p>
                        <h2 class="stat-card-value">{{ number_format($totalUsers) }}</h2>
                        <div class="stat-card-change change-{{ $userGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="bi bi-arrow-{{ $userGrowth >= 0 ? 'up' : 'down' }}"></i>
                            <span>{{ $userGrowth >= 0 ? '+' : '' }}{{ number_format($userGrowth, 1) }}% vs tháng trước</span>
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
                        <p class="text-muted mb-1">Kết nối hoạt động</p>
                        <h2 class="stat-card-value">{{ number_format($activeSessions) }}</h2>
                        <div class="stat-card-change change-{{ $sessionGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="bi bi-arrow-{{ $sessionGrowth >= 0 ? 'up' : 'down' }}"></i>
                            <span>{{ $sessionGrowth >= 0 ? '+' : '' }}{{ number_format($sessionGrowth, 1) }}% vs hôm qua</span>
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
                        <p class="text-muted mb-1">Tổng doanh thu</p>
                        <h2 class="stat-card-value">{{ number_format($totalRevenue) }} ₫</h2>
                        <div class="stat-card-change change-{{ $revenueGrowth >= 0 ? 'positive' : 'negative' }}">
                            <i class="bi bi-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                            <span>{{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}% vs tháng trước</span>
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
                        <p class="text-muted mb-1">Gia sư chờ duyệt</p>
                        <h2 class="stat-card-value">{{ number_format($newTutorRequests) }}</h2>
                        <div class="stat-card-change change-positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>+{{ $lastHourTutorRequests }} trong 1 giờ qua</span>
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
                    <h3>Tăng trưởng người dùng</h3>
                    <p>Số lượng đăng ký mới trong 30 ngày qua</p>
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
                    <h3>Phân bố vai trò</h3>
                    <p>Tỷ lệ Học viên / Gia sư</p>
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
                    <h3>Yêu cầu tìm gia sư mới nhất</h3>
                    <p>Các yêu cầu chờ xử lý hoặc ghép nối</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Học viên</th>
                                <th>Môn học</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestRequests as $req)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($req->student ? $req->student->name : 'Unknown') }}&background=3780f6&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <span>{{ $req->student ? $req->student->name : 'Unknown Student' }}</span>
                                    </div>
                                </td>
                                <td>{{ $req->subject ? $req->subject->name : 'N/A' }}</td>
                                <td>{{ $req->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $statusMap = ['open' => 'Đang mở', 'pending' => 'Chờ duyệt', 'active' => 'Hoạt động', 'closed' => 'Đã đóng'];
                                        $statusLabel = $statusMap[$req->status] ?? ucfirst($req->status);
                                        $badgeClass = match($req->status) {
                                            'open' => 'success',
                                            'pending' => 'warning',
                                            'closed' => 'secondary',
                                            default => 'info'
                                        };
                                    @endphp
                                    <span class="status-badge status-{{ $badgeClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.requests.show', $req->id) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Không có yêu cầu nào</td>
                            </tr>
                            @endforelse
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
                    <h3>Gia sư chờ duyệt</h3>
                    <p>Các hồ sơ gia sư đang chờ xác minh</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tên gia sư</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingTutors as $tutor)
                            <tr>
                                <td>{{ $tutor->user ? $tutor->user->name : 'Không rõ' }}</td>
                                <td>{{ $tutor->created_at->diffForHumans() }}</td>
                                <td><span class="status-badge status-pending">Chờ duyệt</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.tutor-profiles.show', $tutor->id) }}" class="btn btn-sm btn-outline-primary">Xem xét</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không có hồ sơ nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="data-table">
                <div class="table-header">
                    <h3>Giao dịch gần đây</h3>
                    <p>Các giao dịch thanh toán mới nhất</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Nội dung</th>
                                <th>Số tiền</th>
                                <th class="text-end">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $txn)
                            <tr>
                                <td>{{ $txn->user ? $txn->user->name : 'Không rõ' }}</td>
                                <td>{{ $txn->description ?? 'Thanh toán' }}</td>
                                <td>{{ number_format($txn->amount) }} ₫</td>
                                <td class="text-end">
                                    @php
                                        $txnStatus = match(strtolower($txn->status)) {
                                            'completed', 'paid' => ['success', 'Thành công'],
                                            'pending' => ['warning', 'Đang xử lý'],
                                            'failed' => ['danger', 'Thất bại'],
                                            default => ['secondary', ucfirst($txn->status)]
                                        };
                                    @endphp
                                    <span class="status-badge status-{{ $txnStatus[0] }}">{{ $txnStatus[1] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không có giao dịch nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

