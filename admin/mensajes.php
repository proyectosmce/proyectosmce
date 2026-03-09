<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

function buildMessagePreview(string $message): string
{
    $message = trim($message);

    if ($message === '') {
        return '-';
    }

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($message, 0, 80, '...', 'UTF-8');
    }

    return strlen($message) > 80 ? substr($message, 0, 77) . '...' : $message;
}

$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();
$messageStatus = $_GET['msg'] ?? '';
$messageFilter = ($_GET['estado'] ?? 'todos') === 'nuevo' ? 'nuevo' : 'todos';
$searchTerm = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$toast = admin_build_toast($messageStatus, [
    'deleted' => ['message' => 'Mensaje eliminado correctamente.'],
    'read' => ['type' => 'info', 'title' => 'Mensaje actualizado', 'message' => 'Mensaje marcado como leido.'],
    'unread' => ['type' => 'warning', 'title' => 'Mensaje actualizado', 'message' => 'Mensaje marcado como no leido.'],
    'notfound' => ['type' => 'warning', 'title' => 'Mensaje no disponible', 'message' => 'Ese mensaje ya no existe o fue eliminado.'],
    'csrf' => ['type' => 'error', 'title' => 'Sesion no valida', 'message' => 'Recarga la pagina e intenta de nuevo.'],
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestFilter = ($_POST['estado'] ?? 'todos') === 'nuevo' ? 'nuevo' : 'todos';
    $requestSearch = trim((string) ($_POST['q'] ?? ''));
    $requestPage = max(1, (int) ($_POST['page'] ?? 1));

    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: ' . admin_build_url('mensajes.php', ['estado' => $requestFilter === 'nuevo' ? 'nuevo' : null, 'q' => $requestSearch, 'page' => $requestPage, 'msg' => 'csrf']));
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($action === 'export_csv') {
        $conditions = [];
        if ($requestFilter === 'nuevo') {
            $conditions[] = 'leido = 0';
        }
        if ($requestSearch !== '') {
            $safeSearch = $conn->real_escape_string($requestSearch);
            $conditions[] = "(nombre LIKE '%{$safeSearch}%' OR email LIKE '%{$safeSearch}%' OR telefono LIKE '%{$safeSearch}%' OR mensaje LIKE '%{$safeSearch}%')";
        }

        $whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        $rows = [];
        if ($result = $conn->query('SELECT nombre, email, telefono, mensaje, leido, created_at FROM mensajes' . $whereSql . ' ORDER BY created_at DESC')) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = [
                    $row['nombre'],
                    $row['email'],
                    $row['telefono'] ?? '',
                    $row['mensaje'],
                    !empty($row['leido']) ? 'Leido' : 'No leido',
                    $row['created_at'],
                ];
            }
            $result->free();
        }

        admin_log_action($conn, 'export', 'message', null, 'Exportacion CSV de mensajes');
        admin_send_csv('mensajes-' . date('Ymd-His') . '.csv', ['Nombre', 'Email', 'Telefono', 'Mensaje', 'Estado', 'Fecha'], $rows);
    }

    if ($id > 0) {
        $redirectUrl = admin_build_url('mensajes.php', [
            'estado' => $requestFilter === 'nuevo' ? 'nuevo' : null,
            'q' => $requestSearch,
            'page' => $requestPage,
        ]);
        $joiner = strpos($redirectUrl, '?') === false ? '?' : '&';

        if ($action === 'read' || $action === 'unread') {
            $readState = $action === 'read' ? 1 : 0;

            if ($stmt = $conn->prepare('UPDATE mensajes SET leido = ? WHERE id = ?')) {
                $stmt->bind_param('ii', $readState, $id);
                $stmt->execute();
                $stmt->close();
            }

            admin_log_action($conn, $readState ? 'mark_read' : 'mark_unread', 'message', $id, 'Estado del mensaje actualizado desde el listado');
            header('Location: ' . $redirectUrl . $joiner . 'msg=' . ($readState ? 'read' : 'unread'));
            exit;
        }

        if ($action === 'delete') {
            if ($stmt = $conn->prepare('DELETE FROM mensajes WHERE id = ?')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }

            admin_log_action($conn, 'delete', 'message', $id, 'Mensaje eliminado desde el listado');
            header('Location: ' . $redirectUrl . $joiner . 'msg=deleted');
            exit;
        }
    }
}

