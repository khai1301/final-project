{{-- How It Works Section --}}
<section class="home-how-it-works">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mx-auto mb-5" style="max-width: 48rem;">
            <h2 class="home-section-title">{{ __('ui.how_it_works') }}</h2>
            <p class="home-section-description">{{ __('ui.three_simple_steps') }}</p>
        </div>

        {{-- Process Steps --}}
        <div class="position-relative">
            {{-- Connecting Line (Desktop Only) --}}
            <div class="home-process-line d-none d-md-block"></div>

            <div class="row g-5">
                {{-- Step 1: Create Profile --}}
                <div class="col-md-4">
                    <div class="home-process-step">
                        <div class="home-process-icon-wrapper blue">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>
                        <h3 class="home-process-title">Bước 1. Tạo hồ sơ</h3>
                        <p class="home-process-description">
                            Đăng ký tài khoản và cập nhật đầy đủ thông tin, bằng cấp & kinh nghiệm giảng dạy của bạn.
                        </p>
                    </div>
                </div>

                {{-- Step 2: Get Matched --}}
                <div class="col-md-4">
                    <div class="home-process-step">
                        <div class="home-process-icon-wrapper purple">
                            <span class="material-symbols-outlined">auto_awesome</span>
                        </div>
                        <h3 class="home-process-title">Bước 2. AI gợi ý yêu cầu từ học sinh</h3>
                        <p class="home-process-description">
                            Hệ thống AI sẽ phân tích và tự động gợi ý các yêu cầu học tập phù hợp nhất với hồ sơ của bạn.
                        </p>
                    </div>
                </div>

                {{-- Step 3: Start Teaching --}}
                <div class="col-md-4">
                    <div class="home-process-step">
                        <div class="home-process-icon-wrapper green">
                            <span class="material-symbols-outlined">video_chat</span>
                        </div>
                        <h3 class="home-process-title">Bước 3. Kết nối & Dạy</h3>
                        <p class="home-process-description">
                            Chủ động kết nối với học viên, thống nhất lịch học và bắt đầu hành trình chia sẻ kiến thức.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
