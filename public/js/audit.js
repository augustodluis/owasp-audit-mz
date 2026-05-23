document.getElementById('auditForm')?.addEventListener('submit', function () {
    if (window.AUDMZ) {
        window.AUDMZ.notify('info', 'Auditoria iniciada. Sera notificado quando concluir.');
    }
});
