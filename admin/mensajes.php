<?php
// admin/mensajes.php
require_once '../includes/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Marcar como leído
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $conn->query("UPDATE mensajes SET leido = 1 WHERE id = $id");
    header('Location: mensajes.php');
    exit;
}

// Eliminar mensaje
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM mensajes WHERE id = $id");
    header('Location: mensajes.php?msg=deleted');
    exit;
}
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
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-blue-600">MCE Admin</h2>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li><a href="dashboard.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                    <li><a href="proyectos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-folder"></i><span>Proyectos</span></a></li>
                    <li><a href="servicios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-cog"></i><span>Servicios</span></a></li>
                    <li><a href="testimonios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-comment"></i><span>Testimonios</span></a></li>
                    <li><a href="mensajes.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-envelope"></i><span>Mensajes</span></a></li>
                    <li><a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>
        
        <!-- Contenido -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8">Mensajes de Contacto</h1>
                
                <?php if (isset($_GET['msg'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        Mensaje eliminado correctamente
                    </div>
                <?php endif; ?>
                
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Nombre</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Teléfono</th>
                                <th class="px-6 py-3 text-left">Mensaje</th>
                                <th class="px-6 py-3 text-left">Fecha</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                                <th class="px-6 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mensajes = $conn->query("SELECT * FROM mensajes ORDER BY created_at DESC");
                            while ($msg = $mensajes->fetch_assoc()):
                            ?>
                            <tr class="border-t hover:bg-gray-50 <?php echo !$msg['leido'] ? 'font-bold' : ''; ?>">
                                <td class="px-6 py-4"><?php echo $msg['nombre']; ?></td>
                                <td class="px-6 py-4"><?php echo $msg['email']; ?></td>
                                <td class="px-6 py-4"><?php echo $msg['telefono'] ?? '-'; ?></td>
                                <td class="px-6 py-4 max-w-xs truncate"><?php echo substr($msg['mensaje'], 0, 50); ?>...</td>
                                <td class="px-6 py-4"><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($msg['leido']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Leído</span>
                                    <?php else: ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Nuevo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if (!$msg['leido']): ?>
                                        <a href="?read=<?php echo $msg['id']; ?>" class="text-green-600 hover:text-green-800 mr-3" title="Marcar como leído">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $msg['id']; ?>" 
                                       class="text-red-600 hover:text-red-800"
                                       onclick="return confirm('¿Eliminar este mensaje?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
