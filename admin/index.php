<?php
require_once '../includes/config.php';

// Si ya esta logueado, redirigir al dashboard.
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    // Buscar usuario en la base de datos.
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
            header('Location: dashboard.php');
            exit;
        }

        $error = 'Contrasena incorrecta';
    } else {
        $error = 'Usuario no encontrado';
    }

    $stmt->close();
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
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Proyectos MCE</h1>
                <p class="text-gray-600">Panel de Administracion</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Usuario</label>
                    <input
                        type="text"
                        name="username"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Contrasena</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    Ingresar
                </button>
            </form>

            <div class="mt-4 text-center text-sm text-gray-500">
                <p>Usa las credenciales de administrador configuradas en tu base de datos.</p>
                <p class="text-xs mt-2">Si este sitio es nuevo, cambia la contrasena inicial apenas ingreses.</p>
            </div>
        </div>
    </div>
</body>
</html>
