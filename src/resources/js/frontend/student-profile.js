/**
 * Student Profile Edit Page Scripts
 * Handles location cascading and avatar preview
 */

// Location cascading dropdown
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province_id');
    const wardSelect = document.getElementById('ward_id');

    if (provinceSelect && wardSelect) {
        // Get data from window object (passed from blade)
        const wardsData = window.wardsData || [];
        const provincesData = window.provincesData || [];

        provinceSelect.addEventListener('change', function () {
            const selectedProvinceId = this.value;
            const selectedProvince = provincesData.find(p => p.id == selectedProvinceId);

            // Clear wards
            wardSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';

            if (selectedProvince) {
                const filteredWards = wardsData.filter(w => w.province_code === selectedProvince.code);
                filteredWards.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.id;
                    option.textContent = ward.name;
                    wardSelect.appendChild(option);
                });
            }
        });
    }

    // Avatar preview (same as tutor profile)
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
