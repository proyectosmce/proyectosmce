<?php
require_once '../includes/config.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_login_attempts']) || !is_array($_SESSION['admin_login_attempts'])) {
    $_SESSION['admin_login_attempts'] = [];
}

function admin_login_attempts_key(string $username): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return strtolower($username) . '|' . $ip;
}

function admin_login_is_limited(string $key, int $maxAttempts = 5, int $windowSeconds = 600): bool
{
    $now = time();
    $attempts = $_SESSION['admin_login_attempts'][$key] ?? [];
    $attempts = array_values(array_filter($attempts, static function ($ts) use ($now, $windowSeconds) {
        return is_int($ts) && $ts > ($now - $windowSeconds);
    }));
    $_SESSION['admin_login_attempts'][$key] = $attempts;

    return count($attempts) >= $maxAttempts;
}

function admin_login_record_attempt(string $key, bool $success): void
{
    if ($success) {
        unset($_SESSION['admin_login_attempts'][$key]);
        return;
    }

    $_SESSION['admin_login_attempts'][$key][] = time();
}

// Si ya esta logueado, redirigir al dashboard.
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$loginCsrf = admin_get_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Sesion invalida. Recarga la pagina.';
    } else {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        $attemptKey = admin_login_attempts_key($username);

        if (admin_login_is_limited($attemptKey)) {
            $error = 'Demasiados intentos fallidos. Intenta nuevamente en unos minutos.';
        } else {
            $stmt = $conn->prepare("SELECT id, username, password_hash FROM usuarios WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verificar la contrasena almacenada.
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    admin_login_record_attempt($attemptKey, true);
                    header('Location: dashboard.php');
                    exit;
                }

                $error = 'Contrasena incorrecta';
            } else {
                $error = 'Usuario no encontrado';
            }

            $stmt->close();
            admin_login_record_attempt($attemptKey, false);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Proyectos MCE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body
    class="min-h-screen bg-cover bg-center relative"
    style="background: url('../imag/MCE.jpg') center center / cover no-repeat;"
>
    <div class="absolute inset-0 bg-black/55"></div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md text-white">
            <div class="text-center mb-8 drop-shadow">
                <h1 class="text-2xl font-bold">Proyectos MCE</h1>
                <p class="text-base font-medium">Panel de Administracion</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 bg-red-600/80 border border-red-300/60 text-white px-4 py-3 rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($loginCsrf, ENT_QUOTES, 'UTF-8'); ?>">

                <div>
                    <label class="block text-sm font-semibold mb-2">Usuario</label>
                    <input
                        type="text"
                        name="username"
                        required
                        class="w-full px-4 py-3 rounded-lg bg-white/90 text-gray-900 border border-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Contrasena</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 rounded-lg bg-white/90 text-gray-900 border border-white/60 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition text-sm tracking-wide"
                >
                    Ingresar
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-white/80 drop-shadow">
                <p>Usa las credenciales de administrador configuradas en tu base de datos.</p>
                <p class="text-[11px] mt-2">Si este sitio es nuevo, cambia la contrasena inicial apenas ingreses.</p>
            </div>
        </div>
    </div>
</body>
</html>
