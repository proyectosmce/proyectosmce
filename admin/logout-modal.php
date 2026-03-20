<?php
// Modal de confirmación de salida para todo el panel admin.
?>
<div id="logout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
    <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
        <button type="button" id="logoutClose" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-900">¿Cerrar sesión?</h3>
                <p class="mt-1 text-sm text-slate-600">Confirma que deseas salir del panel.</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button id="logoutCancel" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Cancelar
            </button>
            <button id="logoutConfirm" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700">
                Sí, salir
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('logout-modal');
    if (!modal) return;
    const confirmBtn = document.getElementById('logoutConfirm');
    const cancelBtn = document.getElementById('logoutCancel');
    const closeBtn = document.getElementById('logoutClose');
    let targetHref = null;

    const close = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); targetHref = null; };
    const open = (href) => { targetHref = href; modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };

    document.querySelectorAll('a[href="logout.php"]').forEach(link => {
        link.classList.add('logout-link');
        link.addEventListener('click', (ev) => {
            ev.preventDefault();
            ev.stopImmediatePropagation();
            open(link.getAttribute('href'));
        }, true); // captura para adelantarse a cualquier onclick inline
    });

    [cancelBtn, closeBtn].forEach(el => el && el.addEventListener('click', close));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => { if (targetHref) window.location.href = targetHref; });
    }
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
})();
</script>
