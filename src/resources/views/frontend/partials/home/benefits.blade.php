@php
$benefits = [
    [
        'icon' => 'bi-patch-check',
        'title' => 'Gia sư được xác minh',
        'description' => 'Tất cả gia sư đều qua kiểm tra CCCD và chứng chỉ'
    ],
    [
        'icon' => 'bi-gift',
        'title' => 'Học thử miễn phí',
        'description' => 'Buổi học đầu tiên miễn phí để bạn trải nghiệm'
    ],
    [
        'icon' => 'bi-shield-check',
        'title' => 'Thanh toán an toàn',
        'description' => 'Hệ thống thanh toán được bảo mật tuyệt đối'
    ],
    [
        'icon' => 'bi-headset',
        'title' => 'Hỗ trợ 24/7',
        'description' => 'Đội ngũ hỗ trợ luôn sẵn sàng giúp đỡ bạn'
    ],
    [
        'icon' => 'bi-calendar-check',
        'title' => 'Lịch học linh hoạt',
        'description' => 'Tự do sắp xếp thời gian học phù hợp với bạn'
    ],
    [
        'icon' => 'bi-star',
        'title' => 'Chất lượng đảm bảo',
        'description' => 'Hoàn tiền 100% nếu không hài lòng'
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
