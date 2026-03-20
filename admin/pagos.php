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

$searchTerm = trim((string) ($_GET['q'] ?? ''));
$projectId = (int) ($_GET['proyecto_id'] ?? 0);
$statusFilter = trim((string) ($_GET['estado'] ?? ''));
$methodFilter = trim((string) ($_GET['metodo'] ?? ''));
$currencyFilter = strtoupper(trim((string) ($_GET['moneda'] ?? '')));
$formaPagoFilter = trim((string) ($_GET['forma_pago'] ?? ''));
$fromDate = trim((string) ($_GET['desde'] ?? ''));
$toDate = trim((string) ($_GET['hasta'] ?? ''));
$onlyCuotas = isset($_GET['solo_cuotas']) && $_GET['solo_cuotas'] === '1';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 12;

$toast = admin_build_toast($_GET['msg'] ?? '', [
    'saved' => ['message' => 'Pago guardado correctamente.'],
    'deleted' => ['message' => 'Pago eliminado correctamente.'],
    'factura_enviada' => ['message' => 'Factura enviada al correo indicado.'],
    'csrf' => ['type' => 'error', 'title' => 'Sesion no valida', 'message' => 'Recarga la pagina e intenta de nuevo.'],
]);

$filterParams = [
    'q' => $searchTerm,
    'proyecto_id' => $projectId,
    'estado' => $statusFilter,
    'metodo' => $methodFilter,
    'forma_pago' => $formaPagoFilter,
    'moneda' => $currencyFilter,
    'desde' => $fromDate,
    'hasta' => $toDate,
    'solo_cuotas' => $onlyCuotas ? '1' : null,
    'page' => $page,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: ' . admin_build_url('pagos.php', array_merge($filterParams, ['msg' => 'csrf'])));
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($action === 'delete' && $id > 0) {
        if ($stmt = $conn->prepare('DELETE FROM proyecto_pagos WHERE id = ?')) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }

        admin_log_action($conn, 'delete', 'payment', $id, 'Pago eliminado desde el listado');
        header('Location: ' . admin_build_url('pagos.php', array_merge($filterParams, ['msg' => 'deleted'])));
        exit;
    }
}

$whereClauses = [];

if ($projectId > 0) {
    $whereClauses[] = 'pp.proyecto_id = ' . $projectId;
}

$statusOptions = paymentStatusOptions();
if ($statusFilter !== '' && isset($statusOptions[$statusFilter])) {
    $safeStatus = $conn->real_escape_string($statusFilter);
    $whereClauses[] = "pp.estado = '{$safeStatus}'";
}

$methodOptions = paymentMethodOptions();
if ($methodFilter !== '' && isset($methodOptions[$methodFilter])) {
    $safeMethod = $conn->real_escape_string($methodFilter);
    $whereClauses[] = "pp.metodo = '{$safeMethod}'";
}

$formaPagoOptions = [
    'contado' => 'Contado',
    'cuotas' => 'Cuotas',
];
if ($formaPagoFilter !== '' && isset($formaPagoOptions[$formaPagoFilter])) {
    $safeForma = $conn->real_escape_string($formaPagoFilter);
    $whereClauses[] = "pp.forma_pago = '{$safeForma}'";
}

$currencyOptions = paymentCurrencyOptions();
if ($currencyFilter !== '' && isset($currencyOptions[$currencyFilter])) {
    $safeCurrency = $conn->real_escape_string($currencyFilter);
    $whereClauses[] = "pp.moneda = '{$safeCurrency}'";
}

if ($fromDate !== '' && DateTime::createFromFormat('Y-m-d', $fromDate) !== false) {
    $safeFrom = $conn->real_escape_string($fromDate);
    $whereClauses[] = "pp.fecha_pago >= '{$safeFrom}'";
}

if ($toDate !== '' && DateTime::createFromFormat('Y-m-d', $toDate) !== false) {
    $safeTo = $conn->real_escape_string($toDate);
    $whereClauses[] = "pp.fecha_pago <= '{$safeTo}'";
}

if ($searchTerm !== '') {
    $safeSearch = $conn->real_escape_string($searchTerm);
    $whereClauses[] = "(pp.concepto LIKE '%{$safeSearch}%' OR pp.referencia LIKE '%{$safeSearch}%' OR pp.notas LIKE '%{$safeSearch}%' OR pp.cliente LIKE '%{$safeSearch}%' OR pr.titulo LIKE '%{$safeSearch}%' OR pr.cliente LIKE '%{$safeSearch}%')";
}

if ($onlyCuotas) {
    $whereClauses[] = '(pp.cuotas_totales > 1 OR (pp.cuotas_pendientes IS NOT NULL AND pp.cuotas_pendientes > 0))';
}

$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$safeDateDiff = function (?string $dateString) {
    if (empty($dateString)) {
        return null;
    }
    try {
        $today = new DateTime('today');
        $target = new DateTime($dateString);
        return (int) $today->diff($target)->format('%r%a');
    } catch (Exception $e) {
        return null;
    }
};

