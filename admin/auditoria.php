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
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php $activePage = 'auditoria'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 overflow-y-auto lg:ml-0">
            <div class="p-8">
                <div class="mb-4 flex items-center justify-between lg:hidden">
                    <button id="sidebar-open" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50">
                        <span class="flex flex-col gap-1">
                            <span class="block w-5 h-0.5 bg-blue-700"></span>
                            <span class="block w-5 h-0.5 bg-blue-700"></span>
                            <span class="block w-5 h-0.5 bg-blue-700"></span>
                        </span>
                        <span>Menú</span>
                    </button>
                </div>
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
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>
