<?php
require_once '../includes/config.php';
require_once '../includes/project-helpers.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';
require_once '../includes/image-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureTestimonialsSchema($conn);
$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();

function canDeleteManagedTestimonialPhoto(?string $photoName): bool
{
    if (!$photoName) {
        return false;
    }

    return basename($photoName) === $photoName;
}

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
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'La sesion de seguridad no es valida. Recarga la pagina e intenta de nuevo.';
    }

    $submitAction = $_POST['submit_action'] ?? 'save';
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

    if ($submitAction === 'approve') {
        $aprobado = 1;
    }

    if ($submitAction === 'pending') {
        $aprobado = 0;
    }

    if (!isset($error) && ($nombre === '' || $testimonioTexto === '')) {
        $error = 'Nombre y testimonio son obligatorios.';
    }

    if (!isset($error) && isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $uploadResult = image_helper_store_upload(
            $_FILES['foto'],
            '../assets/img/testimonios',
            'test_',
            [
                'max_width' => 800,
                'max_height' => 800,
                'jpeg_quality' => 82,
                'webp_quality' => 80,
                'png_compression' => 6,
            ]
        );

        if (empty($uploadResult['ok'])) {
            $error = $uploadResult['error'] ?? 'No se pudo subir la foto del testimonio.';
        } else {
            if (canDeleteManagedTestimonialPhoto($foto) && file_exists('../assets/img/testimonios/' . $foto)) {
                unlink('../assets/img/testimonios/' . $foto);
            }
            $foto = $uploadResult['filename'];
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
            $savedTestimonialId = $id > 0 ? $id : $stmt->insert_id;
            $stmt->close();
            admin_log_action($conn, $id > 0 ? 'update' : 'create', 'testimonial', (int) $savedTestimonialId, $aprobado === 1 ? 'Testimonio guardado y publicado desde el formulario' : 'Testimonio guardado como pendiente');
            header('Location: testimonios.php?msg=' . ($aprobado === 1 ? 'approved' : 'saved'));
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
    <div class="flex min-h-screen">
        <?php $activePage = 'testimonios'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="flex-1 overflow-y-auto lg:ml-0">
            <div class="p-8">
                <div class="mb-4 flex items-center justify-between lg:hidden">
                    <button id="sidebar-open" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50">
                        <span class="flex flex-col gap-1">
                            <span class="block w-5 h-0.5 bg-blue-700"></span>
                            <span class="block w-5 h-0.5 bg-blue-700"></span>
                            <span class="block w-5 h-0.5 bg-blue-700"></span>
                        </span>
                        <span>Menú</span>
                    </button>
                </div>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <div class="mt-3 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold <?php echo $isApproved ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-800'; ?>">
                        <?php if (!$isApproved): ?>
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-amber-600"></span>
                            </span>
                        <?php endif; ?>
                        <?php echo $isApproved ? 'Publicado en la web publica' : 'Pendiente de confirmacion'; ?>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow max-w-3xl">
                    <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
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
                            <div id="testimonial-photo-dropzone" class="rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/60 p-6 transition hover:border-blue-400 hover:bg-blue-50" tabindex="0" role="button" aria-label="Arrastrar o seleccionar foto del testimonio">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-blue-600 shadow-sm">
                                        <i class="fas fa-image text-2xl"></i>
                                    </span>
                                    <p class="mt-4 text-sm font-semibold text-slate-900">Arrastra la foto aqui o haz clic para seleccionarla</p>
                                    <p id="testimonial-photo-dropzone-label" class="mt-2 text-sm text-blue-700">Formatos JPG, PNG, GIF o WEBP. Ideal en formato cuadrado.</p>
                                </div>
                                <input id="testimonial-photo-input" type="file" name="foto" accept="image/*" class="sr-only">
                            </div>
                            <div id="testimonial-photo-preview-wrapper" class="mt-4 hidden">
                                <p class="mb-2 text-sm font-semibold text-gray-700">Vista previa nueva</p>
                                <div class="flex items-center gap-4">
                                    <img id="testimonial-photo-preview" src="" alt="Vista previa nueva del testimonio" class="h-24 w-24 rounded-full border border-blue-200 object-cover shadow-sm">
                                    <p id="testimonial-photo-preview-name" class="text-sm text-blue-700"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="aprobado" value="1" <?php echo $isApproved ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600">
                                    <span class="text-gray-700">Marcar como confirmado y visible</span>
                                </label>
                                <p class="text-sm text-gray-500">Si lo desmarcas, el testimonio quedara pendiente y no se mostrara en la web publica hasta que lo confirmes.</p>

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

                        <div class="flex flex-wrap justify-end gap-4 pt-4">
                            <a href="testimonios.php" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancelar</a>
                            <button type="submit" name="submit_action" value="pending" class="px-6 py-2 rounded-lg border border-amber-200 bg-amber-50 font-medium text-amber-700 hover:bg-amber-100">Guardar pendiente</button>
                            <button type="submit" name="submit_action" value="approve" class="px-6 py-2 rounded-lg bg-green-600 font-medium text-white hover:bg-green-700">Guardar y confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var input = document.getElementById('testimonial-photo-input');
            var dropzone = document.getElementById('testimonial-photo-dropzone');
            var dropzoneLabel = document.getElementById('testimonial-photo-dropzone-label');
            var wrapper = document.getElementById('testimonial-photo-preview-wrapper');
            var preview = document.getElementById('testimonial-photo-preview');
            var fileName = document.getElementById('testimonial-photo-preview-name');
            var currentObjectUrl = null;
            var defaultLabel = 'Formatos JPG, PNG, GIF o WEBP. Ideal en formato cuadrado.';

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
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>
