<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Lớp Học Mới Nhất Dành Cho Bạn</h2>
            <p class="text-muted">Danh sách các yêu cầu tìm gia sư phù hợp với chuyên môn của bạn.</p>
        </div>

        <div class="row g-4">
            {{-- Incoming Direct Requests --}}
            @if(isset($incomingRequests) && $incomingRequests->count() > 0)
            <div class="col-12 mb-4">
                <div class="card border-warning border-2 shadow-sm">
                    <div class="card-header bg-warning bg-opacity-10 py-3">
                        <h5 class="card-title mb-0 fw-bold text-warning-emphasis">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> Yêu cầu kết nối trực tiếp ({{ $incomingRequests->count() }})
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
                                        <small class="text-muted">Đã gửi yêu cầu kết nối • {{ $req->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('matching.my-requests') }}" class="btn btn-warning btn-sm">Xem chi tiết</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Job Feed List --}}
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @forelse($studentRequests as $req)
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom last-border-0">
                            <div class="flex-shrink-0">
                                <div class="avatar-md bg-light rounded text-center p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <span class="material-symbols-outlined text-primary">school</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $req->subject->name }} - {{ $req->grade }}</h6>
                                        <div class="text-muted small mb-2">
                                             <i class="bi bi-geo-alt me-1"></i> {{ $req->district_name ?? 'Hồ Chí Minh' }}
                                             <span class="mx-1">•</span>
                                             <i class="bi bi-clock me-1"></i> {{ $req->sessions_per_week }} buổi/tuần
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                        {{ number_format($req->budget, 0, ',', '.') }}đ/buổi
                                    </span>
                                </div>
                                <p class="text-secondary small mb-2 text-truncate" style="max-width: 90%;">
                                    {{ $req->description }}
                                </p>
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <button class="btn btn-sm btn-outline-primary whitespace-nowrap">Nhận lớp</button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/folder-is-empty-4064360-3363921.png" alt="Empty" style="width: 100px; opacity: 0.5" class="mb-3">
                            <p class="text-muted">Hiện chưa có yêu cầu nào mới.</p>
                        </div>
                        @endforelse
                        
                        @if($studentRequests->count() > 0)
                        <div class="text-center mt-3 pt-2 border-top">
                            <a href="{{ route('requests.browse') }}" class="btn btn-link text-decoration-none fw-bold">Xem tất cả yêu cầu <i class="bi bi-arrow-right"></i></a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
