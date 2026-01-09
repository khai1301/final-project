@extends('admin.layouts.app')

@section('title', 'Quản lý Thanh toán')
@section('subtitle', 'Lịch sử giao dịch & Doanh thu')

@section('content')
<div class="container-fluid py-4">
    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small text-uppercase fw-bold mb-1">Tổng Doanh thu</div>
                <div class="h3 fw-bold text-success mb-0">{{ number_format($stats['total_revenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small text-uppercase fw-bold mb-1">Doanh thu Hôm nay</div>
                <div class="h3 fw-bold text-primary mb-0">{{ number_format($stats['today_revenue'], 0, ',', '.') }} đ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small text-uppercase fw-bold mb-1">Giao dịch Thành công</div>
                <div class="h3 fw-bold text-dark mb-0">{{ $stats['completed_count'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small text-uppercase fw-bold mb-1">Giao dịch Thất bại</div>
                <div class="h3 fw-bold text-danger mb-0">{{ $stats['failed_count'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                               placeholder="Mã giao dịch, Tên User, Email..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Thành công</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="Từ ngày">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="Đến ngày">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc dữ liệu</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã GD</th>
                            <th>Người dùng</th>
                            <th>Dịch vụ</th>
                            <th>Số tiền</th>
                            <th>Phương thức</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th class="text-center">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td class="ps-4 font-monospace small">#{{ $payment->transaction_id ?? $payment->id }}</td>
                            <td>
                                @if($payment->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar rounded-circle bg-light text-primary me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px">
                                            {{ substr($payment->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $payment->user->name }}</div>
                                            <div class="small text-muted">{{ $payment->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">User Deleted</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->matching)
                                    <span class="badge bg-light text-dark border">Unlock Contact</span>
                                    <div class="small text-muted mt-1">Matching #{{ $payment->matching->id }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">{{ number_format($payment->amount, 0, ',', '.') }} đ</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td>
                                @if($payment->status == 'completed')
                                    <span class="badge bg-success">Thành công</span>
                                @elseif($payment->status == 'pending')
                                    <span class="badge bg-warning text-dark">Đang xử lý</span>
                                @elseif($payment->status == 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Không tìm thấy giao dịch nào phù hợp
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($payments->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $payments->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