$totalMessages = 0;
$unreadMessages = 0;

$totalResult = $conn->query('SELECT COUNT(*) AS total FROM mensajes');
if ($totalResult instanceof mysqli_result) {
    $totalMessages = (int) ($totalResult->fetch_assoc()['total'] ?? 0);
    $totalResult->free();
}

$unreadResult = $conn->query('SELECT COUNT(*) AS total FROM mensajes WHERE leido = 0');
if ($unreadResult instanceof mysqli_result) {
    $unreadMessages = (int) ($unreadResult->fetch_assoc()['total'] ?? 0);
    $unreadResult->free();
}

$conditions = [];
if ($messageFilter === 'nuevo') {
    $conditions[] = 'leido = 0';
}
if ($searchTerm !== '') {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $conditions[] = "(nombre LIKE '%{$safeSearch}%' OR email LIKE '%{$safeSearch}%' OR telefono LIKE '%{$safeSearch}%' OR mensaje LIKE '%{$safeSearch}%')";
}
$whereSql = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

$filteredMessages = 0;
$filteredResult = $conn->query('SELECT COUNT(*) AS total FROM mensajes' . $whereSql);
if ($filteredResult instanceof mysqli_result) {
    $filteredMessages = (int) ($filteredResult->fetch_assoc()['total'] ?? 0);
    $filteredResult->free();
}

