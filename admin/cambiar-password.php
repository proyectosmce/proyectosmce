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
$adminId = isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : 0;
$adminUsername = $_SESSION['admin_username'] ?? 'admin';
$flashMessage = $_GET['msg'] ?? '';
$error = '';
$toast = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'La sesion de seguridad no es valida. Recarga la pagina e intenta de nuevo.';
    } elseif ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Debes completar los tres campos.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'La nueva contrasena debe tener al menos 8 caracteres.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'La nueva contrasena y la confirmacion no coinciden.';
    } elseif ($adminId <= 0) {
        $error = 'No pudimos identificar tu sesion de administrador.';
    } else {
        $passwordHash = null;

        if ($stmt = $conn->prepare('SELECT password_hash FROM usuarios WHERE id = ? LIMIT 1')) {
            $stmt->bind_param('i', $adminId);
            $stmt->execute();
            $stmt->bind_result($passwordHash);
            $stmt->fetch();
            $stmt->close();
        }

        if (!$passwordHash) {
            $error = 'No encontramos el usuario administrador en la base de datos.';
        } elseif (!password_verify($currentPassword, $passwordHash)) {
            $error = 'La contrasena actual es incorrecta.';
        } elseif (password_verify($newPassword, $passwordHash)) {
            $error = 'La nueva contrasena debe ser distinta a la actual.';
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

            if ($stmt = $conn->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')) {
                $stmt->bind_param('si', $newHash, $adminId);

                if ($stmt->execute()) {
                    $stmt->close();
                    admin_log_action($conn, 'change_password', 'admin_user', $adminId, 'Contrasena del administrador actualizada');
                    header('Location: cambiar-password.php?msg=updated');
                    exit;
                }

                $stmt->close();
            }

            $error = 'No se pudo actualizar la contrasena. Intenta nuevamente.';
        }
    }
}

if ($flashMessage === 'updated') {
    $toast = admin_build_toast('updated', [
        'updated' => ['message' => 'La contrasena se actualizo correctamente.'],
    ]);
} elseif ($error !== '') {
    $toast = [
        'type' => 'error',
        'title' => 'No se pudo guardar',
        'message' => $error,
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contrasena - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>.logo-ring{position:absolute;inset:0;border:2px solid transparent;border-radius:8px;background:conic-gradient(from 0deg,#2563eb,#38bdf8,#2563eb);background-origin:border-box;animation:logo-spin 4s linear infinite;}@keyframes logo-spin{to{transform:rotate(360deg);}}</style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="w-64 bg-white shadow-lg">
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
                    <li>
                        <a href="cambiar-password.php" class="flex items-center space-x-2 rounded bg-blue-50 p-2 text-blue-600">
                            <i class="fas fa-lock"></i>
                            <span>Cambiar clave</span>
                        </a>
                    </li>
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <?php admin_render_toast($toast); ?>
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Seguridad del admin</p>
                    <h1 class="mt-2 text-3xl font-bold text-slate-900">Cambiar contrasena</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Usuario actual: <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($adminUsername, ENT_QUOTES, 'UTF-8'); ?></span>
                    </p>
                </div>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                    <form method="POST" class="rounded-2xl bg-white p-8 shadow">
                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                        <div class="grid gap-6">
                            <div>
                                <label class="mb-2 block text-gray-700">Contrasena actual *</label>
                                <input
                                    type="password"
                                    name="current_password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full rounded-lg border px-4 py-3 focus:border-blue-600 focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="mb-2 block text-gray-700">Nueva contrasena *</label>
                                <input
                                    type="password"
                                    name="new_password"
                                    required
                                    minlength="8"
                                    autocomplete="new-password"
                                    class="w-full rounded-lg border px-4 py-3 focus:border-blue-600 focus:outline-none"
                                >
                                <p class="mt-2 text-sm text-gray-500">Usa minimo 8 caracteres. Mejor si mezclas letras, numeros y simbolos.</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-gray-700">Confirmar nueva contrasena *</label>
                                <input
                                    type="password"
                                    name="confirm_password"
                                    required
                                    minlength="8"
                                    autocomplete="new-password"
                                    class="w-full rounded-lg border px-4 py-3 focus:border-blue-600 focus:outline-none"
                                >
                            </div>

                            <div class="flex flex-wrap justify-end gap-4 pt-2">
                                <a href="dashboard.php" class="rounded-lg border px-6 py-3 hover:bg-gray-50">Cancelar</a>
                                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
                                    Guardar nueva contrasena
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="rounded-2xl bg-slate-900 p-8 text-slate-100 shadow">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-300">Recomendacion</p>
                        <h2 class="mt-3 text-2xl font-bold">Cambia la clave inicial</h2>
                        <p class="mt-4 text-sm leading-6 text-slate-300">
                            Si el sitio ya esta publicado, evita mantener usuarios con contrasenas faciles como `password` o `admin123`.
                        </p>
                        <ul class="mt-6 space-y-3 text-sm text-slate-200">
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle mt-1 text-emerald-400"></i>
                                <span>Usa una contrasena unica para este panel.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle mt-1 text-emerald-400"></i>
                                <span>No reutilices la misma clave de correo o hosting.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle mt-1 text-emerald-400"></i>
                                <span>Guarda el cambio desde esta pantalla y prueba el login despues.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>








