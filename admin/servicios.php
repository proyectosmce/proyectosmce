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
    'deleted' => ['message' => 'Servicio eliminado correctamente.'],
    'saved' => ['message' => 'Servicio guardado correctamente.'],
    'featured' => ['type' => 'info', 'title' => 'Estado actualizado', 'message' => 'El estado destacado del servicio se actualizo.'],
    'csrf' => ['type' => 'error', 'title' => 'Sesion no valida', 'message' => 'Recarga la pagina e intenta de nuevo.'],
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: ' . admin_build_url('servicios.php', ['q' => $searchTerm, 'page' => $page, 'msg' => 'csrf']));
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $returnSearch = trim((string) ($_POST['q'] ?? ''));
    $returnPage = max(1, (int) ($_POST['page'] ?? 1));

    if ($id > 0 && $action === 'delete') {
        if ($stmt = $conn->prepare('DELETE FROM servicios WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        admin_log_action($conn, 'delete', 'service', $id, 'Servicio eliminado desde el listado');
        header('Location: ' . admin_build_url('servicios.php', ['q' => $returnSearch, 'page' => $returnPage, 'msg' => 'deleted']));
        exit;
    }

    if ($id > 0 && $action === 'toggle_featured') {
        if ($stmt = $conn->prepare('UPDATE servicios SET destacado = NOT destacado WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        admin_log_action($conn, 'toggle_featured', 'service', $id, 'Servicio marcado o desmarcado como destacado');
        header('Location: ' . admin_build_url('servicios.php', ['q' => $returnSearch, 'page' => $returnPage, 'msg' => 'featured']));
        exit;
    }
}

$whereSql = '';
if ($searchTerm !== '') {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $whereSql = " WHERE titulo LIKE '%{$safeSearch}%' OR descripcion LIKE '%{$safeSearch}%' OR icono LIKE '%{$safeSearch}%'";
}

$totalServices = 0;
$totalResult = $conn->query('SELECT COUNT(*) AS total FROM servicios' . $whereSql);
if ($totalResult instanceof mysqli_result) {
    $totalServices = (int) ($totalResult->fetch_assoc()['total'] ?? 0);
    $totalResult->free();
}

$pagination = admin_paginate($totalServices, $perPage, $page);
$servicesSql = 'SELECT * FROM servicios' . $whereSql . ' ORDER BY orden ASC, id DESC LIMIT ' . $pagination['offset'] . ', ' . $pagination['per_page'];
$servicios = $conn->query($servicesSql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - Admin</title>
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
            <button id="toggleSidebar" class="p-2 rounded border border-gray-200 hover:bg-gray-100 active:scale-95 transition">
                <i class="fas fa-bars"></i>
            </button>
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
                    <li><a href="servicios.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-cog"></i><span>Servicios</span></a></li>
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
                    <li><a href="auditoria.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-clock-rotate-left"></i><span>Actividad</span></a></li>
                    <li><a href="cambiar-password.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-lock"></i><span>Cambiar clave</span></a></li>
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <?php admin_render_toast($toast); ?>
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Servicios</h1>
                        <p class="mt-2 text-sm text-gray-600">Administra la oferta comercial, destacados y orden de visualizacion.</p>
                    </div>
                    <a href="servicio-editar.php" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Nuevo Servicio
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
                                placeholder="Buscar por titulo, descripcion o icono"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 focus:border-blue-600 focus:bg-white focus:outline-none"
                            >
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Buscar</button>
                            <a href="servicios.php" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                    <div class="text-sm text-gray-500">
                        <?php echo $totalServices; ?> servicio<?php echo $totalServices === 1 ? '' : 's'; ?> encontrado<?php echo $totalServices === 1 ? '' : 's'; ?>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Icono</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Titulo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Descripcion</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Precio</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Destacado</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Orden</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($servicios instanceof mysqli_result && $servicios->num_rows > 0): ?>
                                    <?php while ($s = $servicios->fetch_assoc()): ?>
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <i class="fas fa-<?php echo admin_escape($s['icono'] ?? 'code'); ?> text-2xl text-blue-600"></i>
                                            </td>
                                            <td class="px-6 py-4 font-medium">
                                                <a href="servicio-editar.php?id=<?php echo (int) $s['id']; ?>" class="text-slate-900 hover:text-blue-600 hover:underline">
                                                    <?php echo admin_escape($s['titulo']); ?>
                                                </a>
                                            </td>
                                            <td class="max-w-xs px-6 py-4 text-gray-700"><?php echo admin_escape($s['descripcion']); ?></td>
                                            <td class="px-6 py-4">$<?php echo number_format((float) $s['precio_desde'], 2); ?></td>
                                            <td class="px-6 py-4">
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                    <input type="hidden" name="action" value="toggle_featured">
                                                    <input type="hidden" name="id" value="<?php echo (int) $s['id']; ?>">
                                                    <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                    <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
                                                    <button type="submit" class="rounded px-2 py-1 text-sm <?php echo !empty($s['destacado']) ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                                        <?php echo !empty($s['destacado']) ? 'Si' : 'No'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4"><?php echo (int) $s['orden']; ?></td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <a href="servicio-editar.php?id=<?php echo (int) $s['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Eliminar este servicio?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo (int) $s['id']; ?>">
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
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <?php echo $searchTerm !== '' ? 'No encontramos servicios con ese criterio de busqueda.' : 'Todavia no hay servicios registrados.'; ?>
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
                                <a href="<?php echo admin_build_url('servicios.php', ['q' => $searchTerm, 'page' => $pagination['page'] - 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php for ($pageNumber = 1; $pageNumber <= $pagination['total_pages']; $pageNumber++): ?>
                                <?php if (abs($pageNumber - $pagination['page']) > 2 && $pageNumber !== 1 && $pageNumber !== $pagination['total_pages']) continue; ?>
                                <a href="<?php echo admin_build_url('servicios.php', ['q' => $searchTerm, 'page' => $pageNumber]); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo $pageNumber === $pagination['page'] ? 'bg-blue-600 text-white' : 'border text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $pageNumber; ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?php echo admin_build_url('servicios.php', ['q' => $searchTerm, 'page' => $pagination['page'] + 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
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








