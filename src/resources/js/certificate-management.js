/**
 * Certificate Management - Tutor Profile
 */

// Wait for DOM ready
document.addEventListener('DOMContentLoaded', function () {
    initCertificateManagement();
});

function initCertificateManagement() {
    const form = document.getElementById('certificateForm');
    const modal = document.getElementById('addCertificateModal');

    // Guard: Only run if elements exist
    if (!form || !modal) return;

    const modalTitle = document.getElementById('modalTitle');
    const certId = document.getElementById('certId');
    const certName = document.getElementById('certName');
    const certFile = document.getElementById('certFileInput');
    const currentFileName = document.getElementById('currentFileName');

    // Open modal for add new certificate
    const addBtn = document.querySelector('[data-bs-toggle="modal"][data-bs-target="#addCertificateModal"]');
    if (addBtn) {
        addBtn.addEventListener('click', function () {
            modalTitle.textContent = 'Thêm chứng chỉ mới';
            form.reset();
            certId.value = '';
            currentFileName.innerHTML = '';
            certFile.setAttribute('required', 'required');

            // Hide delete button when adding new
            const deleteBtn = document.getElementById('deleteCertBtn');
            if (deleteBtn) {
                deleteBtn.style.display = 'none';
            }
        });
    }

    // Handle edit certificate buttons
    document.addEventListener('click', function (e) {
        if (e.target.closest('.edit-cert-btn')) {
            const btn = e.target.closest('.edit-cert-btn');
            const id = btn.dataset.certId;
            const name = btn.dataset.certName;

            modalTitle.textContent = 'Chỉnh sửa chứng chỉ';
            certId.value = id;
            certName.value = name;
            currentFileName.innerHTML = '<small class="text-muted">Để trống nếu không muốn thay đổi file</small>';
            certFile.removeAttribute('required');

            // Show delete button when editing
            const deleteBtn = document.getElementById('deleteCertBtn');
            if (deleteBtn) {
                deleteBtn.style.display = 'block';
                deleteBtn.dataset.certId = id;
            }

            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    });

    // Handle delete button in modal
    const deleteBtn = document.getElementById('deleteCertBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', async function () {
            const certId = this.dataset.certId;

            const result = await Swal.fire({
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc muốn xóa chứng chỉ này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/tutor/certificates/${certId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // Remove certificate card from DOM
                        const certItem = document.querySelector(`.certificate-item[data-cert-id="${certId}"]`);
                        if (certItem) {
                            certItem.remove();
                        }

                        // Check if list is empty, show empty message
                        const certList = document.getElementById('certificatesList');
                        const remainingCerts = certList.querySelectorAll('.certificate-item');
                        if (remainingCerts.length === 0) {
                            certList.innerHTML = `
                                <div class="alert alert-info" id="noCertificatesMessage">
                                    <span class="material-symbols-outlined align-middle me-2">info</span>
                                    Chưa có chứng chỉ nào. Click "Thêm chứng chỉ" để bắt đầu.
                                </div>
                            `;
                        }

                        // Close modal reliably
                        const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                        bsModal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Đã xóa!',
                            text: 'Chứng chỉ đã được xóa thành công.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error('Không thể xóa chứng chỉ');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: error.message,
                        confirmButtonText: 'Đóng'
                    });
                }
            }
        });
    }

    // Handle delete certificate with SweetAlert (legacy - can be removed)
    document.addEventListener('submit', function (e) {
        if (e.target.classList.contains('delete-cert-form')) {
            e.preventDefault();

            Swal.fire({
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc muốn xóa chứng chỉ này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });

    // Handle form submit (AJAX)
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const isEdit = certId.value !== '';
        const url = isEdit ? '/tutor/certificates/update' : '/tutor/certificates/add';

        const saveBtn = document.getElementById('saveCertBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Close modal
                bootstrap.Modal.getInstance(modal).hide();

                const cert = result.certificate;

                if (isEdit) {
                    // UPDATE: Update certificate name in the list
                    const certItem = document.querySelector(`.certificate-item[data-cert-id="${certId.value}"]`);
                    if (certItem) {
                        const nameElement = certItem.querySelector('.fw-medium');
                        if (nameElement) {
                            nameElement.textContent = cert.name;
                        }
                        // Update edit button data
                        const editBtn = certItem.querySelector('.edit-cert-btn');
                        if (editBtn) {
                            editBtn.dataset.certName = cert.name;
                        }

                        // If file updated, update View link
                        if (formData.get('cert_file') && formData.get('cert_file').size > 0) {
                            const viewBtn = certItem.querySelector('a.btn-outline-primary');
                            if (viewBtn) viewBtn.href = cert.file_url;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Đã cập nhật!',
                        text: result.message || 'Chứng chỉ đã được cập nhật.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // ADD: Create new certificate DOM element
                    const certificatesList = document.getElementById('certificatesList');

                    // Remove "No certificates" message if exists
                    const noCertMsg = document.getElementById('noCertificatesMessage');
                    if (noCertMsg) noCertMsg.remove();

                    // Determine Icon
                    let iconHtml = '';
                    if (cert.file_type && cert.file_type.includes('image')) {
                        iconHtml = '<span class="material-symbols-outlined text-primary">image</span>';
                    } else if (cert.file_type && cert.file_type.includes('pdf')) {
                        iconHtml = '<span class="material-symbols-outlined text-danger">picture_as_pdf</span>';
                    } else {
                        iconHtml = '<span class="material-symbols-outlined text-info">description</span>';
                    }

                    // Create HTML
                    const date = new Date(cert.created_at);
                    const formattedDate = date.toLocaleDateString('en-GB') + ' ' +
                        date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

                    const newCertHtml = `
                    <div class="certificate-item card mb-2" data-cert-id="${cert.id}">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-2">
                                        ${iconHtml}
                                        <div>
                                            <div class="fw-medium">${cert.name}</div>
                                            <small class="text-muted">${formattedDate}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    <div class="btn-group" role="group">
                                        <a href="${cert.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                            Xem
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-cert-btn" 
                                                data-cert-id="${cert.id}"
                                                data-cert-name="${cert.name}">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                            Sửa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                    certificatesList.insertAdjacentHTML('beforeend', newCertHtml);

                    await Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: result.message || 'Chứng chỉ đã được thêm.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                throw new Error(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: error.message,
                confirmButtonText: 'Đóng'
            });
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<span class="material-symbols-outlined align-middle me-1" style="font-size: 16px;">save</span>Lưu chứng chỉ';
        }
    });
}
