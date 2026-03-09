<?php
// admin/proyectos.php
require_once '../includes/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Procesar eliminación
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM proyectos WHERE id = $id");
    header('Location: proyectos.php?msg=deleted');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (igual que en dashboard.php) -->
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
        
        <!-- Contenido -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold">Proyectos</h1>
                    <a href="proyecto-editar.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Nuevo Proyecto
                    </a>
                </div>
                
                <?php if (isset($_GET['msg'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php 
                        if ($_GET['msg'] == 'deleted') echo 'Proyecto eliminado correctamente';
                        if ($_GET['msg'] == 'saved') echo 'Proyecto guardado correctamente';
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Título</th>
                                <th class="px-6 py-3 text-left">Categoría</th>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3 text-left">Destacado</th>
                                <th class="px-6 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $proyectos = $conn->query("SELECT * FROM proyectos ORDER BY orden, id DESC");
                            while ($p = $proyectos->fetch_assoc()):
                            ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo $p['titulo']; ?></td>
                                <td class="px-6 py-4"><?php echo $p['categoria']; ?></td>
                                <td class="px-6 py-4"><?php echo $p['cliente'] ?? '-'; ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($p['destacado']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Sí</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="proyecto-editar.php?id=<?php echo $p['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $p['id']; ?>" 
                                       class="text-red-600 hover:text-red-800"
                                       onclick="return confirm('¿Eliminar este proyecto?')">
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
