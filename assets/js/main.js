document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const alertBox = document.getElementById('formAlert');

    if (alertBox) {
        let message = '';
        let type = 'danger';
        if (params.has('error')) {
            message = params.get('error');
        } else if (params.get('registered') === '1') {
            message = 'Account created successfully. You can now log in.';
            type = 'success';
        } else if (params.get('logout') === '1') {
            message = 'You have been logged out safely.';
            type = 'success';
        }

        if (message) {
            alertBox.textContent = message;
            alertBox.classList.add(`alert-${type}`);
            alertBox.hidden = false;
        }
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.innerHTML = `<i class="bi ${show ? 'bi-eye-slash' : 'bi-eye'}"></i>`;
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
});
function showPreview(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
