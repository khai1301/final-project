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
                    <h5 class="mb-0 fw-bold">Yêu cầu đặt lịch mới</h5>
                    <span class="badge bg-primary">3</span>
                </div>
                <div class="card-body">
                    @php
                    $requests = [
                        ['student' => 'Minh', 'grade' => 'Lớp 10', 'subject' => 'Toán', 'time' => 'T3, 21/12, 18:00-19:30'],
                    ];
                    @endphp
                    
                    @foreach($requests as $request)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <img src="https://i.pravatar.cc/50?img=30" class="rounded-circle me-2" width="50" height="50">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $request['student'] }} - {{ $request['grade'] }}</h6>
                                <small class="text-muted">Muốn học: {{ $request['subject'] }}</small>
                            </div>
                        </div>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-clock me-1"></i>{{ $request['time'] }}
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm">Chấp nhận</button>
                            <button class="btn btn-outline-danger btn-sm">Từ chối</button>
                            <button class="btn btn-outline-secondary btn-sm">Nhắn tin</button>
                        </div>
                    </div>
                    @endforeach
                    
                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Xem tất cả yêu cầu →</a>
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
