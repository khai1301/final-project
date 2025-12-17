// Student Request Form JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Skills input functionality
    const skillsInput = document.getElementById('skillsInput');
    const skillsChips = document.getElementById('skillsChips');
    const skillsHidden = document.getElementById('skillsHidden');
    let skills = [];

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

    // Form validation
    const form = document.querySelector('.student-request-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const scheduleCheckboxes = form.querySelectorAll('input[name="schedule[]"]:checked');
            if (scheduleCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one preferred schedule.');
                return false;
            }

            // Validate address for offline mode
            const modeOffline = document.getElementById('modeOffline');
            const addressInput = document.getElementById('addressInput');
            if (modeOffline && modeOffline.checked && addressInput && !addressInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a learning location address for in-person sessions.');
                addressInput.focus();
                return false;
            }
        });
    }

    // Toggle address field visibility based on learning mode
    const modeOnline = document.getElementById('modeOnline');
    const modeOffline = document.getElementById('modeOffline');
    const addressField = document.getElementById('addressField');
    const addressInput = document.getElementById('addressInput');

    if (modeOnline && modeOffline && addressField) {
        function toggleAddressField() {
            if (modeOffline.checked) {
                addressField.classList.remove('d-none');
                if (addressInput) addressInput.required = true;
            } else {
                addressField.classList.add('d-none');
                if (addressInput) {
                    addressInput.required = false;
                    addressInput.value = '';
                }
            }
        }

        modeOnline.addEventListener('change', toggleAddressField);
        modeOffline.addEventListener('change', toggleAddressField);

        // Initial check
        toggleAddressField();
    }
});
