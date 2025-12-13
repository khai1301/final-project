document.addEventListener('DOMContentLoaded', function () {
    // View User Modal Logic
    const viewUserModalEl = document.getElementById('viewUserModal');
    if (viewUserModalEl) {
        const viewUserModal = new bootstrap.Modal(viewUserModalEl);

        document.querySelectorAll('.view-user-btn').forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');

                // 1. Show Modal & Reset State
                viewUserModal.show();
                document.getElementById('view_loading').classList.remove('d-none');
                document.getElementById('view_content').classList.add('d-none');

                // 2. Fetch Data
                fetch(`/admin/users/${userId}`)
                    .then(response => response.json())
                    .then(user => {
                        // 3. Populate Basic Info
                        document.getElementById('view_name').textContent = user.name;
                        document.getElementById('view_email').textContent = user.email;
                        document.getElementById('view_email_text').textContent = user.email;
                        document.getElementById('view_phone').textContent = user.phone || 'N/A';
                        document.getElementById('view_joined').textContent = new Date(user.created_at).toLocaleDateString();

                        // Avatar
                        const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random&color=fff`;
                        document.getElementById('view_avatar').src = avatarUrl;

                        // Role Badge
                        const roleBadge = document.getElementById('view_role_badge');
                        roleBadge.textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);
                        roleBadge.className = 'badge rounded-pill mb-2 ' +
                            (user.role === 'admin' ? 'bg-dark' :
                                (user.role === 'tutor' ? 'bg-purple text-white' : 'bg-primary'));

                        // Status
                        const statusSpan = document.getElementById('view_status');
                        if (user.banned_at) {
                            statusSpan.textContent = 'Banned';
                            statusSpan.className = 'text-danger fw-bold';
                        } else {
                            statusSpan.textContent = 'Active';
                            statusSpan.className = 'text-success fw-bold';
                        }

                        document.getElementById('view_verified').textContent = user.email_verified_at ? 'Yes' : 'No';

                        // 4. Handle Profile Details (CamelCase keys converted to snake_case by Laravel JSON)
                        const studentSection = document.getElementById('student_details');
                        const tutorSection = document.getElementById('tutor_details');
                        const emptySection = document.getElementById('no_profile_details');

                        studentSection.classList.add('d-none');
                        tutorSection.classList.add('d-none');
                        emptySection.classList.add('d-none');

                        // NOTE: Laravel relationships in JSON are usually snake_case (student_profile, tutor_profile)
                        if (user.role === 'student' && user.student_profile) {
                            studentSection.classList.remove('d-none');
                            document.getElementById('view_student_school').textContent = user.student_profile.school || 'N/A';
                            document.getElementById('view_student_grade').textContent = user.student_profile.grade || 'N/A';
                            document.getElementById('view_student_goals').textContent = user.student_profile.goals || 'No goals set.';
                        } else if (user.role === 'tutor' && user.tutor_profile) {
                            tutorSection.classList.remove('d-none');
                            document.getElementById('view_tutor_bio').textContent = user.tutor_profile.bio || 'No bio available.';

                            const minRate = user.tutor_profile.hourly_rate_min ? '$' + user.tutor_profile.hourly_rate_min : 'N/A';
                            const maxRate = user.tutor_profile.hourly_rate_max ? '$' + user.tutor_profile.hourly_rate_max : '';
                            document.getElementById('view_tutor_rate').textContent = minRate + (maxRate ? ' - ' + maxRate : '');

                            document.getElementById('view_tutor_experience').textContent = (user.tutor_profile.experience_years || 0) + ' years';
                            document.getElementById('view_tutor_education').textContent = user.tutor_profile.education || 'N/A';

                            // Subjects
                            const subjectsContainer = document.getElementById('view_tutor_subjects');
                            subjectsContainer.innerHTML = '';
                            if (user.tutor_profile.subjects && Array.isArray(user.tutor_profile.subjects)) {
                                user.tutor_profile.subjects.forEach(sub => {
                                    const badge = document.createElement('span');
                                    badge.className = 'badge bg-light text-dark border';
                                    badge.textContent = sub;
                                    subjectsContainer.appendChild(badge);
                                });
                            } else {
                                subjectsContainer.textContent = 'No subjects listed';
                            }
                        } else {
                            emptySection.classList.remove('d-none');
                        }

                        // 5. Show Content & Hide Loading
                        document.getElementById('view_loading').classList.add('d-none');
                        document.getElementById('view_content').classList.remove('d-none');
                    })
                    .catch(error => {
                        console.error('Error fetching user details:', error);
                        document.getElementById('view_loading').innerHTML = '<p class="text-danger">Failed to load data. Please try again.</p>';
                    });
            });
        });
    }

    // Edit Modal Data Population
    var editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            // If button is null, it means modal is opened via JS (after validation error)
            if (!button) return;

            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var email = button.getAttribute('data-email');
            var phone = button.getAttribute('data-phone');
            var role = button.getAttribute('data-role');

            var modal = this;
            modal.querySelector('#edit_name').value = name;
            modal.querySelector('#edit_email').value = email;
            modal.querySelector('#edit_phone').value = phone;
            modal.querySelector('#edit_role').value = role;

            // Update Form Action
            var form = modal.querySelector('#editUserForm');
            form.action = '/admin/users/' + id;
        });
    }

    // Error Handling: Open modal if validation errors exist (triggered by global window variable)
    if (window.hasErrors) {
        var formId = window.oldFormId;
        if (formId === 'create_user') {
            var createModal = new bootstrap.Modal(document.getElementById('createUserModal'));
            createModal.show();
        } else if (formId === 'edit_user') {
            var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        }
    }
});
