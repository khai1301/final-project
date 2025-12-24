@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">
        <span class="material-symbols-outlined align-middle me-2">swap_horiz</span>
        My Connection Requests
    </h2>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sent" type="button">
                Sent Requests
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#received" type="button">
                Received Requests
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
                                $avatarUrl = $other->avatar 
                                    ? \Storage::disk('s3')->url($other->avatar) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($other->name).'&size=80';
                            @endphp
                            <img src="{{ $avatarUrl }}" class="rounded-circle" width="80" height="80" alt="{{ $other->name }}">
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">{{ $other->name }}</h5>
                            @php
                                // Hide email for all accepted connections that are not unlocked
                                // Show email for pending/declined/cancelled (no privacy needed)
                                $shouldHideEmail = in_array($request->status, ['accepted']) && !$request->contact_unlocked;
                            @endphp
                            @if($shouldHideEmail)
                                <p class="text-muted small mb-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                                    Email bị khóa
                                </p>
                            @else
                                <p class="text-muted small mb-1">{{ $other->email }}</p>
                            @endif
                            @if($request->message)
                            <p class="mb-0"><strong>Tin nhắn:</strong> {{ $request->message }}</p>
                            @endif
                            <small class="text-muted">Gửi {{ $request->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-2">
                            @if($request->status == 'pending')
                                <span class="badge bg-warning">⏳ Pending</span>
                            @elseif($request->status == 'accepted')
                                <span class="badge bg-success">✓ Accepted</span>
                            @elseif($request->status == 'declined')
                                <span class="badge bg-danger">✗ Declined</span>
                                @if($request->decline_reason)
                                <p class="small mt-2 mb-0"><strong>Reason:</strong> {{ $request->decline_reason }}</p>
                                @endif
                            @elseif($request->status == 'cancelled')
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            @if($request->status == 'pending')
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $request->id }}">
                                Hủy
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
                                            Đã Mở Khóa
                                        </button>
                                    @else
                                        <form action="{{ route('contact.unlock', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                                                Mở Khóa<br>
                                                <small>({{ number_format(\App\Models\Setting::get('contact_unlock_fee', 50000)) }}đ)</small>
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
                                <h5 class="modal-title">Cancel Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Please provide a reason for cancelling this request:</p>
                                <textarea name="reason" class="form-control" rows="3" required minlength="10" maxlength="500" placeholder="Your reason (minimum 10 characters)"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Cancel Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <span class="material-symbols-outlined align-middle me-2">info</span>
                You haven't sent any connection requests yet.
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
                                $avatarUrl = $request->sender->avatar 
                                    ? \Storage::disk('s3')->url($request->sender->avatar) 
                                    : 'https://ui-avatars.com/api/?name='.urlencode($request->sender->name).'&size=80';
                            @endphp
                            <img src="{{ $avatarUrl }}" class="rounded-circle" width="80" height="80" alt="{{ $request->sender->name }}">
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-1">{{ $request->sender->name }}</h5>
                            @if($request->status === 'accepted' && !$request->contact_unlocked)
                                <p class="text-muted small mb-1">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                                    Email bị khóa
                                </p>
                            @else
                                <p class="text-muted small mb-1">{{ $request->sender->email }}</p>
                            @endif
                            @if($request->message)
                            <p class="mb-0"><strong>Message:</strong> {{ Str::limit($request->message, 50) }}</p>
                            @endif
                            <small class="text-muted">Received {{ $request->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-2">
                            @if($request->status == 'pending')
                                <span class="badge bg-warning">⏳ Đang Chờ</span>
                            @elseif($request->status == 'accepted')
                                <span class="badge bg-success">✓ Đã Chấp Nhận</span>
                            @elseif($request->status == 'declined')
                                <span class="badge bg-danger">✗ Đã Từ Chối</span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-info mb-2 w-100" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $request->id }}">
                                <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                Chi Tiết
                            </button>
                            @if($request->status == 'pending')
                            <div class="d-flex gap-2">
                                <form action="{{ route('matching.accept', $request->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success w-100">Chấp Nhận</button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger flex-fill" data-bs-toggle="modal" data-bs-target="#declineModal{{ $request->id }}">
                                    Từ Chối
                                </button>
                            </div>
                            @elseif($request->status == 'accepted' && auth()->user()->role === 'tutor')
                                {{-- Show unlock button for tutors --}}
                                @if($request->contact_unlocked)
                                    <button class="btn btn-sm btn-success w-100" disabled>
                                        <span class="material-symbols-outlined" style="font-size: 16px;">lock_open</span>
                                        Đã Mở Khóa
                                    </button>
                                @else
                                    <form action="{{ route('contact.unlock', $request->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">lock</span>
                                            Mở Khóa ({{ number_format(\App\Models\Setting::get('contact_unlock_fee', 50000)) }} đ)
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
                                Request Details
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
                                            Message
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
                                                About
                                            </h6>
                                            <p class="text-muted">{{ $tutorProfile->bio }}</p>
                                        </div>
                                        @endif
                                        
                                        {{-- Education --}}
                                        @if($tutorProfile->education)
                                        <div class="mb-3">
                                            <h6 class="fw-bold">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">school</span>
                                                Education
                                            </h6>
                                            <p class="text-muted">{{ $tutorProfile->education }}</p>
                                        </div>
                                        @endif
                                        
                                        {{-- Experience & Rate --}}
                                        <div class="mb-3">
                                            <h6 class="fw-bold">Professional Details</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Experience:</strong> {{ $tutorProfile->experience_years ?? 0 }} years</p>
                                                </div>
                                                @if($tutorProfile->hourly_rate_min && $tutorProfile->hourly_rate_max)
                                                <div class="col-6">
                                                    <p class="mb-1"><strong>Rate:</strong> {{ number_format($tutorProfile->hourly_rate_min / 1000) }}k - {{ number_format($tutorProfile->hourly_rate_max / 1000) }}k ₫/hr</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Subjects --}}
                                        @if($tutorProfile->subjects && $tutorProfile->subjects->count() > 0)
                                        <div class="mb-3">
                                            <h6 class="fw-bold">
                                                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">school</span>
                                                Subjects
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
                                            Thông Tin Liên Hệ
                                        </h6>
                                        @if($request->status === 'accepted' && !$request->contact_unlocked)
                                            {{-- Locked for both tutor and student --}}
                                            <div class="alert alert-warning">
                                                <p class="mb-2">
                                                    <span class="material-symbols-outlined align-middle">lock</span>
                                                    Thông tin liên hệ đã bị khóa
                                                </p>
                                                <p class="small text-muted mb-0">
                                                    @if(auth()->user()->role === 'tutor')
                                                        Thanh toán phí mở khóa ({{ number_format(\App\Models\Setting::get('contact_unlock_fee', 50000)) }} VNĐ) để xem email và số điện thoại của học sinh.
                                                    @else
                                                        Chờ gia sư thanh toán phí mở khóa để xem thông tin liên hệ.
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            {{-- Show contact info --}}
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
                                    Decline
                                </button>
                                <form action="{{ route('matching.accept', $request->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                                        Accept Request
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
                                <h5 class="modal-title">Decline Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Please provide a reason for declining this request:</p>
                                <textarea name="reason" class="form-control" rows="3" required minlength="10" maxlength="500" placeholder="Your reason (minimum 10 characters)"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Decline Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <span class="material-symbols-outlined align-middle me-2">info</span>
                You haven't received any connection requests yet.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
