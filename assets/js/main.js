// ============================================================
// DMS Custom JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- AJAX Login from popup modal ----
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const alertBox = document.getElementById('loginAlert');
            const btn = document.getElementById('loginBtn');
            const btnText = btn ? btn.innerHTML : 'Login';

            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Signing in...'; }
            if (alertBox) { alertBox.style.display = 'none'; }

            const formData = new FormData(loginForm);

            fetch('actions/login_action.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        if (alertBox) {
                            alertBox.style.display = 'block';
                            alertBox.textContent = data.message || 'Login failed.';
                        }
                        if (btn) { btn.disabled = false; btn.innerHTML = btnText; }
                    }
                })
                .catch(() => {
                    if (alertBox) {
                        alertBox.style.display = 'block';
                        alertBox.textContent = 'Connection error. Please try again.';
                    }
                    if (btn) { btn.disabled = false; btn.innerHTML = btnText; }
                });
        });
    }

    // ---- Auto-dismiss alerts ----
    const autoAlerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    autoAlerts.forEach(a => setTimeout(() => { if (a) a.style.display = 'none'; }, 4000));

    // ---- Confirm delete ----
    document.querySelectorAll('.btn-confirm').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm || 'Are you sure?')) e.preventDefault();
        });
    });

    // ---- Live document search ----
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.doc-searchable').forEach(card => {
                const text = card.dataset.search.toLowerCase();
                card.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
});
