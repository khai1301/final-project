@extends('frontend.layouts.bootstrap')

@section('title', 'Xác Thực CCCD')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <span class="material-symbols-outlined align-middle">verified_user</span>
                        Xác Thực Căn Cước Công Dân
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <span class="material-symbols-outlined align-middle">check_circle</span>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <span class="material-symbols-outlined align-middle">error</span>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(auth()->user()->is_verified)
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <span class="material-symbols-outlined me-2" style="font-size: 48px;">verified</span>
                                <div>
                                    <h5 class="mb-0">Tài khoản đã được xác thực</h5>
                                    <p class="mb-0 text-muted small">
                                        Xác thực lúc: {{ auth()->user()->verified_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><span class="material-symbols-outlined align-middle">info</span> Hướng dẫn</h5>
                            <ul class="mb-0">
                                <li>Vui lòng upload ảnh CCCD mặt trước rõ nét</li>
                                <li>Đảm bảo ảnh không bị mờ, lóa sáng hoặc bóng tối</li>
                                <li>Định dạng: JPG, JPEG, PNG</li>
                                <li>Kích thước tối đa: 5MB</li>
                                <li>Thông tin sẽ được xác thực tự động qua AI</li>
                            </ul>
                        </div>

                        <form action="{{ route('id-verification.verify') }}" method="POST" enctype="multipart/form-data" id="verificationForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <span class="material-symbols-outlined align-middle">badge</span>
                                    Upload Ảnh CCCD (Mặt Trước)
                                    <span class="text-danger">*</span>
                                </label>
                                
                                <div class="upload-area border-2 border-dashed rounded p-4 text-center" id="uploadArea">
                                    <input type="file" 
                                           name="id_card_image" 
                                           id="idCardImage" 
                                           class="d-none" 
                                           accept="image/jpeg,image/jpg,image/png"
                                           required>
                                    
                                    <div id="uploadPlaceholder">
                                        <span class="material-symbols-outlined" style="font-size: 64px; color: #6c757d;">add_a_photo</span>
                                        <p class="mt-2 mb-0">
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('idCardImage').click()">
                                                Chọn Ảnh
                                            </button>
                                        </p>
                                        <small class="text-muted">Hoặc kéo thả ảnh vào đây</small>
                                    </div>
                                    
                                    <div id="imagePreview" class="d-none">
                                        <img src="" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 300px;">
                                        <p class="mb-0">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearImage()">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                                                Xóa ảnh
                                            </button>
                                        </p>
                                    </div>
                                </div>
                                
                                @error('id_card_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="material-symbols-outlined align-middle">verified_user</span>
                                    Xác Thực
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                    <span class="material-symbols-outlined align-middle">arrow_back</span>
                                    Quay lại
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.upload-area {
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area:hover {
    background-color: #e9ecef;
    border-color: #0d6efd !important;
}

.upload-area.dragover {
    background-color: #e7f1ff;
    border-color: #0d6efd !important;
}
</style>

<script>
// Image preview
document.getElementById('idCardImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload =function(e) {
            document.querySelector('#imagePreview img').src = e.target.result;
            document.getElementById('uploadPlaceholder').classList.add('d-none');
            document.getElementById('imagePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});

function clearImage() {
    document.getElementById('idCardImage').value = '';
    document.getElementById('uploadPlaceholder').classList.remove('d-none');
    document.getElementById('imagePreview').classList.add('d-none');
}

// Drag and drop
const uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('click', function(e) {
    // Don't trigger if clicking the button directly
    if (e.target.tagName !== 'BUTTON') {
        document.getElementById('idCardImage').click();
    }
});

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('idCardImage').files = files;
        document.getElementById('idCardImage').dispatchEvent(new Event('change'));
    }
});

// Form submission
document.getElementById('verificationForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xác thực...';
});
</script>
@endsection
