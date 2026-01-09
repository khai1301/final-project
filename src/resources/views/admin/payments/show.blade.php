@extends('admin.layouts.app')

@section('title', 'Chi tiết Giao dịch')
@section('subtitle', 'Mã GD #' . ($payment->transaction_id ?? $payment->id))

@section('content')
<div class="container-fluid py-4">
    <div class="mb-3">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thông tin giao dịch</h5>
                        @if($payment->status == 'completed')
                            <span class="badge bg-success fs-6">Thành công</span>
                        @elseif($payment->status == 'pending')
                            <span class="badge bg-warning text-dark fs-6">Đang xử lý</span>
                        @elseif($payment->status == 'failed')
                            <span class="badge bg-danger fs-6">Thất bại</span>
                        @else
                            <span class="badge bg-secondary fs-6">{{ ucfirst($payment->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Mã giao dịch</div>
                            <div class="font-monospace fs-5">{{ $payment->transaction_id ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Số tiền</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($payment->amount, 0, ',', '.') }} {{ $payment->currency }}</div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-2">Phương thức</div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card fs-4 me-2"></i>
                                    <span class="fw-medium">{{ ucfirst($payment->payment_method) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-2">Thời gian tạo</div>
                                <div>{{ $payment->created_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="text-muted small text-uppercase fw-bold mb-2">Thời gian thanh toán</div>
                                <div>
                                    @if($payment->paid_at)
                                        {{ $payment->paid_at->format('d/m/Y H:i:s') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Thông tin người thanh toán</h6>
                        @if($payment->user)
                        <div class="d-flex align-items-center">
                            @if($payment->user->avatar)
                                <img src="{{ \Storage::disk('s3')->url($payment->user->avatar) }}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px">
                                    <span class="fs-5 fw-bold text-primary">{{ substr($payment->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1"><a href="{{ route('admin.users.show', $payment->user_id) }}" class="text-decoration-none">{{ $payment->user->name }}</a></h6>
                                <div class="text-muted small">{{ $payment->user->email }}</div>
                                <div class="text-muted small">{{ $payment->user->phone ?? 'SĐT: N/A' }}</div>
                            </div>
                        </div>
                        @else
                            <div class="alert alert-warning">Thông tin người dùng không tồn tại (đã bị xóa).</div>
                        @endif
                    </div>

                    @if($payment->matching)
                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Chi tiết Dịch vụ (Matching)</h6>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-2">Gói: Mở khóa liên hệ</h6>
                                        <p class="mb-1 small">
                                            <strong>Gia sư:</strong> {{ $payment->matching->tutor->name ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1 small">
                                            <strong>Học sinh:</strong> {{ $payment->matching->student->name ?? 'N/A' }}
                                        </p>
                                        <p class="mb-0 small">
                                            <strong>Trạng thái Matching:</strong> {{ ucfirst($payment->matching->status) }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ route('admin.matchings.show', $payment->matching_id) }}" class="btn btn-sm btn-outline-primary">
                                            Xem Matching <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Debug Data -->
                    @if(config('app.debug'))
                    <div class="accordion" id="debugAccordion">
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-light rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDebug">
                                    Technical Data (Debug)
                                </button>
                            </h2>
                            <div id="collapseDebug" class="accordion-collapse collapse" data-bs-parent="#debugAccordion">
                                <div class="accordion-body bg-dark text-light rounded mt-2 overflow-auto" style="max-height: 300px;">
                                    <pre><code>{{ json_encode($payment->payment_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Note</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">
                        {{ $payment->description ?? 'Không có ghi chú.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
