@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <span class="material-symbols-outlined align-middle me-2">link</span>
                {{ __('ui.connected') }}
            </h2>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Matchings List --}}
    <div class="row g-4">
        @forelse($matchings as $matching)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $otherUser = $matching->getOtherUser(auth()->id());
                            @endphp
                            @php
                                $avatarUrl = $otherUser->avatar ? \Storage::disk('s3')->url($otherUser->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->name) . '&size=200&background=3780f6&color=fff';
                            @endphp
                            <img src="{{ $avatarUrl }}" 
                                 alt="{{ $otherUser->name }}" class="rounded-circle" width="60" height="60">
                            <div>
                                <h5 class="mb-1">{{ $otherUser->name }}</h5>
                                @if($matching->contact_unlocked)
                                    <p class="text-muted small mb-0">{{ $otherUser->email }}</p>
                                    @if($otherUser->phone)
                                        <p class="text-muted small mb-0">{{ $otherUser->phone }}</p>
                                    @endif
                                @else
                                    <p class="text-muted small mb-0">
                                        <span class="material-symbols-outlined align-middle" style="font-size: 14px;">lock</span>
                                        {{ __('ui.email_locked') }}
                                    </p>
                                @endif
                                <span class="badge bg-{{ $otherUser->isStudent() ? 'info' : 'success' }}-subtle text-{{ $otherUser->isStudent() ? 'info' : 'success' }}">
                                    {{ $otherUser->isStudent() ? __('ui.student') : __('ui.tutor') }}
                                </span>
                            </div>
                        </div>
                        <div>
                            @if($matching->status == 'pending')
                                <span class="badge bg-warning">{{ __('ui.pending_status') }}</span>
                            @elseif($matching->status == 'accepted')
                                <span class="badge bg-success">{{ __('ui.connected') }}</span>
                            @elseif($matching->status == 'declined')
                                <span class="badge bg-danger">{{ __('ui.declined_status') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($matching->status) }}</span>
                            @endif
                        </div>
                    </div>

                    @if($matching->message)
                    <div class="bg-light p-2 rounded mb-3">
                        <small class="text-muted">{{ $matching->message }}</small>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ $matching->created_at->diffForHumans() }}
                        </small>
                        
                        @if($matching->status == 'pending')
                            @if($matching->isSender(auth()->id()))
                                {{-- User sent this request, can cancel --}}
                                <form action="{{ route('matching.cancel', $matching->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hủy yêu cầu này?')">
                                        {{ __('ui.cancel') }}
                                    </button>
                                </form>
                            @else
                                {{-- User received this request, can accept/decline --}}
                                <div class="d-flex gap-2">
                                    <form action="{{ route('matching.accept', $matching->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">{{ __('ui.accept') }}</button>
                                    </form>
                                    <form action="{{ route('matching.decline', $matching->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('ui.decline') }}</button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <span class="material-symbols-outlined align-middle me-2">info</span>
                Chưa có kết nối nào. Hãy bắt đầu kết nối với gia sư hoặc học sinh!
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($matchings->hasPages())
    <div class="mt-4">
        {{ $matchings->links() }}
    </div>
    @endif
</div>
@endsection
