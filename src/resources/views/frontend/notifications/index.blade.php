@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <span class="material-symbols-outlined align-middle me-2">notifications</span>
                    Notifications
                </h2>
                @if($notifications->total() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <span class="material-symbols-outlined align-middle" style="font-size: 18px;">done_all</span>
                        Mark All as Read
                    </button>
                </form>
                @endif
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Notifications List --}}
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                    <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                @if($notification->type == 'connect_request')
                                    <span class="material-symbols-outlined text-primary fs-2">person_add</span>
                                @elseif($notification->type == 'connect_accepted')
                                    <span class="material-symbols-outlined text-success fs-2">check_circle</span>
                                @elseif($notification->type == 'connect_declined')
                                    <span class="material-symbols-outlined text-danger fs-2">cancel</span>
                                @else
                                    <span class="material-symbols-outlined text-secondary fs-2">notifications</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                            {{ $notification->title }}
                                        </h6>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-muted">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 16px;">schedule</span>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="ms-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as read">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">done</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-5">
                        <span class="material-symbols-outlined fs-1 text-muted d-block mb-2">notifications_off</span>
                        <p class="text-muted mb-0">No notifications yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
