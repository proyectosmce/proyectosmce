<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureTestimonialsSchema($conn);

$pendingCount = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();
$statusMessage = $_GET['msg'] ?? '';
$statusFilter = $_GET['estado'] ?? 'todos';
$statusFilter = in_array($statusFilter, ['todos', 'pendientes', 'publicados'], true) ? $statusFilter : 'todos';
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$publishedCount = 0;
$toast = admin_build_toast($statusMessage, [
    'deleted' => ['message' => 'Testimonio eliminado correctamente.'],
    'saved' => ['message' => 'Testimonio guardado correctamente.'],
    'approved' => ['title' => 'Testimonio confirmado', 'message' => 'El testimonio ya esta publicado en la web.'],
    'hidden' => ['type' => 'warning', 'title' => 'Testimonio pendiente', 'message' => 'El testimonio se marco como pendiente.'],
    'csrf' => ['type' => 'error', 'title' => 'Sesion no valida', 'message' => 'Recarga la pagina e intenta de nuevo.'],
]);

if ($publishedResult = $conn->query('SELECT COUNT(*) AS total FROM testimonios WHERE aprobado = 1')) {
    $publishedCount = (int) ($publishedResult->fetch_assoc()['total'] ?? 0);
    $publishedResult->free();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestStatus = $_POST['estado'] ?? 'todos';
    $requestStatus = in_array($requestStatus, ['todos', 'pendientes', 'publicados'], true) ? $requestStatus : 'todos';
    $requestSearch = trim((string) ($_POST['q'] ?? ''));
    $requestPage = max(1, (int) ($_POST['page'] ?? 1));

    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: ' . admin_build_url('testimonios.php', ['estado' => $requestStatus !== 'todos' ? $requestStatus : null, 'q' => $requestSearch, 'page' => $requestPage, 'msg' => 'csrf']));
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($action === 'export_csv') {
        $conditions = [];
        if ($requestStatus === 'pendientes') {
            $conditions[] = 't.aprobado = 0';
        } elseif ($requestStatus === 'publicados') {
            $conditions[] = 't.aprobado = 1';
        }
        if ($requestSearch !== '') {
            $safeSearch = $conn->real_escape_string($requestSearch);
            $conditions[] = "(t.nombre LIKE '%{$safeSearch}%' OR t.empresa LIKE '%{$safeSearch}%' OR t.testimonio LIKE '%{$safeSearch}%' OR COALESCE(p.titulo, '') LIKE '%{$safeSearch}%')";
        }

        $whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $rows = [];
        $sql = "
            SELECT t.nombre, t.empresa, t.testimonio, t.valoracion, COALESCE(p.titulo, '') AS proyecto_titulo, t.aprobado, t.destacado, t.created_at
            FROM testimonios t
            LEFT JOIN proyectos p ON t.proyecto_id = p.id
            {$whereSql}
            ORDER BY t.aprobado ASC, t.destacado DESC, t.orden ASC, t.id DESC
        ";
        if ($result = $conn->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = [
                    $row['nombre'],
                    $row['empresa'],
                    $row['testimonio'],
                    $row['valoracion'],
                    $row['proyecto_titulo'],
                    !empty($row['aprobado']) ? 'Publicado' : 'Pendiente',
                    !empty($row['destacado']) ? 'Si' : 'No',
                    $row['created_at'],
                ];
            }
            $result->free();
        }

        admin_log_action($conn, 'export', 'testimonial', null, 'Exportacion CSV de testimonios');
        admin_send_csv('testimonios-' . date('Ymd-His') . '.csv', ['Nombre', 'Empresa', 'Testimonio', 'Valoracion', 'Proyecto', 'Estado', 'Destacado', 'Fecha'], $rows);
    }

    if ($id > 0) {
        $redirectUrl = admin_build_url('testimonios.php', [
            'estado' => $requestStatus !== 'todos' ? $requestStatus : null,
            'q' => $requestSearch,
            'page' => $requestPage,
        ]);
        $joiner = strpos($redirectUrl, '?') === false ? '?' : '&';

        if ($action === 'delete') {
            $photo = null;
            if ($stmt = $conn->prepare('SELECT foto FROM testimonios WHERE id = ? LIMIT 1')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->bind_result($photo);
                $stmt->fetch();
                $stmt->close();
            }

            if ($stmt = $conn->prepare('DELETE FROM testimonios WHERE id = ?')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }

            if ($photo && file_exists("../assets/img/testimonios/{$photo}")) {
                unlink("../assets/img/testimonios/{$photo}");
            }

            admin_log_action($conn, 'delete', 'testimonial', $id, 'Testimonio eliminado desde el listado');
            header('Location: ' . $redirectUrl . $joiner . 'msg=deleted');
            exit;
        }

        if ($action === 'approve' || $action === 'hide') {
            $approved = $action === 'approve' ? 1 : 0;
            if ($stmt = $conn->prepare('UPDATE testimonios SET aprobado = ? WHERE id = ?')) {
                $stmt->bind_param('ii', $approved, $id);
                $stmt->execute();
                $stmt->close();
            }

            admin_log_action($conn, $approved ? 'approve' : 'hide', 'testimonial', $id, $approved ? 'Testimonio confirmado y publicado' : 'Testimonio devuelto a pendiente');
            header('Location: ' . $redirectUrl . $joiner . 'msg=' . ($approved ? 'approved' : 'hidden'));
            exit;
        }
    }
}

