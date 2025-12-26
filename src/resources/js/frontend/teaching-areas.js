/**
 * Teaching Areas Management
 * Handles dynamic add/remove of teaching areas and province/ward selection
 */

document.addEventListener('DOMContentLoaded', function () {
    const teachingAreasList = document.getElementById('teachingAreasList');
    const addButton = document.getElementById('addTeachingArea');

    if (!teachingAreasList || !addButton) return;

    let areaIndex = document.querySelectorAll('.teaching-area-item').length;
    let provincesData = [];

    // Load provinces on init
    loadProvinces().then(() => {
        // Auto-add base location if user has one
        addBaseLocationIfExists();
    });

    // Add new teaching area
    addButton.addEventListener('click', function () {
        addTeachingArea();
    });

    // Remove no-areas message if exists
    function removeNoAreasMessage() {
        const noAreasMessage = teachingAreasList.querySelector('.no-areas-message');
        if (noAreasMessage) {
            noAreasMessage.remove();
        }
    }

    // Add new teaching area row
    function addTeachingArea() {
        removeNoAreasMessage();

        const areaHtml = `
            <div class="teaching-area-item card mb-3 p-3" data-index="${areaIndex}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-medium">Province/City</label>
                        <select name="teaching_areas[${areaIndex}][province_id]" class="form-select province-select" required>
                            <option value="">Select Province</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-medium">Ward/Commune <span class="text-muted">(Optional)</span></label>
                        <select name="teaching_areas[${areaIndex}][ward_id]" class="form-select ward-select">
                            <option value="">Entire Province</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-teaching-area">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `;

        teachingAreasList.insertAdjacentHTML('beforeend', areaHtml);

        // Get the newly added item
        const newItem = teachingAreasList.lastElementChild;
        const provinceSelect = newItem.querySelector('.province-select');

        // Populate provinces
        populateProvinceSelect(provinceSelect);

        // Attach event listeners
        attachEventListeners(newItem);

        areaIndex++;
    }

    // Check and add base location if user has one
    function addBaseLocationIfExists() {
        const userProvinceId = teachingAreasList.dataset.userProvince;
        const userWardId = teachingAreasList.dataset.userWard;
        const provinceName = teachingAreasList.dataset.provinceName;
        const wardName = teachingAreasList.dataset.wardName;

        // Only proceed if user has base location
        if (userProvinceId && userProvinceId !== 'null' && userProvinceId !== '') {
            const existingAreas = teachingAreasList.querySelectorAll('.teaching-area-item');

            if (existingAreas.length === 0) {
                // No existing areas - add base location as first
                addBaseLocationArea(userProvinceId, userWardId, provinceName, wardName);
            } else {
                // Check if first area is base location
                const firstArea = existingAreas[0];
                const firstProvinceSelect = firstArea.querySelector('.province-select');
                const firstWardSelect = firstArea.querySelector('.ward-select');

                let isFirstAreaBaseLocation = false;

                if (firstProvinceSelect && firstProvinceSelect.dataset.selected == userProvinceId) {
                    const firstWardId = firstWardSelect?.dataset.selected || null;

                    // Check if ward also matches
                    if ((!userWardId || userWardId === 'null') && (!firstWardId || firstWardId === 'null')) {
                        // Both null, it's base location
                        isFirstAreaBaseLocation = true;
                    } else if (userWardId == firstWardId) {
                        // Ward IDs match
                        isFirstAreaBaseLocation = true;
                    }
                }

                if (isFirstAreaBaseLocation) {
                    // First area is base location - just mark it
                    markAsBaseLocation(firstArea);
                } else {
                    // First area is NOT base location - prepend base location
                    addBaseLocationArea(userProvinceId, userWardId, provinceName, wardName);
                }
            }
        }
    }

    // Add base location as first teaching area (read-only)
    function addBaseLocationArea(provinceId, wardId, provinceName, wardName) {
        removeNoAreasMessage();

        const areaHtml = `
            <div class="teaching-area-item card mb-3 p-3 border-primary" data-index="base" data-is-base="true">
                <div class="row g-3 align-items-end">
                    <div class="col-12">
                        <span class="badge bg-primary mb-2">
                            <i class="bi bi-house-fill"></i> Base Location (From Profile)
                        </span>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-medium">Province/City</label>
                        <input type="text" class="form-control" value="${provinceName}" disabled>
                        <input type="hidden" name="teaching_areas[0][province_id]" value="${provinceId}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-medium">Ward/Commune</label>
                        <input type="text" class="form-control" value="${wardId && wardId !== 'null' ? wardName : 'Entire Province'}" disabled>
                        <input type="hidden" name="teaching_areas[0][ward_id]" value="${wardId && wardId !== 'null' ? wardId : ''}">
                    </div>
                    <div class="col-md-2 text-end">
                        <small class="text-muted d-block">Cannot remove</small>
                    </div>
                </div>
            </div>
        `;

        teachingAreasList.insertAdjacentHTML('afterbegin', areaHtml);

        // Increment areaIndex so next areas start from 1
        areaIndex = 1;
    }

    // Mark an existing teaching area as base location
    function markAsBaseLocation(areaItem) {
        // Add badge
        const row = areaItem.querySelector('.row');
        if (row && !areaItem.querySelector('.base-location-badge')) {
            const badgeHtml = `
                <div class="col-12 base-location-badge">
                    <span class="badge bg-primary mb-2">
                        <i class="bi bi-house-fill"></i> Base Location (From Profile)
                    </span>
                </div>
            `;
            row.insertAdjacentHTML('afterbegin', badgeHtml);
        }

        // Add border
        areaItem.classList.add('border-primary');

        // Mark as base
        areaItem.dataset.isBase = 'true';

        // Disable remove button or hide it
        const removeBtn = areaItem.querySelector('.remove-teaching-area');
        if (removeBtn) {
            removeBtn.disabled = true;
            removeBtn.innerHTML = '<small class="text-muted">Cannot remove</small>';
            removeBtn.classList.remove('btn-outline-danger');
            removeBtn.classList.add('btn-outline-secondary');
        }
    }

    // Load provinces from API
    async function loadProvinces() {
        try {
            const response = await fetch('/api/provinces');
            const data = await response.json();
            provincesData = data;

            // Populate all existing province selects
            document.querySelectorAll('.province-select').forEach(select => {
                populateProvinceSelect(select);

                // If has data-selected, set it
                const selectedId = select.dataset.selected;
                if (selectedId) {
                    select.value = selectedId;
                    // Trigger ward loading
                    loadWards(select);
                }
            });

        } catch (error) {
            console.error('Failed to load provinces:', error);
        }
    }

    // Populate province select
    function populateProvinceSelect(select) {
        const currentValue = select.value;

        // Clear existing options except first
        select.innerHTML = '<option value="">Select Province</option>';

        provincesData.forEach(province => {
            const option = document.createElement('option');
            option.value = province.id;
            option.textContent = `${province.name} (${province.type})`;
            select.appendChild(option);
        });

        // Restore value if exists
        if (currentValue) {
            select.value = currentValue;
        }
    }

    // Load wards for selected province
    async function loadWards(provinceSelect) {
        const provinceId = provinceSelect.value;
        const areaItem = provinceSelect.closest('.teaching-area-item');
        const wardSelect = areaItem.querySelector('.ward-select');

        if (!provinceId) {
            wardSelect.innerHTML = '<option value="">Entire Province</option>';
            wardSelect.disabled = true;
            return;
        }

        wardSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">Loading...</option>';

        try {
            const response = await fetch(`/api/wards/${provinceId}`);
            const wards = await response.json();

            wardSelect.innerHTML = '<option value="">Entire Province</option>';

            wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.id;
                option.textContent = `${ward.name} (${ward.type})`;
                wardSelect.appendChild(option);
            });

            // Restore selected ward if exists
            const selectedWardId = wardSelect.dataset.selected;
            if (selectedWardId) {
                wardSelect.value = selectedWardId;
                delete wardSelect.dataset.selected; // Clear after use
            }

            wardSelect.disabled = false;

        } catch (error) {
            console.error('Failed to load wards:', error);
            wardSelect.innerHTML = '<option value="">Error loading wards</option>';
        }
    }

    // Attach event listeners to area item
    function attachEventListeners(areaItem) {
        const provinceSelect = areaItem.querySelector('.province-select');
        const removeButton = areaItem.querySelector('.remove-teaching-area');

        // Province change -> load wards
        provinceSelect.addEventListener('change', function () {
            loadWards(provinceSelect);
        });

        // Remove button
        removeButton.addEventListener('click', function () {
            areaItem.remove();

            // Show no-areas message if no items left
            if (teachingAreasList.querySelectorAll('.teaching-area-item').length === 0) {
                teachingAreasList.innerHTML = `
                    <div class="alert alert-info no-areas-message">
                        <i class="bi bi-info-circle"></i> No teaching areas added yet. Click "Add Teaching Area" below.
                    </div>
                `;
            }
        });
    }

    // Attach listeners to existing items
    document.querySelectorAll('.teaching-area-item').forEach(item => {
        attachEventListeners(item);
    });
});
