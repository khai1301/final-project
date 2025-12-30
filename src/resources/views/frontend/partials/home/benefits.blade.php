@php
$benefits = [
    [
        'icon' => 'bi-patch-check',
        'title' => __('ui.verified_tutors'),
        'description' => __('ui.verified_tutors_desc')
    ],
    [
        'icon' => 'bi-gift',
        'title' => __('ui.free_trial'),
        'description' => __('ui.free_trial_desc')
    ],
    [
        'icon' => 'bi-shield-check',
        'title' => __('ui.secure_payment'),
        'description' => __('ui.secure_payment_desc')
    ],
    [
        'icon' => 'bi-headset',
        'title' => __('ui.support_247'),
        'description' => __('ui.support_247_desc')
    ],
    [
        'icon' => 'bi-calendar-check',
        'title' => __('ui.flexible_schedule'),
        'description' => __('ui.flexible_schedule_desc')
    ],
    [
        'icon' => 'bi-star',
        'title' => __('ui.quality_guaranteed'),
        'description' => __('ui.quality_guaranteed_desc')
    ],
];
@endphp

<div class="row g-4">
    @foreach($benefits as $benefit)
    <div class="col-md-6">
        <div class="benefit-item">
            <div class="benefit-icon">
                <i class="{{ $benefit['icon'] }}"></i>
            </div>
            <div class="benefit-content">
                <h5>{{ $benefit['title'] }}</h5>
                <p>{{ $benefit['description'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