$conditions = [];
if ($statusFilter === 'pendientes') {
    $conditions[] = 't.aprobado = 0';
} elseif ($statusFilter === 'publicados') {
    $conditions[] = 't.aprobado = 1';
}
if ($searchTerm !== '') {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $conditions[] = "(t.nombre LIKE '%{$safeSearch}%' OR t.empresa LIKE '%{$safeSearch}%' OR t.testimonio LIKE '%{$safeSearch}%' OR COALESCE(p.titulo, '') LIKE '%{$safeSearch}%')";
}
$whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

$filteredTestimonials = 0;
$countSql = "
    SELECT COUNT(*) AS total
    FROM testimonios t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    {$whereSql}
";
if ($countResult = $conn->query($countSql)) {
    $filteredTestimonials = (int) ($countResult->fetch_assoc()['total'] ?? 0);
    $countResult->free();
}

$pagination = admin_paginate($filteredTestimonials, $perPage, $page);
$testimonialsSql = "
    SELECT t.*, p.titulo AS proyecto_titulo
    FROM testimonios t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    {$whereSql}
    ORDER BY t.aprobado ASC, t.destacado DESC, t.orden ASC, t.id DESC
    LIMIT {$pagination['offset']}, {$pagination['per_page']}
";
$testimonios = $conn->query($testimonialsSql);
$totalCount = $pendingCount + $publishedCount;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonios - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>.logo-ring{position:absolute;inset:0;border:2px solid transparent;border-radius:8px;background:conic-gradient(from 0deg,#2563eb,#38bdf8,#2563eb);background-origin:border-box;animation:logo-spin 4s linear infinite;}@keyframes logo-spin{to{transform:rotate(360deg);}}</style>
</head>
<body class="bg-gray-100">
    <!-- Barra móvil -->
    <header class="md:hidden sticky top-0 z-30 flex items-center justify-between bg-white px-4 py-3 shadow">
        <div class="flex items-center gap-2">
            <button id="toggleSidebar" class="p-2 rounded border border-blue-500/60 bg-gradient-to-br from-blue-500 via-blue-400 to-cyan-300 text-white shadow-[0_0_12px_rgba(59,130,246,0.65)] hover:shadow-[0_0_16px_rgba(56,189,248,0.75)] active:scale-95 transition">
                <i class="fas fa-bars text-white"></i>
            </button>
            <div class="relative h-10 w-10 shrink-0">
    <span class="logo-ring"></span>
    <img src="../imag/MCE.jpg" alt="MCE Admin" class="absolute inset-1 h-8 w-8 object-contain">
</div>
        </div>
        <a href="logout.php" class="text-red-600 text-sm flex items-center gap-1"><i class="fas fa-sign-out-alt"></i>Salir</a>
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
                    <li><a href="testimonios.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-comment"></i><span>Testimonios</span></a></li>
                    <li>
                        <a href="testimonios.php" class="flex items-center space-x-2 rounded bg-blue-50 p-2 text-blue-600">
                            <i class="fas fa-comment"></i>
                            <span>Testimonios</span>
                            <?php if ($pendingCount > 0): ?>
                                <span class="ml-auto inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <span class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-600"></span>
                                    </span>
                                    <?php echo $pendingCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
 
                    <li><a href="mensajes.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-envelope"></i><span>Mensajes</span></a></li>
                    <li><a href="auditoria.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-clock-rotate-left"></i><span>Actividad</span></a></li>
                    <li><a href="cambiar-password.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-lock"></i><span>Cambiar clave</span></a></li>
                    <li><a href="logout.php" onclick="return confirm('¿Cerrar sesión?');" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <?php admin_render_toast($toast); ?>
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Testimonios de Clientes</h1>
                        <div class="mt-3 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold <?php echo $pendingCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-700'; ?>">
                            <?php if ($pendingCount > 0): ?>
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-amber-600"></span>
                                </span>
                            <?php endif; ?>
                            <?php echo $pendingCount; ?> pendiente<?php echo $pendingCount === 1 ? '' : 's'; ?> de aprobacion
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                            <input type="hidden" name="action" value="export_csv">
                            <input type="hidden" name="estado" value="<?php echo admin_escape($statusFilter); ?>">
                            <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
                                <i class="fas fa-file-csv"></i>
                                <span>Exportar CSV</span>
                            </button>
                        </form>
                        <a href="testimonio-editar.php" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Nuevo Testimonio
                        </a>
                    </div>
                </div>

                <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-white p-5 shadow lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap gap-3">
                        <a href="<?php echo admin_build_url('testimonios.php', ['q' => $searchTerm]); ?>" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $statusFilter === 'todos' ? 'bg-slate-900 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            Todos
                            <span class="ml-2 rounded-full <?php echo $statusFilter === 'todos' ? 'bg-white/20 text-white' : 'bg-white text-gray-600'; ?> px-2 py-0.5 text-xs">
                                <?php echo $totalCount; ?>
                            </span>
                        </a>
                        <a href="<?php echo admin_build_url('testimonios.php', ['estado' => 'pendientes', 'q' => $searchTerm]); ?>" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $statusFilter === 'pendientes' ? 'bg-amber-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            Pendientes
                            <span class="ml-2 rounded-full <?php echo $statusFilter === 'pendientes' ? 'bg-white/20 text-white' : 'bg-white text-amber-700'; ?> px-2 py-0.5 text-xs">
                                <?php echo $pendingCount; ?>
                            </span>
                        </a>
                        <a href="<?php echo admin_build_url('testimonios.php', ['estado' => 'publicados', 'q' => $searchTerm]); ?>" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $statusFilter === 'publicados' ? 'bg-green-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            Publicados
                            <span class="ml-2 rounded-full <?php echo $statusFilter === 'publicados' ? 'bg-white/20 text-white' : 'bg-white text-green-700'; ?> px-2 py-0.5 text-xs">
                                <?php echo $publishedCount; ?>
                            </span>
                        </a>
                    </div>
                    <form method="GET" class="flex w-full flex-col gap-3 md:flex-row md:items-center lg:max-w-2xl">
                        <?php if ($statusFilter !== 'todos'): ?>
                            <input type="hidden" name="estado" value="<?php echo admin_escape($statusFilter); ?>">
                        <?php endif; ?>
                        <div class="relative w-full">
                            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="text"
                                name="q"
                                value="<?php echo admin_escape($searchTerm); ?>"
                                placeholder="Buscar por cliente, empresa, proyecto o contenido"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 focus:border-blue-600 focus:bg-white focus:outline-none"
                            >
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Buscar</button>
                            <a href="testimonios.php" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                </div>

                <?php if ($pendingCount > 0): ?>
                    <div class="mb-6 flex items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 via-orange-50 to-red-50 px-5 py-4 shadow-sm">
                        <div class="flex items-center gap-4">
                            <span class="relative flex h-4 w-4">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex h-4 w-4 rounded-full bg-red-500"></span>
                            </span>
                            <p class="text-sm font-semibold text-amber-800">
                                Hay testimonios nuevos esperando tu aprobacion. Puedes confirmarlos desde esta tabla o entrar a editar el detalle.
                            </p>
                        </div>
                        <a href="<?php echo admin_build_url('testimonios.php', ['estado' => 'pendientes', 'q' => $searchTerm]); ?>" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-amber-700 shadow-sm hover:bg-amber-100">
                            Revisar pendientes
                        </a>
                    </div>
                <?php endif; ?>

                <div class="mb-4 text-sm text-gray-500">
                    Mostrando <?php echo $filteredTestimonials; ?> resultado<?php echo $filteredTestimonials === 1 ? '' : 's'; ?><?php echo $searchTerm !== '' ? ' para "' . admin_escape($searchTerm) . '"' : ''; ?>.
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1180px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Foto</th>
                                    <th class="px-6 py-3 text-left">Cliente</th>
                                    <th class="px-6 py-3 text-left">Empresa</th>
                                    <th class="px-6 py-3 text-left">Testimonio</th>
                                    <th class="px-6 py-3 text-left">Valoracion</th>
                                    <th class="px-6 py-3 text-left">Proyecto</th>
                                    <th class="px-6 py-3 text-left">Estado</th>
                                    <th class="px-6 py-3 text-left">Destacado</th>
                                    <th class="px-6 py-3 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($testimonios instanceof mysqli_result && $testimonios->num_rows > 0): ?>
                                    <?php while ($t = $testimonios->fetch_assoc()): ?>
                                        <?php $isPending = (int) ($t['aprobado'] ?? 1) === 0; ?>
                                        <tr class="border-t hover:bg-gray-50 <?php echo $isPending ? 'bg-amber-50/60' : ''; ?>">
                                            <td class="px-6 py-4">
                                                <?php if (!empty($t['foto'])): ?>
                                                    <img src="../assets/img/testimonios/<?php echo admin_escape($t['foto']); ?>" alt="<?php echo admin_escape($t['nombre']); ?>" class="h-10 w-10 rounded-full object-cover">
                                                <?php else: ?>
                                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300">
                                                        <i class="fas fa-user text-gray-500"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 font-medium">
                                                <a href="testimonio-editar.php?id=<?php echo (int) $t['id']; ?>" class="text-slate-900 hover:text-blue-600 hover:underline">
                                                    <?php echo admin_escape($t['nombre']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4"><?php echo admin_escape($t['empresa'] ?? '-'); ?></td>
                                            <td class="max-w-xs px-6 py-4">
                                                <a href="testimonio-editar.php?id=<?php echo (int) $t['id']; ?>" class="block truncate text-gray-700 hover:text-blue-600" title="<?php echo admin_escape($t['testimonio']); ?>">
                                                    <?php echo admin_escape($t['testimonio']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex text-yellow-400">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?php if ($i <= (int) ($t['valoracion'] ?? 0)): ?>
                                                            <i class="fas fa-star"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-star"></i>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4"><?php echo admin_escape($t['proyecto_titulo'] ?? '-'); ?></td>
                                            <td class="px-6 py-4">
                                                <?php if ($isPending): ?>
                                                    <span class="rounded bg-amber-100 px-2 py-1 text-sm text-amber-800">Pendiente</span>
                                                <?php else: ?>
                                                    <span class="rounded bg-green-100 px-2 py-1 text-sm text-green-800">Publicado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($t['destacado'])): ?>
                                                    <span class="rounded bg-green-100 px-2 py-1 text-sm text-green-800">Si</span>
                                                <?php else: ?>
                                                    <span class="rounded bg-gray-100 px-2 py-1 text-sm text-gray-700">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <a href="testimonio-editar.php?id=<?php echo (int) $t['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>

                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>">
                                                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                        <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo admin_escape($statusFilter); ?>">
                                                        <input type="hidden" name="action" value="<?php echo $isPending ? 'approve' : 'hide'; ?>">
                                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium <?php echo $isPending ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'; ?>">
                                                            <i class="fas <?php echo $isPending ? 'fa-check-circle' : 'fa-eye-slash'; ?>"></i>
                                                            <span><?php echo $isPending ? 'Confirmar' : 'Pendiente'; ?></span>
                                                        </button>
                                                    </form>

                                                    <form method="POST" class="inline" onsubmit="return confirm('Eliminar este testimonio?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>">
                                                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                        <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo admin_escape($statusFilter); ?>">
                                                        <input type="hidden" name="action" value="delete">
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
                                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                            <?php echo $searchTerm !== '' ? 'No encontramos testimonios con ese criterio de busqueda.' : ($statusFilter === 'pendientes' ? 'No hay testimonios pendientes por confirmar.' : ($statusFilter === 'publicados' ? 'No hay testimonios publicados en este momento.' : 'No se pudieron cargar los testimonios.')); ?>
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
                                <a href="<?php echo admin_build_url('testimonios.php', ['estado' => $statusFilter !== 'todos' ? $statusFilter : null, 'q' => $searchTerm, 'page' => $pagination['page'] - 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php for ($pageNumber = 1; $pageNumber <= $pagination['total_pages']; $pageNumber++): ?>
                                <?php if (abs($pageNumber - $pagination['page']) > 2 && $pageNumber !== 1 && $pageNumber !== $pagination['total_pages']) continue; ?>
                                <a href="<?php echo admin_build_url('testimonios.php', ['estado' => $statusFilter !== 'todos' ? $statusFilter : null, 'q' => $searchTerm, 'page' => $pageNumber]); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo $pageNumber === $pagination['page'] ? 'bg-blue-600 text-white' : 'border text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $pageNumber; ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?php echo admin_build_url('testimonios.php', ['estado' => $statusFilter !== 'todos' ? $statusFilter : null, 'q' => $searchTerm, 'page' => $pagination['page'] + 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
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
</html>









