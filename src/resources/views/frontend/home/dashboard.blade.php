@extends('frontend.layouts.dashboard')

@section('content')
<div class="container-lg py-4">
    
    <!-- Welcome Banner -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #3780f6 0%, #2d6bd9 100%);">
        <div class="card-body p-4 text-white">
            <h2 class="mb-2">
                @if(auth()->user()->isStudent())
                    Chào {{ auth()->user()->name }}! 👋
                @else
                    Chào {{ auth()->user()->isTutor() ? 'Thầy/Cô' : '' }} {{ auth()->user()->name }}! 🎓
                @endif
            </h2>
            <p class="mb-3 opacity-90">
                @if(auth()->user()->isStudent())
                    Bạn có 2 buổi học sắp tới trong tuần này
                @else
                    Bạn có 3 buổi dạy hôm nay
                @endif
            </p>
            <div class="d-flex gap-2 flex-wrap">
                @if(auth()->user()->isStudent())
                    <a href="#" class="btn btn-light btn-sm">
                        <i class="bi bi-calendar me-1"></i>Xem lịch học
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-search me-1"></i>Tìm gia sư mới
                    </a>
                @else
                    <a href="#" class="btn btn-light btn-sm">
                        <i class="bi bi-calendar me-1"></i>Xem lịch dạy
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-people me-1"></i>Quản lý học sinh
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- STUDENT: Upcoming Sessions -->
            @if(auth()->user()->isStudent())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Buổi học sắp tới</h5>
                </div>
                <div class="card-body">
                    @php
                    $upcomingSessions = [
                        ['tutor' => 'Cô Nguyễn A', 'subject' => 'Toán', 'date' => 'T2, 20/12', 'time' => '18:00-19:30', 'type' => 'Online'],
                        ['tutor' => 'Thầy Trần B', 'subject' => 'Tiếng Anh', 'date' => 'T4, 22/12', 'time' => '19:00-20:30', 'type' => 'Offline'],
                    ];
                    @endphp
                    
                    @foreach($upcomingSessions as $session)
                    <div class="d-flex align-items-center p-3 border rounded mb-3">
                        <img src="https://i.pravatar.cc/60?img={{ $loop->index + 10 }}" class="rounded-circle me-3" width="60" height="60">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $session['tutor'] }} - {{ $session['subject'] }}</h6>
                            <div class="small text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $session['date'] }}
                                <i class="bi bi-clock ms-2 me-1"></i>{{ $session['time'] }}
                                <i class="bi bi-{{ $session['type'] == 'Online' ? 'laptop' : 'geo-alt' }} ms-2 me-1"></i>{{ $session['type'] }}
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm">Tham gia</button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-chat"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                    
                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Xem tất cả lịch học →</a>
                </div>
            </div>
            @endif

            <!-- TUTOR: Today's Schedule -->
            @if(auth()->user()->isTutor())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Lịch dạy hôm nay (Thứ 2, 20/12)</h5>
                </div>
                <div class="card-body">
                    @php
                    $todaySessions = [
                        ['student' => 'Minh', 'subject' => 'Toán lớp 10', 'time' => '18:00-19:30', 'type' => 'Online'],
                        ['student' => 'Lan', 'subject' => 'Tiếng Anh lớp 9', 'time' => '20:00-21:00', 'type' => 'Offline'],
                    ];
                    @endphp
                    
                    @foreach($todaySessions as $session)
                    <div class="d-flex align-items-center p-3 border rounded mb-3">
                        <img src="https://i.pravatar.cc/60?img={{ $loop->index + 20 }}" class="rounded-circle me-3" width="60" height="60">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $session['student'] }}</h6>
                            <div class="small text-muted">
                                <i class="bi bi-book me-1"></i>{{ $session['subject'] }}
                                <i class="bi bi-clock ms-2 me-1"></i>{{ $session['time'] }}
                                <i class="bi bi-{{ $session['type'] == 'Online' ? 'laptop' : 'geo-alt' }} ms-2 me-1"></i>{{ $session['type'] }}
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm">Bắt đầu</button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-chat"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                    
                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Xem toàn bộ tuần →</a>
                </div>
            </div>
            @endif

            <!-- TUTOR: Pending Requests -->
            @if(auth()->user()->isTutor())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Yêu cầu kết nối mới</h5>
                    <span class="badge bg-primary">{{ $incomingRequests->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($incomingRequests as $request)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $avatarUrl = $request->sender->avatar 
                                    ? \Storage::disk('s3')->url($request->sender->avatar) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($request->sender->name).'&size=50';
                            @endphp
                            <img src="{{ $avatarUrl }}" class="rounded-circle me-2" width="50" height="50">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $request->sender->name }}</h6>
                                <small class="text-muted">{{ $request->sender->email }}</small>
                            </div>
                        </div>
                        @if($request->message)
                        <div class="small text-muted mb-2">
                            <i class="bi bi-chat-left-text me-1"></i>{{ Str::limit($request->message, 60) }}
                        </div>
                        @endif
                        <div class="small text-muted mb-2">
                            <i class="bi bi-clock me-1"></i>{{ $request->created_at->diffForHumans() }}
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#requestDetailModal{{ $request->id }}">
                                <i class="bi bi-eye me-1"></i>Xem chi tiết
                            </button>
                            <form action="{{ route('matching.accept', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-lg me-1"></i>Chấp nhận
                                </button>
                            </form>
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#declineModal{{ $request->id }}">
                                <i class="bi bi-x-lg me-1"></i>Từ chối
                            </button>
                        </div>
                    </div>

                    {{-- Request Detail Modal --}}
                    <div class="modal fade" id="requestDetailModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-person-circle me-2"></i>
                                        Chi tiết yêu cầu kết nối
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        {{-- Profile Picture and Basic Info --}}
                                        <div class="col-md-3 text-center mb-3">
                                            <img src="{{ $avatarUrl }}" class="rounded-circle mb-2" width="120" height="120">
                                            <h5>{{ $request->sender->name }}</h5>
                                            <span class="badge bg-success">Học sinh</span>
                                        </div>
                                        
                                        {{-- Detailed Information --}}
                                        <div class="col-md-9">
                                            {{-- Request Message --}}
                                            @if($request->message)
                                            <div class="mb-3">
                                                <h6 class="fw-bold">
                                                    <i class="bi bi-chat-square-text"></i> Lời nhắn
                                                </h6>
                                                <p class="text-muted">{{ $request->message }}</p>
                                            </div>
                                            @endif
                                            
                                            {{-- Contact Information --}}
                                            <div class="mb-3">
                                                <h6 class="fw-bold">
                                                    <i class="bi bi-envelope"></i> Liên hệ
                                                </h6>
                                                <p class="mb-1">
                                                    <i class="bi bi-envelope-fill me-2"></i>{{ $request->sender->email }}
                                                </p>
                                                @if($request->sender->phone)
                                                <p class="mb-1">
                                                    <i class="bi bi-telephone-fill me-2"></i>{{ $request->sender->phone }}
                                                </p>
                                                @endif
                                            </div>
                                            
                                            {{-- Request Info --}}
                                            <div class="border-top pt-3">
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Gửi {{ $request->created_at->diffForHumans() }} ({{ $request->created_at->format('d/m/Y H:i') }})
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <button class="btn btn-outline-danger" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#declineModal{{ $request->id }}">
                                        <i class="bi bi-x-lg me-1"></i>Từ chối
                                    </button>
                                    <form action="{{ route('matching.accept', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-lg me-1"></i>Chấp nhận yêu cầu
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Decline Modal --}}
                    <div class="modal fade" id="declineModal{{ $request->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('matching.decline', $request->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Từ chối yêu cầu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Vui lòng cho biết lý do từ chối yêu cầu này:</p>
                                        <textarea name="reason" class="form-control" rows="3" required minlength="10" maxlength="500" placeholder="Lý do của bạn (tối thiểu 10 ký tự)"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">Chưa có yêu cầu mới</p>
                    </div>
                    @endforelse
                    
                    <a href="{{ route('matching.my-requests') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">Xem tất cả yêu cầu →</a>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- STUDENT: Learning Progress -->
            @if(auth()->user()->isStudent())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Tiến độ học tập</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Tuần này: 4/5 buổi học</span>
                            <span class="small fw-bold text-success">80%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 80%"></div>
                        </div>
                    </div>
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-primary">24h</div>
                                <small class="text-muted">Tổng giờ học</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-primary">3</div>
                                <small class="text-muted">Môn đang học</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-warning">⭐ 4.8</div>
                                <small class="text-muted">Đánh giá TB</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-success">95%</div>
                                <small class="text-muted">Hoàn thành</small>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">Xem báo cáo chi tiết →</a>
                </div>
            </div>
            @endif

            <!-- TUTOR: Earnings Dashboard -->
            @if(auth()->user()->isTutor())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Thu nhập</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="fw-bold text-success mb-0">2,400,000đ</h3>
                        <small class="text-muted">Tháng này</small>
                    </div>
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-primary">600,000đ</div>
                                <small class="text-muted">Tuần này</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-warning">150,000đ</div>
                                <small class="text-muted">Chờ thanh toán</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-info">8</div>
                                <small class="text-muted">Buổi dạy</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="fw-bold text-info">12h</div>
                                <small class="text-muted">Giờ dạy</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="btn btn-outline-primary btn-sm flex-fill">Chi tiết</a>
                        <a href="#" class="btn btn-success btn-sm flex-fill">Rút tiền</a>
                    </div>
                </div>
            </div>

            <!-- TUTOR: Performance Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Hiệu suất</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">⭐ Đánh giá TB</span>
                            <span class="fw-bold text-warning">4.9/5</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">📝 Tổng reviews</span>
                            <span class="fw-bold">87</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">✅ Tỷ lệ hoàn thành</span>
                            <span class="fw-bold text-success">98%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small">👀 Lượt xem profile</span>
                            <span class="fw-bold">234 (tuần này)</span>
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Xem chi tiết →</a>
                </div>
            </div>
            @endif

            <!-- Recent Activity (Both roles) -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Hoạt động gần đây</h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        @if(auth()->user()->isStudent())
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="text-muted">2 giờ trước</div>
                            <div>Bạn đã hoàn thành buổi học Toán với Cô A</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="text-muted">1 ngày trước</div>
                            <div>Thầy B đã gửi tài liệu mới</div>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted">2 ngày trước</div>
                            <div>Bạn đã đánh giá Cô C ⭐⭐⭐⭐⭐</div>
                        </div>
                        @else
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="text-muted">1 giờ trước</div>
                            <div>Bạn nhận được yêu cầu mới từ Minh</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="text-muted">3 giờ trước</div>
                            <div>Bạn đã hoàn thành buổi dạy với Lan</div>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted">1 ngày trước</div>
                            <div>Bạn nhận được đánh giá 5⭐ từ Hùng</div>
                        </div>
                        @endif
                    </div>
                    <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-3">Xem tất cả →</a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
