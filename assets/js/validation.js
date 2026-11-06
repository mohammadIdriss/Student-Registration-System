document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('studentForm');
    if (!form) return;

    const fields = ['name', 'email', 'phone', 'address'];
    const submitButton = document.getElementById('submitButton');
    const formStatus = document.getElementById('formStatus');
    let requestSequence = 0;

    function showFieldError(fieldName, message = '') {
        const input = form.elements[fieldName];
        const error = document.getElementById(`${fieldName}Error`);
        if (!input || !error) return;
        input.classList.toggle('is-invalid', Boolean(message));
        input.classList.toggle('is-valid', !message && input.value.trim() !== '');
        error.textContent = message;
    }

    async function validateForm(changedField = null) {
        const sequence = ++requestSequence;
        const data = new FormData();
        fields.forEach((field) => data.append(field, form.elements[field].value));
        data.append('csrf_token', form.elements.csrf_token.value);

        try {
            const response = await fetch('validate_student.php', {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            if (sequence !== requestSequence || !result.errors) return;

            fields.forEach((field) => {
                if (!changedField || field === changedField) {
                    showFieldError(field, result.errors[field] || '');
                }
            });
            return result.errors;
        } catch (error) {
            return null;
        }
    }

    fields.forEach((field) => {
        form.elements[field].addEventListener('blur', () => validateForm(field));
        form.elements[field].addEventListener('input', () => {
            if (form.elements[field].classList.contains('is-invalid')) {
                validateForm(field);
            }
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        formStatus.textContent = '';
        fields.forEach((field) => showFieldError(field));
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

        try {
            const errors = await validateForm();
            if (errors && Object.keys(errors).length > 0) {
                fields.forEach((field) => showFieldError(field, errors[field] || ''));
                form.elements[Object.keys(errors)[0]].focus();
                return;
            }

            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                if (result.errors) {
                    Object.entries(result.errors).forEach(([field, message]) => showFieldError(field, message));
                }
                throw new Error(result.message || 'Please correct the highlighted fields.');
            }
            window.location.href = result.redirect;
        } catch (error) {
            formStatus.textContent = error.message || 'Unable to save. Check your connection and try again.';
            formStatus.className = 'alert alert-danger mt-3 mb-0';
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-check2 me-1"></i>Add student';
        }
    });
});
