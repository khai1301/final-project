// Auth Pages JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // Password visibility toggle
    const eyeButtons = document.querySelectorAll('.eye-btn');

    eyeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('.material-symbols-outlined');

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });
});
