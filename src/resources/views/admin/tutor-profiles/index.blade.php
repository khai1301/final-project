@extends('admin.layouts.app')

@section('title', 'Quản lý Hồ sơ Gia sư')
@section('subtitle', 'Quản lý và duyệt hồ sơ gia sư')

@section('content')
<div class="container-fluid py-4">
    {{-- Success Message --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filters & Search --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.tutor-profiles.index') }}" method="GET" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                       placeholder="Tìm kiếm theo tên hoặc email..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tutor Profiles Table --}}
    <div class="row">
        <div class="col-12">
            <div class="data-table mb-4">
                <div class="table-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Hồ sơ Gia sư</h3>
                        <p>Tổng số hồ sơ: {{ $profiles->total() }} 
                            @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark ms-2">{{ $pendingCount }} Chờ duyệt</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Gia sư</th>
                                <th>Môn học</th>
                                <th>Kinh nghiệm</th>
                                <th>Học phí/giờ</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($profiles as $profile)
                            <tr>
                                <td class="text-muted">#{{ $profile->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar rounded-circle bg-primary text-white me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            {{ substr($profile->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $profile->user->name }}</div>
                                            <div class="text-muted small">{{ $profile->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($profile->subjects && count($profile->subjects) > 0)
                                        @foreach(array_slice($profile->subjects, 0, 2) as $subject)
                                            <span class="badge bg-light text-dark">{{ $subject }}</span>
                                        @endforeach
                                        @if(count($profile->subjects) > 2)
                                            <span class="badge bg-light text-muted">+{{ count($profile->subjects) - 2 }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $profile->experience_years ?? 0 }} năm</td>
                                <td>
                                    @if($profile->hourly_rate_min && $profile->hourly_rate_max)
                                        {{ number_format($profile->hourly_rate_min, 0, ',', '.') }} - {{ number_format($profile->hourly_rate_max, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">Chưa thiết lập</span>
                                    @endif
                                </td>
                                <td>
                                    @if($profile->is_approved)
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $profile->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tutor-profiles.show', $profile->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if(!$profile->is_approved)
                                        <form action="{{ route('admin.tutor-profiles.approve', $profile->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Duyệt">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.tutor-profiles.unapprove', $profile->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Hủy duyệt">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        @endif

                                        <form action="{{ route('admin.tutor-profiles.destroy', $profile->id) }}" 
                                              method="POST" class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Không tìm thấy hồ sơ nào
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($profiles->hasPages())
                <div class="table-footer">
                    {{ $profiles->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
