<?php
// admin/dashboard.php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';

// Verificar si está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Obtener estadísticas
ensureTestimonialsSchema($conn);

$total_proyectos = $conn->query("SELECT COUNT(*) as total FROM proyectos")->fetch_assoc()['total'];
$total_servicios = $conn->query("SELECT COUNT(*) as total FROM servicios")->fetch_assoc()['total'];
$mensajes_no_leidos = $conn->query("SELECT COUNT(*) as total FROM mensajes WHERE leido = 0")->fetch_assoc()['total'];
$total_mensajes = $conn->query("SELECT COUNT(*) as total FROM mensajes")->fetch_assoc()['total'];
$testimonios_pendientes = getPendingTestimonialsCount($conn);

// Datos para gráficos
$categorias_result = $conn->query("SELECT categoria, COUNT(*) AS total FROM proyectos GROUP BY categoria");
$categorias_labels = [];
$categorias_counts = [];
while ($row = $categorias_result->fetch_assoc()) {
    $categorias_labels[] = $row['categoria'];
    $categorias_counts[] = (int)$row['total'];
}

$mensajes_result = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes, COUNT(*) AS total FROM mensajes GROUP BY mes ORDER BY mes DESC LIMIT 6");
$mensajes_labels = [];
$mensajes_counts = [];
while ($row = $mensajes_result->fetch_assoc()) {
    $mensajes_labels[] = date('M Y', strtotime($row['mes'] . '-01'));
    $mensajes_counts[] = (int)$row['total'];
}
$mensajes_labels = array_reverse($mensajes_labels);
$mensajes_counts = array_reverse($mensajes_counts);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Proyectos MCE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li>
                        <a href="dashboard.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="proyectos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-folder"></i>
                            <span>Proyectos</span>
                        </a>
                    </li>
                    <li>
                        <a href="servicios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-cog"></i>
                            <span>Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="testimonios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-comment"></i>
                            <span>Testimonios</span>
                            <?php if ($testimonios_pendientes > 0): ?>
                                <span class="ml-auto inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <span class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-500 opacity-75"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-600"></span>
                                    </span>
                                    <?php echo $testimonios_pendientes; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="mensajes.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-envelope"></i>
                            <span>Mensajes</span>
                            <?php if ($mensajes_no_leidos > 0): ?>
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded"><?php echo $mensajes_no_leidos; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Salir</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <!-- Contenido principal -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

                <?php if ($testimonios_pendientes > 0): ?>
                    <a href="testimonios.php" class="mb-8 flex items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 via-orange-50 to-red-50 px-6 py-5 shadow-sm transition hover:shadow-md">
                        <div class="flex items-center gap-4">
                            <span class="relative flex h-4 w-4">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex h-4 w-4 rounded-full bg-red-500"></span>
                            </span>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Alerta de moderacion</p>
                                <p class="text-lg font-bold text-slate-900">
                                    Tienes <?php echo $testimonios_pendientes; ?> testimonio<?php echo $testimonios_pendientes === 1 ? '' : 's'; ?> pendiente<?php echo $testimonios_pendientes === 1 ? '' : 's'; ?> de aprobacion
                                </p>
                            </div>
                        </div>
                        <span class="rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-amber-700">Revisar ahora</span>
                    </a>
                <?php endif; ?>
                
                <!-- Tarjetas de estadísticas -->
                <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-5">
                    <a href="proyectos.php" class="block rounded-lg bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Proyectos</p>
                                <p class="text-3xl font-bold"><?php echo $total_proyectos; ?></p>
                            </div>
                            <i class="fas fa-folder text-4xl text-blue-600"></i>
                        </div>
                    </a>
                    
                    <a href="servicios.php" class="block rounded-lg bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Servicios</p>
                                <p class="text-3xl font-bold"><?php echo $total_servicios; ?></p>
                            </div>
                            <i class="fas fa-cog text-4xl text-green-600"></i>
                        </div>
                    </a>
                    
                    <a href="mensajes.php" class="block rounded-lg bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Mensajes</p>
                                <p class="text-3xl font-bold"><?php echo $total_mensajes; ?></p>
                            </div>
                            <i class="fas fa-envelope text-4xl text-yellow-600"></i>
                        </div>
                    </a>
                    
                    <a href="mensajes.php?estado=nuevo" class="block rounded-lg bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">No leidos</p>
                                <p class="text-3xl font-bold text-red-600"><?php echo $mensajes_no_leidos; ?></p>
                            </div>
                            <i class="fas fa-envelope-open-text text-4xl text-red-600"></i>
                        </div>
                    </a>

                    <a href="testimonios.php" class="relative block overflow-hidden rounded-lg border border-amber-100 bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-amber-50 to-transparent"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Pendientes</p>
                                <div class="mt-1 flex items-center gap-3">
                                    <p class="text-3xl font-bold <?php echo $testimonios_pendientes > 0 ? 'text-amber-700' : 'text-green-600'; ?>">
                                        <?php echo $testimonios_pendientes; ?>
                                    </p>
                                    <?php if ($testimonios_pendientes > 0): ?>
                                        <span class="relative flex h-3 w-3">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex h-3 w-3 rounded-full bg-amber-500"></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-2 text-sm <?php echo $testimonios_pendientes > 0 ? 'text-amber-700' : 'text-green-700'; ?>">
                                    <?php echo $testimonios_pendientes > 0 ? 'Esperando revision' : 'Todo aprobado'; ?>
                                </p>
                            </div>
                            <i class="fas fa-comment-dots text-4xl <?php echo $testimonios_pendientes > 0 ? 'text-amber-500' : 'text-green-500'; ?>"></i>
                        </div>
                    </a>
                </div>

                <!-- Gráficos -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Gráfico de proyectos por categoría -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Proyectos por Categoría</h3>
                        <canvas id="categoriasChart"></canvas>
                    </div>
                    
                    <!-- Gráfico de mensajes por mes -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Mensajes por Mes</h3>
                        <canvas id="mensajesChart"></canvas>
                    </div>
                </div>
                
                <!-- Últimos mensajes -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 class="text-xl font-bold">Ultimos mensajes</h2>
                        <a href="mensajes.php" class="text-sm font-medium text-blue-600 hover:underline">Ver todos</a>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px]">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Nombre</th>
                                <th class="text-left py-2">Email</th>
                                <th class="text-left py-2">Fecha</th>
                                <th class="text-left py-2">Estado</th>
                                <th class="text-left py-2">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $mensajes = $conn->query("SELECT * FROM mensajes ORDER BY created_at DESC LIMIT 5");
                            while ($msg = $mensajes->fetch_assoc()):
                            ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2">
                                    <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="font-medium text-slate-900 hover:text-blue-600 hover:underline">
                                        <?php echo htmlspecialchars($msg['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </td>
                                <td class="py-2">
                                    <a href="mailto:<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text-blue-600 hover:underline">
                                        <?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </td>
                                <td class="py-2"><?php echo date('d/m/Y', strtotime($msg['created_at'])); ?></td>
                                <td class="py-2">
                                    <?php if ($msg['leido']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Leido</span>
                                    <?php else: ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Nuevo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2">
                                    <a href="mensaje-ver.php?id=<?php echo (int) $msg['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                        <i class="fas fa-eye"></i>
                                        <span>Abrir</span>
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
    </div>

    <script>
    const categoriasData = {
        labels: <?php echo json_encode($categorias_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($categorias_counts); ?>,
            backgroundColor: ['#2563eb', '#16a34a', '#dc2626', '#f59e0b', '#0ea5e9']
        }]
    };

    const mensajesData = {
        labels: <?php echo json_encode($mensajes_labels); ?>,
        datasets: [{
            label: 'Mensajes',
            data: <?php echo json_encode($mensajes_counts); ?>,
            fill: true,
            backgroundColor: 'rgba(37, 99, 235, 0.15)',
            borderColor: '#2563eb',
            tension: 0.3
        }]
    };

    if (document.getElementById('categoriasChart')) {
        new Chart(document.getElementById('categoriasChart'), {
            type: 'doughnut',
            data: categoriasData
        });
    }

    if (document.getElementById('mensajesChart')) {
        new Chart(document.getElementById('mensajesChart'), {
            type: 'line',
            data: mensajesData,
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
    </script>
</body>
</html>
