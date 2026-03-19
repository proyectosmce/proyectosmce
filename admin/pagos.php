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

$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$exporting = isset($_GET['export']) && $_GET['export'] === 'csv';
if ($exporting) {
    $exportSql = "SELECT pp.id, pp.concepto, pp.monto, pp.moneda, pp.estado, pp.metodo, pp.forma_pago, pp.proxima_cuota, pp.referencia, pp.fecha_pago, pr.titulo AS proyecto, COALESCE(pp.cliente, pr.cliente) AS cliente FROM proyecto_pagos pp LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id {$whereSql} ORDER BY pp.fecha_pago DESC, pp.id DESC";
    $exportRows = [];
    $exportResult = $conn->query($exportSql);
    if ($exportResult instanceof mysqli_result) {
        while ($row = $exportResult->fetch_assoc()) {
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
                $row['proxima_cuota'],
                $row['referencia'],
                $row['fecha_pago'],
            ];
        }
        $exportResult->free();
    }

    admin_send_csv('pagos.csv', ['ID', 'Concepto', 'Proyecto', 'Cliente', 'Monto', 'Moneda', 'Estado', 'Metodo', 'Forma', 'Proxima cuota', 'Referencia', 'Fecha'], $exportRows);
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

$pagination = admin_paginate($totalItems, $perPage, $page);
$paymentsSql = "SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente FROM proyecto_pagos pp LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id {$whereSql} ORDER BY pp.fecha_pago DESC, pp.id DESC LIMIT {$pagination['offset']}, {$pagination['per_page']}";
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

                <?php if (count($totalesMoneda) > 0): ?>
                    <div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($totalesMoneda as $currency => $summary): ?>
                            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                                <p class="text-sm font-semibold text-gray-500">Total en <?php echo admin_escape($currency); ?></p>
                                <p class="mt-2 text-2xl font-bold text-slate-900"><?php echo payment_format_amount((float) $summary['monto'], $currency); ?></p>
                                <p class="text-sm text-gray-500 mt-1"><?php echo (int) $summary['items']; ?> pago<?php echo ((int) $summary['items'] === 1) ? '' : 's'; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-white p-5 shadow">
                    <form method="GET" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
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
                    <div class="text-sm text-gray-500">
                        <?php echo $totalItems; ?> pago<?php echo $totalItems === 1 ? '' : 's'; ?> encontrado<?php echo $totalItems === 1 ? '' : 's'; ?>
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
                                            <td class="px-6 py-4 text-sm text-gray-700"><?php echo admin_escape($proximaCuota); ?></td>
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
    <?php include __DIR__ . '/partials/sidebar-script.php'; ?>
</body>
</html>