$exporting = isset($_GET['export']) && $_GET['export'] === 'csv';
if ($exporting) {
    $exportSql = "SELECT pp.id, pp.concepto, pp.monto, pp.moneda, pp.estado, pp.metodo, pp.forma_pago, pp.cuotas_totales, pp.cuotas_pendientes, pp.proxima_cuota, pp.referencia, pp.fecha_pago, pr.titulo AS proyecto, COALESCE(pp.cliente, pr.cliente) AS cliente FROM proyecto_pagos pp LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id {$whereSql} ORDER BY pp.fecha_pago DESC, pp.id DESC";
    $exportRows = [];
    $exportResult = $conn->query($exportSql);
    if ($exportResult instanceof mysqli_result) {
        while ($row = $exportResult->fetch_assoc()) {
            $risk = '';
            $diffDays = $safeDateDiff($row['proxima_cuota'] ?? null);
            if ($diffDays !== null) {
                if ($diffDays < 0) {
                    $risk = 'vencida';
                } elseif ($diffDays <= 7) {
                    $risk = 'proxima_7d';
                } else {
                    $risk = 'ok';
                }
            }
            $exportRows[] = [
                $row['id'],
                $row['concepto'],
                $row['proyecto'] ?? '',
                $row['cliente'] ?? '',
                $row['monto'],
                $row['moneda'],
                $row['estado'],
                $row['metodo'],
                $row['forma_pago'],
                $row['cuotas_totales'],
                $row['cuotas_pendientes'],
                $row['proxima_cuota'],
                $row['referencia'],
                $row['fecha_pago'],
                $risk,
            ];
        }
        $exportResult->free();
    }

    admin_send_csv('pagos.csv', ['ID', 'Concepto', 'Proyecto', 'Cliente', 'Monto', 'Moneda', 'Estado', 'Metodo', 'Forma', 'Cuotas totales', 'Cuotas pendientes', 'Proxima cuota', 'Referencia', 'Fecha', 'Riesgo'], $exportRows);
}

$totalItems = 0;
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM proyecto_pagos pp {$whereSql}");
if ($totalResult instanceof mysqli_result) {
    $totalItems = (int) ($totalResult->fetch_assoc()['total'] ?? 0);
    $totalResult->free();
}

$totalesMoneda = [];
$totalsByCurrency = $conn->query("SELECT pp.moneda, COUNT(*) AS total_items, COALESCE(SUM(pp.monto), 0) AS monto_total FROM proyecto_pagos pp {$whereSql} GROUP BY pp.moneda");
if ($totalsByCurrency instanceof mysqli_result) {
    while ($row = $totalsByCurrency->fetch_assoc()) {
        $currencyKey = strtoupper(trim((string) ($row['moneda'] ?? 'COP')));
        $totalesMoneda[$currencyKey] = [
            'items' => (int) ($row['total_items'] ?? 0),
            'monto' => (float) ($row['monto_total'] ?? 0),
        ];
    }
    $totalsByCurrency->free();
}

// Aseguramos que COP y USD siempre aparezcan aunque no tengan movimientos
foreach (['COP', 'USD'] as $defaultCurrency) {
    if (!isset($totalesMoneda[$defaultCurrency])) {
        $totalesMoneda[$defaultCurrency] = [
            'items' => 0,
            'monto' => 0.0,
        ];
    }
}

// Totales por forma de pago y progreso global de cuotas
$totalesForma = [
    'contado' => ['items' => 0, 'monto' => 0.0],
    'cuotas' => ['items' => 0, 'monto' => 0.0, 'pagado' => 0.0, 'total' => 0.0],
];
$progresoCuotasProyectos = [];
$detallesForma = [
    'contado' => [],
    'cuotas' => [],
];
$detallesMoneda = [];
$maxCardItems = 6;

foreach (['contado', 'cuotas'] as $forma) {
    $whereForma = $whereClauses;
    $whereForma[] = "pp.forma_pago = '{$conn->real_escape_string($forma)}'";
    $whereSqlForma = 'WHERE ' . implode(' AND ', $whereForma);
    $resForma = $conn->query("SELECT COUNT(*) AS total_items, COALESCE(SUM(pp.monto), 0) AS monto_total FROM proyecto_pagos pp {$whereSqlForma}");
    if ($resForma instanceof mysqli_result) {
        if ($row = $resForma->fetch_assoc()) {
            $totalesForma[$forma]['items'] = (int) ($row['total_items'] ?? 0);
            $totalesForma[$forma]['monto'] = (float) ($row['monto_total'] ?? 0);
        }
        $resForma->free();
    }

    // Detalle reciente por forma de pago
    $detalleSql = "SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente 
                   FROM proyecto_pagos pp 
                   LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id 
                   {$whereSqlForma}
                   ORDER BY pp.fecha_pago DESC, pp.id DESC
                   LIMIT {$maxCardItems}";
    $detalleRes = $conn->query($detalleSql);
    if ($detalleRes instanceof mysqli_result) {
        while ($row = $detalleRes->fetch_assoc()) {
            $detallesForma[$forma][] = $row;
        }
        $detalleRes->free();
    }
}

