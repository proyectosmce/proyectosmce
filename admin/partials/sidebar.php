<?php
// Sidebar responsivo para panel admin
// Variables esperadas:
// $activePage: dashboard | proyectos | servicios | pagos | testimonios | mensajes | auditoria | password
// $pendingTestimonials opcional
$activePage = $activePage ?? '';
$pendingTestimonials = $pendingTestimonials ?? 0;

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
        @keyframes spin-slow { to { transform: rotate(360deg); } }
        .animate-spin-slow { animation: spin-slow 6s linear infinite; }
    </style>
    <div class="p-4 border-b flex items-center justify-between lg:block">
        <div class="flex items-center gap-3">
            <div class="relative w-12 h-12">
                <div class="absolute inset-0 rounded-full border-2 border-transparent bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-700 animate-spin-slow"></div>
                <div class="absolute inset-[3px] rounded-full bg-white"></div>
                <div class="relative w-full h-full rounded-full overflow-hidden border border-blue-100 shadow-sm">
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
    <nav class="p-4">
        <ul class="space-y-2">
            <li><?= mce_nav_item('dashboard', 'dashboard.php', 'Dashboard', 'fa-home', $activePage) ?></li>
            <li><?= mce_nav_item('proyectos', 'proyectos.php', 'Proyectos', 'fa-folder', $activePage) ?></li>
            <li><?= mce_nav_item('servicios', 'servicios.php', 'Servicios', 'fa-cog', $activePage) ?></li>
            <li><?= mce_nav_item('pagos', 'pagos.php', 'Pagos', 'fa-receipt', $activePage) ?></li>
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
