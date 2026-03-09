<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';

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
$messageStatus = $_GET['msg'] ?? '';
$messageFilter = ($_GET['estado'] ?? 'todos') === 'nuevo' ? 'nuevo' : 'todos';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $redirectFilter = ($_POST['estado'] ?? 'todos') === 'nuevo' ? 'nuevo' : 'todos';
    $redirectUrl = 'mensajes.php' . ($redirectFilter === 'nuevo' ? '?estado=nuevo' : '');
    $joiner = $redirectFilter === 'nuevo' ? '&' : '?';

    if ($id > 0) {
        if ($action === 'read' || $action === 'unread') {
            $readState = $action === 'read' ? 1 : 0;

            if ($stmt = $conn->prepare('UPDATE mensajes SET leido = ? WHERE id = ?')) {
                $stmt->bind_param('ii', $readState, $id);
                $stmt->execute();
                $stmt->close();
            }

            header('Location: ' . $redirectUrl . $joiner . 'msg=' . ($readState ? 'read' : 'unread'));
            exit;
        }

        if ($action === 'delete') {
            if ($stmt = $conn->prepare('DELETE FROM mensajes WHERE id = ?')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }

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

$messagesSql = 'SELECT * FROM mensajes';
if ($messageFilter === 'nuevo') {
    $messagesSql .= ' WHERE leido = 0';
}
$messagesSql .= ' ORDER BY created_at DESC';

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
    <div class="flex h-screen">
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
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Mensajes de Contacto</h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Abre cada mensaje para ver el contenido completo y gestionarlo desde el panel.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="mensajes.php" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $messageFilter === 'todos' ? 'bg-blue-600 text-white shadow' : 'bg-white text-gray-700 shadow hover:bg-gray-50'; ?>">
                            Todos
                            <span class="ml-2 rounded-full <?php echo $messageFilter === 'todos' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'; ?> px-2 py-0.5 text-xs">
                                <?php echo $totalMessages; ?>
                            </span>
                        </a>
                        <a href="mensajes.php?estado=nuevo" class="rounded-full px-4 py-2 text-sm font-semibold transition <?php echo $messageFilter === 'nuevo' ? 'bg-red-600 text-white shadow' : 'bg-white text-gray-700 shadow hover:bg-gray-50'; ?>">
                            Nuevos
                            <span class="ml-2 rounded-full <?php echo $messageFilter === 'nuevo' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-600'; ?> px-2 py-0.5 text-xs">
                                <?php echo $unreadMessages; ?>
                            </span>
                        </a>
                    </div>
                </div>

                <?php if ($messageStatus !== ''): ?>
                    <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                        <?php
                        if ($messageStatus === 'deleted') echo 'Mensaje eliminado correctamente';
                        if ($messageStatus === 'read') echo 'Mensaje marcado como leido';
                        if ($messageStatus === 'unread') echo 'Mensaje marcado como no leido';
                        if ($messageStatus === 'notfound') echo 'Ese mensaje ya no existe o fue eliminado';
                        ?>
                    </div>
                <?php endif; ?>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px]">
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
                                        <?php $isUnread = empty($msg['leido']); ?>
                                        <tr class="border-t <?php echo $isUnread ? 'bg-sky-50' : 'bg-white'; ?> transition hover:bg-sky-100/70">
                                            <td class="px-6 py-4">
                                                <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="font-medium <?php echo $isUnread ? 'text-slate-950' : 'text-slate-900'; ?> hover:text-blue-600 hover:underline">
                                                    <?php echo htmlspecialchars($msg['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="mailto:<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text-blue-600 hover:underline">
                                                    <?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if (!empty($msg['telefono'])): ?>
                                                    <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $msg['telefono']), ENT_QUOTES, 'UTF-8'); ?>" class="text-blue-600 hover:underline">
                                                        <?php echo htmlspecialchars($msg['telefono'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="max-w-xs px-6 py-4">
                                                <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="block truncate text-gray-700 hover:text-blue-600" title="<?php echo htmlspecialchars($msg['mensaje'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo htmlspecialchars(buildMessagePreview((string) ($msg['mensaje'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
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
                                                <div class="flex items-center gap-3">
                                                    <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                                        <i class="fas fa-eye"></i>
                                                        <span>Abrir</span>
                                                    </a>

                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="id" value="<?php echo (int) $msg['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $messageFilter; ?>">
                                                        <input type="hidden" name="action" value="<?php echo !empty($msg['leido']) ? 'unread' : 'read'; ?>">
                                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium <?php echo !empty($msg['leido']) ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-50 text-green-700 hover:bg-green-100'; ?>">
                                                            <i class="fas <?php echo !empty($msg['leido']) ? 'fa-envelope' : 'fa-check'; ?>"></i>
                                                            <span><?php echo !empty($msg['leido']) ? 'No leido' : 'Leido'; ?></span>
                                                        </button>
                                                    </form>

                                                    <form method="POST" class="inline" onsubmit="return confirm('Eliminar este mensaje?');">
                                                        <input type="hidden" name="id" value="<?php echo (int) $msg['id']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $messageFilter; ?>">
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
                                            <?php echo $messageFilter === 'nuevo' ? 'No hay mensajes nuevos por revisar.' : 'No hay mensajes registrados todavia.'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