// Progreso de pagos en cuotas (global y por proyecto)
$whereCuotas = $whereClauses;
$whereCuotas[] = "pp.forma_pago = 'cuotas'";
$whereSqlCuotas = 'WHERE ' . implode(' AND ', $whereCuotas);
$cuotasResult = $conn->query("SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente FROM proyecto_pagos pp LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id {$whereSqlCuotas}");
if ($cuotasResult instanceof mysqli_result) {
    while ($row = $cuotasResult->fetch_assoc()) {
        $totalCuotas = (int) ($row['cuotas_totales'] ?? 0);
        $pendientes = $row['cuotas_pendientes'] !== null ? (int) $row['cuotas_pendientes'] : $totalCuotas;
        $pendientes = max(0, $pendientes);
        $pagadas = $totalCuotas > 0 ? max(0, min($totalCuotas, $totalCuotas - $pendientes)) : 0;

        $monto = (float) ($row['monto'] ?? 0);
        $pagadoMonto = $totalCuotas > 0 ? $monto * ($pagadas / $totalCuotas) : $monto;

        $totalesForma['cuotas']['pagado'] += $pagadoMonto;
        $totalesForma['cuotas']['total'] += $monto;

        $proyectoId = (int) ($row['proyecto_id'] ?? 0);
        $projectKey = $proyectoId > 0 ? 'pr_' . $proyectoId : 'sin_proyecto';
        if (!isset($progresoCuotasProyectos[$projectKey])) {
            $progresoCuotasProyectos[$projectKey] = [
                'id' => $proyectoId,
                'titulo' => $row['proyecto_titulo'] ?? ($row['cliente'] ?? 'Sin proyecto'),
                'cliente' => $row['proyecto_cliente'] ?? ($row['cliente'] ?? ''),
                'total' => 0.0,
                'pagado' => 0.0,
                'moneda' => strtoupper(trim((string) ($row['moneda'] ?? 'COP'))),
            ];
        }

        $progresoCuotasProyectos[$projectKey]['total'] += $monto;
        $progresoCuotasProyectos[$projectKey]['pagado'] += $pagadoMonto;
    }
    $cuotasResult->free();
}

// Detalles por moneda (usando mismos filtros)
foreach (array_keys($totalesMoneda) as $currencyKey) {
    $whereMoneda = $whereClauses;
    $whereMoneda[] = "pp.moneda = '" . $conn->real_escape_string($currencyKey) . "'";
    $whereSqlMoneda = 'WHERE ' . implode(' AND ', $whereMoneda);
    $monedaSql = "SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente 
                  FROM proyecto_pagos pp 
                  LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id 
                  {$whereSqlMoneda}
                  ORDER BY pp.fecha_pago DESC, pp.id DESC
                  LIMIT {$maxCardItems}";
    $monedaRes = $conn->query($monedaSql);
    $detallesMoneda[$currencyKey] = [];
    if ($monedaRes instanceof mysqli_result) {
        while ($row = $monedaRes->fetch_assoc()) {
            $detallesMoneda[$currencyKey][] = $row;
        }
        $monedaRes->free();
    }
}

// Ingresos totales con filtro por mes/año (sin alterar filtros generales)
$ingresosMonth = isset($_GET['mes_ingresos']) ? (int) $_GET['mes_ingresos'] : (int) date('m');
$ingresosYear = isset($_GET['anio_ingresos']) ? (int) $_GET['anio_ingresos'] : (int) date('Y');
$ingresosMonth = ($ingresosMonth >= 1 && $ingresosMonth <= 12) ? $ingresosMonth : (int) date('m');
$ingresosYear = ($ingresosYear >= 2000 && $ingresosYear <= 2100) ? $ingresosYear : (int) date('Y');

$filterParams['mes_ingresos'] = $ingresosMonth;
$filterParams['anio_ingresos'] = $ingresosYear;

$ingresosWhere = $whereClauses;
$ingresosWhere[] = 'MONTH(pp.fecha_pago) = ' . $ingresosMonth;
$ingresosWhere[] = 'YEAR(pp.fecha_pago) = ' . $ingresosYear;
$ingresosWhereSql = 'WHERE ' . implode(' AND ', $ingresosWhere);

$ingresosTotales = ['items' => 0, 'monto' => 0.0];
$ingresosMoneda = [];

$ingRes = $conn->query("SELECT COUNT(*) AS total_items, COALESCE(SUM(pp.monto), 0) AS monto_total FROM proyecto_pagos pp {$ingresosWhereSql}");
if ($ingRes instanceof mysqli_result) {
    if ($row = $ingRes->fetch_assoc()) {
        $ingresosTotales['items'] = (int) ($row['total_items'] ?? 0);
        $ingresosTotales['monto'] = (float) ($row['monto_total'] ?? 0);
    }
    $ingRes->free();
}

$ingMonRes = $conn->query("SELECT pp.moneda, COUNT(*) AS total_items, COALESCE(SUM(pp.monto), 0) AS monto_total FROM proyecto_pagos pp {$ingresosWhereSql} GROUP BY pp.moneda");
if ($ingMonRes instanceof mysqli_result) {
    while ($row = $ingMonRes->fetch_assoc()) {
        $monKey = strtoupper(trim((string) ($row['moneda'] ?? 'COP')));
        $ingresosMoneda[$monKey] = [
            'items' => (int) ($row['total_items'] ?? 0),
            'monto' => (float) ($row['monto_total'] ?? 0),
        ];
    }
    $ingMonRes->free();
}

