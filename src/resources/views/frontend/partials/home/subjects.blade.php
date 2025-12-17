@php
$subjects = [
    ['name' => 'Toán', 'icon' => '📐'],
    ['name' => 'Tiếng Anh', 'icon' => '🗣️'],
    ['name' => 'Vật Lý', 'icon' => '🔬'],
    ['name' => 'Hóa Học', 'icon' => '⚗️'],
    ['name' => 'Sinh Học', 'icon' => '🧬'],
    ['name' => 'Ngữ Văn', 'icon' => '📖'],
    ['name' => 'Lịch Sử', 'icon' => '🏛️'],
    ['name' => 'Địa Lý', 'icon' => '🌍'],
    ['name' => 'Lập trình', 'icon' => '💻'],
    ['name' => 'Âm nhạc', 'icon' => '🎵'],
    ['name' => 'Nghệ thuật', 'icon' => '🎨'],
    ['name' => 'Thể thao', 'icon' => '⚽'],
];
@endphp

<div class="row g-3">
    @foreach($subjects as $subject)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="subject-card">
            <div class="subject-icon">{{ $subject['icon'] }}</div>
            <div class="subject-name">{{ $subject['name'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="text-center mt-4">
    <a href="#" class="btn btn-outline-primary">Xem tất cả môn học →</a>
</div>
