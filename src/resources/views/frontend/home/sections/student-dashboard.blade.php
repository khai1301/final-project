<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-primary">
            Chào mừng trở lại, {{ auth()->user()->name }}! 👋
        </h2>
        <p class="text-muted">Quản lý việc học và tìm kiếm gia sư phù hợp nhất.</p>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-white-50">Yêu cầu đang tìm</h6>
                        <h3 class="fw-bold mb-0 mt-2">{{ $myRequests->where('status', 'open')->count() ?? 0 }}</h3>
                    </div>
                    <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.8;">search</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-white-50">Đang kết nối</h6>
                        <h3 class="fw-bold mb-0 mt-2">{{ count($pendingRequests) + $incomingRequests->count() }}</h3>
                    </div>
                    <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.8;">connect_without_contact</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-white-50">Lớp đang học</h6>
                        <h3 class="fw-bold mb-0 mt-2">{{ $activeClassesCount ?? 0 }}</h3>
                    </div>
                    <span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.8;">school</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Main Content: My Requests --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">Yêu Cầu Tìm Gia Sư</h5>
                <a href="{{ route('student.request.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> Tạo yêu cầu mới
                </a>
            </div>
            <div class="card-body">
                @if(isset($myRequests) && $myRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Môn học</th>
                                    <th>Lớp</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myRequests->take(5) as $req)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded text-center me-2 p-1">
                                                <i class="bi bi-book text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 small fw-bold">{{ $req->subject->name ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $req->grade }}</span></td>
                                    <td>
                                        @if($req->status == 'open')
                                            <span class="badge bg-success">Đang tìm</span>
                                        @elseif($req->status == 'closed')
                                            <span class="badge bg-secondary">Đã đóng</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $req->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($myRequests->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('student.requests.index') }}" class="text-decoration-none small fw-bold">Xem tất cả <i class="bi bi-arrow-right"></i></a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" style="width: 150px; opacity: 0.5">
                        <p class="text-muted mt-2">Bạn chưa có yêu cầu tìm gia sư nào.</p>
                        <a href="{{ route('student.request.create') }}" class="btn btn-outline-primary btn-sm mt-2">Tạo ngay</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar: Recommended Tutors --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Gia Sư Đề Xuất</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($featuredTutors->take(4) as $tutor)
                    <a href="{{ route('tutor.show', $tutor->id) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-bottom-0">
                        <img src="{{ $tutor->avatar ? Storage::disk('s3')->url($tutor->avatar) : 'https://ui-avatars.com/api/?name='.$tutor->name }}" class="rounded-circle object-fit-cover" width="50" height="50">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold text-dark">{{ $tutor->name }}</h6>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                {{ $tutor->tutorProfile->subjects->pluck('name')->join(', ') }}
                            </small>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge bg-light text-warning border border-warning star-rating small px-1 py-0 me-2">
                                    <i class="bi bi-star-fill" style="font-size: 10px;"></i> 5.0
                                </span>
                                <small class="text-muted">{{ number_format($tutor->tutorProfile->hourly_rate_min/1000) }}k/h</small>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="card-footer bg-white text-center border-0 py-3">
                    <a href="{{ route('tutors.browse') }}" class="btn btn-outline-primary w-100">Tìm kiếm nâng cao</a>
                </div>
            </div>
        </div>
    </div>
</div>
