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

$statusOptions = paymentStatusOptions();
$methodOptions = paymentMethodOptions();
$currencyOptions = paymentCurrencyOptions();
$formaPagoOptions = [
    'contado' => 'Contado',
    'cuotas' => 'Cuotas',
];
$projectsOptions = fetchProjectDropdownOptions($conn);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$payment = null;
$pageTitle = 'Registrar pago';

if ($id > 0) {
    $result = $conn->query('SELECT * FROM proyecto_pagos WHERE id = ' . $id . ' LIMIT 1');
    if ($result instanceof mysqli_result) {
        $payment = $result->fetch_assoc();
        $result->free();
    }

    if ($payment) {
        $pageTitle = 'Editar pago';
    } else {
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'La sesion de seguridad no es valida. Recarga la pagina e intenta de nuevo.';
    }

    $proyectoId = isset($_POST['proyecto_id']) ? (int) $_POST['proyecto_id'] : 0;
    $clienteLibre = sanitize($_POST['cliente'] ?? '');
    $concepto = sanitize($_POST['concepto'] ?? '');
    $montoInput = str_replace(',', '.', trim((string) ($_POST['monto'] ?? '0')));
    $monto = is_numeric($montoInput) ? (float) $montoInput : 0;
    $formaPago = trim((string) ($_POST['forma_pago'] ?? 'contado'));
    $proximaCuota = trim((string) ($_POST['proxima_cuota'] ?? ''));
    $cuotasTotales = isset($_POST['cuotas_totales']) ? (int) $_POST['cuotas_totales'] : null;
    $cuotasPendientes = isset($_POST['cuotas_pendientes']) ? (int) $_POST['cuotas_pendientes'] : null;
    $moneda = strtoupper(trim((string) ($_POST['moneda'] ?? 'COP')));
    $estado = trim((string) ($_POST['estado'] ?? 'recibido'));
    $metodo = trim((string) ($_POST['metodo'] ?? ''));
    $referencia = sanitize($_POST['referencia'] ?? '');
    $notas = trim((string) ($_POST['notas'] ?? ''));
    $fecha_pago = trim((string) ($_POST['fecha_pago'] ?? ''));

    if (!isset($currencyOptions[$moneda])) {
        $moneda = 'COP';
    }

    if (!isset($statusOptions[$estado])) {
        $estado = 'recibido';
    }

    if ($metodo !== '' && !isset($methodOptions[$metodo])) {
        $metodo = 'otro';
    }

    if (!isset($formaPagoOptions[$formaPago])) {
        $formaPago = 'contado';
    }

    $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha_pago) !== false;
    $proximaValida = $proximaCuota !== '' ? DateTime::createFromFormat('Y-m-d', $proximaCuota) !== false : true;

    if ($formaPago === 'cuotas' && $proximaCuota === '') {
        $error = 'Indica la fecha de la próxima cuota para pagos en cuotas.';
    }
    if ($formaPago === 'cuotas') {
        if ($cuotasTotales === null || $cuotasTotales < 1) {
            $error = 'Define cuántas cuotas tendrá el pago.';
        }
        if (!isset($error) && ($cuotasPendientes === null || $cuotasPendientes < 0 || $cuotasPendientes > $cuotasTotales)) {
            $error = 'Cuotas pendientes debe estar entre 0 y el total de cuotas.';
        }
        if (!isset($error) && $cuotasPendientes === null) {
            $cuotasPendientes = $cuotasTotales;
        }
    } else {
        $cuotasTotales = null;
        $cuotasPendientes = null;
        $proximaCuota = '';
    }

    if (!isset($error) && !$proximaValida) {
        $error = 'La fecha de próxima cuota no es válida.';
    }

    if (!isset($error) && ($concepto === '' || $monto <= 0 || !$fechaValida)) {
        $error = 'Concepto, monto y fecha de pago son obligatorios (el monto debe ser mayor a cero).';
    }

    if (!isset($error)) {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE proyecto_pagos SET proyecto_id = NULLIF(?, 0), cliente = NULLIF(?, \'\'), forma_pago = ?, proxima_cuota = NULLIF(?, \'\'), cuotas_totales = ?, cuotas_pendientes = ?, concepto = ?, monto = ?, moneda = ?, estado = ?, metodo = ?, referencia = ?, notas = ?, fecha_pago = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            if ($stmt) {
                $stmt->bind_param(
                    'isssiisdssssss',
                    $proyectoId,
                    $clienteLibre,
                    $formaPago,
                    $proximaCuota,
                    $cuotasTotales,
                    $cuotasPendientes,
                    $concepto,
                    $monto,
                    $moneda,
                    $estado,
                    $metodo,
                    $referencia,
                    $notas,
                    $fecha_pago,
                    $id
                );
            } else {
                $error = 'No se pudo preparar la consulta (update): ' . $conn->error;
            }
        } else {
            $stmt = $conn->prepare('INSERT INTO proyecto_pagos (proyecto_id, cliente, forma_pago, proxima_cuota, cuotas_totales, cuotas_pendientes, concepto, monto, moneda, estado, metodo, referencia, notas, fecha_pago) VALUES (NULLIF(?, 0), NULLIF(?, \'\'), ?, NULLIF(?, \'\'), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param(
                    'isssiisdssssss',
                    $proyectoId,
                    $clienteLibre,
                    $formaPago,
                    $proximaCuota,
                    $cuotasTotales,
                    $cuotasPendientes,
                    $concepto,
                    $monto,
                    $moneda,
                    $estado,
                    $metodo,
                    $referencia,
                    $notas,
                    $fecha_pago
                );
            } else {
                $error = 'No se pudo preparar la consulta (insert): ' . $conn->error;
            }
        }
    }

    if (!isset($error) && isset($stmt) && $stmt && $stmt->execute()) {
        $savedId = $id > 0 ? $id : $stmt->insert_id;
        admin_log_action($conn, $id > 0 ? 'update' : 'create', 'payment', (int) $savedId, 'Pago guardado desde el formulario');
        header('Location: pagos.php?msg=saved');
        exit;
    }

    if (!isset($error)) {
        $error = 'Error al guardar: ' . ($stmt ? $stmt->error : $conn->error);
    }

    $payment = [
        'proyecto_id' => $proyectoId,
        'cliente' => $clienteLibre,
        'forma_pago' => $formaPago,
        'proxima_cuota' => $proximaCuota,
        'concepto' => $concepto,
        'monto' => $monto,
        'moneda' => $moneda,
        'estado' => $estado,
        'metodo' => $metodo,
        'referencia' => $referencia,
        'notas' => $notas,
        'fecha_pago' => $fecha_pago,
        'cuotas_totales' => $cuotasTotales,
        'cuotas_pendientes' => $cuotasPendientes,
    ];
}

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
    <div class="flex min-h-screen">
        <?php $activePage = 'pagos'; include __DIR__ . '/partials/sidebar.php'; ?>

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
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div>
                        <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p class="mt-2 text-sm text-gray-600">Asocia cada pago a un proyecto y deja notas internas.</p>
                    </div>
                    <a href="pagos.php" class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a pagos
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="grid gap-6 lg:grid-cols-3">
                    <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                    <div class="lg:col-span-2 rounded-2xl bg-white p-8 shadow">
                        <div class="grid gap-6">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Proyecto</label>
                                    <select name="proyecto_id" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                        <option value="0">Sin asignar</option>
                                        <?php foreach ($projectsOptions as $project): ?>
                                            <option value="<?php echo (int) $project['id']; ?>" <?php echo (isset($payment['proyecto_id']) && (int) $payment['proyecto_id'] === (int) $project['id']) ? 'selected' : ''; ?>>
                                                <?php echo admin_escape($project['titulo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha de pago *</label>
                                    <input
                                        type="date"
                                        name="fecha_pago"
                                        required
                                        value="<?php echo admin_escape($payment['fecha_pago'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cliente (si no hay proyecto)</label>
                                <input
                                    type="text"
                                    name="cliente"
                                    value="<?php echo admin_escape($payment['cliente'] ?? ''); ?>"
                                    placeholder="Nombre del cliente"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Concepto *</label>
                                <input
                                    type="text"
                                    name="concepto"
                                    required
                                    value="<?php echo admin_escape($payment['concepto'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-600 focus:outline-none"
                                >
                            </div>

                            <div class="grid gap-4 md:grid-cols-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Monto *</label>
                                    <input
                                        type="number"
                                        name="monto"
                                        step="0.01"
                                        min="0"
                                        required
                                        value="<?php echo isset($payment['monto']) ? htmlspecialchars((string) $payment['monto'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-600 focus:outline-none"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Moneda</label>
                                    <select name="moneda" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                        <?php foreach ($currencyOptions as $key => $label): ?>
                                            <option value="<?php echo admin_escape($key); ?>" <?php echo ($payment['moneda'] ?? 'COP') === $key ? 'selected' : ''; ?>>
                                                <?php echo admin_escape($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                                    <select name="estado" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                        <?php foreach ($statusOptions as $key => $label): ?>
                                            <option value="<?php echo admin_escape($key); ?>" <?php echo ($payment['estado'] ?? 'recibido') === $key ? 'selected' : ''; ?>>
                                                <?php echo admin_escape($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Metodo</label>
                                    <select name="metodo" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                        <option value="">Selecciona</option>
                                        <?php foreach ($methodOptions as $key => $label): ?>
                                            <option value="<?php echo admin_escape($key); ?>" <?php echo ($payment['metodo'] ?? '') === $key ? 'selected' : ''; ?>>
                                                <?php echo admin_escape($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Referencia</label>
                                    <input
                                        type="text"
                                        name="referencia"
                                        value="<?php echo admin_escape($payment['referencia'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-600 focus:outline-none"
                                    >
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Forma de pago</label>
                                    <select name="forma_pago" id="forma_pago" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                        <?php foreach ($formaPagoOptions as $key => $label): ?>
                                            <option value="<?php echo admin_escape($key); ?>" <?php echo ($payment['forma_pago'] ?? 'contado') === $key ? 'selected' : ''; ?>>
                                                <?php echo admin_escape($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Próxima cuota (si aplica)</label>
                                    <input
                                        type="date"
                                        name="proxima_cuota"
                                        id="proxima_cuota"
                                        value="<?php echo admin_escape($payment['proxima_cuota'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none"
                                    >
                                    <p class="mt-1 text-xs text-gray-500">Solo se requiere si la forma de pago es en cuotas.</p>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2" id="cuotas_wrapper">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total de cuotas</label>
                                    <input
                                        type="number"
        min="1"
        name="cuotas_totales"
        id="cuotas_totales"
        value="<?php echo admin_escape($payment['cuotas_totales'] ?? ''); ?>"
        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cuotas pendientes</label>
                                    <input
                                        type="number"
        min="0"
        name="cuotas_pendientes"
        id="cuotas_pendientes"
        value="<?php echo admin_escape($payment['cuotas_pendientes'] ?? ''); ?>"
        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none"
                                    >
                                    <p class="mt-1 text-xs text-gray-500">Usa 0 cuando ya no quedan cuotas pendientes.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Notas internas</label>
                                <textarea
                                    name="notas"
                                    rows="4"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-blue-600 focus:outline-none"
                                ><?php echo htmlspecialchars($payment['notas'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-3">
                            <div class="text-sm text-gray-500">Los campos marcados con * son obligatorios.</div>
                            <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Guardar pago
                            </button>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow">
                        <h3 class="text-lg font-semibold text-slate-900">Tips rapidos</h3>
                        <ul class="mt-4 space-y-3 text-sm text-gray-600">
                            <li class="flex gap-2">
                                <i class="fas fa-circle-info mt-1 text-blue-500"></i>
                                <span>Usa "Sin asignar" si el pago no pertenece a un proyecto del portafolio.</span>
                            </li>
                            <li class="flex gap-2">
                                <i class="fas fa-layer-group mt-1 text-blue-500"></i>
                                <span>Guarda los abonos en varios registros para ver el historial por proyecto.</span>
                            </li>
                            <li class="flex gap-2">
                                <i class="fas fa-lock mt-1 text-blue-500"></i>
                                <span>Las referencias y notas quedan solo para el equipo interno.</span>
                            </li>
                        </ul>

                        <?php if ($id > 0 && isset($payment['created_at'])): ?>
                            <div class="mt-6 rounded-xl bg-gray-50 p-4 text-xs text-gray-600">
                                <p>Creado: <?php echo date('d/m/Y H:i', strtotime($payment['created_at'])); ?></p>
                                <?php if (!empty($payment['updated_at'])): ?>
                                    <p>Actualizado: <?php echo date('d/m/Y H:i', strtotime($payment['updated_at'])); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
    <script>
        (function() {
            const forma = document.getElementById('forma_pago');
            const proxima = document.getElementById('proxima_cuota');
            const cuotasTot = document.getElementById('cuotas_totales');
            const cuotasPend = document.getElementById('cuotas_pendientes');
            const wrapper = document.getElementById('cuotas_wrapper');
            if (!forma || !proxima) return;
            function toggle() {
                const cuotas = forma.value === 'cuotas';
                proxima.disabled = !cuotas;
                proxima.classList.toggle('bg-gray-100', !cuotas);
                if (wrapper) { wrapper.classList.toggle('hidden', !cuotas); }
                if (cuotasTot) { cuotasTot.disabled = !cuotas; cuotasTot.classList.toggle('bg-gray-100', !cuotas); if (!cuotas) cuotasTot.value = ''; }
                if (cuotasPend) { cuotasPend.disabled = !cuotas; cuotasPend.classList.toggle('bg-gray-100', !cuotas); if (!cuotas) cuotasPend.value = ''; }
            }
            forma.addEventListener('change', toggle);
            toggle();
        }());
    </script>
</body>
</html>
