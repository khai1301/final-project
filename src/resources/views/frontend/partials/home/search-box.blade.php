<!-- Quick Search Box -->
<div class="home-search-box">
    <h3 class="text-center mb-4 fw-bold">{{ __('ui.find_suitable_tutor') }}</h3>
    
    <form action="#" method="GET">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="subject">
                    <option value="">{{ __('forms.select_subject') }}</option>
                    <option value="toan">Toán</option>
                    <option value="tieng-anh">Tiếng Anh</option>
                    <option value="vat-ly">Vật Lý</option>
                    <option value="hoa-hoc">Hóa Học</option>
                    <option value="sinh-hoc">Sinh Học</option>
                    <option value="van">Ngữ Văn</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="grade">
                    <option value="">{{ __('forms.select_grade') }}</option>
                    <option value="tieu-hoc">Tiểu học</option>
                    <option value="thcs">THCS</option>
                    <option value="thpt">THPT</option>
                    <option value="dai-hoc">Đại học</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="price">
                    <option value="">{{ __('ui.price_range') }}</option>
                    <option value="0-200">Dưới 200k/giờ</option>
                    <option value="200-300">200k - 300k/giờ</option>
                    <option value="300-500">300k - 500k/giờ</option>
                    <option value="500+">Trên 500k/giờ</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>{{ __('ui.search') }}
                </button>
            </div>
        </div>
    </form>
    
    <div class="home-popular-subjects mt-4 text-center">
        <p class="text-muted small mb-2">{{ __('ui.or_choose_popular') }}:</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="#" class="btn btn-outline-primary btn-sm">Toán</a>
            <a href="#" class="btn btn-outline-primary btn-sm">Tiếng Anh</a>
            <a href="#" class="btn btn-outline-primary btn-sm">Vật Lý</a>
            <a href="#" class="btn btn-outline-primary btn-sm">Hóa Học</a>
            <a href="#" class="btn btn-outline-primary btn-sm">Lập trình</a>
        </div>
    </div>
</div>
