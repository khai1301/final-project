// Student Request Form JavaScript

import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    // Skills input functionality
    const skillsInput = document.getElementById('skillsInput');
    const skillsChips = document.getElementById('skillsChips');
    const skillsHidden = document.getElementById('skillsHidden');
    let skills = [];

    // Initialize skills from hidden input (for edit mode)
    if (skillsHidden && skillsHidden.value) {
        try {
            const initialSkills = JSON.parse(skillsHidden.value);
            if (Array.isArray(initialSkills)) {
                initialSkills.forEach(skill => {
                    if (skill && !skills.includes(skill)) {
                        skills.push(skill);
                        addSkillChip(skill);
                    }
                });
            }
        } catch (e) {
            console.error('Failed to parse initial skills:', e);
        }
    }

    if (skillsInput) {
        skillsInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim();
                if (value && !skills.includes(value)) {
                    skills.push(value);
                    addSkillChip(value);
                    this.value = '';
                    updateSkillsHidden();
                }
            }
        });
    }

    function addSkillChip(skill) {
        const chip = document.createElement('div');
        chip.className = 'skill-chip';
        chip.innerHTML = `
            ${skill}
            <button type="button" class="skill-chip-remove" aria-label="Remove ${skill}">
                <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
            </button>
        `;

        chip.querySelector('.skill-chip-remove').addEventListener('click', function () {
            skills = skills.filter(s => s !== skill);
            chip.remove();
            updateSkillsHidden();
        });

        skillsChips.appendChild(chip);
    }

    function updateSkillsHidden() {
        skillsHidden.value = JSON.stringify(skills);
    }

    // Budget sliders functionality
    const budgetMinSlider = document.getElementById('budgetMinSlider');
    const budgetMaxSlider = document.getElementById('budgetMaxSlider');
    const budgetMinDisplay = document.getElementById('budgetMin');
    const budgetMaxDisplay = document.getElementById('budgetMax');

    if (budgetMinSlider && budgetMaxSlider) {
        budgetMinSlider.addEventListener('input', function () {
            let minValue = parseInt(this.value);
            let maxValue = parseInt(budgetMaxSlider.value);

            if (minValue > maxValue) {
                budgetMaxSlider.value = minValue;
                maxValue = minValue;
            }

            budgetMinDisplay.textContent = minValue;
            budgetMaxDisplay.textContent = maxValue;
        });

        budgetMaxSlider.addEventListener('input', function () {
            let minValue = parseInt(budgetMinSlider.value);
            let maxValue = parseInt(this.value);

            if (maxValue < minValue) {
                budgetMinSlider.value = maxValue;
                minValue = maxValue;
            }

            budgetMinDisplay.textContent = minValue;
            budgetMaxDisplay.textContent = maxValue;
        });
    }

    // Form submission - no custom validation needed, relying on Laravel FormRequest
    const form = document.querySelector('.student-request-form');
    if (form) {
        // Form will submit normally, no JS validation
    }

    // Handle custom location checkbox
    const useDifferentLocation = document.getElementById('use_different_location');
    const customLocationFields = document.getElementById('custom_location_fields');
    const requestProvinceSelect = document.getElementById('request_province_id');
    const requestWardSelect = document.getElementById('request_ward_id');

    // Get location data from window (embedded in Blade)
    const provincesData = window.locationData?.provinces || [];
    const wardsData = window.locationData?.wards || [];

    if (useDifferentLocation && customLocationFields) {
        useDifferentLocation.addEventListener('change', function () {
            if (this.checked) {
                customLocationFields.classList.remove('d-none');
                loadProvincesForRequest();
            } else {
                customLocationFields.classList.add('d-none');
                requestProvinceSelect.value = '';
                requestWardSelect.value = '';
                requestWardSelect.disabled = true;
            }
        });
    }

    // Load provinces for request (if user has no profile location OR checking "use different location")
    if (requestProvinceSelect && !document.getElementById('use_different_location')) {
        // User has no profile location - load provinces immediately
        loadProvincesForRequest();
    }

    function loadProvincesForRequest() {
        if (!requestProvinceSelect) return;

        requestProvinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
        provincesData.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = province.name;
            requestProvinceSelect.appendChild(option);
        });
    }

    // Province change -> load wards for request  
    if (requestProvinceSelect) {
        requestProvinceSelect.addEventListener('change', function () {
            const provinceId = this.value;

            if (!provinceId) {
                requestWardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                requestWardSelect.disabled = true;
                return;
            }

            // Find province to get code
            const selectedProvince = provincesData.find(p => p.id == provinceId);
            if (selectedProvince) {
                loadWardsForRequest(selectedProvince.code);
            }
        });
    }

    function loadWardsForRequest(provinceCode) {
        if (!requestWardSelect) return;

        requestWardSelect.disabled = true;
        requestWardSelect.innerHTML = '<option value="">Đang tải...</option>';

        // Filter wards by province_code (client-side, instant)
        const provinceWards = wardsData.filter(ward => ward.province_code === provinceCode);

        requestWardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        provinceWards.forEach(ward => {
            const option = document.createElement('option');
            option.value = ward.id;
            option.textContent = ward.name;
            requestWardSelect.appendChild(option);
        });

        requestWardSelect.disabled = false;
    }

});
