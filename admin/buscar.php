<?php
require_once '../includes/config.php';
require_once '../includes/project-helpers.php';
require_once '../includes/testimonial-helpers.php';
require_once '../includes/admin-helpers.php';
require_once '../includes/payment-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureTestimonialsSchema($conn);
ensureProjectPaymentsSchema($conn);
$pendingTestimonials = getPendingTestimonialsCount($conn);
$csrfToken = admin_get_csrf_token();

$q = trim((string) ($_GET['q'] ?? ''));
$limit = 15;

function search_like(mysqli $conn, string $sql, string $term, int $limit): array
{
    $termLike = '%' . $term . '%';
    $stmt = $conn->prepare($sql . " LIMIT {$limit}");
    if (!$stmt) return [];
    $stmt->bind_param('ss', $termLike, $termLike);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows ?: [];
}

$results = [
    'proyectos' => [],
    'pagos' => [],
    'mensajes' => [],
    'citas' => [],
];

if ($q !== '') {
    $results['proyectos'] = search_like(
        $conn,
        "SELECT id, titulo, cliente FROM proyectos WHERE titulo LIKE ? OR cliente LIKE ? ORDER BY created_at DESC",
        $q,
        $limit
    );

    $termLike = '%' . $q . '%';
    $stmtPay = $conn->prepare("SELECT pp.id, pp.concepto, pp.referencia, pp.cliente, pp.forma_pago, pp.monto, pp.moneda, pr.titulo AS proyecto 
         FROM proyecto_pagos pp 
         LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id 
         WHERE pp.concepto LIKE ? OR pp.referencia LIKE ? OR pp.cliente LIKE ? OR pr.titulo LIKE ? 
         ORDER BY pp.fecha_pago DESC, pp.id DESC 
         LIMIT {$limit}");
    if ($stmtPay) {
        $stmtPay->bind_param('ssss', $termLike, $termLike, $termLike, $termLike);
        $stmtPay->execute();
        $resPay = $stmtPay->get_result();
        $results['pagos'] = $resPay ? $resPay->fetch_all(MYSQLI_ASSOC) : [];
        $stmtPay->close();
    }

    // Mensajes (nombre o email o mensaje)
    $stmtMsg = $conn->prepare("SELECT id, nombre, email, telefono, created_at FROM mensajes WHERE nombre LIKE ? OR email LIKE ? OR mensaje LIKE ? ORDER BY created_at DESC LIMIT {$limit}");
    if ($stmtMsg) {
        $stmtMsg->bind_param('sss', $termLike, $termLike, $termLike);
        $stmtMsg->execute();
        $resMsg = $stmtMsg->get_result();
        $results['mensajes'] = $resMsg ? $resMsg->fetch_all(MYSQLI_ASSOC) : [];
        $stmtMsg->close();
    }

    // Citas
    $stmtCita = $conn->prepare("SELECT id, nombre, email, fecha, hora FROM citas WHERE nombre LIKE ? OR email LIKE ? ORDER BY fecha DESC, hora DESC LIMIT {$limit}");
    if ($stmtCita) {
        $stmtCita->bind_param('ss', $termLike, $termLike);
        $stmtCita->execute();
        $resCita = $stmtCita->get_result();
        $results['citas'] = $resCita ? $resCita->fetch_all(MYSQLI_ASSOC) : [];
        $stmtCita->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador global - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <?php $activePage = 'buscar'; include __DIR__ . '/partials/sidebar.php'; ?>
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

                <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Buscador global</h1>
                        <p class="mt-2 text-sm text-gray-600">Encuentra pagos, proyectos, citas o mensajes desde un solo lugar.</p>
                    </div>
                </div>

                <form method="GET" class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            name="q"
                            value="<?php echo admin_escape($q); ?>"
                            autofocus
                            placeholder="Cliente, referencia, proyecto, email..."
                            class="w-full rounded-xl border border-gray-200 bg-white py-3 pl-10 pr-4 shadow-sm focus:border-blue-600 focus:outline-none"
                        >
                    </div>
                </form>

                <?php if ($q === ''): ?>
                    <div class="rounded-xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">
                        Escribe algo para buscar en pagos, proyectos, citas y mensajes.
                    </div>
                <?php else: ?>
                    <div class="grid gap-6 xl:grid-cols-2">
                        <div class="rounded-2xl bg-white p-5 shadow border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-slate-900">Pagos</h3>
                                <span class="text-xs text-gray-500"><?php echo count($results['pagos']); ?> encontrados</span>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                <?php if (count($results['pagos']) === 0): ?>
                                    <li class="py-3 text-sm text-gray-500">Sin resultados.</li>
                                <?php else: ?>
                                    <?php foreach ($results['pagos'] as $row): ?>
                                        <li class="py-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <a href="pago-editar.php?id=<?php echo (int) $row['id']; ?>" class="font-semibold text-slate-900 hover:text-blue-700"><?php echo admin_escape($row['concepto']); ?></a>
                                                    <p class="text-sm text-gray-600">
                                                        <?php echo admin_escape($row['proyecto'] ?: ($row['cliente'] ?? 'Sin proyecto')); ?>
                                                        · <?php echo payment_format_amount((float) $row['monto'], (string) $row['moneda']); ?>
                                                    </p>
                                                    <?php if (!empty($row['referencia'])): ?>
                                                        <p class="text-xs text-gray-500">Ref: <?php echo admin_escape($row['referencia']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                    <?php echo admin_escape(forma_pago_label($row['forma_pago'] ?? 'contado')); ?>
                                                </span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-slate-900">Proyectos</h3>
                                <span class="text-xs text-gray-500"><?php echo count($results['proyectos']); ?> encontrados</span>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                <?php if (count($results['proyectos']) === 0): ?>
                                    <li class="py-3 text-sm text-gray-500">Sin resultados.</li>
                                <?php else: ?>
                                    <?php foreach ($results['proyectos'] as $row): ?>
                                        <li class="py-3">
                                            <a href="proyecto-editar.php?id=<?php echo (int) $row['id']; ?>" class="font-semibold text-slate-900 hover:text-blue-700"><?php echo admin_escape($row['titulo']); ?></a>
                                            <?php if (!empty($row['cliente'])): ?>
                                                <p class="text-sm text-gray-600">Cliente: <?php echo admin_escape($row['cliente']); ?></p>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-slate-900">Mensajes</h3>
                                <span class="text-xs text-gray-500"><?php echo count($results['mensajes']); ?> encontrados</span>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                <?php if (count($results['mensajes']) === 0): ?>
                                    <li class="py-3 text-sm text-gray-500">Sin resultados.</li>
                                <?php else: ?>
                                    <?php foreach ($results['mensajes'] as $row): ?>
                                        <li class="py-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <a href="mensaje-ver.php?id=<?php echo (int) $row['id']; ?>" class="font-semibold text-slate-900 hover:text-blue-700"><?php echo admin_escape($row['nombre']); ?></a>
                                                    <p class="text-sm text-gray-600"><?php echo admin_escape($row['email']); ?></p>
                                                    <p class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></p>
                                                </div>
                                                <?php if (!empty($row['telefono'])): ?>
                                                    <span class="text-xs text-gray-500"><?php echo admin_escape($row['telefono']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-slate-900">Citas</h3>
                                <span class="text-xs text-gray-500"><?php echo count($results['citas']); ?> encontradas</span>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                <?php if (count($results['citas']) === 0): ?>
                                    <li class="py-3 text-sm text-gray-500">Sin resultados.</li>
                                <?php else: ?>
                                    <?php foreach ($results['citas'] as $row): ?>
                                        <li class="py-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-semibold text-slate-900"><?php echo admin_escape($row['nombre']); ?></p>
                                                    <p class="text-sm text-gray-600"><?php echo admin_escape($row['email']); ?></p>
                                                    <p class="text-xs text-gray-500"><?php echo date('d/m/Y', strtotime($row['fecha'])) . ' ' . substr((string)$row['hora'], 0, 5); ?></p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>
