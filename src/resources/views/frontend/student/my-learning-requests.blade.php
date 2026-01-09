@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <span class="material-symbols-outlined align-middle me-2">assignment</span>
            Yêu cầu học tập của tôi
        </h2>
        <a href="{{ route('student.request.create') }}" class="btn btn-primary">
            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">add</span>
            Tạo yêu cầu mới
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <span class="material-symbols-outlined d-block mb-3 text-muted" style="font-size: 64px;">assignment_late</span>
                <h5>Bạn chưa có yêu cầu học tập nào</h5>
                <p class="text-muted">Tạo yêu cầu học tập để tìm gia sư phù hợp với bạn</p>
                <a href="{{ route('student.request.create') }}" class="btn btn-primary mt-3">
                    <span class="material-symbols-outlined align-middle" style="font-size: 18px;">add</span>
                    Tạo yêu cầu đầu tiên
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($requests as $req)
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">{{ $req->title }}</h5>
                                
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($req->subject)
                                    <span class="badge bg-primary">{{ $req->subject->name }}</span>
                                    @endif
                                    @if($req->educationLevel)
                                    <span class="badge bg-info">{{ $req->educationLevel->name }}</span>
                                    @endif
                                    @if($req->learningMode)
                                    <span class="badge bg-success">{{ $req->learningMode->name }}</span>
                                    @endif
                                    
                                    @if($req->status === 'open')
                                    <span class="badge bg-success">Đang mở</span>
                                    @elseif($req->status === 'closed')
                                    <span class="badge bg-secondary">Đã đóng</span>
                                    @elseif($req->status === 'completed')
                                    <span class="badge bg-primary">Hoàn thành</span>
                                    @endif
                                </div>

                                @if($req->description)
                                <p class="text-muted mb-3">
                                    {{ Str::limit($req->description, 150) }}
                                </p>
                                @endif

                                <div class="d-flex flex-wrap gap-3 small text-muted">
                                    @if($req->province)
                                    <div>
                                        <span class="material-symbols-outlined" style="font-size: 16px;">location_on</span>
                                        {{ $req->ward?->name }}{{ $req->ward ? ', ' : '' }}{{ $req->province->name }}
                                    </div>
                                    @endif
                                    <div>
                                        <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                                        {{ $req->created_at->diffForHumans() }}
                                    </div>
                                    <div>
                                        <span class="material-symbols-outlined" style="font-size: 16px;">people</span>
                                        {{ $req->pending_connections }} chờ, {{ $req->accepted_connections }} chấp nhận
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 d-flex flex-column justify-content-between align-items-end">
                                <div class="text-end mb-3">
                                    <div class="h4 text-primary mb-0">
                                        {{ number_format($req->budget_min / 1000) }}k - {{ number_format($req->budget_max / 1000) }}k ₫
                                    </div>
                                    <small class="text-muted">/ giờ</small>
                                </div>

                                <div class="d-flex gap-2">
                                    @if($req->accepted_connections > 0)
                                        <button class="btn btn-sm btn-success" disabled>
                                            <span class="material-symbols-outlined" style="font-size: 16px;">verified</span>
                                            Đã kết nối
                                        </button>
                                        <a href="{{ route('matching.my-requests') }}" class="btn btn-sm btn-outline-primary">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                            Xem chi tiết
                                        </a>
                                    @elseif($req->status === 'active' || $req->status === 'open')
                                    <a href="{{ route('student.request.edit', $req->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                        Sửa
                                    </a>
                                    <form action="{{ route('student.request.destroy', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            Xóa
                                        </button>
                                    </form>
                                    <a href="{{ route('matching.my-requests') }}" class="btn btn-sm btn-outline-primary">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                        Xem kết nối
                                    </a>
                                    @else
                                    <a href="{{ route('matching.my-requests') }}" class="btn btn-sm btn-outline-primary">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                        Xem kết nối
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
