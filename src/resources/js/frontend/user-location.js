/**
 * User Location Selection (Register/Profile)
 * Handles province and ward dropdowns - uses data from window.locationData
 */

document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province_id');
    const wardSelect = document.getElementById('ward_id');

    if (!provinceSelect) return;

    console.log('User location script loaded - all data from database');

    // Get data from window.locationData (embedded in Blade)
    const provincesData = window.locationData?.provinces || [];
    const wardsData = window.locationData?.wards || [];

    // Provinces are already populated in Blade, just check for old value to restore wards
    const oldProvinceId = provinceSelect.value;
    if (oldProvinceId) {
        const selectedProvince = provincesData.find(p => p.id == oldProvinceId);
        if (selectedProvince) {
            loadWards(selectedProvince.code);
        }
    }

    // Province change -> load wards
    provinceSelect.addEventListener('change', function () {
        const provinceId = this.value;

        if (!provinceId) {
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            wardSelect.disabled = true;
            return;
        }

        // Find province by ID to get code
        const selectedProvince = provincesData.find(p => p.id == provinceId);
        if (selectedProvince) {
            loadWards(selectedProvince.code);
        }
    });

    // Load wards for selected province (from memory, no API call)
    function loadWards(provinceCode) {
        if (!wardSelect) return;

        wardSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';

        // Filter wards by province_code
        const provinceWards = wardsData.filter(ward => ward.province_code === provinceCode);

        wardSelect.innerHTML = '<option value="">Chọn phường/xã (tùy chọn)</option>';

        provinceWards.forEach(ward => {
            const option = document.createElement('option');
            option.value = ward.id;
            option.textContent = `${ward.name}`;
            wardSelect.appendChild(option);
        });

        // Restore old ward value if exists
        const oldWardId = wardSelect.dataset.oldValue;
        if (oldWardId) {
            wardSelect.value = oldWardId;
        }

        wardSelect.disabled = false;
    }
});
