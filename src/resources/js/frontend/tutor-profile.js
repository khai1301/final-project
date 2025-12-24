// Tutor Profile Form JavaScript
document.addEventListener('DOMContentLoaded', function () {

    // Chip Input Handler
    class ChipInput {
        constructor(inputId, chipsId, hiddenId) {
            this.input = document.getElementById(inputId);
            this.chipsContainer = document.getElementById(chipsId);
            this.hiddenInput = document.getElementById(hiddenId);
            this.chips = [];

            if (this.input && this.chipsContainer && this.hiddenInput) {
                this.init();
            }
        }

        init() {
            // Load existing chips from DOM
            const existingChips = this.chipsContainer.querySelectorAll('.skill-chip');
            existingChips.forEach(chip => {
                const text = chip.textContent.trim();
                if (text) {
                    this.chips.push(text);
                }
            });
            this.updateHidden();

            // Add event listener for Enter key
            this.input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = this.input.value.trim();
                    if (value) {
                        this.addChip(value);
                        this.input.value = '';
                    }
                }
            });

            // Handle existing remove buttons
            this.attachRemoveListeners();
        }

        addChip(text) {
            if (!this.chips.includes(text)) {
                this.chips.push(text);
                this.renderChip(text);
                this.updateHidden();
            }
        }

        removeChip(text) {
            this.chips = this.chips.filter(c => c !== text);
            this.updateHidden();
        }

        renderChip(text) {
            const chip = document.createElement('span');
            chip.className = 'skill-chip';
            chip.innerHTML = `
                ${text}
                <button type="button" class="skill-chip-remove">
                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                </button>
            `;

            chip.querySelector('.skill-chip-remove').addEventListener('click', () => {
                this.removeChip(text);
                chip.remove();
            });

            this.chipsContainer.appendChild(chip);
        }

        attachRemoveListeners() {
            this.chipsContainer.querySelectorAll('.skill-chip-remove').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const chip = e.target.closest('.skill-chip');
                    const text = chip.textContent.trim();
                    this.removeChip(text);
                    chip.remove();
                });
            });
        }

        updateHidden() {
            this.hiddenInput.value = JSON.stringify(this.chips);
        }
    }

    // Initialize chip inputs (subjects removed as they're now checkboxes)
    const areasChip = new ChipInput('areasInput', 'areasChips', 'areasHidden');
    const skillsChip = new ChipInput('skillsInput', 'skillsChips', 'skillsHidden');

    // File upload previews
    const setupFilePreview = (inputId, previewId, labelId) => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input) {
            input.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);

                    if (preview) {
                        preview.classList.remove('d-none');
                        const fileNameSpan = preview.querySelector('.file-name');
                        if (fileNameSpan) {
                            fileNameSpan.textContent = `${fileName} (${fileSize} MB)`;
                        }
                    }
                }
            });
        }
    };

    setupFilePreview('cvInput', 'cvPreview', null);
    setupFilePreview('profilePhotoInput', 'profilePhotoPreview', null);

    // Profile photo preview
    const profilePhotoInput = document.getElementById('profilePhotoInput');
    const profilePhotoPreview = document.getElementById('profilePhotoPreview');

    if (profilePhotoInput && profilePhotoPreview) {
        profilePhotoInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    profilePhotoPreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Form submission handler
    const form = document.querySelector('.tutor-profile-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            // Validate subjects (now checkboxes)
            const subjectCheckboxes = document.querySelectorAll('input[name="subjects[]"]:checked');
            if (subjectCheckboxes.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select at least one subject you teach.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }

            // Validate teaching areas (chip input)
            const teachingAreas = document.getElementById('areasHidden');
            if (teachingAreas && (!teachingAreas.value || teachingAreas.value === '[]')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please add at least one teaching area/location.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return false;
            }
        });
    }

    // Success message handler
    const successMsg = document.querySelector('[data-success-message]');
    if (successMsg) {
        const message = successMsg.getAttribute('data-success-message');
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
});
