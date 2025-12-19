// Student Request Form JavaScript

import Swal from 'sweetalert2';

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
                Swal.fire({
                    icon: 'warning',
                    title: 'Schedule Required',
                    text: 'Please select at least one preferred schedule.',
                    confirmButtonColor: '#3780f6'
                });
                return false;
            }

            // Validate address for non-online modes
            const selectedMode = form.querySelector('input[name="mode"]:checked');
            const addressInput = document.getElementById('addressInput');
            if (selectedMode && addressInput) {
                const modeValue = selectedMode.value.toLowerCase();
                if (modeValue !== 'online' && !addressInput.value.trim()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Address Required',
                        text: 'Please enter a learning location address for in-person sessions.',
                        confirmButtonColor: '#3780f6'
                    });
                    addressInput.focus();
                    return false;
                }
            }
        });
    }

    // Toggle address field visibility based on learning mode
    const modeRadios = document.querySelectorAll('input[name="mode"]');
    const addressField = document.getElementById('addressField');
    const addressInput = document.getElementById('addressInput');

    if (modeRadios.length > 0 && addressField) {
        function toggleAddressField() {
            const selectedMode = document.querySelector('input[name="mode"]:checked');
            if (selectedMode) {
                const modeValue = selectedMode.value.toLowerCase();
                if (modeValue !== 'online') {
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
        }

        modeRadios.forEach(radio => {
            radio.addEventListener('change', toggleAddressField);
        });

        // Initial check
        toggleAddressField();
    }

    // Handle success messages from server
    const successMessage = document.querySelector('[data-success-message]');
    if (successMessage) {
        const message = successMessage.getAttribute('data-success-message');
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
});
