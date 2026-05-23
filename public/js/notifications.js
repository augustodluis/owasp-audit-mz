(function () {
    const stack = document.getElementById('toast-stack');
    const csrf  = document.querySelector('meta[name=csrf-token]')?.content;

    function notify(type, message) {
        if (!stack) return;
        const colorMap = {
            info: 'text-bg-info',
            success: 'text-bg-success',
            warning: 'text-bg-warning',
            critical: 'text-bg-danger',
        };
        const el = document.createElement('div');
        el.className = `toast align-items-center ${colorMap[type] || 'text-bg-info'} show`;
        el.role = 'alert';
        el.innerHTML = `<div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
        stack.appendChild(el);
        setTimeout(() => el.remove(), 7000);

        if ((type === 'critical' || type === 'warning') && 'Notification' in window) {
            if (Notification.permission === 'granted') {
                new Notification('OWASP-AUDIT-MZ', { body: message });
            }
        }
    }

    async function markRead(id) {
        const form = new FormData();
        form.append('_method', 'PATCH');
        form.append('_token', csrf);
        await fetch(`/notifications/${id}/read`, {
            method: 'POST',
            body: form,
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
    }

    async function poll() {
        if (document.visibilityState !== 'visible') return;
        try {
            const res = await fetch('/notifications', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const items = await res.json();
            for (const item of items) {
                notify(item.type, item.message);
                await markRead(item.id);
            }
        } catch (e) {}
    }

    window.AUDMZ = { notify, poll };

    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    setInterval(poll, 6000);
    poll();
})();
