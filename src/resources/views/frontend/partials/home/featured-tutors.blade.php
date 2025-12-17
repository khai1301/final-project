@php
$featuredTutors = [
    [
        'id' => 1,
        'name' => 'Nguyễn Văn An',
        'avatar' => 'https://i.pravatar.cc/150?img=12',
        'subjects' => ['Toán', 'Vật Lý'],
        'rating' => 4.9,
        'reviews' => 120,
        'price' => 300000,
        'verified' => true
    ],
    [
        'id' => 2,
        'name' => 'Trần Thị Bình',
        'avatar' => 'https://i.pravatar.cc/150?img=45',
        'subjects' => ['Tiếng Anh'],
        'rating' => 5.0,
        'reviews' => 89,
        'price' => 250000,
        'verified' => true
    ],
    [
        'id' => 3,
        'name' => 'Lê Minh Châu',
        'avatar' => 'https://i.pravatar.cc/150?img=33',
        'subjects' => ['Hóa Học', 'Sinh Học'],
        'rating' => 4.8,
        'reviews' => 156,
        'price' => 280000,
        'verified' => true
    ],
    [
        'id' => 4,
        'name' => 'Phạm Thị Dung',
        'avatar' => 'https://i.pravatar.cc/150?img=47',
        'subjects' => ['Ngữ Văn'],
        'rating' => 4.7,
        'reviews' => 95,
        'price' => 220000,
        'verified' => true
    ],
    [
        'id' => 5,
        'name' => 'Hoàng Văn Em',
        'avatar' => 'https://i.pravatar.cc/150?img=68',
        'subjects' => ['Lập trình', 'Tin học'],
        'rating' => 4.9,
        'reviews' => 142,
        'price' => 350000,
        'verified' => true
    ],
    [
        'id' => 6,
        'name' => 'Vũ Thị Phương',
        'avatar' => 'https://i.pravatar.cc/150?img=38',
        'subjects' => ['Toán', 'Tiếng Anh'],
        'rating' => 4.8,
        'reviews' => 108,
        'price' => 290000,
        'verified' => true
    ],
];
@endphp

<div class="row g-4">
    @foreach($featuredTutors as $tutor)
    <div class="col-md-6 col-lg-4">
        <div class="tutor-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $tutor['avatar'] }}" alt="{{ $tutor['name'] }}" class="tutor-avatar me-3">
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold">
                            {{ $tutor['name'] }}
                            @if($tutor['verified'])
                            <i class="bi bi-patch-check-fill text-primary" title="Đã xác minh"></i>
                            @endif
                        </h5>
                        <div class="tutor-rating">
                            <i class="bi bi-star-fill"></i>
                            {{ $tutor['rating'] }} ({{ $tutor['reviews'] }})
                        </div>
                    </div>
                </div>
                
                <div class="tutor-subjects mb-3">
                    @foreach($tutor['subjects'] as $subject)
                    <span class="subject-badge">{{ $subject }}</span>
                    @endforeach
                </div>
                
                <div class="d-flex align-items-center justify-content-between">
                    <div class="tutor-price">{{ number_format($tutor['price']) }}đ/giờ</div>
                    <a href="#" class="btn btn-outline-primary btn-sm">Xem profile</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="text-center mt-4">
    <a href="#" class="btn btn-primary btn-lg">Xem tất cả gia sư →</a>
</div>
