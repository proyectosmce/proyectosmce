<?php
// Sidebar responsivo para panel admin
// Variables esperadas:
// $activePage: dashboard | proyectos | servicios | pagos | testimonios | mensajes | auditoria | password
// $pendingTestimonials opcional
$activePage = $activePage ?? '';
$pendingTestimonials = $pendingTestimonials ?? 0;
$alertPagos = $alertPagos ?? null;
$citasHoy = $citasHoy ?? null;

if ($alertPagos === null && isset($conn) && $conn instanceof mysqli) {
    $alertPagos = admin_count_pagos_alerta($conn);
}
if ($citasHoy === null && isset($conn) && $conn instanceof mysqli) {
    $citasHoy = admin_count_citas_hoy($conn);
}

function mce_nav_item(string $slug, string $href, string $label, string $icon, string $activePage): string
{
    $isActive = $slug === $activePage;
    $base = 'nav-link flex items-center space-x-2 p-2 rounded ';
    $active = 'bg-blue-50 text-blue-600';
    $inactive = 'hover:bg-gray-100';
    $classes = $base . ($isActive ? $active : $inactive);
    return '<a href="' . $href . '" class="' . $classes . '"><i class="fas ' . $icon . '"></i><span>' . $label . '</span></a>';
}
?>
<div id="sidebar-backdrop" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>
<aside id="sidebar" class="fixed lg:static z-40 inset-y-0 left-0 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <style>
        @keyframes logo-spin { to { transform: rotate(360deg); } }
        .animate-logo-spin { animation: logo-spin 8s linear infinite; }
    </style>
    <div class="p-4 border-b flex items-center justify-between lg:block">
        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10">
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-700 animate-logo-spin"></div>
                <div class="absolute inset-[3px] rounded-xl bg-white overflow-hidden border border-blue-100 shadow-sm">
                    <img src="../MCE.jpg" alt="MCE" class="w-full h-full object-cover">
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-[0.2em]">MCE</p>
                <h2 class="text-lg font-bold text-blue-700 leading-tight">Proyectos</h2>
            </div>
        </div>
        <button id="sidebar-close" class="lg:hidden text-blue-700 hover:text-blue-900">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    <div class="px-4 pt-3">
        <form action="buscar.php" method="get" class="relative">
            <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
                type="text"
                name="q"
                placeholder="Buscar cliente, proyecto..."
                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-3 text-sm focus:border-blue-600 focus:bg-white focus:outline-none"
            >
        </form>
    </div>
    <nav class="p-4">
        <ul class="space-y-2">
            <li class="flex items-center justify-between gap-2">
                <?= mce_nav_item('dashboard', 'dashboard.php', 'Dashboard', 'fa-home', $activePage) ?>
                <?php if (($citasHoy ?? 0) > 0): ?>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Citas hoy: <?= (int)$citasHoy; ?></span>
                <?php endif; ?>
            </li>
            <li><?= mce_nav_item('buscar', 'buscar.php', 'Buscar', 'fa-magnifying-glass', $activePage) ?></li>
            <li><?= mce_nav_item('proyectos', 'proyectos.php', 'Proyectos', 'fa-folder', $activePage) ?></li>
            <li><?= mce_nav_item('servicios', 'servicios.php', 'Servicios', 'fa-cog', $activePage) ?></li>
            <li class="flex items-center justify-between gap-2">
                <?= mce_nav_item('pagos', 'pagos.php', 'Pagos', 'fa-receipt', $activePage) ?>
                <?php if (($alertPagos ?? 0) > 0): ?>
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-500 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-red-600"></span>
                        </span>
                        <?= (int)$alertPagos; ?>
                    </span>
                <?php endif; ?>
            </li>
            <li>
                <div class="flex items-center space-x-2">
                    <?= mce_nav_item('testimonios', 'testimonios.php', 'Testimonios', 'fa-comment', $activePage) ?>
                    <?php if ($pendingTestimonials > 0): ?>
                        <span class="ml-auto inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-600"></span>
                            </span>
                            <?= $pendingTestimonials; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </li>
            <li><?= mce_nav_item('mensajes', 'mensajes.php', 'Mensajes', 'fa-envelope', $activePage) ?></li>
            <li><?= mce_nav_item('auditoria', 'auditoria.php', 'Actividad', 'fa-clock-rotate-left', $activePage) ?></li>
            <li><?= mce_nav_item('password', 'cambiar-password.php', 'Cambiar clave', 'fa-lock', $activePage) ?></li>
            <li><a href="logout.php" class="nav-link flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
        </ul>
    </nav>
</aside>
