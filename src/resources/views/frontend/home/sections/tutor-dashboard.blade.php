<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-primary">
            Xin chào, Gia sư {{ auth()->user()->name }}! 👋
        </h2>
        <p class="text-muted">Xem các yêu cầu dạy học mới nhất và quản lý học sinh của bạn.</p>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-white-50">Yêu cầu chờ duyệt</h6>
                        <h3 class="fw-bold mb-0 mt-2">{{ count($pendingRequests) + $incomingRequests->count() }}</h3>
                    </div>
                    <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.8;">pending_actions</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-white-50">Lớp đang dạy</h6>
                        <h3 class="fw-bold mb-0 mt-2">{{ $activeClassesCount ?? 0 }}</h3>
                    </div>
                    <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.8;">cast_for_education</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-white-50">Đánh giá trung bình</h6>
                        <h3 class="fw-bold mb-0 mt-2">5.0 <small class="fs-6 opacity-75">/ 5</small></h3>
                    </div>
                    <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.8;">star</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Main Content: Job Feed (Latest Requests) --}}
    <div class="col-lg-8">
        {{-- Incoming Direct Requests --}}
        @if($incomingRequests && $incomingRequests->count() > 0)
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-warning">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold text-warning">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> Yêu cầu kết nối mới
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($incomingRequests as $req)
                    <div class="list-group-item d-flex align-items-center justify-content-between p-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $req->sender->avatar ? Storage::disk('s3')->url($req->sender->avatar) : 'https://ui-avatars.com/api/?name='.$req->sender->name }}" class="rounded-circle" width="40" height="40">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $req->sender->name }}</h6>
                                <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <a href="{{ route('matching.my-requests') }}" class="btn btn-sm btn-warning">Xem ngay</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Việc Tìm Gia Sư Mới Nhất</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">Lọc theo môn</a>
            </div>
            <div class="card-body">
                @forelse($studentRequests as $req)
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom last-border-0">
                    <div class="flex-shrink-0">
                        <div class="avatar-md bg-light rounded text-center p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="material-symbols-outlined text-primary">school</span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-1">{{ $req->subject->name }} - {{ $req->grade }}</h6>
                            <span class="badge bg-light text-success border border-success">{{ number_format($req->budget, 0, ',', '.') }}đ/buổi</span>
                        </div>
                        <p class="text-muted small mb-2 d-flex align-items-center gap-2">
                             <i class="bi bi-geo-alt"></i> {{ $req->district_name ?? 'Hồ Chí Minh' }}
                             <span class="text-secondary">•</span>
                             <i class="bi bi-clock"></i> {{ $req->sessions_per_week }} buổi/tuần
                        </p>
                        <p class="text-secondary small mb-2 text-truncate" style="max-width: 500px;">
                            {{ $req->description }}
                        </p>
                        <div class="d-flex gap-2">
                             {{-- Placeholder Loop for badges --}}
                             <span class="badge bg-light text-secondary border">Online/Offline</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                        <button class="btn btn-sm btn-primary">Nhận lớp</button>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-muted">Hiện chưa có yêu cầu nào mới.</p>
                </div>
                @endforelse
                
                @if($studentRequests->count() > 0)
                <div class="text-center mt-2">
                    <button class="btn btn-link text-decoration-none fw-bold">Xem thêm yêu cầu</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Profile Completion Card (Mockup) --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="position-relative d-inline-block mb-3">
                    <div class="rounded-circle border-4 border-primary border p-1 d-inline-block">
                         <img src="{{ auth()->user()->avatar ? Storage::disk('s3')->url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.auth()->user()->name }}" class="rounded-circle" width="80" height="80">
                    </div>
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-white">
                        Verified
                    </span>
                </div>
                <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted small mb-3">Gia sư chuyên nghiệp</p>
                
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span>Hoàn thiện hồ sơ</span>
                    <span>85%</span>
                </div>
                
                <a href="{{ route('tutor.profile') }}" class="btn btn-outline-primary btn-sm w-100">Cập nhật hồ sơ</a>
            </div>
        </div>
    </div>
</div>
