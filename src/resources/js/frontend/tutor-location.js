/**
 * Tutor Location Selection (Tutor Profile Form)
 * Handles province and ward dropdowns - uses data from window.locationData
 */

document.addEventListener('DOMContentLoaded', function () {
    const tutorProvinceSelect = document.getElementById('tutor_province_id');
    const tutorWardSelect = document.getElementById('tutor_ward_id');

    if (!tutorProvinceSelect) return;

    console.log('Tutor location script loaded - all data from database');

    // Get data from window.locationData (embedded in Blade)
    const provincesData = window.locationData?.provinces || [];
    const wardsData = window.locationData?.wards || [];

    // Populate provinces (same as user-location.js but for tutor_ prefixed IDs)
    populateProvinces();

    // Restore old values if exists
    const oldProvinceId = tutorProvinceSelect.dataset.selected;
    if (oldProvinceId) {
        tutorProvinceSelect.value = oldProvinceId;
        const selectedProvince = provincesData.find(p => p.id == oldProvinceId);
        if (selectedProvince) {
            loadWards(selectedProvince.code);
        }
    }

    // Province change -> load wards
    tutorProvinceSelect.addEventListener('change', function () {
        const provinceId = this.value;

        if (!provinceId) {
            tutorWardSelect.innerHTML = '<option value="">Select Ward</option>';
            tutorWardSelect.disabled = true;
            return;
        }

        // Find province by ID to get code
        const selectedProvince = provincesData.find(p => p.id == provinceId);
        if (selectedProvince) {
            loadWards(selectedProvince.code);
        }
    });

    function populateProvinces() {
        tutorProvinceSelect.innerHTML = '<option value="">Select Province</option>';
        provincesData.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = `${province.name}`;
            tutorProvinceSelect.appendChild(option);
        });
    }

    // Load wards for selected province (from memory, no API call)
    function loadWards(provinceCode) {
        if (!tutorWardSelect) return;

        tutorWardSelect.disabled = true;
        tutorWardSelect.innerHTML = '<option value="">Loading...</option>';

        // Filter wards by province_code
        const provinceWards = wardsData.filter(ward => ward.province_code === provinceCode);

        tutorWardSelect.innerHTML = '<option value="">Select Ward</option>';

        provinceWards.forEach(ward => {
            const option = document.createElement('option');
            option.value = ward.id;
            option.textContent = `${ward.name}`;
            tutorWardSelect.appendChild(option);
        });

        // Restore old ward value if exists
        const oldWardId = tutorWardSelect.dataset.selected;
        if (oldWardId) {
            tutorWardSelect.value = oldWardId;
        }

        tutorWardSelect.disabled = false;
    }
});
