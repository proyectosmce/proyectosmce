<?php
// admin/servicio-editar.php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$servicio = null;
$titulo_pagina = 'Nuevo Servicio';

if ($id > 0) {
    $result = $conn->query("SELECT * FROM servicios WHERE id = $id");
    $servicio = $result->fetch_assoc();
    $titulo_pagina = 'Editar Servicio';
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'La sesion de seguridad no es valida. Recarga la pagina e intenta de nuevo.';
    }

    $titulo = sanitize($_POST['titulo']);
    $descripcion = sanitize($_POST['descripcion']);
    $icono = sanitize($_POST['icono']);
    $precio_desde = (float)$_POST['precio_desde'];
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $orden = (int)$_POST['orden'];
    
    if ($id > 0) {
        // Actualizar
        $stmt = $conn->prepare("UPDATE servicios SET titulo=?, descripcion=?, icono=?, precio_desde=?, destacado=?, orden=? WHERE id=?");
        $stmt->bind_param("sssdiii", $titulo, $descripcion, $icono, $precio_desde, $destacado, $orden, $id);
    } else {
        // Insertar
        $stmt = $conn->prepare("INSERT INTO servicios (titulo, descripcion, icono, precio_desde, destacado, orden) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdii", $titulo, $descripcion, $icono, $precio_desde, $destacado, $orden);
    }
    
    if (!isset($error) && $stmt->execute()) {
        $savedServiceId = $id > 0 ? $id : $stmt->insert_id;
        admin_log_action($conn, $id > 0 ? 'update' : 'create', 'service', (int) $savedServiceId, 'Servicio guardado desde el formulario');
        header('Location: servicios.php?msg=saved');
        exit;
    } else if (!isset($error)) {
        $error = "Error al guardar: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php $activePage = 'servicios'; include __DIR__ . '/partials/sidebar.php'; ?>
        
        <!-- Contenido -->
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
                <h1 class="text-3xl font-bold mb-8"><?php echo $titulo_pagina; ?></h1>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="bg-white p-8 rounded-lg shadow max-w-3xl">
                    <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                    <div class="grid gap-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Título del servicio *</label>
                            <input type="text" name="titulo" required 
                                   value="<?php echo $servicio['titulo'] ?? ''; ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Descripción *</label>
                            <textarea name="descripcion" rows="4" required 
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"><?php echo $servicio['descripcion'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Icono (FontAwesome) *</label>
                                <input type="text" name="icono" required 
                                       value="<?php echo $servicio['icono'] ?? 'code'; ?>"
                                       placeholder="ej: code, shopping-cart, boxes"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                                <p class="text-sm text-gray-500 mt-1">Ver iconos en <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-600">FontAwesome</a></p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Precio desde ($) *</label>
                                <input type="number" name="precio_desde" required step="0.01" min="0"
                                       value="<?php echo $servicio['precio_desde'] ?? ''; ?>"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="destacado" value="1" 
                                           <?php echo (isset($servicio['destacado']) && $servicio['destacado']) ? 'checked' : ''; ?>
                                           class="w-4 h-4 text-blue-600">
                                    <span class="text-gray-700">Destacar en la página principal</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Orden de visualización</label>
                                <input type="number" name="orden" min="0"
                                       value="<?php echo $servicio['orden'] ?? 0; ?>"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-4 pt-4">
                            <a href="servicios.php" class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Guardar Servicio
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>
