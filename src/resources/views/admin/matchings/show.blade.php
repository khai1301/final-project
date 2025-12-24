@extends('admin.layouts.app')

@section('title', 'Matching Details')
@section('subtitle', 'View connection details')

@section('content')
<div class="container-fluid py-4">
    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ route('admin.matchings.index') }}" class="btn btn-light">
            <span class="material-symbols-outlined align-middle">arrow_back</span>
            Back to Matchings
        </a>
    </div>

    <div class="row">
        {{-- Main Info --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Matching #{{ $matching->id }}</h5>
                </div>
                <div class="card-body">
                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="fw-bold text-muted small">Status</label>
                        <div class="mt-1">
                            @if($matching->status == 'pending')
                                <span class="badge bg-warning fs-6">Pending</span>
                            @elseif($matching->status == 'accepted')
                                <span class="badge bg-success fs-6">Accepted</span>
                            @elseif($matching->status == 'declined')
                                <span class="badge bg-danger fs-6">Declined</span>
                            @else
                                <span class="badge bg-secondary fs-6">{{ ucfirst($matching->status) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Student --}}
                    <div class="mb-4">
                        <label class="fw-bold text-muted small">Student</label>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($matching->student->name) }}&size=50" 
                                 class="rounded-circle" width="50" height="50">
                            <div>
                                <h6 class="mb-0">{{ $matching->student->name }}</h6>
                                <small class="text-muted">{{ $matching->student->email }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Tutor --}}
                    <div class="mb-4">
                        <label class="fw-bold text-muted small">Tutor</label>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($matching->tutor->name) }}&size=50" 
                                 class="rounded-circle" width="50" height="50">
                            <div>
                                <h6 class="mb-0">{{ $matching->tutor->name }}</h6>
                                <small class="text-muted">{{ $matching->tutor->email }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Sender Info --}}
                    <div class="mb-4">
                        <label class="fw-bold text-muted small">Initiated By</label>
                        <div class="mt-1">
                            <span class="badge bg-{{ $matching->sender->isStudent() ? 'info' : 'success' }}-subtle text-{{ $matching->sender->isStudent() ? 'info' : 'success' }}">
                                {{ $matching->sender->name }} ({{ $matching->sender->isStudent() ? 'Student' : 'Tutor' }})
                            </span>
                        </div>
                    </div>

                    {{-- Message --}}
                    @if($matching->message)
                    <div class="mb-4">
                        <label class="fw-bold text-muted small">Message</label>
                        <div class="p-3 bg-light rounded mt-2">
                            {{ $matching->message }}
                        </div>
                    </div>
                    @endif

                    {{-- Timestamps --}}
                    <div class="border-top pt-3 mt-4">
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">calendar_today</span>
                                Created: {{ $matching->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                            <div class="col-md-6">
                                <span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">update</span>
                                Updated: {{ $matching->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Related Notifications --}}
            @if($matching->notifications->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">Related Notifications</h6>
                </div>
                <div class="card-body">
                    @foreach($matching->notifications as $notification)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-symbols-outlined text-muted">notifications</span>
                            <div class="flex-grow-1">
                                <div class="fw-medium small">{{ $notification->title }}</div>
                                <div class="text-muted small">{{ $notification->message }}</div>
                                <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
