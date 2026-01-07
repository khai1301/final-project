@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">
        <span class="material-symbols-outlined align-middle me-2">swap_horiz</span>
        {{ __('ui.my_connection_requests') }}
    </h2>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sent" type="button">
                {{ __('ui.sent_requests') }}
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#received" type="button">
                {{ __('ui.received_requests') }}
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Sent Requests Tab --}}
        <div class="tab-pane fade show active" id="sent">
            @forelse($sentRequests as $request)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @php
                                $other = $request->getOtherUser(auth()->id());
                                $avatarUrl = $other->avatar_url;
                            @endphp
                            <img src="{{ $avatarUrl }}" class="rounded-circle" width="80" height="80" alt="{{ $other->name }}">
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">
                                {{ $other->name }}
                                <x-verified-badge :user="$other" class="ms-1" />
                            </h5>
                            @if(!$request->contact_unlocked)
                                <p class="text-muted small mb-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                                    {{ __('ui.email_locked') }}
                                </p>
                            @else
                                <p class="text-muted small mb-1">{{ $other->email }}</p>
                                @if($other->phone)
                                <p class="text-muted small mb-0">{{ $other->phone }}</p>
                                @endif
                            @endif
                            @if($request->message)
                            <p class="mb-0"><strong>{{ __('ui.message') }}:</strong> {{ $request->message }}</p>
                            @endif
                            <small class="text-muted">{{ __('ui.sent') }} {{ $request->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-2">
                            @if($request->status == 'pending')
                                <span class="badge bg-warning">⏳ {{ __('ui.pending_status') }}</span>
                            @elseif($request->status == 'accepted')
                                <span class="badge bg-success">✓ {{ __('ui.accepted_status') }}</span>
                            @elseif($request->status == 'declined')
                                <span class="badge bg-danger">✗ {{ __('ui.declined_status') }}
                                @if($request->decline_reason)
                                <p class="small mt-2 mb-0"><strong>{{ __('ui.reason') }}:</strong> {{ $request->decline_reason }}</p>
                                @endif
                            @elseif($request->status == 'cancelled')
                                <span class="badge bg-secondary">{{ __('ui.cancelled_status') }}</span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            @if($request->status == 'pending')
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $request->id }}">
                                {{ __('ui.cancel') }}
                            </button>
                            @elseif($request->status == 'accepted')
                                @php
                                    $isTutorViewingStudent = auth()->user()->role === 'tutor' && $other->role === 'student';
                                @endphp
                                @if($isTutorViewingStudent)
                                    {{-- Unlock button for tutors --}}
                                    @if($request->contact_unlocked)
                                        <button class="btn btn-sm btn-success w-100" disabled>
                                            <span class="material-symbols-outlined" style="font-size: 14px;">lock_open</span>
                                            {{ __('ui.unlocked') }}
                                        </button>
                                    @else
                                        <form action="{{ route('payment.unlock', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">lock_open</span>
                                                {{ __('ui.unlock') }}<br>
                                                <small>({{ number_format(\App\Models\Setting::get('contact_unlock_fee', 10000)) }}đ)</small>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cancel Modal --}}
            <div class="modal fade" id="cancelModal{{ $request->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('matching.cancel', $request->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('ui.cancel_request') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ __('ui.provide_cancel_reason') }}</p>
                                <textarea name="reason" class="form-control" rows="3" required minlength="10" maxlength="500" placeholder="{{ __('ui.reason_placeholder') }}"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}</button>
                                <button type="submit" class="btn btn-danger">{{ __('ui.cancel_request') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <span class="material-symbols-outlined align-middle me-2">info</span>
                {{ __('ui.no_sent_requests') }}
            </div>
            @endforelse
        </div>

        {{-- Received Requests Tab --}}
        <div class="tab-pane fade" id="received">
            @forelse($receivedRequests as $request)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @php
                                $avatarUrl = $request->sender->avatar_url;
                            @endphp
                            <img src="{{ $avatarUrl }}" class="rounded-circle" width="80" height="80" alt="{{ $request->sender->name }}">
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">
                                {{ $request->sender->name }}
                                <x-verified-badge :user="$request->sender" class="ms-1" />
                            </h5>
                            @if(!$request->contact_unlocked)
                                <p class="text-muted small mb-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                                    {{ __('ui.email_locked') }}
                                </p>
                            @else
                                <p class="text-muted small mb-1">{{ $request->sender->email }}</p>
                                @if($request->sender->phone)
                                <p class="text-muted small mb-0">{{ $request->sender->phone }}</p>
                                @endif
                            @endif
                            @if($request->message)
                            <p class="mb-0"><strong>{{ __('ui.message_label') }}:</strong> {{ Str::limit($request->message, 50) }}</p>
                            @endif
                            <small class="text-muted">{{ __('ui.received') }} {{ $request->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-2">
                            @if($request->status == 'pending')
                                <span class="badge bg-warning">⏳ {{ __('ui.pending_status') }}</span>
                            @elseif($request->status == 'accepted')
                                <span class="badge bg-success">✓ {{ __('ui.accepted_status') }}</span>
                            @elseif($request->status == 'declined')
                                <span class="badge bg-danger">✗ {{ __('ui.declined_status') }}</span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-info mb-2 w-100" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $request->id }}">
                                <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                {{ __('ui.details') }}
                            </button>
                            @if($request->status == 'pending')
                            <div class="d-flex gap-2">
                                <form action="{{ route('matching.accept', $request->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success w-100">{{ __('ui.accept') }}</button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger flex-fill" data-bs-toggle="modal" data-bs-target="#declineModal{{ $request->id }}">
                                    {{ __('ui.decline') }}
                                </button>
                            </div>
                            @elseif($request->status == 'accepted' && auth()->user()->role === 'tutor')
                                {{-- Show unlock button for tutors --}}
                                @if($request->contact_unlocked)
                                    <span class="badge bg-success">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">lock_open</span>
                                        {{ __('ui.unlocked') }}
                                    </span>
                                @else
                                    <form action="{{ route('payment.unlock', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">lock_open</span>
                                            Mở khóa ({{ number_format(\App\Models\Setting::get('contact_unlock_fee', 10000)) }} đ)
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details Modal --}}
            <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <span class="material-symbols-outlined align-middle me-2">account_circle</span>
                                {{ __('ui.request_details') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                {{-- Profile Picture and Basic Info --}}
                                <div class="col-md-3 text-center mb-3">
                                    @php
                                        $avatarUrl = $request->sender->avatar 
                                            ? \Storage::disk('s3')->url($request->sender->avatar) 
                                            : 'https://ui-avatars.com/api/?name='.urlencode($request->sender->name).'&size=150';
                                    @endphp
                                    <img src="{{ $avatarUrl }}" class="rounded-circle mb-2" width="120" height="120" alt="{{ $request->sender->name }}">
                                    <h5>{{ $request->sender->name }}</h5>
                                    <span class="badge bg-{{ $request->sender->role == 'tutor' ? 'primary' : 'success' }}">
                                        {{ ucfirst($request->sender->role) }}
                                    </span>
                                </div>
                                
                                {{-- Detailed Information --}}
                                <div class="col-md-9">
                                    {{-- Request Message --}}
                                    @if($request->message)
                                    <div class="mb-3">
                                        <h6 class="fw-bold">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">message</span>
                                            {{ __('ui.message_label') }}
                                        </h6>
                                        <p class="text-muted">{{ $request->message }}</p>
                                    </div>
                                    @endif
                                    
                                    {{-- Tutor-specific information --}}
                                    @if($request->sender->role == 'tutor' && $request->sender->tutorProfile)
                                        @php $tutorProfile = $request->sender->tutorProfile; @endphp
                                        
                                        {{-- Bio --}}
                                        @if($tutorProfile->bio)
                                        <div class="mb-3">
                                            <h6 class="fw-bold">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">info</span>
                                                {{ __('ui.about') }}
                                            </h6>
                                            <p class="text-muted">{{ $tutorProfile->bio }}</p>
                                        </div>
                                        @endif
                                        
                                        {{-- Education --}}
                                        @if($tutorProfile->education)
                                        <div class="mb-3">
                                            <h6 class="fw-bold">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">school</span>
                                                {{ __('ui.education') }}
                                            </h6>
                                            <p class="text-muted">{{ $tutorProfile->education }}</p>
                                        </div>
                                        @endif
                                        
                                        {{-- Experience & Rate --}}
                                        <div class="mb-3">
                                           <h6 class="fw-bold">{{ __('ui.professional_details') }}</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>{{ __('ui.experience') }}:</strong> {{ $tutorProfile->experience_years ?? 0 }} {{ __('ui.years') }}</p>
                                                </div>
                                                @if($tutorProfile->hourly_rate_min && $tutorProfile->hourly_rate_max)
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>{{ __('ui.rate') }}:</strong> {{ number_format($tutorProfile->hourly_rate_min / 1000) }}k - {{ number_format($tutorProfile->hourly_rate_max / 1000) }}k ₫/hr</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Subjects --}}
                                        @if($tutorProfile->subjects && $tutorProfile->subjects->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="fw-bold">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">school</span>
                                                {{ __('ui.subjects') }}
                                            </h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($tutorProfile->subjects as $subject)
                                                <span class="badge bg-light text-dark">{{ $subject->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                    
                                    {{-- Contact Information --}}
                                    <div class="mb-3">
                                        <h6 class="fw-bold">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">contact_mail</span>
                                            {{ __('ui.contact_information') }}
                                        </h6>
                                        @if(!$request->contact_unlocked)
                                            {{-- ALWAYS LOCKED until tutor pays unlock fee --}}
                                            <div class="alert alert-warning">
                                                <p class="mb-2">
                                                    <span class="material-symbols-outlined align-middle">lock</span>
                                                    {{ __('ui.contact_locked_info') }}
                                                </p>
                                                <p class="small text-muted mb-0">
                                                    @if(auth()->user()->role === 'tutor')
                                                        @if($request->status === 'accepted')
                                                            {{ __('ui.tutor_unlock_fee_msg', ['fee' => number_format(\App\Models\Setting::get('contact_unlock_fee', 50000))]) }}
                                                        @else
                                                            Chấp nhận yêu cầu và thanh toán phí mở khóa để xem thông tin liên hệ
                                                        @endif
                                                    @else
                                                        {{ __('ui.student_wait_unlock') }}
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            {{-- Show contact info ONLY after unlock --}}
                                            <p class="mb-1">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 16px;">email</span>
                                                {{ $request->sender->email }}
                                            </p>
                                            @if($request->sender->phone)
                                            <p class="mb-1">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 16px;">phone</span>
                                                {{ $request->sender->phone }}
                                            </p>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    {{-- Request Info --}}
                                    <div class="border-top pt-3">
                                        <small class="text-muted">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 14px;">schedule</span>
                                            Sent {{ $request->created_at->diffForHumans() }} ({{ $request->created_at->format('M d, Y H:i') }})
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            @if($request->status == 'pending')
                                <button class="btn btn-outline-danger" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#declineModal{{ $request->id }}">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                    {{ __('ui.decline') }}
                                </button>
                                <form action="{{ route('matching.accept', $request->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                                        {{ __('ui.accept_request') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Decline Modal --}}
            <div class="modal fade" id="declineModal{{ $request->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('matching.decline', $request->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('ui.decline_request') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ __('ui.provide_decline_reason') }}</p>
                                <textarea name="reason" class="form-control" rows="3" required minlength="10" maxlength="500" placeholder="{{ __('ui.reason_placeholder') }}"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('ui.close') }}</button>
                                <button type="submit" class="btn btn-danger">{{ __('ui.decline_request') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <span class="material-symbols-outlined align-middle me-2">info</span>
                {{ __('ui.no_received_requests') }}
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
