<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: mensajes.php?msg=notfound');
    exit;
}

$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();
$unreadMessages = 0;
$unreadResult = $conn->query('SELECT COUNT(*) AS total FROM mensajes WHERE leido = 0');
if ($unreadResult instanceof mysqli_result) {
    $unreadMessages = (int) ($unreadResult->fetch_assoc()['total'] ?? 0);
    $unreadResult->free();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: mensajes.php?msg=csrf');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        if ($stmt = $conn->prepare('DELETE FROM mensajes WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        admin_log_action($conn, 'delete', 'message', $id, 'Mensaje eliminado desde la vista detalle');
        header('Location: mensajes.php?msg=deleted');
        exit;
    }

    if ($action === 'unread') {
        if ($stmt = $conn->prepare('UPDATE mensajes SET leido = 0 WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        admin_log_action($conn, 'mark_unread', 'message', $id, 'Mensaje marcado como no leido desde detalle');
        header('Location: mensajes.php?estado=nuevo&msg=unread');
        exit;
    }
}

$mensaje = null;
if ($stmt = $conn->prepare('SELECT * FROM mensajes WHERE id = ? LIMIT 1')) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $mensaje = $result instanceof mysqli_result ? $result->fetch_assoc() : null;
    $stmt->close();
}

if (!$mensaje) {
    header('Location: mensajes.php?msg=notfound');
    exit;
}

if (empty($mensaje['leido'])) {
    if ($stmt = $conn->prepare('UPDATE mensajes SET leido = 1 WHERE id = ?')) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    $mensaje['leido'] = 1;
    $unreadMessages = max(0, $unreadMessages - 1);
    admin_log_action($conn, 'mark_read', 'message', $id, 'Mensaje abierto y marcado como leido');
}

