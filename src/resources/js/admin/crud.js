// Admin CRUD modal population logic
document.addEventListener('DOMContentLoaded', function () {
    // Subjects edit modal
    const editSubjectModal = document.getElementById('editSubjectModal');
    if (editSubjectModal) {
        editSubjectModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const description = button.getAttribute('data-description');
            const isActive = button.getAttribute('data-is-active') === '1';

            this.querySelector('#edit_name').value = name;
            this.querySelector('#edit_description').value = description || '';
            this.querySelector('#edit_is_active').checked = isActive;

            const form = this.querySelector('#editSubjectForm');
            form.action = `/admin/subjects/${id}`;
        });
    }

    // Education Levels edit modal
    const editLevelModal = document.getElementById('editLevelModal');
    if (editLevelModal) {
        editLevelModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const order = button.getAttribute('data-order');
            const isActive = button.getAttribute('data-is-active') === '1';

            this.querySelector('#edit_name').value = name;
            this.querySelector('#edit_order').value = order;
            this.querySelector('#edit_is_active').checked = isActive;

            const form = this.querySelector('#editLevelForm');
            form.action = `/admin/education-levels/${id}`;
        });
    }

    // Learning Modes edit modal
    const editModeModal = document.getElementById('editModeModal');
    if (editModeModal) {
        editModeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const icon = button.getAttribute('data-icon');
            const isActive = button.getAttribute('data-is-active') === '1';

            this.querySelector('#edit_name').value = name;
            this.querySelector('#edit_icon').value = icon || '';
            this.querySelector('#edit_is_active').checked = isActive;

            const form = this.querySelector('#editModeForm');
            form.action = `/admin/learning-modes/${id}`;
        });
    }
});
