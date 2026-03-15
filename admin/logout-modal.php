<?php
// Modal de confirmacion de salida para todo el panel admin.
?>
<div id="logout-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative mx-auto mt-28 w-11/12 max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
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
    const backdrop = modal.firstElementChild;
    const confirmBtn = document.getElementById('logoutConfirm');
    const cancelBtn = document.getElementById('logoutCancel');
    let targetHref = null;

    const close = () => { modal.classList.add('hidden'); targetHref = null; };
    const open = (href) => { targetHref = href; modal.classList.remove('hidden'); };

    document.querySelectorAll('a[href="logout.php"]').forEach(link => {
        link.classList.add('logout-link');
        link.addEventListener('click', (ev) => {
            ev.preventDefault();
            ev.stopImmediatePropagation();
            open(link.getAttribute('href'));
        }, true); // captura para adelantarse a cualquier onclick inline
    });

    [backdrop, cancelBtn].forEach(el => el && el.addEventListener('click', close));
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => { if (targetHref) window.location.href = targetHref; });
    }
})();
</script>
