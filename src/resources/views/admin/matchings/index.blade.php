@extends('admin.layouts.app')

@section('title', 'Matchings Management')
@section('subtitle', 'View and manage all student-tutor connections')

@section('content')
<div class="container-fluid py-4">
    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Matchings</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <span class="material-symbols-outlined fs-1 text-primary">link</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Pending</p>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <span class="material-symbols-outlined fs-1 text-warning">schedule</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Accepted</p>
                            <h3 class="mb-0">{{ $stats['accepted'] }}</h3>
                        </div>
                        <span class="material-symbols-outlined fs-1 text-success">check_circle</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Declined</p>
                            <h3 class="mb-0">{{ $stats['declined'] }}</h3>
                        </div>
                        <span class="material-symbols-outlined fs-1 text-danger">cancel</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.matchings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sender Role</label>
                    <select name="sender_role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="student" {{ request('sender_role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="tutor" {{ request('sender_role') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <span class="material-symbols-outlined align-middle">search</span>
                        Filter
                    </button>
                    <a href="{{ route('admin.matchings.index') }}" class="btn btn-outline-secondary">
                        <span class="material-symbols-outlined align-middle">refresh</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Matchings Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Tutor</th>
                            <th>Sender</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matchings as $matching)
                        <tr>
                            <td>#{{ $matching->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($matching->student->name) }}&size=32" 
                                         class="rounded-circle" width="32" height="32">
                                    <div>
                                        <div class="fw-medium">{{ $matching->student->name }}</div>
                                        <small class="text-muted">{{ $matching->student->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($matching->tutor->name) }}&size=32" 
                                         class="rounded-circle" width="32" height="32">
                                    <div>
                                        <div class="fw-medium">{{ $matching->tutor->name }}</div>
                                        <small class="text-muted">{{ $matching->tutor->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $matching->sender->isStudent() ? 'info' : 'success' }}-subtle text-{{ $matching->sender->isStudent() ? 'info' : 'success' }}">
                                    {{ $matching->sender->isStudent() ? 'Student' : 'Tutor' }}
                                </span>
                            </td>
                            <td>
                                @if($matching->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($matching->status == 'accepted')
                                    <span class="badge bg-success">Accepted</span>
                                @elseif($matching->status == 'declined')
                                    <span class="badge bg-danger">Declined</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($matching->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $matching->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $matching->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.matchings.show', $matching->id) }}" class="btn btn-sm btn-outline-primary">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <span class="material-symbols-outlined fs-1 text-muted d-block mb-2">search_off</span>
                                <p class="text-muted mb-0">No matchings found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($matchings->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $matchings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
