@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">
        <span class="material-symbols-outlined align-middle me-2">payments</span>
        Lịch sử thanh toán
    </h2>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Tổng chi tiêu</p>
                            <h4 class="mb-0">{{ number_format($stats['total_spent']) }}đ</h4>
                        </div>
                        <span class="material-symbols-outlined text-primary" style="font-size: 48px;">account_balance_wallet</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Giao dịch thành công</p>
                            <h4 class="mb-0">{{ $stats['total_transactions'] }}</h4>
                        </div>
                        <span class="material-symbols-outlined text-success" style="font-size: 48px;">check_circle</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Đang chờ</p>
                            <h4 class="mb-0">{{ $stats['pending_payments'] }}</h4>
                        </div>
                        <span class="material-symbols-outlined text-warning" style="font-size: 48px;">pending</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History Table --}}
    @if($payments->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <span class="material-symbols-outlined d-block mb-3 text-muted" style="font-size: 64px;">receipt_long</span>
                <h5>Chưa có giao dịch nào</h5>
                <p class="text-muted">Lịch sử thanh toán của bạn sẽ hiển thị ở đây</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã GD</th>
                                <th>Ngày</th>
                                <th>Mô tả</th>
                                <th>Phương thức</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <code class="small">{{ Str::limit($payment->transaction_id, 12) }}</code>
                                </td>
                                <td>
                                    <small>{{ $payment->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($payment->matching)
                                        <div>
                                            <strong>Mở khóa thông tin liên hệ</strong>
                                            <br>
                                            <small class="text-muted">
                                                @if(auth()->user()->role === 'tutor')
                                                    Học sinh: {{ $payment->matching->student->name }}
                                                @else
                                                    Gia sư: {{ $payment->matching->tutor->name }}
                                                @endif
                                            </small>
                                        </div>
                                    @else
                                        {{ $payment->description }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ strtoupper($payment->payment_method ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ number_format($payment->amount) }}đ</strong>
                                </td>
                                <td>
                                    @if($payment->status === 'completed')
                                        <span class="badge bg-success">Thành công</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge bg-warning">Đang chờ</span>
                                    @elseif($payment->status === 'failed')
                                        <span class="badge bg-danger">Thất bại</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $payment->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
