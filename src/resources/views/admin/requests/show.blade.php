@extends('admin.layouts.app')

@section('title', 'Request Details')
@section('subtitle', 'View learning request information')

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

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.requests.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i> Back to Requests
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $learningRequest->title }}</h4>
                            <small class="text-muted">Request #{{ $learningRequest->id }}</small>
                        </div>
                        @php
                            $statusClasses = [
                                'open' => 'status-badge status-pending',
                                'locked' => 'status-badge status-warning',
                                'matched' => 'status-badge status-approved',
                                'closed' => 'status-badge status-rejected',
                                'cancelled' => 'status-badge status-rejected'
                            ];
                        @endphp
                        <span class="{{ $statusClasses[$learningRequest->status] ?? 'badge bg-secondary' }}">
                            {{ ucfirst($learningRequest->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Subject Info -->
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Subject Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">Subject</label>
                                <div class="fw-medium">{{ $learningRequest->subjectRelation->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">Education Level</label>
                                <div class="fw-medium">{{ $learningRequest->educationLevelRelation->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Skills -->
                    @if($learningRequest->skills && count($learningRequest->skills) > 0)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Specific Skills/Topics</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($learningRequest->skills as $skill)
                                <span class="badge bg-primary-light text-primary px-3 py-2">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Learning Mode & Location -->
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Logistics</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">Learning Mode</label>
                                <div>
                                    @if($learningRequest->learningModeRelation)
                                        <span class="badge bg-info-light text-info">
                                            <i class="bi {{ $learningRequest->learningModeRelation->icon ?? 'bi-book' }} me-1"></i>
                                            {{ $learningRequest->learningModeRelation->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold text-muted small">Student Location</label>
                                <div class="fw-medium">
                                    <i class="bi bi-geo-alt-fill text-muted me-1"></i>
                                    @if($learningRequest->student->province_id)
                                        {{ $learningRequest->student->province->name ?? 'N/A' }}
                                        @if($learningRequest->student->ward_id)
                                            → {{ $learningRequest->student->ward->name ?? '' }}
                                        @endif
                                        @if($learningRequest->student->address_detail)
                                            <br><small class="text-muted ms-4">{{ $learningRequest->student->address_detail }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slots -->
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Preferred Schedule</h5>
                        @if($learningRequest->timeSlots && $learningRequest->timeSlots->count() > 0)
                            @foreach($learningRequest->timeSlots->groupBy('day_of_week') as $dayNum => $slots)
                                <div class="mb-3">
                                    <h6 class="text-primary small fw-bold">
                                        <i class="bi bi-calendar-day me-1"></i>
                                        {{ $slots->first()->getDayName() }}
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($slots as $slot)
                                            <span class="badge bg-light text-dark border px-3 py-2">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ date('H:i', strtotime($slot->start_time)) }} - {{ date('H:i', strtotime($slot->end_time)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">No time slots selected</p>
                        @endif
                    </div>

                    <!-- Budget -->
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Budget</h5>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-stack text-success me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold text-success fs-4">
                                        ${{ number_format($learningRequest->budget_min, 0) }} - ${{ number_format($learningRequest->budget_max, 0) }}
                                    </div>
                                    <small class="text-muted">per hour</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description/Notes -->
                    @if($learningRequest->description)
                    <div class="mb-4">
                        <h5 class="text-muted text-uppercase small fw-bold mb-3">Additional Notes</h5>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0 text-break">{{ $learningRequest->description }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Metadata -->
                    <div class="border-top pt-3 mt-4">
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <i class="bi bi-calendar me-1"></i>
                                Created: {{ $learningRequest->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                            <div class="col-md-6">
                                <i class="bi bi-clock-history me-1"></i>
                                Updated: {{ $learningRequest->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Student Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($learningRequest->student->name) }}&size=80&background=random&color=fff" 
                             alt="Avatar" class="rounded-circle mb-2" width="80" height="80">
                        <h5 class="fw-bold mb-1">{{ $learningRequest->student->name }}</h5>
                        <span class="badge bg-primary-light text-primary">Student</span>
                    </div>
                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <i class="bi bi-envelope text-muted me-2"></i>
                            <small>{{ $learningRequest->student->email }}</small>
                        </div>
                        @if($learningRequest->student->phone)
                        <div class="mb-2">
                            <i class="bi bi-phone text-muted me-2"></i>
                            <small>{{ $learningRequest->student->phone }}</small>
                        </div>
                        @endif
                        <div>
                            <i class="bi bi-calendar text-muted me-2"></i>
                            <small>Joined {{ $learningRequest->student->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Management Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Manage Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.requests.update-status', $learningRequest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-medium">Change Status</label>
                            <select name="status" class="form-select" required>
                                <option value="open" {{ $learningRequest->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="locked" {{ $learningRequest->status === 'locked' ? 'selected' : '' }}>Locked</option>
                                <option value="matched" {{ $learningRequest->status === 'matched' ? 'selected' : '' }}>Matched</option>
                                <option value="closed" {{ $learningRequest->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="cancelled" {{ $learningRequest->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-1"></i>
                            Update Status
                        </button>
                    </form>

                    <div class="mt-3 p-2 bg-light rounded small text-muted">
                        <strong>Status Guide:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            <li><strong>Open:</strong> Pending assignment</li>
                            <li><strong>Locked:</strong> Being processed</li>
                            <li><strong>Matched:</strong> Tutor assigned</li>
                            <li><strong>Closed:</strong> Completed</li>
                            <li><strong>Cancelled:</strong> Request cancelled</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
