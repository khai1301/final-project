@extends('admin.layouts.app')

@section('title', 'User Management')
@section('subtitle', 'Manage all registered users')

@section('content')
<div class="container-fluid py-4">
    <!-- Filters & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                       placeholder="Search by name, email, phone..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">All Roles</option>
                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="tutor" {{ request('role') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
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

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="data-table mb-4">
                <div class="table-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Users List</h3>
                        <p>Total users: {{ $users->total() }}</p>
                    </div>
                    <!-- Create Button Toggle Modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="bi bi-plus-lg me-1"></i> Add New User
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Info</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" 
                                             alt="Avatar" class="rounded-circle me-2" width="40" height="40">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="text-muted small">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-dark">Admin</span>
                                    @elseif($user->isTutor())
                                        <span class="badge bg-purple-light text-purple">Tutor</span>
                                        @if($user->tutorProfile && !$user->tutorProfile->is_approved)
                                            <span class="badge bg-warning text-dark" title="Pending Approval">Pending</span>
                                        @endif
                                    @else
                                        <span class="badge bg-primary-light text-primary">Student</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->phone)
                                        <div><i class="bi bi-phone me-1"></i>{{ $user->phone }}</div>
                                    @else
                                        <span class="text-muted small">No phone</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isBanned())
                                        <span class="status-badge status-rejected">Banned</span>
                                    @else
                                        <span class="status-badge status-approved">Active</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <!-- Edit Action (Opens Modal) -->
                                            <li>
                                                <button class="dropdown-item view-user-btn" 
                                                    data-id="{{ $user->id }}"
                                                >
                                                    <i class="bi bi-eye me-2 text-info"></i> View Details
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUserModal"
                                                    data-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}"
                                                    data-role="{{ $user->role }}"
                                                >
                                                    <i class="bi bi-pencil me-2 text-primary"></i> Edit
                                                </button>
                                            </li>
                                            
                                            @if($user->isTutor() && $user->tutorProfile && !$user->tutorProfile->is_approved)
                                            <li>
                                                <form action="{{ route('admin.users.approve-tutor', $user) }}" method="POST" class="approve-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-check-circle me-2 text-success"></i> Approve Tutor
                                                    </button>
                                                </form>
                                            </li>
                                            @endif

                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <li>
                                                <form action="{{ route('admin.users.toggle-ban', $user) }}" method="POST" class="ban-form" data-banned="{{ $user->isBanned() ? '1' : '0' }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if($user->isBanned())
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="bi bi-unlock me-2"></i> Unban User
                                                    </button>
                                                    @else
                                                    <button type="submit" class="dropdown-item text-warning">
                                                        <i class="bi bi-lock me-2"></i> Ban User
                                                    </button>
                                                    @endif
                                                </form>
                                            </li>

                                            <li>
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form">
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
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-search display-6 d-block mb-2"></i>
                                    No users found matching your filters.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($users->hasPages())
                <div class="p-3 border-top">
                    {{ $users->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Added autocomplete="off" and "new-password" to prevent browser auto-fill -->
            <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="form_id" value="create_user">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" autocomplete="off" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" autocomplete="off" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone') }}" autocomplete="off">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="tutor" {{ old('role') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Hack to prevent auto-filling in Chrome -->
                        <input type="password" style="display:none">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" autocomplete="new-password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_id" value="edit_user">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="edit_name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="edit_email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="edit_phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="edit_role" name="role" required>
                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="tutor" {{ old('role') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hack to prevent auto-filling in Chrome -->
                        <input type="password" style="display:none">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password (Optional)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" placeholder="Leave blank to keep current" autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <!-- Loading State -->
                <div id="view_loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Content (Hidden by default) -->
                <div id="view_content" class="d-none">
                    <div class="text-center mb-4">
                        <img id="view_avatar" src="" alt="Avatar" class="rounded-circle mb-3" width="100" height="100">
                        <h4 id="view_name" class="fw-bold mb-1"></h4>
                        <span id="view_role_badge" class="badge bg-primary rounded-pill mb-2"></span>
                        <div class="text-muted small" id="view_email"></div>
                    </div>

                <ul class="nav nav-tabs nav-fill mb-4" id="viewUserTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">Profile Details</button>
                    </li>
                </ul>

                <div class="tab-content" id="viewUserTabContent">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-bold mb-2">Contact Info</div>
                                    <div class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i> <span id="view_email_text"></span></div>
                                    <div class="mb-2"><i class="bi bi-phone me-2 text-muted"></i> <span id="view_phone"></span></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <div class="small text-muted text-uppercase fw-bold mb-2">Account Status</div>
                                    <div class="mb-2">Status: <span id="view_status"></span></div>
                                    <div class="mb-2">Joined: <span id="view_joined"></span></div>
                                    <div class="mb-2">Verified: <span id="view_verified"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details Tab -->
                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <!-- Student Section -->
                        <div id="student_details" class="d-none">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted small">School</label>
                                    <div id="view_student_school" class="fw-medium"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted small">Grade</label>
                                    <div id="view_student_grade" class="fw-medium"></div>
                                </div>
                                <div class="col-12">
                                    <label class="fw-bold text-muted small">Goals</label>
                                    <div id="view_student_goals" class="p-3 bg-light rounded-3 text-break"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Tutor Section -->
                        <div id="tutor_details" class="d-none">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="fw-bold text-muted small">Bio</label>
                                    <div id="view_tutor_bio" class="p-3 bg-light rounded-3 text-break mb-3"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted small">Hourly Rate</label>
                                    <div class="fw-medium"><span id="view_tutor_rate"></span></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted small">Experience</label>
                                    <div id="view_tutor_experience" class="fw-medium"></div>
                                </div>
                                <div class="col-12">
                                    <label class="fw-bold text-muted small">Subjects</label>
                                    <div id="view_tutor_subjects" class="d-flex flex-wrap gap-2 mt-1"></div>
                                </div>
                                <div class="col-12">
                                    <label class="fw-bold text-muted small">Education</label>
                                    <div id="view_tutor_education" class="text-break"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="no_profile_details" class="text-center py-4 text-muted d-none">
                            <i class="bi bi-person-badge display-6 mb-3 d-block"></i>
                            <p>No specific profile information available for this user role.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pass server-side data to window for users.js
    @if ($errors->any())
        window.hasErrors = true;
        window.oldFormId = "{{ old('form_id') }}";
    @endif
</script>
@endpush
@endsection
