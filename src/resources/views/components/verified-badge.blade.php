@props(['user'])

@if($user && $user->is_verified)
<span class="verified-badge" title="Đã xác thực CCCD" {{ $attributes }}>
    <span class="material-symbols-outlined text-success align-middle" style="font-size: 18px;">verified</span>
</span>
@endif