$ingresosDetalle = [];
$ingDetRes = $conn->query("SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente 
                           FROM proyecto_pagos pp 
                           LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id 
                           {$ingresosWhereSql}
                           ORDER BY pp.fecha_pago DESC, pp.id DESC
                           LIMIT 10");
if ($ingDetRes instanceof mysqli_result) {
    while ($row = $ingDetRes->fetch_assoc()) {
        $ingresosDetalle[] = $row;
    }
    $ingDetRes->free();
}

$safeDateDiff = function (?string $dateString) {
    if (empty($dateString)) {
        return null;
    }
    try {
        $today = new DateTime('today');
        $target = new DateTime($dateString);
        return (int) $today->diff($target)->format('%r%a');
    } catch (Exception $e) {
        return null;
    }
};

$pagination = admin_paginate($totalItems, $perPage, $page);

// Consulta principal (se había perdido en el refactor)
$paymentsSql = "SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente 
                FROM proyecto_pagos pp 
                LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id 
                {$whereSql} 
                ORDER BY pp.fecha_pago DESC, pp.id DESC 
                LIMIT {$pagination['offset']}, {$pagination['per_page']}";
$payments = $conn->query($paymentsSql);
$projectsOptions = fetchProjectDropdownOptions($conn);

function payment_status_badge_class(string $status): string
{
    $map = [
        'recibido' => 'bg-emerald-100 text-emerald-700',
        'pendiente' => 'bg-amber-100 text-amber-700',
        'parcial' => 'bg-blue-100 text-blue-700',
        'reembolsado' => 'bg-red-100 text-red-700',
    ];

    return $map[$status] ?? 'bg-slate-100 text-slate-700';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos - Admin</title>
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
                <?php admin_render_toast($toast); ?>
                <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Pagos</h1>
                        <p class="mt-2 text-sm text-gray-600">Registra y controla los pagos recibidos por cada proyecto.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['export' => 'csv'])); ?>" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-file-export mr-2"></i>Exportar CSV
                        </a>
                        <a href="pago-editar.php" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Registrar pago
                        </a>
                    </div>
                </div>

                <div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div class="group rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md transition cursor-pointer" role="button" tabindex="0" data-toggle-target="#panel-ingresos">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-500">Ingresos totales (<?php echo str_pad($ingresosMonth, 2, '0', STR_PAD_LEFT) . '/' . $ingresosYear; ?>)</p>
                                <p class="mt-2 text-2xl font-bold text-slate-900"><?php echo payment_format_amount((float) $ingresosTotales['monto'], 'COP'); ?></p>
                                <p class="text-sm text-gray-500 mt-1"><?php echo (int) $ingresosTotales['items']; ?> pago<?php echo ((int) $ingresosTotales['items'] === 1) ? '' : 's'; ?></p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 group-hover:text-gray-600"></i>
                        </div>
                    </div>
                    <?php foreach ($totalesMoneda as $currency => $summary): ?>
                        <div class="group rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md transition cursor-pointer" role="button" tabindex="0" data-toggle-target="#panel-moneda-<?php echo admin_escape($currency); ?>">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500">Total en <?php echo admin_escape($currency); ?></p>
                                    <p class="mt-2 text-2xl font-bold text-slate-900"><?php echo payment_format_amount((float) $summary['monto'], $currency); ?></p>
                                    <p class="text-sm text-gray-500 mt-1"><?php echo (int) $summary['items']; ?> pago<?php echo ((int) $summary['items'] === 1) ? '' : 's'; ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-gray-600"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="panel-ingresos" class="mb-6 hidden rounded-2xl bg-white p-5 shadow">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Detalle de ingresos</h3>
                            <p class="text-sm text-gray-500">Mes y año seleccionados.</p>
                        </div>
                        <form method="GET" class="flex flex-wrap items-end gap-2 text-sm">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Mes</label>
                                <select name="mes_ingresos" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo $m; ?>" <?php echo $ingresosMonth === $m ? 'selected' : ''; ?>><?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Año</label>
                                <select name="anio_ingresos" class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-600 focus:outline-none">
                                    <?php for ($y = (int) date('Y') + 1; $y >= (int) date('Y') - 4; $y--): ?>
                                        <option value="<?php echo $y; ?>" <?php echo $ingresosYear === $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <?php foreach ($filterParams as $key => $value): ?>
                                <?php if ($value !== null && !in_array($key, ['mes_ingresos', 'anio_ingresos'], true)): ?>
                                    <input type="hidden" name="<?php echo admin_escape($key); ?>" value="<?php echo admin_escape($value); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-white font-semibold hover:bg-slate-800">Aplicar</button>
                        </form>
                    </div>
                    <?php if (count($ingresosMoneda) > 0): ?>
                        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <?php foreach ($ingresosMoneda as $cur => $info): ?>
                                <div class="rounded-xl border border-gray-100 p-4">
                                    <p class="text-xs font-semibold text-gray-500">Moneda <?php echo admin_escape($cur); ?></p>
                                    <p class="mt-1 text-lg font-bold text-slate-900"><?php echo payment_format_amount((float) $info['monto'], $cur); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo (int) $info['items']; ?> pago<?php echo ((int) $info['items'] === 1) ? '' : 's'; ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-4 divide-y divide-gray-100">
                        <?php if (count($ingresosDetalle) === 0): ?>
                            <p class="py-2 text-sm text-gray-500">No hay ingresos en el periodo seleccionado.</p>
                        <?php else: ?>
                            <?php foreach ($ingresosDetalle as $ing): ?>
                                <div class="py-2 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900"><?php echo admin_escape($ing['concepto']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo !empty($ing['proyecto_titulo']) ? admin_escape($ing['proyecto_titulo']) : 'Sin proyecto'; ?>
                                            • <?php echo date('d/m/Y', strtotime($ing['fecha_pago'])); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-slate-900"><?php echo payment_format_amount((float) $ing['monto'], $ing['moneda']); ?></p>
                                        <a href="pago-editar.php?id=<?php echo (int) $ing['id']; ?>" class="text-xs text-blue-700 hover:underline">Ver</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php foreach ($totalesMoneda as $currency => $summary): ?>
                    <div id="panel-moneda-<?php echo admin_escape($currency); ?>" class="mb-6 hidden rounded-2xl bg-white p-5 shadow">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div>
                                <p class="text-sm font-semibold text-gray-500">Pagos en <?php echo admin_escape($currency); ?></p>
                                <h3 class="text-lg font-bold text-slate-900"><?php echo payment_format_amount((float) $summary['monto'], $currency); ?></h3>
                            </div>
                            <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['moneda' => $currency, 'page' => 1])); ?>" class="text-sm text-blue-700 hover:underline">Ver todo filtrado</a>
                        </div>
                        <div class="mt-3 divide-y divide-gray-100">
                            <?php if (count($detallesMoneda[$currency] ?? []) === 0): ?>
                                <p class="py-2 text-sm text-gray-500">No hay pagos en esta moneda con los filtros actuales.</p>
                            <?php else: ?>
                                <?php foreach ($detallesMoneda[$currency] as $item): ?>
                                    <div class="py-2 flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900"><?php echo admin_escape($item['concepto']); ?></p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo !empty($item['proyecto_titulo']) ? admin_escape($item['proyecto_titulo']) : 'Sin proyecto'; ?>
                                                • <?php echo date('d/m/Y', strtotime($item['fecha_pago'])); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-slate-900"><?php echo payment_format_amount((float) $item['monto'], $item['moneda']); ?></p>
                                            <a href="pago-editar.php?id=<?php echo (int) $item['id']; ?>" class="text-xs text-blue-700 hover:underline">Ver</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mb-6 grid gap-4 md:grid-cols-2">
                    <div class="group rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md transition cursor-pointer" role="button" tabindex="0" data-toggle-target="#panel-contado">
                        <p class="text-sm font-semibold text-gray-500">Total a contado</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900"><?php echo payment_format_amount((float) $totalesForma['contado']['monto'], 'COP'); ?></p>
                        <p class="text-sm text-gray-500 mt-1"><?php echo (int) $totalesForma['contado']['items']; ?> pago<?php echo ((int) $totalesForma['contado']['items'] === 1) ? '' : 's'; ?></p>
                        <i class="fas fa-chevron-down mt-2 text-gray-400 group-hover:text-gray-600"></i>
                    </div>
                    <div class="group rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md transition cursor-pointer" role="button" tabindex="0" data-toggle-target="#panel-cuotas">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-500">Total en cuotas</p>
                                <p class="mt-2 text-2xl font-bold text-slate-900"><?php echo payment_format_amount((float) $totalesForma['cuotas']['total'], 'COP'); ?></p>
                                <p class="text-sm text-gray-500 mt-1"><?php echo (int) $totalesForma['cuotas']['items']; ?> plan<?php echo ((int) $totalesForma['cuotas']['items'] === 1) ? '' : 'es'; ?> en cuotas</p>
                            </div>
                            <?php
                                $progresoGlobal = ($totalesForma['cuotas']['total'] > 0)
                                    ? round(($totalesForma['cuotas']['pagado'] / $totalesForma['cuotas']['total']) * 100)
                                    : 0;
                                $progresoGlobal = max(0, min(100, $progresoGlobal));
                                $barColorGlobal = $progresoGlobal >= 90 ? 'bg-emerald-500' : ($progresoGlobal >= 50 ? 'bg-amber-500' : 'bg-rose-500');
                            ?>
                        </div>
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Progreso global</span>
                                <span><?php echo payment_format_amount((float) $totalesForma['cuotas']['pagado'], 'COP'); ?> de <?php echo payment_format_amount((float) $totalesForma['cuotas']['total'], 'COP'); ?></span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-100">
                                <div class="h-2 rounded-full <?php echo $barColorGlobal; ?>" style="width: <?php echo $progresoGlobal; ?>%;"></div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down mt-2 text-gray-400 group-hover:text-gray-600"></i>
                    </div>
                </div>

                <div id="panel-contado" class="mb-6 hidden rounded-2xl bg-white p-5 shadow">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <h3 class="text-lg font-bold text-slate-900">Pagos a contado</h3>
                        <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['forma_pago' => 'contado', 'page' => 1])); ?>" class="text-sm text-blue-700 hover:underline">Ver todo filtrado</a>
                    </div>
                    <div class="mt-3 divide-y divide-gray-100">
                        <?php if (count($detallesForma['contado']) === 0): ?>
                            <p class="py-2 text-sm text-gray-500">No hay pagos a contado con los filtros actuales.</p>
                        <?php else: ?>
                            <?php foreach ($detallesForma['contado'] as $item): ?>
                                <div class="py-2 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900"><?php echo admin_escape($item['concepto']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo !empty($item['proyecto_titulo']) ? admin_escape($item['proyecto_titulo']) : 'Sin proyecto'; ?>
                                            • <?php echo date('d/m/Y', strtotime($item['fecha_pago'])); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-slate-900"><?php echo payment_format_amount((float) $item['monto'], $item['moneda']); ?></p>
                                        <a href="pago-editar.php?id=<?php echo (int) $item['id']; ?>" class="text-xs text-blue-700 hover:underline">Ver</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    </div>

                    <div id="panel-cuotas" class="mb-6 hidden rounded-2xl bg-white p-5 shadow">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h3 class="text-lg font-bold text-slate-900">Pagos en cuotas</h3>
                        <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['forma_pago' => 'cuotas', 'page' => 1])); ?>" class="text-sm text-blue-700 hover:underline">Ver todo filtrado</a>
                    </div>
                    <div class="mt-3 divide-y divide-gray-100">
                        <?php if (count($detallesForma['cuotas']) === 0): ?>
                            <p class="py-2 text-sm text-gray-500">No hay pagos en cuotas con los filtros actuales.</p>
                        <?php else: ?>
                            <?php foreach ($detallesForma['cuotas'] as $item): ?>
                                <?php
                                    $totalC = (int) ($item['cuotas_totales'] ?? 0);
                                    $pendC = ($item['cuotas_pendientes'] !== null) ? (int) $item['cuotas_pendientes'] : $totalC;
                                    $pendC = max(0, $pendC);
                                    $pagadas = max(0, $totalC - $pendC);
                                    $percent = ($totalC > 0) ? round(($pagadas / $totalC) * 100) : 0;
                                    $barColor = 'bg-slate-300';
                                    if ($percent >= 90) { $barColor = 'bg-emerald-500'; }
                                    elseif ($percent >= 50) { $barColor = 'bg-amber-500'; }
                                    elseif ($percent > 0) { $barColor = 'bg-rose-500'; }
                                ?>
                                <div class="py-3">
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900"><?php echo admin_escape($item['concepto']); ?></p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo !empty($item['proyecto_titulo']) ? admin_escape($item['proyecto_titulo']) : 'Sin proyecto'; ?>
                                                • <?php echo date('d/m/Y', strtotime($item['fecha_pago'])); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-slate-900"><?php echo payment_format_amount((float) $item['monto'], $item['moneda']); ?></p>
                                            <a href="pago-editar.php?id=<?php echo (int) $item['id']; ?>#cuotas_wrapper" class="text-xs text-blue-700 hover:underline">Registrar cuota</a>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-xs text-gray-600">
                                        <span><?php echo "{$pagadas}/{$totalC} pagadas"; ?></span>
                                        <span><?php echo $percent; ?>%</span>
                                    </div>
                                    <div class="mt-1 h-2 w-full rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full <?php echo $barColor; ?>" style="width: <?php echo min(100, max(0, $percent)); ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (count($progresoCuotasProyectos) > 0): ?>
                        <div class="mt-6 rounded-xl border border-gray-100 p-4 bg-slate-50">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Seguimiento de cuotas por proyecto</p>
                                    <h4 class="text-lg font-bold text-slate-900">Progreso individual</h4>
                                </div>
                            </div>
                            <div class="mt-3 space-y-3">
                                <?php foreach ($progresoCuotasProyectos as $proyecto): ?>
                                    <?php
                                        $pct = ($proyecto['total'] > 0) ? round(($proyecto['pagado'] / $proyecto['total']) * 100) : 0;
                                        $pct = max(0, min(100, $pct));
                                        $color = $pct >= 90 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-rose-500');
                                    ?>
                                    <div class="rounded-lg border border-gray-200 p-3 bg-white">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500"><?php echo $proyecto['id'] > 0 ? 'Proyecto' : 'Sin proyecto'; ?></p>
                                                <p class="text-sm font-bold text-slate-900">
                                                    <?php echo admin_escape($proyecto['titulo']); ?>
                                                </p>
                                                <?php if (!empty($proyecto['cliente'])): ?>
                                                    <p class="text-[11px] text-gray-500">Cliente: <?php echo admin_escape($proyecto['cliente']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-xs text-gray-700 text-right">
                                                <p><span class="font-semibold text-slate-900"><?php echo payment_format_amount((float) $proyecto['pagado'], $proyecto['moneda']); ?></span> pagado</p>
                                                <p class="text-[11px] text-gray-500"><?php echo payment_format_amount((float) $proyecto['total'], $proyecto['moneda']); ?> total</p>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="flex items-center justify-between text-[11px] text-gray-600">
                                                <span>Progreso</span>
                                                <span><?php echo $pct; ?>%</span>
                                            </div>
                                            <div class="mt-1 h-2 w-full rounded-full bg-slate-100">
                                                <div class="h-2 rounded-full <?php echo $color; ?>" style="width: <?php echo $pct; ?>%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-white p-5 shadow">
                    <form id="filter-form" method="GET" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                            <div class="relative">
                                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input
                                    type="text"
                                    name="q"
                                    value="<?php echo admin_escape($searchTerm); ?>"
                                    placeholder="Concepto, cliente, referencia..."
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-10 pr-4 focus:border-blue-600 focus:bg-white focus:outline-none"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Proyecto</label>
                            <select name="proyecto_id" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                <option value="0">Todos</option>
                                <?php foreach ($projectsOptions as $project): ?>
                                    <option value="<?php echo (int) $project['id']; ?>" <?php echo $projectId === (int) $project['id'] ? 'selected' : ''; ?>>
                                        <?php echo admin_escape($project['titulo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                            <select name="estado" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                <option value="">Todos</option>
                                <?php foreach ($statusOptions as $key => $label): ?>
                                    <option value="<?php echo admin_escape($key); ?>" <?php echo $statusFilter === $key ? 'selected' : ''; ?>><?php echo admin_escape($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metodo</label>
                            <select name="metodo" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                <option value="">Todos</option>
                                <?php foreach ($methodOptions as $key => $label): ?>
                                    <option value="<?php echo admin_escape($key); ?>" <?php echo $methodFilter === $key ? 'selected' : ''; ?>><?php echo admin_escape($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Forma de pago</label>
                            <select name="forma_pago" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                <option value="">Todas</option>
                                <?php foreach ($formaPagoOptions as $key => $label): ?>
                                    <option value="<?php echo admin_escape($key); ?>" <?php echo $formaPagoFilter === $key ? 'selected' : ''; ?>><?php echo admin_escape($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Moneda</label>
                            <select name="moneda" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                                <option value="">Todas</option>
                                <?php foreach ($currencyOptions as $key => $label): ?>
                                    <option value="<?php echo admin_escape($key); ?>" <?php echo $currencyFilter === $key ? 'selected' : ''; ?>><?php echo admin_escape($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Desde</label>
                            <input type="date" name="desde" value="<?php echo admin_escape($fromDate); ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Hasta</label>
                            <input type="date" name="hasta" value="<?php echo admin_escape($toDate); ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 focus:border-blue-600 focus:bg-white focus:outline-none">
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">Filtrar</button>
                            <a href="pagos.php" class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Limpiar</a>
                        </div>
                    </form>
                    <div class="flex items-center gap-3 text-sm text-gray-500">
                        <span><?php echo $totalItems; ?> pago<?php echo $totalItems === 1 ? '' : 's'; ?> encontrado<?php echo $totalItems === 1 ? '' : 's'; ?></span>
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="solo_cuotas" value="1" form="filter-form" <?php echo $onlyCuotas ? 'checked' : ''; ?> class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Solo cuotas activas
                        </label>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[960px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Concepto</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Proyecto</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Monto</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Forma</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Método</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cuotas</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Próxima cuota</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($payments instanceof mysqli_result && $payments->num_rows > 0): ?>
                                    <?php while ($pago = $payments->fetch_assoc()): ?>
                                        <?php
                                            $estadoKey = trim((string) ($pago['estado'] ?? ''));
                                            $estadoLabel = $statusOptions[$estadoKey] ?? ucfirst($estadoKey);
                                            $estadoClass = payment_status_badge_class($estadoKey);
                                            $metodoLabel = $methodOptions[$pago['metodo'] ?? ''] ?? ($pago['metodo'] ?? '-');
                                            $formaKey = trim((string) ($pago['forma_pago'] ?? 'contado'));
                                            $formaLabel = $formaPagoOptions[$formaKey] ?? ucfirst($formaKey);
                                            $proximaCuota = !empty($pago['proxima_cuota']) ? date('d/m/Y', strtotime($pago['proxima_cuota'])) : '-';
                                        ?>
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-6 py-4 font-medium text-slate-900">
                                                <div class="flex flex-col gap-1">
                                                    <a href="pago-editar.php?id=<?php echo (int) $pago['id']; ?>" class="text-slate-900 hover:text-blue-600 hover:underline">
                                                        <?php echo admin_escape($pago['concepto']); ?>
                                                    </a>
                                                    <?php if (!empty($pago['referencia'])): ?>
                                                        <p class="text-xs text-gray-500">Ref: <?php echo admin_escape($pago['referencia']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <?php if (!empty($pago['proyecto_id'])): ?>
                                                    <a href="proyecto-editar.php?id=<?php echo (int) $pago['proyecto_id']; ?>" class="text-blue-700 hover:underline">
                                                        <?php echo admin_escape($pago['proyecto_titulo'] ?? 'Proyecto'); ?>
                                                    </a>
                                                    <?php if (!empty($pago['proyecto_cliente'])): ?>
                                                        <p class="text-xs text-gray-500">Cliente: <?php echo admin_escape($pago['proyecto_cliente']); ?></p>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-500">Sin proyecto</span>
                                                    <?php if (!empty($pago['cliente'])): ?>
                                                        <p class="text-xs text-gray-500">Cliente: <?php echo admin_escape($pago['cliente']); ?></p>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                                <?php echo payment_format_amount((float) $pago['monto'], (string) $pago['moneda']); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $estadoClass; ?>"><?php echo admin_escape($estadoLabel); ?></span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                    <?php echo admin_escape($formaLabel); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo admin_escape($metodoLabel ?: '-'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                <?php
                                                    $totalC = (int) ($pago['cuotas_totales'] ?? 0);
                                                    $pendC = ($pago['cuotas_pendientes'] !== null) ? (int) $pago['cuotas_pendientes'] : $totalC;
                                                    $pendC = max(0, $pendC);
                                                    $pagadas = max(0, $totalC - $pendC);
                                                    $percent = ($totalC > 0) ? round(($pagadas / $totalC) * 100) : 0;
                                                    $barColor = 'bg-slate-300';
                                                    if ($percent >= 90) { $barColor = 'bg-emerald-500'; }
                                                    elseif ($percent >= 50) { $barColor = 'bg-amber-500'; }
                                                    elseif ($percent > 0) { $barColor = 'bg-rose-500'; }
                                                ?>
                                                <?php if ($totalC > 0): ?>
                                                    <div class="space-y-1">
                                                        <div class="text-xs text-gray-600"><?php echo admin_escape("{$pagadas} de {$totalC} pagadas"); ?> (<?php echo $percent; ?>%)</div>
                                                        <div class="h-2 w-32 rounded-full bg-slate-100">
                                                            <div class="h-2 rounded-full <?php echo $barColor; ?>" style="width: <?php echo min(100, max(0, $percent)); ?>%;"></div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                <?php
                                                    $proximaCuotaRaw = $pago['proxima_cuota'] ?? '';
                                                    $proximaCuota = !empty($proximaCuotaRaw) ? date('d/m/Y', strtotime($proximaCuotaRaw)) : '-';
                                                    $badge = '';
                                                    $diff = $safeDateDiff($proximaCuotaRaw);
                                                    if ($diff !== null) {
                                                        if ($diff < 0) {
                                                            $badge = '<span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">Vencida</span>';
                                                        } elseif ($diff <= 7) {
                                                            $badge = '<span class="ml-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700">Próx 7 días</span>';
                                                        }
                                                    }
                                                    echo admin_escape($proximaCuota);
                                                    echo $badge;
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($pago['fecha_pago'])); ?></td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a href="pago-editar.php?id=<?php echo (int) $pago['id']; ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                                        <i class="fas fa-edit"></i>
                                                        <span>Editar</span>
                                                    </a>
                                                    <a href="pago-factura.php?id=<?php echo (int) $pago['id']; ?>&modo=pdf" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100" title="Descargar PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                        <span>PDF</span>
                                                    </a>
                                                    <a href="pago-factura.php?id=<?php echo (int) $pago['id']; ?>&modo=html" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100" title="Ver / Imprimir">
                                                        <i class="fas fa-print"></i>
                                                        <span>Imprimir</span>
                                                    </a>
                                                    <a href="pago-factura.php?id=<?php echo (int) $pago['id']; ?>&modo=sendform" class="inline-flex items-center gap-2 rounded-lg bg-purple-50 px-3 py-2 text-sm font-medium text-purple-700 hover:bg-purple-100" title="Enviar por correo">
                                                        <i class="fas fa-paper-plane"></i>
                                                        <span>Enviar</span>
                                                    </a>
                                                    <?php if ((int) ($pago['cuotas_totales'] ?? 0) > 1): ?>
                                                        <a href="pago-editar.php?id=<?php echo (int) $pago['id']; ?>#cuotas_wrapper" class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100" title="Registrar cuota / actualizar cuotas">
                                                            <i class="fas fa-plus-circle"></i>
                                                            <span>Registrar cuota</span>
                                                        </a>
                                                    <?php endif; ?>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Eliminar este pago?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo (int) $pago['id']; ?>">
                                                        <input type="hidden" name="q" value="<?php echo admin_escape($searchTerm); ?>">
                                                        <input type="hidden" name="proyecto_id" value="<?php echo (int) $projectId; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo admin_escape($statusFilter); ?>">
                                                        <input type="hidden" name="metodo" value="<?php echo admin_escape($methodFilter); ?>">
                                                        <input type="hidden" name="forma_pago" value="<?php echo admin_escape($formaPagoFilter); ?>">
                                                        <input type="hidden" name="moneda" value="<?php echo admin_escape($currencyFilter); ?>">
                                                        <input type="hidden" name="desde" value="<?php echo admin_escape($fromDate); ?>">
                                                        <input type="hidden" name="hasta" value="<?php echo admin_escape($toDate); ?>">
                                                        <input type="hidden" name="solo_cuotas" value="<?php echo $onlyCuotas ? '1' : ''; ?>">
                                                        <input type="hidden" name="page" value="<?php echo (int) $pagination['page']; ?>">
                                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                                                            <i class="fas fa-trash"></i>
                                                            <span>Eliminar</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr class="border-t">
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <?php echo $searchTerm !== '' ? 'No encontramos pagos con ese criterio de busqueda.' : 'Todavia no hay pagos registrados.'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">
                            Pagina <?php echo $pagination['page']; ?> de <?php echo $pagination['total_pages']; ?>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <?php if ($pagination['has_prev']): ?>
                                <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['page' => $pagination['page'] - 1])); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                            <?php endif; ?>
                            <?php for ($pageNumber = 1; $pageNumber <= $pagination['total_pages']; $pageNumber++): ?>
                                <?php if (abs($pageNumber - $pagination['page']) > 2 && $pageNumber !== 1 && $pageNumber !== $pagination['total_pages']) continue; ?>
                                <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['page' => $pageNumber])); ?>" class="rounded-lg px-4 py-2 text-sm font-medium <?php echo $pageNumber === $pagination['page'] ? 'bg-blue-600 text-white' : 'border text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $pageNumber; ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($pagination['has_next']): ?>
                                <a href="<?php echo admin_build_url('pagos.php', array_merge($filterParams, ['page' => $pagination['page'] + 1])); ?>" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-toggle-target]').forEach(function (el) {
                const toggle = function (evt) {
                    if (evt.type === 'keypress' && !['Enter', ' ', 'Spacebar'].includes(evt.key)) {
                        return;
                    }
                    const target = el.getAttribute('data-toggle-target');
                    if (!target) return;
                    const panel = document.querySelector(target);
                    if (panel) {
                        panel.classList.toggle('hidden');
                    }
                };
                el.addEventListener('click', toggle);
                el.addEventListener('keypress', toggle);
            });
        });
    </script>
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>

