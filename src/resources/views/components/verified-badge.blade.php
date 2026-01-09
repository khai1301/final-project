@props(['user', 'size' => 18])

@if($user)
    @if($user->is_verified)
        <span class="verified-badge" title="Đã xác thực CCCD" {{ $attributes }}>
            <span class="material-symbols-outlined text-success align-middle" style="font-size: {{ $size }}px;">verified</span>
        </span>
    @elseif($user->isTutor())
        <span class="unverified-badge" title="Chưa xác thực CCCD" {{ $attributes }}>
            <span class="material-symbols-outlined text-warning align-middle" style="font-size: {{ $size }}px;">error</span>
        </span>
    @endif
@endif
