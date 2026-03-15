<?php
// admin/dashboard.php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';

// Verificar si está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Obtener estadísticas
ensureTestimonialsSchema($conn);
ensureAdminActivityLogSchema($conn);

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

$unreadPreview = $conn->query("SELECT id, nombre, email, created_at FROM mensajes WHERE leido = 0 ORDER BY created_at DESC LIMIT 5");
$pendingPreview = $conn->query("
    SELECT t.id, t.nombre, COALESCE(p.titulo, t.empresa, 'Sin proyecto') AS referencia
    FROM testimonios t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    WHERE t.aprobado = 0
    ORDER BY t.created_at DESC
    LIMIT 5
");
$activityPreview = $conn->query("SELECT admin_username, action, entity_type, created_at FROM admin_activity_log ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Proyectos MCE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>.logo-ring{position:absolute;inset:0;border:2px solid transparent;border-radius:8px;background:conic-gradient(from 0deg,#2563eb,#38bdf8,#2563eb);background-origin:border-box;animation:logo-spin 4s linear infinite;}@keyframes logo-spin{to{transform:rotate(360deg);}}</style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Barra superior móvil -->
    <header class="md:hidden sticky top-0 z-30 flex items-center justify-between bg-white px-4 py-3 shadow">
        <div class="flex items-center gap-2">
            <div class="relative h-10 w-10 shrink-0">
    <span class="logo-ring"></span>
    <img src="../imag/MCE.jpg" alt="MCE Admin" class="absolute inset-1 h-8 w-8 object-contain">
</div>
            <button id="toggleSidebar" class="p-2 rounded border border-blue-500/60 bg-gradient-to-br from-blue-500 via-blue-400 to-cyan-300 text-white shadow-[0_0_12px_rgba(59,130,246,0.65)] hover:shadow-[0_0_16px_rgba(56,189,248,0.75)] active:scale-95 transition">
                <i class="fas fa-bars text-white"></i>
            </button>
        </div>
        <a href="logout.php" onclick="return confirm('¿Cerrar sesión?' );" class="text-red-600 text-sm flex items-center gap-1"><i class="fas fa-sign-out-alt"></i>Salir</a>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed md:static inset-y-0 left-0 w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-200 z-40">
            <div class="p-4 border-b">
                <div class="relative h-10 w-10 shrink-0">
    <span class="logo-ring"></span>
    <img src="../imag/MCE.jpg" alt="MCE Admin" class="absolute inset-1 h-8 w-8 object-contain">
</div>
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
                        <a href="pagos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-credit-card"></i>
                            <span>Pagos</span>
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
                        <a href="cambiar-password.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-lock"></i>
                            <span>Cambiar clave</span>
                        </a>
                    </li>
                    <li>
                        <a href="auditoria.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span>Actividad</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" onclick="return confirm('¿Cerrar sesión?');" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Salir</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <!-- Contenido principal -->
        <!-- Overlay móvil -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>

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
                    <a href="proyectos.php" class="block rounded-lg bg-white p-6 shadow transition transform hover:-translate-y-1 hover:shadow-lg active:scale-95">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Proyectos</p>
                                <p class="text-3xl font-bold"><?php echo $total_proyectos; ?></p>
                            </div>
                            <i class="fas fa-folder text-4xl text-blue-600"></i>
                        </div>
                    </a>
                    
                    <a href="servicios.php" class="block rounded-lg bg-white p-6 shadow transition transform hover:-translate-y-1 hover:shadow-lg active:scale-95">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Servicios</p>
                                <p class="text-3xl font-bold"><?php echo $total_servicios; ?></p>
                            </div>
                            <i class="fas fa-cog text-4xl text-green-600"></i>
                        </div>
                    </a>
                    
                    <a href="mensajes.php" class="block rounded-lg bg-white p-6 shadow transition transform hover:-translate-y-1 hover:shadow-lg active:scale-95">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Mensajes</p>
                                <p class="text-3xl font-bold"><?php echo $total_mensajes; ?></p>
                            </div>
                            <i class="fas fa-envelope text-4xl text-yellow-600"></i>
                        </div>
                    </a>
                    
                    <a href="mensajes.php?estado=nuevo" class="block rounded-lg bg-white p-6 shadow transition transform hover:-translate-y-1 hover:shadow-lg active:scale-95">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">No leidos</p>
                                <p class="text-3xl font-bold text-red-600"><?php echo $mensajes_no_leidos; ?></p>
                            </div>
                            <i class="fas fa-envelope-open-text text-4xl text-red-600"></i>
                        </div>
                    </a>

                    <a href="testimonios.php" class="relative block overflow-hidden rounded-lg border border-amber-100 bg-white p-6 shadow transition transform hover:-translate-y-1 hover:shadow-lg active:scale-95">
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

                <div class="mb-8 grid gap-6 xl:grid-cols-[1.2fr_1fr]">
                    <div class="rounded-2xl bg-white p-6 shadow">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Acciones rapidas</p>
                                <h2 class="mt-2 text-2xl font-bold text-slate-900">Atajos del panel</h2>
                            </div>
                            <a href="auditoria.php" class="text-sm font-medium text-blue-600 hover:underline">Ver actividad</a>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <a href="proyecto-editar.php" class="rounded-2xl border border-blue-100 bg-blue-50 p-5 transition transform hover:-translate-y-1 hover:shadow-md active:scale-95">
                                <p class="text-lg font-bold text-blue-900">Nuevo proyecto</p>
                                <p class="mt-2 text-sm text-blue-700">Carga un caso nuevo al portafolio.</p>
                            </a>
                            <a href="servicio-editar.php" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 transition transform hover:-translate-y-1 hover:shadow-md active:scale-95">
                                <p class="text-lg font-bold text-emerald-900">Nuevo servicio</p>
                                <p class="mt-2 text-sm text-emerald-700">Agrega o ajusta tu oferta comercial.</p>
                            </a>
                            <a href="mensajes.php?estado=nuevo" class="rounded-2xl border border-sky-100 bg-sky-50 p-5 transition transform hover:-translate-y-1 hover:shadow-md active:scale-95">
                                <p class="text-lg font-bold text-sky-900">Responder mensajes</p>
                                <p class="mt-2 text-sm text-sky-700">Abre solo la cola de no leidos.</p>
                            </a>
                            <a href="testimonios.php?estado=pendientes" class="rounded-2xl border border-amber-100 bg-amber-50 p-5 transition transform hover:-translate-y-1 hover:shadow-md active:scale-95">
                                <p class="text-lg font-bold text-amber-900">Confirmar testimonios</p>
                                <p class="mt-2 text-sm text-amber-700">Revisa primero lo pendiente.</p>
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-900 p-6 text-white shadow">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-300">Actividad reciente</p>
                        <div class="mt-5 space-y-4">
                            <?php if ($activityPreview instanceof mysqli_result && $activityPreview->num_rows > 0): ?>
                                <?php while ($activity = $activityPreview->fetch_assoc()): ?>
                                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-sm font-semibold text-white"><?php echo admin_escape($activity['admin_username']); ?></p>
                                        <p class="mt-1 text-sm text-slate-300">
                                            <?php echo admin_escape($activity['action']); ?> sobre <?php echo admin_escape($activity['entity_type']); ?>
                                        </p>
                                        <p class="mt-2 text-xs text-slate-400"><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300">
                                    Aun no hay eventos registrados en la auditoria.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
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
                            <?php $isUnread = empty($msg['leido']); ?>
                            <tr class="border-b <?php echo $isUnread ? 'bg-sky-50' : 'bg-white'; ?> transition hover:bg-sky-100/70">
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
                                        <span class="bg-sky-100 text-sky-800 px-2 py-1 rounded text-sm font-semibold">No leido</span>
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

                <div class="grid gap-6 mb-8 xl:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h2 class="text-xl font-bold">Testimonios pendientes</h2>
                            <a href="testimonios.php?estado=pendientes" class="text-sm font-medium text-blue-600 hover:underline">Gestionar</a>
                        </div>
                        <div class="space-y-3">
                            <?php if ($pendingPreview instanceof mysqli_result && $pendingPreview->num_rows > 0): ?>
                                <?php while ($pending = $pendingPreview->fetch_assoc()): ?>
                                    <div class="flex items-center justify-between gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
                                        <div>
                                            <p class="font-semibold text-slate-900"><?php echo admin_escape($pending['nombre']); ?></p>
                                            <p class="text-sm text-amber-800"><?php echo admin_escape($pending['referencia']); ?></p>
                                        </div>
                                        <a href="testimonio-editar.php?id=<?php echo (int) $pending['id']; ?>" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100">Abrir</a>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="rounded-xl border border-green-100 bg-green-50 p-4 text-sm text-green-700">
                                    No hay testimonios pendientes por confirmar.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h2 class="text-xl font-bold">Mensajes no leidos</h2>
                            <a href="mensajes.php?estado=nuevo" class="text-sm font-medium text-blue-600 hover:underline">Gestionar</a>
                        </div>
                        <div class="space-y-3">
                            <?php if ($unreadPreview instanceof mysqli_result && $unreadPreview->num_rows > 0): ?>
                                <?php while ($pendingMessage = $unreadPreview->fetch_assoc()): ?>
                                    <div class="flex items-center justify-between gap-4 rounded-xl border border-sky-100 bg-sky-50 p-4">
                                        <div>
                                            <p class="font-semibold text-slate-900"><?php echo admin_escape($pendingMessage['nombre']); ?></p>
                                            <p class="text-sm text-sky-800"><?php echo admin_escape($pendingMessage['email']); ?></p>
                                        </div>
                                        <a href="mensaje-ver.php?id=<?php echo (int) $pendingMessage['id']; ?>" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-sky-700 hover:bg-sky-100">Abrir</a>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="rounded-xl border border-green-100 bg-green-50 p-4 text-sm text-green-700">
                                    No hay mensajes nuevos en este momento.
                                </div>
                            <?php endif; ?>
                        </div>
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

    // Toggle sidebar en móvil
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('toggleSidebar');
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    </script>
<?php include 'logout-modal.php'; ?>
</body>
</html>










