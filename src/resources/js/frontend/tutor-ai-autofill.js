/**
 * Tutor Profile CV Upload & AI Auto-fill
 * Handles CV file upload, drag-drop, AI parsing, and form auto-filling
 */

document.addEventListener('DOMContentLoaded', function () {
    let uploadedCVFile = null;
    let aiParsedData = null;

    const cvInput = document.getElementById('cvInput');
    const cvPreview = document.getElementById('cvPreview');
    const cvRemoveBtn = document.getElementById('cvRemoveBtn');
    const aiAutoFillBtn = document.getElementById('aiAutoFillBtn');
    const aiProcessingAlert = document.getElementById('aiProcessingAlert');
    const aiStatusText = document.getElementById('aiStatusText');
    const cvUploadArea = document.getElementById('cvUploadArea');

    // Exit if elements don't exist
    if (!cvInput || !aiAutoFillBtn) return;

    // Handle CV file selection
    cvInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            handleCVFile(file);
        }
    });

    // Handle drag and drop
    if (cvUploadArea) {
        cvUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            cvUploadArea.classList.add('drag-over');
        });

        cvUploadArea.addEventListener('dragleave', () => {
            cvUploadArea.classList.remove('drag-over');
        });

        cvUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            cvUploadArea.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file) {
                handleCVFile(file);
            }
        });
    }

    function handleCVFile(file) {
        // Validate file type
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file PDF, DOC, DOCX');
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File không được vượt quá 10MB');
            return;
        }

        uploadedCVFile = file;

        // Show preview
        if (cvPreview) {
            const fileNameEl = cvPreview.querySelector('.file-name');
            if (fileNameEl) {
                fileNameEl.textContent = file.name;
            }
            cvPreview.classList.remove('d-none');
        }

        // Enable AI button
        aiAutoFillBtn.disabled = false;
    }

    // Remove CV
    if (cvRemoveBtn) {
        cvRemoveBtn.addEventListener('click', function () {
            cvInput.value = '';
            uploadedCVFile = null;
            if (cvPreview) {
                cvPreview.classList.add('d-none');
            }
            aiAutoFillBtn.disabled = true;
        });
    }

    // AI Auto-fill button click
    aiAutoFillBtn.addEventListener('click', async function () {
        if (!uploadedCVFile) {
            alert('Vui lòng upload CV trước');
            return;
        }

        // Show processing alert
        if (aiProcessingAlert) {
            aiProcessingAlert.classList.remove('d-none');
            if (aiStatusText) {
                aiStatusText.textContent = 'Đang tải CV lên S3...';
            }
        }
        aiAutoFillBtn.disabled = true;

        try {
            // Upload CV and parse with AI
            const formData = new FormData();
            formData.append('cv_file', uploadedCVFile);

            if (aiStatusText) {
                aiStatusText.textContent = 'Đang phân tích CV với AI...';
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const response = await fetch('/cv/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content
                },
                body: formData
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Lỗi khi xử lý CV');
            }

            // Store parsed data
            aiParsedData = result.data;

            // Hide processing alert
            if (aiProcessingAlert) {
                aiProcessingAlert.classList.add('d-none');
            }

            // Auto-apply data to form immediately (no modal)
            applyAIDataToForm(aiParsedData);

        } catch (error) {
            console.error('Error:', error);
            if (aiProcessingAlert) {
                aiProcessingAlert.classList.add('d-none');
            }
            alert('Lỗi: ' + error.message);
            aiAutoFillBtn.disabled = false;
        }
    });

    // Function to apply AI data to form
    function applyAIDataToForm(data) {
        if (!data) return;

        // Fill user basic info
        if (data.name) {
            const nameInput = document.querySelector('[name="name"]');
            if (nameInput) nameInput.value = data.name;
        }

        if (data.phone) {
            const phoneInput = document.querySelector('[name="phone"]');
            if (phoneInput) phoneInput.value = data.phone;
        }

        // Fill profile fields
        if (data.education) {
            const eduInput = document.querySelector('[name="education"]');
            if (eduInput) eduInput.value = data.education;
        }

        if (data.experience_years) {
            const expInput = document.querySelector('[name="experience_years"]');
            if (expInput) expInput.value = data.experience_years;
        }

        if (data.hourly_rate_min) {
            const minInput = document.querySelector('[name="hourly_rate_min"]');
            if (minInput) minInput.value = data.hourly_rate_min;
        }

        if (data.hourly_rate_max) {
            const maxInput = document.querySelector('[name="hourly_rate_max"]');
            if (maxInput) maxInput.value = data.hourly_rate_max;
        }

        if (data.bio) {
            const bioInput = document.querySelector('[name="bio"]');
            if (bioInput) bioInput.value = data.bio;
        }

        // Check subject checkboxes
        if (data.subject_ids && data.subject_ids.length > 0) {
            data.subject_ids.forEach(id => {
                const checkbox = document.querySelector(`input[name="subjects[]"][value="${id}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }

        // Show success message
        alert('✅ Đã tự động điền thông tin từ CV! Vui lòng xem lại và bấm "Submit for Review".');

        // Scroll to top to see filled fields
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
