<?php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureTestimonialsSchema($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id > 0) {
        if ($action === 'delete') {
            $photo = null;
            if ($stmt = $conn->prepare('SELECT foto FROM testimonios WHERE id = ? LIMIT 1')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->bind_result($photo);
                $stmt->fetch();
                $stmt->close();
            }

            if ($stmt = $conn->prepare('DELETE FROM testimonios WHERE id = ?')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }

            if ($photo && file_exists("../assets/img/testimonios/{$photo}")) {
                unlink("../assets/img/testimonios/{$photo}");
            }

            header('Location: testimonios.php?msg=deleted');
            exit;
        }

        if ($action === 'approve' || $action === 'hide') {
            $approved = $action === 'approve' ? 1 : 0;
            if ($stmt = $conn->prepare('UPDATE testimonios SET aprobado = ? WHERE id = ?')) {
                $stmt->bind_param('ii', $approved, $id);
                $stmt->execute();
                $stmt->close();
            }

            header('Location: testimonios.php?msg=' . ($approved ? 'approved' : 'hidden'));
            exit;
        }
    }
}

$pendingCount = 0;
$pendingResult = $conn->query('SELECT COUNT(*) AS total FROM testimonios WHERE aprobado = 0');
if ($pendingResult instanceof mysqli_result) {
    $pendingCount = (int) ($pendingResult->fetch_assoc()['total'] ?? 0);
    $pendingResult->free();
}

$testimonios = $conn->query("
    SELECT t.*, p.titulo AS proyecto_titulo
    FROM testimonios t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    ORDER BY t.aprobado ASC, t.destacado DESC, t.orden ASC, t.id DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonios - Admin</title>
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
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold">Testimonios de Clientes</h1>
                        <p class="text-sm text-gray-600 mt-2">
                            Pendientes de aprobacion:
                            <span class="font-semibold <?php echo $pendingCount > 0 ? 'text-amber-600' : 'text-green-600'; ?>">
                                <?php echo $pendingCount; ?>
                            </span>
                        </p>
                    </div>
                    <a href="testimonio-editar.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>Nuevo Testimonio
                    </a>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php
                        if ($_GET['msg'] === 'deleted') echo 'Testimonio eliminado correctamente';
                        if ($_GET['msg'] === 'saved') echo 'Testimonio guardado correctamente';
                        if ($_GET['msg'] === 'approved') echo 'Testimonio publicado correctamente';
                        if ($_GET['msg'] === 'hidden') echo 'Testimonio marcado como pendiente';
                        ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Foto</th>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3 text-left">Empresa</th>
                                <th class="px-6 py-3 text-left">Testimonio</th>
                                <th class="px-6 py-3 text-left">Valoracion</th>
                                <th class="px-6 py-3 text-left">Proyecto</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                                <th class="px-6 py-3 text-left">Destacado</th>
                                <th class="px-6 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($testimonios instanceof mysqli_result): ?>
                                <?php while ($t = $testimonios->fetch_assoc()): ?>
                                    <tr class="border-t hover:bg-gray-50 <?php echo (int) ($t['aprobado'] ?? 1) === 0 ? 'bg-amber-50/60' : ''; ?>">
                                        <td class="px-6 py-4">
                                            <?php if (!empty($t['foto'])): ?>
                                                <img src="../assets/img/testimonios/<?php echo htmlspecialchars($t['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8'); ?>" class="w-10 h-10 rounded-full object-cover">
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <i class="fas fa-user text-gray-500"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($t['empresa'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4 max-w-xs truncate"><?php echo htmlspecialchars($t['testimonio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex text-yellow-400">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <?php if ($i <= (int) ($t['valoracion'] ?? 0)): ?>
                                                        <i class="fas fa-star"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($t['proyecto_titulo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ((int) ($t['aprobado'] ?? 1) === 1): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Publicado</span>
                                            <?php else: ?>
                                                <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-sm">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (!empty($t['destacado'])): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Si</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="testimonio-editar.php?id=<?php echo (int) $t['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-3" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>">
                                                <input type="hidden" name="action" value="<?php echo (int) ($t['aprobado'] ?? 1) === 1 ? 'hide' : 'approve'; ?>">
                                                <button type="submit" class="<?php echo (int) ($t['aprobado'] ?? 1) === 1 ? 'text-amber-600 hover:text-amber-800' : 'text-green-600 hover:text-green-800'; ?> mr-3" title="<?php echo (int) ($t['aprobado'] ?? 1) === 1 ? 'Marcar como pendiente' : 'Publicar'; ?>">
                                                    <i class="fas <?php echo (int) ($t['aprobado'] ?? 1) === 1 ? 'fa-eye-slash' : 'fa-check'; ?>"></i>
                                                </button>
                                            </form>

                                            <form method="POST" class="inline" onsubmit="return confirm('Eliminar este testimonio?');">
                                                <input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr class="border-t">
                                    <td colspan="9" class="px-6 py-6 text-center text-gray-500">No se pudieron cargar los testimonios.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
