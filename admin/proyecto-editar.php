<?php
// admin/proyecto-editar.php
require_once '../includes/config.php';
require_once '../includes/project-helpers.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';
require_once '../includes/image-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();

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
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'La sesion de seguridad no es valida. Recarga la pagina e intenta de nuevo.';
    }

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

    if (!isset($error) && ($titulo === '' || $descripcion === '' || $categoria === '')) {
        $error = 'Titulo, descripcion y categoria son obligatorios.';
    }

    if (!isset($error) && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = image_helper_store_upload(
            $_FILES['imagen'],
            '../assets/img/proyectos',
            'proy_',
            [
                'max_width' => 1600,
                'max_height' => 1200,
                'jpeg_quality' => 82,
                'webp_quality' => 80,
                'png_compression' => 6,
            ]
        );

        if (empty($uploadResult['ok'])) {
            $error = $uploadResult['error'] ?? 'No se pudo subir la imagen del proyecto.';
        } else {
            if (canDeleteManagedProjectImage($currentImage) && file_exists('../' . $currentImage)) {
                unlink('../' . $currentImage);
            }
            $imagen = 'assets/img/proyectos/' . $uploadResult['filename'];
        }
    }

    if (!isset($error)) {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE proyectos SET titulo = ?, descripcion = ?, imagen = ?, categoria = ?, url_demo = ?, url_repo = ?, cliente = ?, fecha_completado = ?, destacado = ?, orden = ? WHERE id = ?');
            $stmt->bind_param('ssssssssiii', $titulo, $descripcion, $imagen, $categoria, $url_demo, $url_repo, $cliente, $fecha_completado, $destacado, $orden, $id);
        } else {
            $stmt = $conn->prepare('INSERT INTO proyectos (titulo, descripcion, imagen, categoria, url_demo, url_repo, cliente, fecha_completado, destacado, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssssssii', $titulo, $descripcion, $imagen, $categoria, $url_demo, $url_repo, $cliente, $fecha_completado, $destacado, $orden);
        }
    }

    if (!isset($error) && isset($stmt) && $stmt->execute()) {
        $savedProjectId = $id > 0 ? $id : $stmt->insert_id;
        admin_log_action($conn, $id > 0 ? 'update' : 'create', 'project', (int) $savedProjectId, 'Proyecto guardado desde el formulario');
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

        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow max-w-4xl">
                    <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
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
                                <div id="project-image-dropzone" class="rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/60 p-6 transition hover:border-blue-400 hover:bg-blue-50" tabindex="0" role="button" aria-label="Arrastrar o seleccionar imagen del proyecto">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-blue-600 shadow-sm">
                                            <i class="fas fa-cloud-arrow-up text-2xl"></i>
                                        </span>
                                        <p class="mt-4 text-sm font-semibold text-slate-900">Arrastra una imagen aqui o haz clic para seleccionarla</p>
                                        <p id="project-image-dropzone-label" class="mt-2 text-sm text-blue-700">JPG, PNG, GIF o WEBP. Se optimiza automaticamente al guardar.</p>
                                    </div>
                                    <input id="project-image-input" type="file" name="imagen" accept="image/*" class="sr-only">
                                </div>
                                <div id="project-image-preview-wrapper" class="mt-4 hidden">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">Vista previa nueva</p>
                                    <img id="project-image-preview" src="" alt="Vista previa nueva del proyecto" class="h-52 w-full max-w-md rounded-lg border border-blue-200 object-cover shadow-sm">
                                    <p id="project-image-preview-name" class="mt-2 text-sm text-blue-700"></p>
                                </div>
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
    <script>
        (function () {
            var input = document.getElementById('project-image-input');
            var dropzone = document.getElementById('project-image-dropzone');
            var dropzoneLabel = document.getElementById('project-image-dropzone-label');
            var wrapper = document.getElementById('project-image-preview-wrapper');
            var preview = document.getElementById('project-image-preview');
            var fileName = document.getElementById('project-image-preview-name');
            var currentObjectUrl = null;
            var defaultLabel = 'JPG, PNG, GIF o WEBP. Se optimiza automaticamente al guardar.';

            if (!input || !dropzone || !dropzoneLabel || !wrapper || !preview || !fileName) {
                return;
            }

            function setPreview(file) {
                if (!file) {
                    wrapper.classList.add('hidden');
                    preview.src = '';
                    fileName.textContent = '';
                    dropzoneLabel.textContent = defaultLabel;
                    return;
                }

                if (currentObjectUrl) {
                    URL.revokeObjectURL(currentObjectUrl);
                }

                currentObjectUrl = URL.createObjectURL(file);
                preview.src = currentObjectUrl;
                fileName.textContent = file.name;
                dropzoneLabel.textContent = 'Archivo seleccionado: ' + file.name;
                wrapper.classList.remove('hidden');
            }

            function activateDropzone(active) {
                dropzone.classList.toggle('border-blue-500', active);
                dropzone.classList.toggle('bg-blue-100', active);
                dropzone.classList.toggle('shadow-lg', active);
            }

            dropzone.addEventListener('click', function () {
                input.click();
            });

            dropzone.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    input.click();
                }
            });

            ['dragenter', 'dragover'].forEach(function (eventName) {
                dropzone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    activateDropzone(true);
                });
            });

            ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
                dropzone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    activateDropzone(false);
                });
            });

            dropzone.addEventListener('drop', function (event) {
                var files = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files : null;
                if (!files || !files.length) {
                    return;
                }

                if (typeof DataTransfer !== 'undefined') {
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[0]);
                    input.files = dataTransfer.files;
                } else {
                    try {
                        input.files = files;
                    } catch (error) {
                    }
                }

                setPreview(files[0]);
            });

            input.addEventListener('change', function () {
                var file = input.files && input.files[0] ? input.files[0] : null;
                setPreview(file);
            });
        }());
    </script>
</body>
</html>
