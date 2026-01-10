{{-- Stats Section --}}
<section class="home-stats">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="home-stat-item">
                    <div class="home-stat-number">{{ $totalStudents }}+</div>
                    <div class="home-stat-label">{{__('ui.total_students')}} {{ __('ui.active') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="home-stat-item">
                    <div class="home-stat-number">{{ $totalTutors }}+</div>
                    <div class="home-stat-label">{{ __('ui.total_tutors') }} {{ __('ui.verified') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="home-stat-item">
                    <div class="home-stat-number">{{ $totalSubjects }}+</div>
                    <div class="home-stat-label">{{ __('ui.total_subjects') }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="home-stat-item">
                    <div class="home-stat-number">{{ $totalAcceptedMatches }}+</div>
                    <div class="home-stat-label">Kết nối thành công</div>
                </div>
            </div>
        </div>
    </div>
</section>
