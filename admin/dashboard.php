<?php
// admin/dashboard.php
require_once '../includes/config.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/payment-helpers.php';
require_once '../includes/admin-helpers.php';

// Verificar si está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Auto-migración global preventiva (asegura que soft-delete no crashee fetch_assoc en BD limpia)
foreach (['mensajes', 'proyectos'] as $tbl) {
    $chk = $conn->query("SHOW COLUMNS FROM {$tbl} LIKE 'deleted_at'");
    if (!$chk || $chk->num_rows === 0) {
        $conn->query("ALTER TABLE {$tbl} ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
    }
}

// Obtener estadísticas
ensureTestimonialsSchema($conn);
ensureAdminActivityLogSchema($conn);
ensureProjectPaymentsSchema($conn);
ensureCitasSchema($conn);

$citasHoy = admin_count_citas_hoy($conn);
$csrfAgenda = admin_get_csrf_token();

$citasProximas = [];
$citasPendientes = [];
$citasCanceladas = [];
$citasAgendaError = null;

$citasUpcomingStmt = $conn->prepare("
    SELECT id, nombre, email, telefono, servicio, fecha, hora, notas, COALESCE(estado,'pendiente') AS estado
    FROM citas
    WHERE fecha >= CURDATE()
    ORDER BY fecha ASC, hora ASC
    LIMIT 20
");

if ($citasUpcomingStmt) {
    if ($citasUpcomingStmt->execute()) {
        $res = $citasUpcomingStmt->get_result();
        if ($res instanceof mysqli_result) {
            while ($cita = $res->fetch_assoc()) {
                $estado = strtolower(trim((string) ($cita['estado'] ?? 'pendiente')));
                if ($estado !== 'cancelada') {
                    $citasProximas[] = $cita;
                }
                if ($estado === 'pendiente') {
                    $citasPendientes[] = $cita;
                }
            }
            $res->free();
        }
    } else {
        $citasAgendaError = $citasUpcomingStmt->error ?? 'No se pudieron cargar las citas.';
    }
    $citasUpcomingStmt->close();
} else {
    $citasAgendaError = $conn->error ?? 'No se pudieron cargar las citas.';
}

$citasCanceladasStmt = $conn->prepare("
    SELECT id, nombre, email, telefono, servicio, fecha, hora, notas, COALESCE(estado,'pendiente') AS estado
    FROM citas
    WHERE estado = 'cancelada'
    ORDER BY fecha DESC, hora DESC, id DESC
    LIMIT 10
");

if ($citasCanceladasStmt) {
    if ($citasCanceladasStmt->execute()) {
        $res = $citasCanceladasStmt->get_result();
        if ($res instanceof mysqli_result) {
            $citasCanceladas = $res->fetch_all(MYSQLI_ASSOC);
            $res->free();
        }
    }
    $citasCanceladasStmt->close();
}

$total_proyectos = $conn->query("SELECT COUNT(*) as total FROM proyectos WHERE deleted_at IS NULL")->fetch_assoc()['total'];
$total_servicios = $conn->query("SELECT COUNT(*) as total FROM servicios WHERE LOWER(titulo) <> 'tiendas online'")->fetch_assoc()['total'];
$mensajes_no_leidos = $conn->query("SELECT COUNT(*) as total FROM mensajes WHERE leido = 0 AND deleted_at IS NULL")->fetch_assoc()['total'];
$total_mensajes = $conn->query("SELECT COUNT(*) as total FROM mensajes WHERE deleted_at IS NULL")->fetch_assoc()['total'];
$testimonios_pendientes = getPendingTestimonialsCount($conn);
$pagosMesActual = [];
$pagosMesTotalCount = 0;
$pagosMesResult = $conn->query("SELECT moneda, COUNT(*) AS total, COALESCE(SUM(monto), 0) AS monto FROM proyecto_pagos WHERE DATE_FORMAT(fecha_pago, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m') AND deleted_at IS NULL GROUP BY moneda");
if ($pagosMesResult instanceof mysqli_result) {
    while ($row = $pagosMesResult->fetch_assoc()) {
        $currency = strtoupper(trim((string) ($row['moneda'] ?? 'COP')));
        $pagosMesActual[$currency] = [
            'total' => (int) ($row['total'] ?? 0),
            'monto' => (float) ($row['monto'] ?? 0),
        ];
        $pagosMesTotalCount += (int) ($row['total'] ?? 0);
    }
    $pagosMesResult->free();
}
$pagosMesLabel = [];
foreach ($pagosMesActual as $currency => $data) {
    $pagosMesLabel[] = payment_format_amount((float) $data['monto'], $currency);
}

// Datos para gráficos
$categorias_result = $conn->query("SELECT categoria, COUNT(*) AS total FROM proyectos WHERE deleted_at IS NULL GROUP BY categoria");
$categorias_labels = [];
$categorias_counts = [];
while ($row = $categorias_result->fetch_assoc()) {
    $categorias_labels[] = $row['categoria'];
    $categorias_counts[] = (int)$row['total'];
}

$mensajes_result = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS mes, COUNT(*) AS total FROM mensajes WHERE deleted_at IS NULL GROUP BY mes ORDER BY mes DESC LIMIT 6");
$mensajes_labels = [];
$mensajes_counts = [];
while ($row = $mensajes_result->fetch_assoc()) {
    $mensajes_labels[] = date('M Y', strtotime($row['mes'] . '-01'));
    $mensajes_counts[] = (int)$row['total'];
}
$mensajes_labels = array_reverse($mensajes_labels);
$mensajes_counts = array_reverse($mensajes_counts);

$unreadPreview = $conn->query("SELECT id, nombre, email, created_at FROM mensajes WHERE leido = 0 AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
$pendingPreview = $conn->query("
    SELECT t.id, t.nombre, COALESCE(p.titulo, t.empresa, 'Sin proyecto') AS referencia
    FROM testimonios t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    WHERE t.aprobado = 0
    ORDER BY t.created_at DESC
    LIMIT 5
");
$activityPreview = $conn->query("SELECT admin_username, action, entity_type, created_at FROM admin_activity_log ORDER BY created_at DESC LIMIT 5");
$lastPayments = $conn->query("
    SELECT pp.id, pp.concepto, pp.monto, pp.moneda, pp.fecha_pago, pr.titulo AS proyecto
    FROM proyecto_pagos pp
    LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id
    WHERE pp.deleted_at IS NULL
    ORDER BY pp.fecha_pago DESC, pp.id DESC
    LIMIT 5
");
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
    <div class="flex min-h-screen">
        <?php $activePage = 'dashboard'; $pendingTestimonials = $testimonios_pendientes ?? 0; include __DIR__ . '/partials/sidebar.php'; ?>
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

                <?php if (!empty($_SESSION['agenda_flash'])): ?>
                    <?php $flash = $_SESSION['agenda_flash']; unset($_SESSION['agenda_flash']); ?>
                    <div class="mb-4 rounded-xl border <?php echo ($flash['ok'] ?? false) ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800'; ?> px-4 py-3">
                        <?php if (!empty($flash['ok'])): ?>
                            Correo enviado al cliente (<?php echo admin_escape($flash['email'] ?? ''); ?>) con estado <?php echo admin_escape($flash['estado'] ?? ''); ?>. ID cita: <?php echo (int) ($flash['id'] ?? 0); ?>.
                        <?php else: ?>
                            No se pudo enviar el correo al cliente (ID <?php echo (int) ($flash['id'] ?? 0); ?>). <?php if (!empty($flash['error'])): ?>Detalle: <?php echo admin_escape($flash['error']); ?><?php endif; ?>
                            <?php if (!empty($flash['debug'])): ?>
                                <div class="mt-1 text-xs opacity-80">Debug: <?php echo admin_escape($flash['debug']); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h1 class="text-3xl font-bold">Dashboard</h1>
                    
                    <?php 
                    // Verificación robusta del estado de mantenimiento
                    $maint_file_path = __DIR__ . '/../includes/.maintenance';
                    $is_currently_maint = file_exists($maint_file_path);
                    $back_timestamp = $is_currently_maint ? (int)@file_get_contents($maint_file_path) : 0;
                    $is_expired_alert = ($is_currently_maint && $back_timestamp > 0 && time() >= $back_timestamp);

                    if ($is_expired_alert): 
                    ?>
                        <!-- Alerta Extrema de Rayos Rojos para el Admin -->
                        <div class="relative overflow-hidden bg-red-700 rounded-2xl p-5 shadow-2xl shadow-red-300 border-2 border-red-500 flex flex-col sm:flex-row items-center gap-5 transition-all animate-pulse">
                            <style>
                                .admin-bolt-v2 { position: absolute; color: #fff; opacity: 0.6; font-size: 1.8rem; animation: admin-lightning-v2 0.15s infinite; pointer-events: none; }
                                @keyframes admin-lightning-v2 { 0% { opacity: 0; transform: scale(1); } 50% { opacity: 1; transform: scale(1.3); } 100% { opacity: 0; transform: scale(1); } }
                            </style>
                            <!-- Rayos dispersos por la alerta -->
                            <i class="fas fa-bolt admin-bolt-v2" style="top:5%; left:10%;"></i>
                            <i class="fas fa-bolt admin-bolt-v2" style="bottom:5%; right:15%; animation-delay: 0.05s;"></i>
                            <i class="fas fa-bolt admin-bolt-v2" style="top:40%; left:5%; font-size: 1rem; animation-delay: 0.1s;"></i>
                            <i class="fas fa-bolt admin-bolt-v2" style="top:10%; right:5%; font-size: 1.2rem; animation-delay: 0.08s;"></i>

                            <div class="flex items-center gap-4 relative z-10">
                                <div class="bg-white/30 p-3 rounded-full border border-white/50 shadow-inner">
                                    <i class="fas fa-radiation-alt text-white text-2xl"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-white font-black text-lg tracking-tight uppercase leading-none">PÁGINA PÚBLICA BLOQUEADA</p>
                                    <p class="text-red-100 text-xs font-bold bg-black/20 px-2 py-0.5 rounded-md inline-block">¡TIEMPO AGOTADO! DESACTIVA EL MANTENIMIENTO AHORA</p>
                                </div>
                            </div>
                            <form method="POST" action="toggle-mantenimiento.php" class="relative z-10 ml-auto">
                                <input type="hidden" name="csrf_token" value="<?php echo admin_escape(admin_get_csrf_token()); ?>">
                                <button type="submit" class="bg-white text-red-700 px-6 py-3 rounded-xl font-black text-sm hover:bg-gray-100 active:scale-95 transition-all shadow-xl shadow-red-900/50 uppercase tracking-widest border-b-4 border-gray-300">
                                    DESBLOQUEAR YA
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="toggle-mantenimiento.php" class="bg-white px-5 py-4 rounded-2xl border <?php echo MAINTENANCE_MODE ? 'border-amber-400 bg-amber-50 shadow-amber-100' : 'border-gray-200'; ?> shadow-sm flex flex-col sm:flex-row items-center gap-4 transition-all">
                            <input type="hidden" name="csrf_token" value="<?php echo admin_escape(admin_get_csrf_token()); ?>">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-800">Modo Mantenimiento</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="h-2 w-2 rounded-full <?php echo MAINTENANCE_MODE ? 'bg-amber-500 animate-pulse' : 'bg-green-500'; ?>"></span>
                                    <p class="text-xs <?php echo MAINTENANCE_MODE ? 'text-amber-700 font-semibold' : 'text-gray-500'; ?>">
                                        <?php echo MAINTENANCE_MODE ? 'BLOQUEADO' : 'EN LÍNEA'; ?>
                                    </p>
                                </div>
                            </div>

                            <?php if (MAINTENANCE_MODE): ?>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center bg-amber-100 rounded-lg border border-amber-200 px-2 py-1">
                                        <input type="number" name="hours" placeholder="H" class="w-10 bg-transparent border-none text-right text-sm focus:ring-0" min="0" value="0">
                                        <span class="text-xs font-bold text-amber-600">:</span>
                                        <input type="number" name="minutes" placeholder="M" class="w-10 bg-transparent border-none text-sm focus:ring-0" min="0" max="59" value="0">
                                    </div>
                                    <button type="submit" name="update_time" class="rounded-lg bg-amber-200 px-3 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-300 transition-colors">
                                        Actualizar
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Duración esperada:</span>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 px-2 py-1 focus-within:border-blue-400 transition-all">
                                                <input type="number" name="hours" placeholder="H" class="w-10 bg-transparent border-none text-right text-sm focus:ring-0" min="0" value="0">
                                                <span class="text-xs font-bold text-gray-400">:</span>
                                                <input type="number" name="minutes" placeholder="M" class="w-10 bg-transparent border-none text-sm focus:ring-0" min="0" max="59" value="30">
                                            </div>
                                            <span class="text-[11px] text-gray-400 font-medium ml-1">h:m</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 <?php echo MAINTENANCE_MODE ? 'bg-amber-500' : 'bg-gray-300'; ?>" role="switch">
                                <span class="pointer-events-none translate-x-0 inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out <?php echo MAINTENANCE_MODE ? 'translate-x-5' : 'translate-x-0'; ?>"></span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Script para monitoreo de tiempo en Admin y auto-recarga -->
                <?php if (MAINTENANCE_MODE && $maintenance_back_at > time()): ?>
                    <script>
                        const adminBackAt = <?php echo $maintenance_back_at * 1000; ?>;
                        if (adminBackAt > 0) {
                            const adminCheck = setInterval(() => {
                                if (Date.now() >= adminBackAt) {
                                    clearInterval(adminCheck);
                                    location.reload(); // Recarga automática para mostrar alerta roja
                                }
                            }, 1000);
                        }
                    </script>
                <?php endif; ?>

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

                    <a href="pagos.php" class="block rounded-lg bg-white p-6 shadow transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Pagos (mes)</p>
                                <p class="text-xl font-bold text-slate-900">
                                    <?php echo count($pagosMesLabel) > 0 ? $pagosMesLabel[0] : 'COP 0,00'; ?>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?php if (count($pagosMesLabel) > 1): ?>
                                        Otros: <?php echo implode(' | ', array_slice($pagosMesLabel, 1)); ?>
                                    <?php elseif ($pagosMesTotalCount === 0): ?>
                                        Sin pagos registrados este mes
                                    <?php else: ?>
                                        <?php echo $pagosMesTotalCount; ?> pago<?php echo $pagosMesTotalCount === 1 ? '' : 's'; ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <i class="fas fa-receipt text-4xl text-purple-600"></i>
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

                <!-- Agenda de llamadas -->
                <div id="agenda-llamadas" class="mb-8 rounded-2xl bg-white p-6 shadow">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-600">Agenda de llamadas</p>
                            <h2 class="mt-1 text-2xl font-bold text-slate-900">Citas programadas</h2>
                            <p class="text-sm text-gray-500">Divide por proximas, por confirmar y canceladas.</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 text-sm">
                            <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 font-semibold text-amber-700">
                                <i class="fas fa-phone-volume"></i>
                                Hoy: <?php echo (int) ($citasHoy ?? 0); ?>
                            </span>
                            <span class="text-gray-500">Próximas: <?php echo count($citasProximas); ?> · Por confirmar: <?php echo count($citasPendientes); ?> · Canceladas: <?php echo count($citasCanceladas); ?></span>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" class="agenda-tab active rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700" data-tab="proximas">Próximas</button>
                        <button type="button" class="agenda-tab rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700" data-tab="pendientes">Por confirmar</button>
                        <button type="button" class="agenda-tab rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700" data-tab="canceladas">Canceladas</button>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <?php if ($citasAgendaError): ?>
                            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
                                No pudimos cargar la agenda. <?php echo admin_escape($citasAgendaError); ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        $agendaLists = [
                            'proximas' => $citasProximas,
                            'pendientes' => $citasPendientes,
                            'canceladas' => $citasCanceladas,
                        ];
                        foreach ($agendaLists as $tabKey => $listaCitas):
                        ?>
                            <div class="agenda-panel <?php echo $tabKey === 'proximas' ? '' : 'hidden'; ?>" data-tab-panel="<?php echo $tabKey; ?>">
                                <?php if (count($listaCitas) === 0): ?>
                                    <div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-700 my-2">
                                        <?php if ($tabKey === 'proximas'): ?>
                                            No hay citas programadas para hoy ni los próximos días.
                                        <?php elseif ($tabKey === 'pendientes'): ?>
                                            No hay citas pendientes de confirmación.
                                        <?php else: ?>
                                            No hay cancelaciones recientes.
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($listaCitas as $cita): ?>
                                        <?php
                                            $fechaRaw = $cita['fecha'] ?? '';
                                            $horaRaw = $cita['hora'] ?? '';
                                            $fechaTs = strtotime($fechaRaw);
                                            $horaLabel = $horaRaw ? date('H:i', strtotime($horaRaw)) : '--';
                                            $hoy = date('Y-m-d');
                                            $manana = date('Y-m-d', strtotime('+1 day'));
                                            if ($fechaRaw === $hoy) {
                                                $fechaEtiqueta = 'Hoy';
                                            } elseif ($fechaRaw === $manana) {
                                                $fechaEtiqueta = 'Mañana';
                                            } elseif ($fechaTs) {
                                                $fechaEtiqueta = date('d/m', $fechaTs);
                                            } else {
                                                $fechaEtiqueta = 'Agendada';
                                            }
                                            $notasPreview = '';
                                            if (!empty($cita['notas'])) {
                                                $notasPreview = trim((string) $cita['notas']);
                                                if (strlen($notasPreview) > 140) {
                                                    $notasPreview = substr($notasPreview, 0, 140) . '…';
                                                }
                                            }
                                            $estado = strtolower(trim((string) ($cita['estado'] ?? 'pendiente')));
                                            $estadoLabel = ucfirst($estado);
                                            $estadoClasses = [
                                                'pendiente' => 'bg-amber-100 text-amber-800',
                                                'confirmada' => 'bg-green-100 text-green-800',
                                                'cancelada' => 'bg-red-100 text-red-800',
                                            ];
                                            $badgeClass = $estadoClasses[$estado] ?? 'bg-slate-100 text-slate-700';
                                        ?>
                                        <div class="flex items-start justify-between gap-4 py-3">
                                            <div class="flex items-start gap-3">
                                                <div class="min-w-[100px] rounded-xl bg-blue-50 px-3 py-2 text-center text-blue-800">
                                                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em]"><?php echo admin_escape($fechaEtiqueta); ?></p>
                                                    <p class="text-xl font-bold leading-tight"><?php echo admin_escape($horaLabel); ?></p>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <p class="font-semibold text-slate-900"><?php echo admin_escape($cita['nombre']); ?></p>
                                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold <?php echo $badgeClass; ?>">
                                                            <span class="h-2 w-2 rounded-full bg-current opacity-70"></span>
                                                            <?php echo admin_escape($estadoLabel); ?>
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600">
                                                        <?php echo admin_escape($cita['email']); ?>
                                                        <?php if (!empty($cita['telefono'])): ?>
                                                            <span class="mx-1 text-gray-400">&bull;</span>
                                                            <?php echo admin_escape($cita['telefono']); ?>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if (!empty($cita['servicio'])): ?>
                                                        <p class="mt-1 text-xs font-semibold text-blue-700 uppercase tracking-[0.08em]">Interes: <?php echo admin_escape($cita['servicio']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($notasPreview !== ''): ?>
                                                        <p class="mt-1 text-xs text-gray-600">Nota: <?php echo admin_escape($notasPreview); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end gap-2">
                                                <?php if ($fechaTs): ?>
                                                    <span class="inline-flex items-center gap-2 rounded-full <?php echo ($fechaRaw === $hoy) ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700'; ?> px-3 py-1 text-xs font-semibold">
                                                        <i class="fas fa-calendar-day"></i>
                                                        <?php echo date('d/m', $fechaTs); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <div class="flex gap-2">
                                                    <a href="mailto:<?php echo admin_escape($cita['email']); ?>" class="text-xs font-medium text-blue-600 hover:underline">Correo</a>
                                                    <?php if (!empty($cita['telefono'])): ?>
                                                        <a href="https://wa.me/<?php echo preg_replace('/\D+/', '', $cita['telefono']); ?>" class="text-xs font-medium text-green-600 hover:underline" target="_blank" rel="noopener">WhatsApp</a>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($estado !== 'cancelada'): ?>
                                                    <div class="flex gap-2">
                                                        <?php if ($estado !== 'confirmada'): ?>
                                                            <form method="post" action="cita-estado.php">
                                                                <input type="hidden" name="csrf" value="<?php echo admin_escape($csrfAgenda); ?>">
                                                                <input type="hidden" name="id" value="<?php echo (int) $cita['id']; ?>">
                                                                <input type="hidden" name="estado" value="confirmada">
                                                                <input type="hidden" name="redirect" value="dashboard.php#agenda-llamadas">
                                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 hover:bg-green-100">
                                                                    <i class="fas fa-check"></i> Confirmar
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        <form method="post" action="cita-estado.php">
                                                            <input type="hidden" name="csrf" value="<?php echo admin_escape($csrfAgenda); ?>">
                                                            <input type="hidden" name="id" value="<?php echo (int) $cita['id']; ?>">
                                                            <input type="hidden" name="estado" value="cancelada">
                                                            <input type="hidden" name="redirect" value="dashboard.php#agenda-llamadas">
                                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                <i class="fas fa-ban"></i> Cancelar
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php else: ?>
                                                    <form method="post" action="cita-estado.php">
                                                        <input type="hidden" name="csrf" value="<?php echo admin_escape($csrfAgenda); ?>">
                                                        <input type="hidden" name="id" value="<?php echo (int) $cita['id']; ?>">
                                                        <input type="hidden" name="estado" value="confirmada">
                                                        <input type="hidden" name="redirect" value="dashboard.php#agenda-llamadas">
                                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                            <i class="fas fa-rotate-left"></i> Reabrir
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                            <a href="proyecto-editar.php" class="rounded-2xl border border-blue-100 bg-blue-50 p-5 transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-lg font-bold text-blue-900">Nuevo proyecto</p>
                                <p class="mt-2 text-sm text-blue-700">Carga un caso nuevo al portafolio.</p>
                            </a>
                            <a href="servicio-editar.php" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-lg font-bold text-emerald-900">Nuevo servicio</p>
                                <p class="mt-2 text-sm text-emerald-700">Agrega o ajusta tu oferta comercial.</p>
                            </a>
                            <a href="mensajes.php?estado=nuevo" class="rounded-2xl border border-sky-100 bg-sky-50 p-5 transition hover:-translate-y-1 hover:shadow-md">
                                <p class="text-lg font-bold text-sky-900">Responder mensajes</p>
                                <p class="mt-2 text-sm text-sky-700">Abre solo la cola de no leidos.</p>
                            </a>
                            <a href="testimonios.php?estado=pendientes" class="rounded-2xl border border-amber-100 bg-amber-50 p-5 transition hover:-translate-y-1 hover:shadow-md">
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

                <!-- Últimos pagos -->
                <div class="bg-white p-6 rounded-lg shadow mb-8">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 class="text-xl font-bold">Ultimos pagos</h2>
                        <a href="pagos.php" class="text-sm font-medium text-blue-600 hover:underline">Ver todos</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px]">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Concepto</th>
                                    <th class="text-left py-2">Proyecto</th>
                                    <th class="text-left py-2">Monto</th>
                                    <th class="text-left py-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($lastPayments instanceof mysqli_result && $lastPayments->num_rows > 0): ?>
                                    <?php while ($pago = $lastPayments->fetch_assoc()): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-2 font-medium text-slate-900">
                                                <a href="pago-editar.php?id=<?php echo (int) $pago['id']; ?>" class="hover:text-blue-600 hover:underline">
                                                    <?php echo admin_escape($pago['concepto']); ?>
                                                </a>
                                            </td>
                                            <td class="py-2 text-sm text-gray-700">
                                                <?php if (!empty($pago['proyecto'])): ?>
                                                    <?php echo admin_escape($pago['proyecto']); ?>
                                                <?php else: ?>
                                                    <span class="text-gray-500">Sin proyecto</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-2 text-sm font-semibold text-slate-900">
                                                <?php echo payment_format_amount((float) $pago['monto'], (string) $pago['moneda']); ?>
                                            </td>
                                            <td class="py-2 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="border-b">
                                        <td colspan="4" class="py-4 text-center text-gray-500">Todavia no hay pagos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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

    // Tabs agenda
    const agendaTabs = document.querySelectorAll('.agenda-tab');
    const agendaPanels = document.querySelectorAll('.agenda-panel');
    agendaTabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tab = btn.getAttribute('data-tab');
            agendaTabs.forEach(function (b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            agendaPanels.forEach(function (panel) {
                if (panel.getAttribute('data-tab-panel') === tab) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });
        });
    });
    </script>
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>
