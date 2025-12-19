@extends('admin.layouts.app')

@section('title', 'Learning Requests Management')
@section('subtitle', 'Manage student learning requests')

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
                                <option value="">All Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked</option>
                                <option value="matched" {{ request('status') == 'matched' ? 'selected' : '' }}>Matched</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="mode" class="form-select">
                                <option value="">All Modes</option>
                                <option value="online" {{ request('mode') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ request('mode') == 'offline' ? 'selected' : '' }}>In-Person</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="education_level" class="form-select">
                                <option value="">All Education Levels</option>
                                <option value="Elementary" {{ request('education_level') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                                <option value="Middle School" {{ request('education_level') == 'Middle School' ? 'selected' : '' }}>Middle School</option>
                                <option value="High School" {{ request('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                                <option value="Undergraduate" {{ request('education_level') == 'Undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                                <option value="Postgraduate" {{ request('education_level') == 'Postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                                <option value="Professional Certification" {{ request('education_level') == 'Professional Certification' ? 'selected' : '' }}>Professional</option>
                                <option value="Hobby / Casual" {{ request('education_level') == 'Hobby / Casual' ? 'selected' : '' }}>Hobby</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
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
                        <h3>Learning Requests</h3>
                        <p>Total requests: {{ $requests->total() }}</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Education Level</th>
                                <th>Mode</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td><span class="badge bg-light text-dark">#{{ $request->id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($request->student->name) }}&background=random&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <div class="fw-medium text-dark">{{ $request->student->name }}</div>
                                            <div class="text-muted small">{{ $request->student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $request->subject }}</div>
                                    @if($request->skills && count($request->skills) > 0)
                                        <div class="text-muted small">
                                            {{ implode(', ', array_slice($request->skills, 0, 2)) }}
                                            @if(count($request->skills) > 2)
                                                <span class="badge bg-light text-muted">+{{ count($request->skills) - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $request->education_level }}</td>
                                <td>
                                    @if($request->mode === 'online')
                                        <span class="badge bg-info-light text-info">
                                            <i class="bi bi-laptop me-1"></i>Online
                                        </span>
                                    @else
                                        <span class="badge bg-success-light text-success">
                                            <i class="bi bi-geo-alt me-1"></i>In-Person
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-success">${{ number_format($request->budget_min, 0) }} - ${{ number_format($request->budget_max, 0) }}</div>
                                    <small class="text-muted">/hr</small>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'open' => 'status-badge status-pending',
                                            'locked' => 'status-badge status-warning',
                                            'matched' => 'status-badge status-approved',
                                            'closed' => 'status-badge status-rejected',
                                            'cancelled' => 'status-badge status-rejected'
                                        ];
                                    @endphp
                                    <span class="{{ $statusClasses[$request->status] ?? 'badge bg-secondary' }}">
                                        {{ ucfirst($request->status) }}
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
                                                    <i class="bi bi-eye me-2 text-info"></i> View Details
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.requests.destroy', $request->id) }}" 
                                                      method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i> Delete
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
                                    No learning requests found matching your filters.
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
