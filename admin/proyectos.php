<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();
$statusMessage = $_GET['msg'] ?? '';
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$toast = admin_build_toast($statusMessage, [
    'deleted' => ['message' => 'Proyecto eliminado correctamente.'],
    'saved' => ['message' => 'Proyecto guardado correctamente.'],
    'csrf' => ['type' => 'error', 'title' => 'Sesion no valida', 'message' => 'Recarga la pagina e intenta de nuevo.'],
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: ' . admin_build_url('proyectos.php', ['q' => $searchTerm, 'page' => $page, 'msg' => 'csrf']));
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($action === 'delete' && $id > 0) {
        if ($stmt = $conn->prepare('DELETE FROM proyectos WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        admin_log_action($conn, 'delete', 'project', $id, 'Proyecto eliminado desde el listado');
        header('Location: ' . admin_build_url('proyectos.php', ['q' => trim((string) ($_POST['q'] ?? '')), 'page' => (int) ($_POST['page'] ?? 1), 'msg' => 'deleted']));
        exit;
    }
}

$whereSql = '';
if ($searchTerm !== '') {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $whereSql = " WHERE titulo LIKE '%{$safeSearch}%' OR categoria LIKE '%{$safeSearch}%' OR cliente LIKE '%{$safeSearch}%'";
}

$totalProjects = 0;
$totalResult = $conn->query('SELECT COUNT(*) AS total FROM proyectos' . $whereSql);
if ($totalResult instanceof mysqli_result) {
    $totalProjects = (int) ($totalResult->fetch_assoc()['total'] ?? 0);
    $totalResult->free();
}

$pagination = admin_paginate($totalProjects, $perPage, $page);
$projectsSql = 'SELECT * FROM proyectos' . $whereSql . ' ORDER BY orden ASC, id DESC LIMIT ' . $pagination['offset'] . ', ' . $pagination['per_page'];
$proyectos = $conn->query($projectsSql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php $activePage = 'proyectos'; include __DIR__ . '/partials/sidebar.php'; ?>

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
                <?php admin_render_toast($toast); ?>
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Proyectos</h1>
                        <p class="mt-2 text-sm text-gray-600">Busca, edita o elimina proyectos del portafolio desde este listado.</p>
                    </div>
                    <a href="proyecto-editar.php" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Nuevo Proyecto
                    </a>
                </div>

                <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-white p-5 shadow lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                        <div class="relative w-full md:max-w-xl">
                            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="text"
                                name="q"
                                value="<?php echo admin_escape($searchTerm); ?>"
                                placeholder="Buscar por titulo, categoria o cliente"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 focus:border-blue-600 focus:bg-white focus:outline-none"
                            >
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Buscar</button>
                            <a href="proyectos.php" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                    <div class="text-sm text-gray-500">
                        <?php echo $totalProjects; ?> proyecto<?php echo $totalProjects === 1 ? '' : 's'; ?> encontrado<?php echo $totalProjects === 1 ? '' : 's'; ?>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Titulo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Categoria</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Destacado</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($proyectos instanceof mysqli_result && $proyectos->num_rows > 0): ?>
                                    <?php while ($p = $proyectos->fetch_assoc()): ?>
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium">
                                                <a href="proyecto-editar.php?id=<?php echo (int) $p['id']; ?>" class="text-slate-900 hover:text-blue-600 hover:underline">
                                                    <?php echo admin_escape($p['titulo']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4"><?php echo admin_escape($p['categoria'] ?? '-'); ?></td>
                                            <td class="px-6 py-4"><?php echo admin_escape($p['cliente'] ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <?php echo !empty($p['fecha_completado']) ? date('d/m/Y', strtotime($p['fecha_completado'])) : '-'; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($p['destacado'])): ?>
                                                    <span class="rounded bg-green-100 px-2 py-1 text-sm text-green-700">Si</span>
                                                <?php else: ?>
                                                    <span class="rounded bg-gray-100 px-2 py-1 text-sm text-gray-700">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <a href="proyecto-editar.php?id=<?php echo (int) $p['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Eliminar este proyecto?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                                                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                        <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
                                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                                                            <i class="fas fa-trash"></i>
                                                            <span>Eliminar</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="border-t">
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <?php echo $searchTerm !== '' ? 'No encontramos proyectos con ese criterio de busqueda.' : 'Todavia no hay proyectos registrados.'; ?>
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
                                <a href="<?php echo admin_build_url('proyectos.php', ['q' => $searchTerm, 'page' => $pagination['page'] - 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php for ($pageNumber = 1; $pageNumber <= $pagination['total_pages']; $pageNumber++): ?>
                                <?php if (abs($pageNumber - $pagination['page']) > 2 && $pageNumber !== 1 && $pageNumber !== $pagination['total_pages']) continue; ?>
                                <a href="<?php echo admin_build_url('proyectos.php', ['q' => $searchTerm, 'page' => $pageNumber]); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo $pageNumber === $pagination['page'] ? 'bg-blue-600 text-white' : 'border text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $pageNumber; ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?php echo admin_build_url('proyectos.php', ['q' => $searchTerm, 'page' => $pagination['page'] + 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
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

