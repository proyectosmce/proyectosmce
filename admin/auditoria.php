<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureAdminActivityLogSchema($conn);

$pendingTestimonials = getPendingTestimonialsCount($conn);
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;

$whereSql = '';
if ($searchTerm !== '') {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $whereSql = " WHERE admin_username LIKE '%{$safeSearch}%' OR action LIKE '%{$safeSearch}%' OR entity_type LIKE '%{$safeSearch}%' OR COALESCE(description, '') LIKE '%{$safeSearch}%'";
}

$totalLogs = 0;
if ($result = $conn->query('SELECT COUNT(*) AS total FROM admin_activity_log' . $whereSql)) {
    $totalLogs = (int) ($result->fetch_assoc()['total'] ?? 0);
    $result->free();
}

$pagination = admin_paginate($totalLogs, $perPage, $page);
$logs = $conn->query('SELECT * FROM admin_activity_log' . $whereSql . ' ORDER BY created_at DESC LIMIT ' . $pagination['offset'] . ', ' . $pagination['per_page']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>.logo-ring{position:absolute;inset:0;border:2px solid transparent;border-radius:8px;background:conic-gradient(from 0deg,#2563eb,#38bdf8,#2563eb);background-origin:border-box;animation:logo-spin 4s linear infinite;}@keyframes logo-spin{to{transform:rotate(360deg);}}</style>
</head>
<body class="bg-gray-100">
    <!-- Barra móvil -->
    <header class="md:hidden sticky top-0 z-30 flex items-center justify-between bg-white px-4 py-3 shadow">
        <div class="flex items-center gap-2">
            <div class="relative h-10 w-10 shrink-0">
    <span class="logo-ring"></span>
    <img src="../imag/MCE.jpg" alt="MCE Admin" class="absolute inset-1 h-8 w-8 object-contain">
</div>
            <button id="toggleSidebar" class="p-2 rounded border border-blue-500/60 bg-gradient-to-br from-blue-500 via-blue-400 to-cyan-300 text-white shadow-[0_0_12px_rgba(59,130,246,0.65)] hover:shadow-[0_0_16px_rgba(56,189,248,0.75)] active:scale-95 transition">
                <i class="fas fa-bars text-white"></i>
            </button>
        </div>
        <a href="logout.php" onclick="return confirm('¿Cerrar sesión?' );" class="text-red-600 text-sm flex items-center gap-1"><i class="fas fa-sign-out-alt"></i>Salir</a>
    </header>

    <div class="flex min-h-screen">
        <div id="sidebar" class="fixed md:static inset-y-0 left-0 w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-200 z-40">
            <div class="p-4 border-b">
                <div class="relative h-10 w-10 shrink-0">
    <span class="logo-ring"></span>
    <img src="../imag/MCE.jpg" alt="MCE Admin" class="absolute inset-1 h-8 w-8 object-contain">
</div>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li><a href="dashboard.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                    <li><a href="proyectos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-folder"></i><span>Proyectos</span></a></li>
                    <li><a href="servicios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-cog"></i><span>Servicios</span></a></li>
                    <li><a href="pagos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-credit-card"></i><span>Pagos</span></a></li>
                    <li>
                        <a href="testimonios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-comment"></i>
                            <span>Testimonios</span>
                            <?php if ($pendingTestimonials > 0): ?>
                                <span class="ml-auto inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <span class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-600"></span>
                                    </span>
                                    <?php echo $pendingTestimonials; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="mensajes.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-envelope"></i><span>Mensajes</span></a></li>
                    <li><a href="auditoria.php" class="flex items-center space-x-2 rounded bg-blue-50 p-2 text-blue-600"><i class="fas fa-clock-rotate-left"></i><span>Actividad</span></a></li>
                    <li><a href="cambiar-password.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-lock"></i><span>Cambiar clave</span></a></li>
                    <li><a href="logout.php" onclick="return confirm('¿Cerrar sesión?');" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold">Actividad del Admin</h1>
                    <p class="mt-2 text-sm text-gray-600">Aqui ves los cambios recientes hechos dentro del panel.</p>
                </div>

                <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-white p-5 shadow lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                        <div class="relative w-full md:max-w-xl">
                            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="text"
                                name="q"
                                value="<?php echo admin_escape($searchTerm); ?>"
                                placeholder="Buscar por usuario, accion, modulo o descripcion"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 focus:border-blue-600 focus:bg-white focus:outline-none"
                            >
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Buscar</button>
                            <a href="auditoria.php" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                    <div class="text-sm text-gray-500">
                        <?php echo $totalLogs; ?> evento<?php echo $totalLogs === 1 ? '' : 's'; ?> registrado<?php echo $totalLogs === 1 ? '' : 's'; ?>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Usuario</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Accion</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Modulo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Descripcion</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($logs instanceof mysqli_result && $logs->num_rows > 0): ?>
                                    <?php while ($log = $logs->fetch_assoc()): ?>
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                            <td class="px-6 py-4 font-medium"><?php echo admin_escape($log['admin_username']); ?></td>
                                            <td class="px-6 py-4"><?php echo admin_escape($log['action']); ?></td>
                                            <td class="px-6 py-4"><?php echo admin_escape($log['entity_type']); ?></td>
                                            <td class="px-6 py-4"><?php echo isset($log['entity_id']) ? (int) $log['entity_id'] : '-'; ?></td>
                                            <td class="px-6 py-4 max-w-xl text-gray-700"><?php echo admin_escape($log['description'] ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"><?php echo admin_escape($log['ip_address'] ?? '-'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="border-t">
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <?php echo $searchTerm !== '' ? 'No encontramos actividad con ese criterio de busqueda.' : 'Todavia no hay actividad registrada.'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Pagina <?php echo $pagination['page']; ?> de <?php echo $pagination['total_pages']; ?>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <?php if ($pagination['has_prev']): ?>
                                <a href="<?php echo admin_build_url('auditoria.php', ['q' => $searchTerm, 'page' => $pagination['page'] - 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php for ($pageNumber = 1; $pageNumber <= $pagination['total_pages']; $pageNumber++): ?>
                                <?php if (abs($pageNumber - $pagination['page']) > 2 && $pageNumber !== 1 && $pageNumber !== $pagination['total_pages']) continue; ?>
                                <a href="<?php echo admin_build_url('auditoria.php', ['q' => $searchTerm, 'page' => $pageNumber]); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo $pageNumber === $pagination['page'] ? 'bg-blue-600 text-white' : 'border text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $pageNumber; ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?php echo admin_build_url('auditoria.php', ['q' => $searchTerm, 'page' => $pagination['page'] + 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.getElementById('toggleSidebar');
function closeSidebar(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
function openSidebar(){ sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
if (toggleBtn){ toggleBtn.addEventListener('click', ()=> sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar()); }
if (overlay){ overlay.addEventListener('click', closeSidebar); }
</script>
<?php include 'logout-modal.php'; ?>
</html>










