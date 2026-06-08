(function () {
    const btn = document.getElementById('passkey-login-btn');
    const block = document.getElementById('passkey-login-block');
    const error = document.getElementById('passkey-login-error');

    if (!btn || !block || !window.EsseWebAuthn || !EsseWebAuthn.isSupported()) {
        return;
    }

    const defaultHtml = btn.innerHTML;
    block.classList.remove('d-none');

    btn.addEventListener('click', async function () {
        error.classList.add('d-none');
        btn.disabled = true;
        btn.textContent = 'Warte auf Passkey ...';

        try {
            const result = await EsseWebAuthn.login(btn.dataset.csrfToken || '', btn.dataset.redirect || '');
            window.location.href = result.redirect || '/';
        } catch (e) {
            error.textContent = e.message || 'Anmeldung mit Passkey fehlgeschlagen.';
            error.classList.remove('d-none');
            btn.disabled = false;
            btn.innerHTML = defaultHtml;
        }
    });
})();
