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
    $concepto = sanitize($_POST['concepto'] ?? '');
    $montoInput = str_replace(',', '.', trim((string) ($_POST['monto'] ?? '0')));
    $monto = is_numeric($montoInput) ? (float) $montoInput : 0;
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

    $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha_pago) !== false;

    if (!isset($error) && ($concepto === '' || $monto <= 0 || !$fechaValida)) {
        $error = 'Concepto, monto y fecha de pago son obligatorios (el monto debe ser mayor a cero).';
    }

    if (!isset($error)) {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE proyecto_pagos SET proyecto_id = NULLIF(?, 0), concepto = ?, monto = ?, moneda = ?, estado = ?, metodo = ?, referencia = ?, notas = ?, fecha_pago = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->bind_param(
                'isdssssssi',
                $proyectoId,
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
            $stmt = $conn->prepare('INSERT INTO proyecto_pagos (proyecto_id, concepto, monto, moneda, estado, metodo, referencia, notas, fecha_pago) VALUES (NULLIF(?, 0), ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param(
                'isdssssss',
                $proyectoId,
                $concepto,
                $monto,
                $moneda,
                $estado,
                $metodo,
                $referencia,
                $notas,
                $fecha_pago
            );
        }
    }

    if (!isset($error) && isset($stmt) && $stmt->execute()) {
        $savedId = $id > 0 ? $id : $stmt->insert_id;
        admin_log_action($conn, $id > 0 ? 'update' : 'create', 'payment', (int) $savedId, 'Pago guardado desde el formulario');
        header('Location: pagos.php?msg=saved');
        exit;
    }

    if (!isset($error)) {
        $error = 'Error al guardar: ' . $conn->error;
    }

    $payment = [
        'proyecto_id' => $proyectoId,
        'concepto' => $concepto,
        'monto' => $monto,
        'moneda' => $moneda,
        'estado' => $estado,
        'metodo' => $metodo,
        'referencia' => $referencia,
        'notas' => $notas,
        'fecha_pago' => $fecha_pago,
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
</body>
</html>
