@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu tìm gia sư')
@section('subtitle', 'Quản lý các yêu cầu học tập từ học viên')

@section('content')
<div class="container-fluid py-4">
    <!-- Success Message -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.requests.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                       placeholder="Search by student, subject..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Đang mở</option>
                                <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Đã khóa</option>
                                <option value="matched" {{ request('status') == 'matched' ? 'selected' : '' }}>Đã ghép</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="learning_mode_id" class="form-select">
                                <option value="">Tất cả hình thức</option>
                                @foreach($learningModes as $mode)
                                    <option value="{{ $mode->id }}" {{ request('learning_mode_id') == $mode->id ? 'selected' : '' }}>
                                        {{ $mode->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="education_level_id" class="form-select">
                                <option value="">Tất cả trình độ</option>
                                @foreach($educationLevels as $level)
                                    <option value="{{ $level->id }}" {{ request('education_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="data-table mb-4">
                <div class="table-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Danh sách yêu cầu</h3>
                        <p>Tổng số: {{ $requests->total() }}</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Học viên</th>
                                <th>Môn học</th>
                                <th>Trình độ</th>
                                <th>Hình thức</th>
                                <th>Học phí/h</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td><span class="badge bg-light text-dark">#{{ $request->id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($request->student->name ?? 'N/A') }}&background=3780f6&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <div class="fw-medium text-dark">{{ $request->student->name ?? 'Không rõ' }}</div>
                                            <div class="text-muted small">{{ $request->student->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $request->subject->name ?? 'N/A' }}</div>
                                    @if($request->skills && count($request->skills) > 0)
                                        <div class="text-muted small">
                                            {{ implode(', ', array_slice($request->skills, 0, 2)) }}
                                            @if(count($request->skills) > 2)
                                                <span class="badge bg-light text-muted">+{{ count($request->skills) - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $request->educationLevel->name ?? 'N/A' }}</td>
                                <td>
                                    @if($request->learningMode && $request->learningMode->slug === 'online')
                                        <span class="badge bg-info-light text-info">
                                            <i class="bi bi-laptop me-1"></i>Online
                                        </span>
                                    @else
                                        <span class="badge bg-success-light text-success">
                                            <i class="bi bi-geo-alt me-1"></i>Tại nhà
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-success">{{ number_format($request->budget_min, 0) }} - {{ number_format($request->budget_max, 0) }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'open' => ['status-badge status-pending', 'Đang mở'],
                                            'locked' => ['status-badge status-warning', 'Đã khóa'],
                                            'matched' => ['status-badge status-approved', 'Đã ghép'],
                                            'closed' => ['status-badge status-rejected', 'Đã đóng'],
                                            'cancelled' => ['status-badge status-rejected', 'Đã hủy']
                                        ];
                                        $statusInfo = $statusClasses[$request->status] ?? ['badge bg-secondary', ucfirst($request->status)];
                                    @endphp
                                    <span class="{{ $statusInfo[0] }}">
                                        {{ $statusInfo[1] }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li>
                                                <a href="{{ route('admin.requests.show', $request->id) }}" class="dropdown-item">
                                                    <i class="bi bi-eye me-2 text-info"></i> Xem chi tiết
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.requests.destroy', $request->id) }}" 
                                                      method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    Không có yêu cầu nào phù hợp.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($requests->hasPages())
                <div class="p-3 border-top">
                    {{ $requests->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