$pagination = admin_paginate($filteredMessages, $perPage, $page);
$messagesSql = 'SELECT * FROM mensajes' . $whereSql . ' ORDER BY created_at DESC LIMIT ' . $pagination['offset'] . ', ' . $pagination['per_page'];
$mensajes = $conn->query($messagesSql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-blue-600">MCE Admin</h2>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li><a href="dashboard.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                    <li><a href="proyectos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-folder"></i><span>Proyectos</span></a></li>
                    <li><a href="servicios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-cog"></i><span>Servicios</span></a></li>
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
                    <li>
                        <a href="mensajes.php" class="flex items-center space-x-2 rounded bg-blue-50 p-2 text-blue-600">
                            <i class="fas fa-envelope"></i>
                            <span>Mensajes</span>
                            <?php if ($unreadMessages > 0): ?>
                                <span class="ml-auto rounded-full bg-red-500 px-2 py-1 text-xs font-semibold text-white">
                                    <?php echo $unreadMessages; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="auditoria.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-clock-rotate-left"></i><span>Actividad</span></a></li>
                    <li><a href="cambiar-password.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-lock"></i><span>Cambiar clave</span></a></li>
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <?php admin_render_toast($toast); ?>
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Mensajes de Contacto</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Busca, filtra, exporta o responde mensajes desde un solo lugar.
                        </p>
                    </div>
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                        <input type="hidden" name="action" value="export_csv">
                        <input type="hidden" name="estado" value="<?php echo $messageFilter; ?>">
                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white hover:bg-emerald-700">
                            <i class="fas fa-file-csv"></i>
                            <span>Exportar CSV</span>
                        </button>
                    </form>
                </div>

                <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-white p-5 shadow lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap gap-3">
                        <a href="<?php echo admin_build_url('mensajes.php', ['q' => $searchTerm]); ?>" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $messageFilter === 'todos' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            Todos
                            <span class="ml-2 rounded-full <?php echo $messageFilter === 'todos' ? 'bg-white/20 text-white' : 'bg-white text-gray-600'; ?> px-2 py-0.5 text-xs">
                                <?php echo $totalMessages; ?>
                            </span>
                        </a>
                        <a href="<?php echo admin_build_url('mensajes.php', ['estado' => 'nuevo', 'q' => $searchTerm]); ?>" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $messageFilter === 'nuevo' ? 'bg-red-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            No leidos
                            <span class="ml-2 rounded-full <?php echo $messageFilter === 'nuevo' ? 'bg-white/20 text-white' : 'bg-white text-red-600'; ?> px-2 py-0.5 text-xs">
                                <?php echo $unreadMessages; ?>
                            </span>
                        </a>
                    </div>
                    <form method="GET" class="flex w-full flex-col gap-3 md:flex-row md:items-center lg:max-w-2xl">
                        <?php if ($messageFilter === 'nuevo'): ?>
                            <input type="hidden" name="estado" value="nuevo">
                        <?php endif; ?>
                        <div class="relative w-full">
                            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="text"
                                name="q"
                                value="<?php echo admin_escape($searchTerm); ?>"
                                placeholder="Buscar por nombre, email, telefono o mensaje"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 focus:border-blue-600 focus:bg-white focus:outline-none"
                            >
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Buscar</button>
                            <a href="mensajes.php" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                </div>

                <div class="mb-4 text-sm text-gray-500">
                    Mostrando <?php echo $filteredMessages; ?> resultado<?php echo $filteredMessages === 1 ? '' : 's'; ?><?php echo $searchTerm !== '' ? ' para "' . admin_escape($searchTerm) . '"' : ''; ?>.
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1140px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Telefono</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Vista previa</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($mensajes instanceof mysqli_result && $mensajes->num_rows > 0): ?>
                                    <?php while ($msg = $mensajes->fetch_assoc()): ?>
                                        <?php
                                        $isUnread = empty($msg['leido']);
                                        $whatsAppUrl = admin_whatsapp_url($msg['telefono'] ?? '', $msg['nombre'] ?? '');
                                        $replyTemplates = admin_get_message_reply_templates($msg['nombre'] ?? '');
                                        $rowEmail = trim((string) ($msg['email'] ?? ''));
                                        $rowPhone = admin_normalize_phone($msg['telefono'] ?? '');
                                        $canUseTemplates = $rowEmail !== '' || $rowPhone !== '';
                                        ?>
                                        <tr class="border-t <?php echo $isUnread ? 'bg-sky-50' : 'bg-white'; ?> transition hover:bg-sky-100/70">
                                            <td class="px-6 py-4">
                                                <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="font-medium <?php echo $isUnread ? 'text-slate-950' : 'text-slate-900'; ?> hover:text-blue-600 hover:underline">
                                                    <?php echo admin_escape($msg['nombre']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="mailto:<?php echo admin_escape($msg['email']); ?>" class="text-blue-600 hover:underline">
                                                    <?php echo admin_escape($msg['email']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($msg['telefono'])): ?>
                                                    <a href="tel:<?php echo admin_escape(admin_normalize_phone($msg['telefono'])); ?>" class="text-blue-600 hover:underline">
                                                        <?php echo admin_escape($msg['telefono']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="max-w-xs px-6 py-4">
                                                <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="block truncate text-gray-700 hover:text-blue-600" title="<?php echo admin_escape($msg['mensaje']); ?>">
                                                    <?php echo admin_escape(buildMessagePreview((string) ($msg['mensaje'] ?? ''))); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($msg['leido'])): ?>
                                                    <span class="rounded bg-green-100 px-2 py-1 text-sm text-green-800">Leido</span>
                                                <?php else: ?>
                                                    <span class="rounded bg-sky-100 px-2 py-1 text-sm font-semibold text-sky-800">No leido</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                                        <i class="fas fa-eye"></i>
                                                        <span>Abrir</span>
                                                    </a>

                                                    <?php if ($whatsAppUrl): ?>
                                                        <a href="<?php echo admin_escape($whatsAppUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100">
                                                            <i class="fab fa-whatsapp"></i>
                                                            <span>WhatsApp</span>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($canUseTemplates): ?>
                                                        <details class="relative">
                                                            <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-lg bg-violet-50 px-3 py-2 text-sm font-medium text-violet-700 hover:bg-violet-100">
                                                                <i class="fas fa-bolt"></i>
                                                                <span>Plantillas</span>
                                                            </summary>
                                                            <div class="absolute right-0 z-20 mt-2 w-80 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                                                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Respuesta rapida</p>
                                                                <div class="mt-3 space-y-3">
                                                                    <?php foreach ($replyTemplates as $template): ?>
                                                                        <?php
                                                                        $templateMailtoUrl = $rowEmail !== '' ? admin_mailto_url($rowEmail, $template['subject'], $template['body']) : null;
                                                                        $templateWhatsappUrl = $rowPhone !== '' ? 'https://wa.me/' . $rowPhone . '?text=' . rawurlencode($template['body']) : null;
                                                                        ?>
                                                                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                                                            <p class="text-sm font-semibold text-slate-900"><?php echo admin_escape($template['label']); ?></p>
                                                                            <p class="mt-1 text-xs leading-5 text-slate-600"><?php echo admin_escape($template['description']); ?></p>
                                                                            <div class="mt-3 flex flex-wrap gap-2">
                                                                                <?php if ($templateMailtoUrl): ?>
                                                                                    <a href="<?php echo admin_escape($templateMailtoUrl); ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                                                                        <i class="fas fa-envelope"></i>
                                                                                        <span>Correo</span>
                                                                                    </a>
                                                                                <?php endif; ?>
                                                                                <?php if ($templateWhatsappUrl): ?>
                                                                                    <a href="<?php echo admin_escape($templateWhatsappUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                                                                        <i class="fab fa-whatsapp"></i>
                                                                                        <span>WhatsApp</span>
                                                                                    </a>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </details>
                                                    <?php endif; ?>

                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int) $msg['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $messageFilter; ?>">
                                                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                        <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
                                                        <input type="hidden" name="action" value="<?php echo !empty($msg['leido']) ? 'unread' : 'read'; ?>">
                                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium <?php echo !empty($msg['leido']) ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-50 text-green-700 hover:bg-green-100'; ?>">
                                                            <i class="fas <?php echo !empty($msg['leido']) ? 'fa-envelope' : 'fa-check'; ?>"></i>
                                                            <span><?php echo !empty($msg['leido']) ? 'No leido' : 'Leido'; ?></span>
                                                        </button>
                                                    </form>

                                                    <form method="POST" class="inline" onsubmit="return confirm('Eliminar este mensaje?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int) $msg['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $messageFilter; ?>">
                                                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                        <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
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
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <?php echo $searchTerm !== '' ? 'No encontramos mensajes con ese criterio de busqueda.' : ($messageFilter === 'nuevo' ? 'No hay mensajes nuevos por revisar.' : 'No hay mensajes registrados todavia.'); ?>
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
                                <a href="<?php echo admin_build_url('mensajes.php', ['estado' => $messageFilter === 'nuevo' ? 'nuevo' : null, 'q' => $searchTerm, 'page' => $pagination['page'] - 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php for ($pageNumber = 1; $pageNumber <= $pagination['total_pages']; $pageNumber++): ?>
                                <?php if (abs($pageNumber - $pagination['page']) > 2 && $pageNumber !== 1 && $pageNumber !== $pagination['total_pages']) continue; ?>
                                <a href="<?php echo admin_build_url('mensajes.php', ['estado' => $messageFilter === 'nuevo' ? 'nuevo' : null, 'q' => $searchTerm, 'page' => $pageNumber]); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo $pageNumber === $pagination['page'] ? 'bg-blue-600 text-white' : 'border text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $pageNumber; ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?php echo admin_build_url('mensajes.php', ['estado' => $messageFilter === 'nuevo' ? 'nuevo' : null, 'q' => $searchTerm, 'page' => $pagination['page'] + 1]); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
