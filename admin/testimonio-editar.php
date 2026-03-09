<?php
require_once '../includes/config.php';
require_once '../includes/project-helpers.php';
require_once '../includes/testimonial-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureTestimonialsSchema($conn);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$testimonio = null;
$tituloPagina = 'Nuevo Testimonio';

if ($id > 0) {
    if ($stmt = $conn->prepare('SELECT * FROM testimonios WHERE id = ? LIMIT 1')) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $testimonio = $result instanceof mysqli_result ? $result->fetch_assoc() : null;
        $stmt->close();
    }

    if ($testimonio) {
        $tituloPagina = 'Editar Testimonio';
    } else {
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitize($_POST['nombre'] ?? '');
    $cargo = sanitize($_POST['cargo'] ?? '');
    $empresa = sanitize($_POST['empresa'] ?? '');
    $testimonioTexto = trim((string) ($_POST['testimonio'] ?? ''));
    $valoracion = max(1, min(5, (int) ($_POST['valoracion'] ?? 5)));
    $proyectoId = !empty($_POST['proyecto_id']) ? (int) $_POST['proyecto_id'] : null;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $aprobado = isset($_POST['aprobado']) ? 1 : 0;
    $orden = max(0, (int) ($_POST['orden'] ?? 0));
    $foto = $testimonio['foto'] ?? null;

    if ($nombre === '' || $testimonioTexto === '') {
        $error = 'Nombre y testimonio son obligatorios.';
    }

    if (!isset($error) && isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed, true)) {
            $error = 'La foto debe estar en formato JPG, PNG, GIF o WEBP.';
        } else {
            if (!file_exists('../assets/img/testimonios')) {
                mkdir('../assets/img/testimonios', 0777, true);
            }

            $newFilename = uniqid('test_', true) . '.' . $ext;
            $uploadPath = '../assets/img/testimonios/' . $newFilename;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                if ($foto && file_exists('../assets/img/testimonios/' . $foto)) {
                    unlink('../assets/img/testimonios/' . $foto);
                }
                $foto = $newFilename;
            } else {
                $error = 'No se pudo subir la foto del testimonio.';
            }
        }
    }

    if (!isset($error)) {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE testimonios SET nombre = ?, cargo = ?, empresa = ?, testimonio = ?, foto = ?, valoracion = ?, proyecto_id = ?, destacado = ?, aprobado = ?, orden = ? WHERE id = ?');
            $stmt->bind_param('sssssiiiiii', $nombre, $cargo, $empresa, $testimonioTexto, $foto, $valoracion, $proyectoId, $destacado, $aprobado, $orden, $id);
        } else {
            $stmt = $conn->prepare('INSERT INTO testimonios (nombre, cargo, empresa, testimonio, foto, valoracion, proyecto_id, destacado, aprobado, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssiiiii', $nombre, $cargo, $empresa, $testimonioTexto, $foto, $valoracion, $proyectoId, $destacado, $aprobado, $orden);
        }

        if ($stmt && $stmt->execute()) {
            $stmt->close();
            header('Location: testimonios.php?msg=saved');
            exit;
        }

        if ($stmt) {
            $stmt->close();
        }

        $error = 'Error al guardar: ' . $conn->error;
    }

    $testimonio = [
        'nombre' => $nombre,
        'cargo' => $cargo,
        'empresa' => $empresa,
        'testimonio' => $testimonioTexto,
        'foto' => $foto,
        'valoracion' => $valoracion,
        'proyecto_id' => $proyectoId,
        'destacado' => $destacado,
        'aprobado' => $aprobado,
        'orden' => $orden,
    ];
}

$projectOptions = fetchProjectDropdownOptions($conn);
$isApproved = isset($testimonio['aprobado']) ? (int) $testimonio['aprobado'] === 1 : true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?> - Admin</title>
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
                    <li><a href="testimonios.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-comment"></i><span>Testimonios</span></a></li>
                    <li><a href="mensajes.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-envelope"></i><span>Mensajes</span></a></li>
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8"><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?></h1>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow max-w-3xl">
                    <div class="grid gap-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Nombre del cliente *</label>
                                <input type="text" name="nombre" required value="<?php echo htmlspecialchars($testimonio['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Cargo</label>
                                <input type="text" name="cargo" value="<?php echo htmlspecialchars($testimonio['cargo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Empresa</label>
                                <input type="text" name="empresa" value="<?php echo htmlspecialchars($testimonio['empresa'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Valoracion (1-5) *</label>
                                <select name="valoracion" required class="w-full px-4 py-2 border rounded-lg">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($testimonio['valoracion']) && (int) $testimonio['valoracion'] === $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?> <?php echo $i === 1 ? 'estrella' : 'estrellas'; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2">Testimonio *</label>
                            <textarea name="testimonio" rows="4" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"><?php echo htmlspecialchars($testimonio['testimonio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2">Proyecto relacionado</label>
                            <select name="proyecto_id" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">-- Ninguno --</option>
                                <?php foreach ($projectOptions as $projectOption): ?>
                                    <option value="<?php echo (int) $projectOption['id']; ?>" <?php echo (isset($testimonio['proyecto_id']) && (int) $testimonio['proyecto_id'] === (int) $projectOption['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($projectOption['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-2">Foto del cliente</label>
                            <?php if ($id > 0 && !empty($testimonio['foto'])): ?>
                                <div class="mb-2">
                                    <img src="../assets/img/testimonios/<?php echo htmlspecialchars($testimonio['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto" class="w-20 h-20 rounded-full object-cover">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WEBP. Recomendado: cuadrado.</p>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="aprobado" value="1" <?php echo $isApproved ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600">
                                    <span class="text-gray-700">Publicar testimonio</span>
                                </label>
                                <p class="text-sm text-gray-500">Si lo desmarcas, el testimonio quedara pendiente y no se mostrara en la web publica.</p>

                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="destacado" value="1" <?php echo !empty($testimonio['destacado']) ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600">
                                    <span class="text-gray-700">Destacar en la pagina principal</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-gray-700 mb-2">Orden</label>
                                <input type="number" name="orden" min="0" value="<?php echo isset($testimonio['orden']) ? (int) $testimonio['orden'] : 0; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="testimonios.php" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Testimonio</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