$email = trim((string) ($mensaje['email'] ?? ''));
$phone = trim((string) ($mensaje['telefono'] ?? ''));
$phoneHref = admin_normalize_phone($phone);
$whatsAppUrl = admin_whatsapp_url($phone, $mensaje['nombre'] ?? '');
$replyTemplates = admin_get_message_reply_templates($mensaje['nombre'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensaje - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4 border-b">
                <div class="relative h-10 w-10 shrink-0">
    <span class="absolute inset-0 rounded-full border-2 border-blue-500/70 border-t-transparent animate-spin"></span>
    <img src="../imag/MCE.jpg" alt="MCE Admin" class="absolute inset-1 h-8 w-8 object-contain rounded-full">
</div>
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
                <div class="mb-8 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <a href="mensajes.php" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:underline">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver a mensajes</span>
                        </a>
                        <h1 class="mt-3 text-3xl font-bold">Mensaje de <?php echo admin_escape($mensaje['nombre']); ?></h1>
                        <p class="mt-2 text-sm text-gray-600">
                            Recibido el <?php echo date('d/m/Y H:i', strtotime($mensaje['created_at'])); ?>.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <?php if ($email !== ''): ?>
                            <a href="mailto:<?php echo admin_escape($email); ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                <i class="fas fa-reply"></i>
                                <span>Responder por correo</span>
                            </a>
                        <?php endif; ?>

                        <?php if ($whatsAppUrl): ?>
                            <a href="<?php echo admin_escape($whatsAppUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>
                        <?php endif; ?>

                        <?php if ($phone !== '' && $phoneHref !== ''): ?>
                            <a href="tel:<?php echo admin_escape($phoneHref); ?>" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                <i class="fas fa-phone"></i>
                                <span>Llamar</span>
                            </a>
                        <?php endif; ?>

                        <form method="POST" class="inline">
                            <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                            <input type="hidden" name="action" value="unread">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                                <i class="fas fa-envelope"></i>
                                <span>Marcar como no leido</span>
                            </button>
                        </form>

                        <form method="POST" class="inline" onsubmit="return confirm('Eliminar este mensaje?');">
                            <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mb-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl bg-white p-5 shadow">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Estado</p>
                        <div class="mt-3">
                            <span class="rounded-full px-3 py-1 text-sm font-semibold <?php echo !empty($mensaje['leido']) ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo !empty($mensaje['leido']) ? 'Leido' : 'Nuevo'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-5 shadow">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Correo</p>
                        <p class="mt-3 break-all text-base font-semibold text-slate-900">
                            <?php echo $email !== '' ? admin_escape($email) : 'No registrado'; ?>
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white p-5 shadow">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Telefono</p>
                        <p class="mt-3 text-base font-semibold text-slate-900">
                            <?php echo $phone !== '' ? admin_escape($phone) : 'No registrado'; ?>
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white p-5 shadow">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Recibido</p>
                        <p class="mt-3 text-base font-semibold text-slate-900">
                            <?php echo date('d/m/Y H:i', strtotime($mensaje['created_at'])); ?>
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-8 shadow">
                    <div class="mb-6 flex items-center justify-between gap-4 border-b pb-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">Mensaje completo</p>
                            <p class="mt-2 text-lg font-bold text-slate-900">
                                <?php echo admin_escape($mensaje['nombre']); ?>
                            </p>
                        </div>
                        <?php if ($email !== ''): ?>
                            <a href="mailto:<?php echo admin_escape($email); ?>" class="text-sm font-medium text-blue-600 hover:underline">
                                Responder ahora
                            </a>
                        <?php endif; ?>
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Texto</p>
                        <div class="rounded-2xl bg-slate-50 p-6 text-base leading-7 text-slate-800">
                            <?php echo nl2br(admin_escape($mensaje['mensaje'])); ?>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl bg-white p-8 shadow">
                    <div class="mb-6 border-b pb-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500">Plantillas rapidas</p>
                        <p class="mt-2 text-sm text-gray-600">Usa respuestas base para contestar por correo, WhatsApp o copiar el texto y ajustarlo antes de enviarlo.</p>
                    </div>

                    <div class="grid gap-4 xl:grid-cols-3">
                        <?php foreach ($replyTemplates as $template): ?>
                            <?php
                            $templateMailtoUrl = $email !== '' ? admin_mailto_url($email, $template['subject'], $template['body']) : null;
                            $templateWhatsappUrl = $phoneHref !== '' ? 'https://wa.me/' . $phoneHref . '?text=' . rawurlencode($template['body']) : null;
                            $copyText = $template['subject'] . "\n\n" . $template['body'];
                            ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-bold text-slate-900"><?php echo admin_escape($template['label']); ?></h2>
                                        <p class="mt-1 text-sm text-slate-600"><?php echo admin_escape($template['description']); ?></p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                        Plantilla
                                    </span>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Asunto</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900"><?php echo admin_escape($template['subject']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Mensaje</p>
                                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700"><?php echo admin_escape($template['body']); ?></p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap gap-3">
                                    <?php if ($templateMailtoUrl): ?>
                                        <a href="<?php echo admin_escape($templateMailtoUrl); ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                            <i class="fas fa-envelope"></i>
                                            <span>Usar en correo</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($templateWhatsappUrl): ?>
                                        <a href="<?php echo admin_escape($templateWhatsappUrl); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>Usar en WhatsApp</span>
                                        </a>
                                    <?php endif; ?>

                                    <button type="button" data-copy-text="<?php echo admin_escape($copyText); ?>" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-100">
                                        <i class="fas fa-copy"></i>
                                        <span>Copiar</span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('[data-copy-text]').forEach(function (button) {
            button.addEventListener('click', function () {
                var text = button.getAttribute('data-copy-text') || '';
                var label = button.querySelector('span');
                var defaultLabel = label ? label.textContent : 'Copiar';

                if (!navigator.clipboard) {
                    return;
                }

                navigator.clipboard.writeText(text).then(function () {
                    if (label) {
                        label.textContent = 'Copiado';
                    }
                    button.classList.remove('text-slate-700', 'ring-slate-200');
                    button.classList.add('text-emerald-700', 'ring-emerald-200', 'bg-emerald-50');

                    window.setTimeout(function () {
                        if (label) {
                            label.textContent = defaultLabel;
                        }
                        button.classList.add('text-slate-700', 'ring-slate-200');
                        button.classList.remove('text-emerald-700', 'ring-emerald-200', 'bg-emerald-50');
                    }, 1800);
                });
            });
        });
    </script>
</body>
</html>



