<?php
// admin/proyecto-editar.php
require_once '../includes/config.php';
require_once '../includes/project-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

function canDeleteManagedProjectImage(?string $imagePath): bool
{
    if (!$imagePath) {
        return false;
    }

    $normalized = ltrim(str_replace('\\', '/', $imagePath), '/');
    return strpos($normalized, 'assets/img/proyectos/') === 0;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$project = null;
$pageTitle = 'Nuevo Proyecto';

if ($id > 0) {
    $result = $conn->query("SELECT * FROM proyectos WHERE id = $id LIMIT 1");
    if ($result instanceof mysqli_result) {
        $project = $result->fetch_assoc();
        $result->free();
    }

    if ($project) {
        $pageTitle = 'Editar Proyecto';
    } else {
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitize($_POST['titulo'] ?? '');
    $descripcion = sanitize($_POST['descripcion'] ?? '');
    $categoria = sanitize($_POST['categoria'] ?? '');
    $cliente = sanitize($_POST['cliente'] ?? '');
    $url_demo = sanitize($_POST['url_demo'] ?? '');
    $url_repo = sanitize($_POST['url_repo'] ?? '');
    $fecha_completado = trim($_POST['fecha_completado'] ?? '');
    $fecha_completado = $fecha_completado !== '' ? $fecha_completado : null;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $orden = (int) ($_POST['orden'] ?? 0);
    $imagen = trim((string) ($_POST['imagen_actual'] ?? ''));
    $imagen = ltrim(str_replace('\\', '/', $imagen), '/');
    $currentImage = $project['imagen'] ?? null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed, true)) {
            $error = 'La imagen debe estar en formato JPG, PNG, GIF o WEBP.';
        } else {
            $uploadDir = '../assets/img/proyectos';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFilename = uniqid('proy_', true) . '.' . $ext;
            $uploadPath = $uploadDir . '/' . $newFilename;

            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
                $error = 'No se pudo subir la imagen del proyecto.';
            } else {
                if (canDeleteManagedProjectImage($currentImage) && file_exists('../' . $currentImage)) {
                    unlink('../' . $currentImage);
                }
                $imagen = 'assets/img/proyectos/' . $newFilename;
            }
        }
    }

    if (!isset($error)) {
        if ($titulo === '' || $descripcion === '' || $categoria === '') {
            $error = 'Titulo, descripcion y categoria son obligatorios.';
        } elseif ($id > 0) {
            $stmt = $conn->prepare('UPDATE proyectos SET titulo = ?, descripcion = ?, imagen = ?, categoria = ?, url_demo = ?, url_repo = ?, cliente = ?, fecha_completado = ?, destacado = ?, orden = ? WHERE id = ?');
            $stmt->bind_param('ssssssssiii', $titulo, $descripcion, $imagen, $categoria, $url_demo, $url_repo, $cliente, $fecha_completado, $destacado, $orden, $id);
        } else {
            $stmt = $conn->prepare('INSERT INTO proyectos (titulo, descripcion, imagen, categoria, url_demo, url_repo, cliente, fecha_completado, destacado, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssssssii', $titulo, $descripcion, $imagen, $categoria, $url_demo, $url_repo, $cliente, $fecha_completado, $destacado, $orden);
        }
    }

    if (!isset($error) && isset($stmt) && $stmt->execute()) {
        header('Location: proyectos.php?msg=saved');
        exit;
    }

    if (!isset($error)) {
        $error = 'Error al guardar: ' . $conn->error;
    }

    $project = [
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'imagen' => $imagen,
        'categoria' => $categoria,
        'url_demo' => $url_demo,
        'url_repo' => $url_repo,
        'cliente' => $cliente,
        'fecha_completado' => $fecha_completado,
        'destacado' => $destacado,
        'orden' => $orden,
    ];
}

$currentImageUrl = !empty($project['imagen']) ? getProjectImageUrl($project) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> - Admin</title>
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
                    <li><a href="proyectos.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-folder"></i><span>Proyectos</span></a></li>
                    <li><a href="servicios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-cog"></i><span>Servicios</span></a></li>
                    <li><a href="testimonios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-comment"></i><span>Testimonios</span></a></li>
                    <li><a href="mensajes.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-envelope"></i><span>Mensajes</span></a></li>
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow max-w-4xl">
                    <div class="grid gap-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Titulo del proyecto *</label>
                                <input
                                    type="text"
                                    name="titulo"
                                    required
                                    value="<?php echo htmlspecialchars($project['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Categoria *</label>
                                <input
                                    type="text"
                                    name="categoria"
                                    required
                                    value="<?php echo htmlspecialchars($project['categoria'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="Ej. Sistemas Web"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2">Descripcion *</label>
                            <textarea
                                name="descripcion"
                                rows="5"
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                            ><?php echo htmlspecialchars($project['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Cliente</label>
                                <input
                                    type="text"
                                    name="cliente"
                                    value="<?php echo htmlspecialchars($project['cliente'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="Ej. Cliente privado"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Fecha de completado</label>
                                <input
                                    type="date"
                                    name="fecha_completado"
                                    value="<?php echo htmlspecialchars($project['fecha_completado'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">URL publica o demo</label>
                                <input
                                    type="text"
                                    name="url_demo"
                                    value="<?php echo htmlspecialchars($project['url_demo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="https://demo.com o destello-oro.php"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">URL del repositorio</label>
                                <input
                                    type="text"
                                    name="url_repo"
                                    value="<?php echo htmlspecialchars($project['url_repo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="https://github.com/usuario/proyecto"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Ruta actual de imagen</label>
                                <input
                                    type="text"
                                    name="imagen_actual"
                                    value="<?php echo htmlspecialchars($project['imagen'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="fondo.jpeg o assets/img/proyectos/mi-imagen.jpg"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                                <p class="text-sm text-gray-500 mt-1">Puedes usar una ruta existente o subir una nueva imagen abajo.</p>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Subir nueva imagen</label>
                                <input type="file" name="imagen" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                                <p class="text-sm text-gray-500 mt-1">Si subes una imagen, reemplaza la ruta actual.</p>
                            </div>
                        </div>

                        <?php if ($currentImageUrl): ?>
                            <div>
                                <p class="block text-gray-700 mb-2">Vista previa actual</p>
                                <img src="<?php echo htmlspecialchars($currentImageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Vista previa del proyecto" class="w-full max-w-md h-52 object-cover rounded-lg border border-gray-200">
                            </div>
                        <?php endif; ?>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center space-x-2">
                                    <input
                                        type="checkbox"
                                        name="destacado"
                                        value="1"
                                        <?php echo !empty($project['destacado']) ? 'checked' : ''; ?>
                                        class="w-4 h-4 text-blue-600"
                                    >
                                    <span class="text-gray-700">Destacar en el portafolio</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Orden</label>
                                <input
                                    type="number"
                                    name="orden"
                                    min="0"
                                    value="<?php echo htmlspecialchars((string) ($project['orden'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"
                                >
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="proyectos.php" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Proyecto</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
