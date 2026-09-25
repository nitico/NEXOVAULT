(() => {
    const mask = '••••••••••••';
    async function readSecret(button) {
        const response = await fetch(button.dataset.url, {
            method: 'POST', cache: 'no-store',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="credential-csrf"]').content, Accept: 'application/json' }
        });
        if (!response.ok || response.redirected) throw new Error('Secret unavailable');
        const data = await response.json();
        if (typeof data.password !== 'string') throw new Error('Secret unavailable');
        return data.password;
    }
    document.querySelectorAll('.reveal[data-url]').forEach(button => {
        let timer;
        const secret = button.closest('.row').querySelector('.secret');
        const hide = () => { clearTimeout(timer); secret.textContent = mask; button.textContent = 'Revelar'; };
        button.onclick = async () => {
            if (timer) { hide(); timer = null; return; }
            button.disabled = true;
            try {
                secret.textContent = await readSecret(button);
                button.textContent = 'Ocultar';
                timer = setTimeout(() => { hide(); timer = null; }, 15000);
            } catch { hide(); button.textContent = 'Reintentar'; }
            finally { button.disabled = false; }
        };
        window.addEventListener('pagehide', hide);
    });
    document.querySelectorAll('.copy-secret[data-url]').forEach(button => {
        button.onclick = async () => {
            button.disabled = true;
            try {
                await navigator.clipboard.writeText(await readSecret(button));
                button.textContent = 'Copiado';
            } catch { button.textContent = 'Reintentar copia'; }
            finally { button.disabled = false; }
        };
    });
})();
