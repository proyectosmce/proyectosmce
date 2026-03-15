<?php
// admin/pagos.php
require_once '../includes/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

function valid_date_param($value)
{
    return is_string($value) && preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $value);
}

// Crea tablas financieras si no existen (evita errores 500 si faltan migraciones).
function ensureFinanceSchema(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS pagos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente VARCHAR(150) NOT NULL,
            proyecto VARCHAR(150),
            monto DECIMAL(12,2) NOT NULL,
            moneda VARCHAR(10) DEFAULT 'USD',
            metodo VARCHAR(50) DEFAULT 'transferencia',
            estado ENUM('pendiente','en_revision','confirmado','fallido','reembolsado') DEFAULT 'pendiente',
            fee_pasarela DECIMAL(12,2) DEFAULT 0,
            fecha_pago DATE NOT NULL,
            fecha_confirmacion DATETIME NULL,
            comprobante VARCHAR(255),
            notas TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS ventas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            proyecto VARCHAR(150) NOT NULL,
            cliente VARCHAR(150),
            precio DECIMAL(12,2) NOT NULL,
            descuento DECIMAL(12,2) DEFAULT 0,
            estado_entrega ENUM('pendiente','entregado','soporte') DEFAULT 'pendiente',
            fecha_venta DATE NOT NULL,
            pago_id INT NULL,
            notas TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_ventas_pago FOREIGN KEY (pago_id) REFERENCES pagos(id) ON DELETE SET NULL
        ) ENGINE=InnoDB;
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS gastos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            categoria VARCHAR(100) NOT NULL,
            descripcion VARCHAR(255),
            proveedor VARCHAR(150),
            proyecto VARCHAR(150),
            monto DECIMAL(12,2) NOT NULL,
            moneda VARCHAR(10) DEFAULT 'USD',
            impuesto DECIMAL(12,2) DEFAULT 0,
            fecha_gasto DATE NOT NULL,
            metodo VARCHAR(50) DEFAULT 'transferencia',
            comprobante VARCHAR(255),
            notas TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
}

$hoy   = date('Y-m-d');
$desde = (isset($_GET['desde']) && valid_date_param($_GET['desde'])) ? $_GET['desde'] : date('Y-m-01');
$hasta = (isset($_GET['hasta']) && valid_date_param($_GET['hasta'])) ? $_GET['hasta'] : $hoy;

// Asegura tablas antes de cualquier consulta.
ensureFinanceSchema($conn);

// Crear / actualizar registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'crear_pago') {
        $cliente   = sanitize($_POST['cliente'] ?? '');
        $proyecto  = sanitize($_POST['proyecto'] ?? '');
        $monto     = (float) ($_POST['monto'] ?? 0);
        $moneda    = sanitize($_POST['moneda'] ?? 'USD');
        $metodo    = sanitize($_POST['metodo'] ?? 'transferencia');
        $estado    = sanitize($_POST['estado'] ?? 'pendiente');
        $fee       = (float) ($_POST['fee_pasarela'] ?? 0);
        $fechaPago = valid_date_param($_POST['fecha_pago'] ?? '') ? $_POST['fecha_pago'] : $hoy;
        $comprobante = sanitize($_POST['comprobante'] ?? '');
        $notas       = sanitize($_POST['notas'] ?? '');
        $fechaConfirm = $estado === 'confirmado' ? date('Y-m-d H:i:s') : null;

        $stmt = $conn->prepare("INSERT INTO pagos (cliente, proyecto, monto, moneda, metodo, estado, fee_pasarela, fecha_pago, fecha_confirmacion, comprobante, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssdsssdssss",
            $cliente,
            $proyecto,
            $monto,
            $moneda,
            $metodo,
            $estado,
            $fee,
            $fechaPago,
            $fechaConfirm,
            $comprobante,
            $notas
        );
        $stmt->execute();
        $stmt->close();

        header("Location: pagos.php?msg=pago_creado&desde=$desde&hasta=$hasta");
        exit;
    }

    if ($action === 'crear_gasto') {
        $categoria   = sanitize($_POST['categoria'] ?? 'Operativo');
        $descripcion = sanitize($_POST['descripcion'] ?? '');
        $proveedor   = sanitize($_POST['proveedor'] ?? '');
        $proyecto    = sanitize($_POST['proyecto'] ?? '');
        $monto       = (float) ($_POST['monto'] ?? 0);
        $moneda      = sanitize($_POST['moneda'] ?? 'USD');
        $impuesto    = (float) ($_POST['impuesto'] ?? 0);
        $fechaGasto  = valid_date_param($_POST['fecha_gasto'] ?? '') ? $_POST['fecha_gasto'] : $hoy;
        $metodo      = sanitize($_POST['metodo'] ?? 'transferencia');
        $comprobante = sanitize($_POST['comprobante'] ?? '');
        $notas       = sanitize($_POST['notas'] ?? '');

        $stmt = $conn->prepare("INSERT INTO gastos (categoria, descripcion, proveedor, proyecto, monto, moneda, impuesto, fecha_gasto, metodo, comprobante, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssdsdssss",
            $categoria,
            $descripcion,
            $proveedor,
            $proyecto,
            $monto,
            $moneda,
            $impuesto,
            $fechaGasto,
            $metodo,
            $comprobante,
            $notas
        );
        $stmt->execute();
        $stmt->close();

        header("Location: pagos.php?msg=gasto_creado&desde=$desde&hasta=$hasta");
        exit;
    }

    if ($action === 'crear_venta') {
        $proyecto    = sanitize($_POST['proyecto'] ?? '');
        $cliente     = sanitize($_POST['cliente'] ?? '');
        $precio      = (float) ($_POST['precio'] ?? 0);
        $descuento   = (float) ($_POST['descuento'] ?? 0);
        $estadoEnt   = sanitize($_POST['estado_entrega'] ?? 'pendiente');
        $fechaVenta  = valid_date_param($_POST['fecha_venta'] ?? '') ? $_POST['fecha_venta'] : $hoy;
        $pagoId      = isset($_POST['pago_id']) && $_POST['pago_id'] !== '' ? (int) $_POST['pago_id'] : null;
        $notas       = sanitize($_POST['notas'] ?? '');

        $stmt = $conn->prepare("INSERT INTO ventas (proyecto, cliente, precio, descuento, estado_entrega, fecha_venta, pago_id, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssddssis",
            $proyecto,
            $cliente,
            $precio,
            $descuento,
            $estadoEnt,
            $fechaVenta,
            $pagoId,
            $notas
        );
        $stmt->execute();
        $stmt->close();

        header("Location: pagos.php?msg=venta_creada&desde=$desde&hasta=$hasta");
        exit;
    }
}

// Acciones sobre pagos (confirmar, reembolsar, eliminar)
if (isset($_GET['accion'], $_GET['pago_id'])) {
    $pagoId = (int) $_GET['pago_id'];
    $accion = $_GET['accion'];

    if ($accion === 'eliminar') {
        $stmt = $conn->prepare("DELETE FROM pagos WHERE id = ?");
        $stmt->bind_param("i", $pagoId);
        $stmt->execute();
        $stmt->close();
        header("Location: pagos.php?msg=pago_eliminado&desde=$desde&hasta=$hasta");
        exit;
    }

    $nuevoEstado = null;
    $fechaConfirm = null;

    if ($accion === 'confirmar') {
        $nuevoEstado = 'confirmado';
        $fechaConfirm = date('Y-m-d H:i:s');
    } elseif ($accion === 'reembolsar') {
        $nuevoEstado = 'reembolsado';
    } elseif ($accion === 'pendiente') {
        $nuevoEstado = 'pendiente';
    } elseif ($accion === 'en_revision') {
        $nuevoEstado = 'en_revision';
    }

    if ($nuevoEstado !== null) {
        $stmt = $conn->prepare("UPDATE pagos SET estado = ?, fecha_confirmacion = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nuevoEstado, $fechaConfirm, $pagoId);
        $stmt->execute();
        $stmt->close();
        header("Location: pagos.php?msg=pago_actualizado&desde=$desde&hasta=$hasta");
        exit;
    }
}

// Borrar gasto
if (isset($_GET['delete_gasto'])) {
    $id = (int) $_GET['delete_gasto'];
    $stmt = $conn->prepare("DELETE FROM gastos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: pagos.php?msg=gasto_eliminado&desde=$desde&hasta=$hasta");
    exit;
}

// Borrar venta
if (isset($_GET['delete_venta'])) {
    $id = (int) $_GET['delete_venta'];
    $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: pagos.php?msg=venta_eliminada&desde=$desde&hasta=$hasta");
    exit;
}

// Estadísticas
$statsPagos = $conn->prepare("
    SELECT 
        SUM(CASE WHEN estado = 'confirmado' THEN monto ELSE 0 END) AS total_confirmado,
        SUM(CASE WHEN estado = 'confirmado' THEN fee_pasarela ELSE 0 END) AS total_fees,
        SUM(CASE WHEN estado = 'pendiente' THEN monto ELSE 0 END) AS total_pendiente,
        SUM(CASE WHEN estado = 'reembolsado' THEN monto ELSE 0 END) AS total_reembolsado,
        COUNT(*) AS total_registros
    FROM pagos
    WHERE fecha_pago BETWEEN ? AND ?
");
$statsPagos->bind_param("ss", $desde, $hasta);
$statsPagos->execute();
$statsPagosRes = $statsPagos->get_result()->fetch_assoc();
$statsPagos->close();

$statsGastos = $conn->prepare("
    SELECT SUM(monto + impuesto) AS total_gastos
    FROM gastos
    WHERE fecha_gasto BETWEEN ? AND ?
");
$statsGastos->bind_param("ss", $desde, $hasta);
$statsGastos->execute();
$statsGastosRes = $statsGastos->get_result()->fetch_assoc();
$statsGastos->close();

$totalIngresosBrutos = (float) ($statsPagosRes['total_confirmado'] ?? 0);
$totalFees           = (float) ($statsPagosRes['total_fees'] ?? 0);
$totalIngresosNetos  = $totalIngresosBrutos - $totalFees;
$totalGastos         = (float) ($statsGastosRes['total_gastos'] ?? 0);
$gananciaNeta        = $totalIngresosNetos - $totalGastos;
$margen              = $totalIngresosBrutos > 0 ? round(($gananciaNeta / $totalIngresosBrutos) * 100, 1) : 0;

// Listados (limit para no saturar)
$pagos   = $conn->query("SELECT * FROM pagos ORDER BY fecha_pago DESC LIMIT 200");
$gastos  = $conn->query("SELECT * FROM gastos ORDER BY fecha_gasto DESC LIMIT 200");
$ventas  = $conn->query("SELECT v.*, p.estado AS estado_pago FROM ventas v LEFT JOIN pagos p ON p.id = v.pago_id ORDER BY fecha_venta DESC LIMIT 200");
$pagosConfirmados = $conn->query("SELECT id, cliente, proyecto, monto, moneda FROM pagos WHERE estado = 'confirmado' ORDER BY fecha_pago DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos y Finanzas - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>.logo-ring{position:absolute;inset:0;border:2px solid transparent;border-radius:8px;background:conic-gradient(from 0deg,#2563eb,#38bdf8,#2563eb);background-origin:border-box;animation:logo-spin 4s linear infinite;}@keyframes logo-spin{to{transform:rotate(360deg);}}</style>
</head>
<body class="bg-gray-100">
    <!-- Barra móvil -->
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
        <a href="logout.php" class="text-red-600 text-sm flex items-center gap-1"><i class="fas fa-sign-out-alt"></i>Salir</a>
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
                    <li><a href="dashboard.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                    <li><a href="proyectos.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-folder"></i><span>Proyectos</span></a></li>
                    <li><a href="servicios.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-cog"></i><span>Servicios</span></a></li>
                    <li><a href="pagos.php" class="flex items-center space-x-2 p-2 bg-blue-50 text-blue-600 rounded"><i class="fas fa-credit-card"></i><span>Pagos</span></a></li>
                    <li><a href="mensajes.php" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded"><i class="fas fa-envelope"></i><span>Mensajes</span></a></li>
                    <li><a href="logout.php" onclick="return confirm('¿Cerrar sesión?');" class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded text-red-600"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
                </ul>
            </nav>
        </div>
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>
        
        <!-- Contenido principal -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-8 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold">Pagos, Gastos y Ganancias</h1>
                        <p class="text-gray-500">Resumen de ingresos confirmados, costos y margen.</p>
                    </div>
                    <form class="flex flex-wrap gap-2 items-center" method="get">
                        <label class="text-sm text-gray-600">Desde</label>
                        <input type="date" name="desde" value="<?php echo $desde; ?>" class="border rounded px-3 py-2">
                        <label class="text-sm text-gray-600">Hasta</label>
                        <input type="date" name="hasta" value="<?php echo $hasta; ?>" class="border rounded px-3 py-2">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" type="submit">
                            <i class="fas fa-filter mr-2"></i>Filtrar
                        </button>
                    </form>
                </div>

                <?php if (isset($_GET['msg'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        <?php
                        $msgs = [
                            'pago_creado' => 'Pago registrado.',
                            'gasto_creado' => 'Gasto registrado.',
                            'venta_creada' => 'Proyecto vendido registrado.',
                            'pago_eliminado' => 'Pago eliminado.',
                            'gasto_eliminado' => 'Gasto eliminado.',
                            'venta_eliminada' => 'Venta eliminada.',
                            'pago_actualizado' => 'Estado de pago actualizado.'
                        ];
                        $key = $_GET['msg'];
                        echo $msgs[$key] ?? 'Acción realizada.';
                        ?>
                    </div>
                <?php endif; ?>

                <!-- KPIs -->
                <div class="grid md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <p class="text-gray-500 text-sm">Ingresos brutos confirmados</p>
                        <p class="text-2xl font-bold">$<?php echo number_format($totalIngresosBrutos, 2); ?></p>
                        <span class="text-xs text-gray-400">Periodo: <?php echo $desde; ?> a <?php echo $hasta; ?></span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <p class="text-gray-500 text-sm">Gastos totales</p>
                        <p class="text-2xl font-bold text-red-600">$<?php echo number_format($totalGastos, 2); ?></p>
                        <span class="text-xs text-gray-400">Incluye impuestos</span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <p class="text-gray-500 text-sm">Ganancia neta</p>
                        <p class="text-2xl font-bold text-green-600">$<?php echo number_format($gananciaNeta, 2); ?></p>
                        <span class="text-xs text-gray-400">Ingresos netos - gastos</span>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <p class="text-gray-500 text-sm">Margen</p>
                        <p class="text-2xl font-bold"><?php echo $margen; ?>%</p>
                        <div class="text-xs text-gray-400">Pendiente: $<?php echo number_format($statsPagosRes['total_pendiente'] ?? 0, 2); ?> | Reembolsado: $<?php echo number_format($statsPagosRes['total_reembolsado'] ?? 0, 2); ?></div>
                    </div>
                </div>

                <!-- Formularios -->
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow p-5">
                        <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-plus-circle text-blue-600"></i>Nuevo pago/cobro</h3>
                        <form method="post" class="space-y-3">
                            <input type="hidden" name="action" value="crear_pago">
                            <div>
                                <label class="text-sm text-gray-600">Cliente *</label>
                                <input type="text" name="cliente" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Proyecto</label>
                                <input type="text" name="proyecto" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Monto *</label>
                                    <input type="number" step="0.01" name="monto" required class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Moneda</label>
                                    <select name="moneda" class="w-full border rounded px-3 py-2">
                                        <option value="USD">USD</option>
                                        <option value="COP">COP</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Método</label>
                                    <select name="metodo" class="w-full border rounded px-3 py-2">
                                        <option value="transferencia">Transferencia</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Estado</label>
                                    <select name="estado" class="w-full border rounded px-3 py-2">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_revision">En revisión</option>
                                        <option value="confirmado">Confirmado</option>
                                        <option value="fallido">Fallido</option>
                                        <option value="reembolsado">Reembolsado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Fee pasarela</label>
                                    <input type="number" step="0.01" name="fee_pasarela" class="w-full border rounded px-3 py-2" value="0">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Fecha cobro</label>
                                    <input type="date" name="fecha_pago" value="<?php echo $hoy; ?>" class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Comprobante (URL)</label>
                                <input type="text" name="comprobante" class="w-full border rounded px-3 py-2" placeholder="https://...">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Notas</label>
                                <textarea name="notas" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                            </div>
                            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700" type="submit">Guardar pago</button>
                        </form>
                    </div>

                    <div class="bg-white rounded-lg shadow p-5">
                        <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-receipt text-amber-600"></i>Registrar gasto</h3>
                        <form method="post" class="space-y-3">
                            <input type="hidden" name="action" value="crear_gasto">
                            <div>
                                <label class="text-sm text-gray-600">Categoría</label>
                                <select name="categoria" class="w-full border rounded px-3 py-2">
                                    <option value="Operativo">Operativo</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Licencias">Licencias</option>
                                    <option value="Hosting">Hosting</option>
                                    <option value="Pago a terceros">Pago a terceros</option>
                                    <option value="Nómina">Nómina</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Descripción</label>
                                <input type="text" name="descripcion" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Proveedor</label>
                                    <input type="text" name="proveedor" class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Proyecto (opcional)</label>
                                    <input type="text" name="proyecto" class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Monto *</label>
                                    <input type="number" step="0.01" name="monto" required class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Moneda</label>
                                    <select name="moneda" class="w-full border rounded px-3 py-2">
                                        <option value="USD">USD</option>
                                        <option value="COP">COP</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Impuesto</label>
                                    <input type="number" step="0.01" name="impuesto" value="0" class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Fecha gasto</label>
                                    <input type="date" name="fecha_gasto" value="<?php echo $hoy; ?>" class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Método</label>
                                    <select name="metodo" class="w-full border rounded px-3 py-2">
                                        <option value="transferencia">Transferencia</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Comprobante (URL)</label>
                                    <input type="text" name="comprobante" class="w-full border rounded px-3 py-2" placeholder="https://...">
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Notas</label>
                                <textarea name="notas" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                            </div>
                            <button class="w-full bg-amber-600 text-white py-2 rounded hover:bg-amber-700" type="submit">Guardar gasto</button>
                        </form>
                    </div>

                    <div class="bg-white rounded-lg shadow p-5">
                        <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-box-open text-green-600"></i>Registrar proyecto vendido</h3>
                        <form method="post" class="space-y-3">
                            <input type="hidden" name="action" value="crear_venta">
                            <div>
                                <label class="text-sm text-gray-600">Proyecto *</label>
                                <input type="text" name="proyecto" required class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Cliente</label>
                                <input type="text" name="cliente" class="w-full border rounded px-3 py-2">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Precio *</label>
                                    <input type="number" step="0.01" name="precio" required class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Descuento</label>
                                    <input type="number" step="0.01" name="descuento" value="0" class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">Estado entrega</label>
                                    <select name="estado_entrega" class="w-full border rounded px-3 py-2">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="entregado">Entregado</option>
                                        <option value="soporte">En soporte</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Fecha venta</label>
                                    <input type="date" name="fecha_venta" value="<?php echo $hoy; ?>" class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Asociar pago (confirmado)</label>
                                <select name="pago_id" class="w-full border rounded px-3 py-2">
                                    <option value="">Sin asociar</option>
                                    <?php while ($pc = $pagosConfirmados->fetch_assoc()): ?>
                                        <option value="<?php echo $pc['id']; ?>">
                                            #<?php echo $pc['id']; ?> - <?php echo $pc['cliente']; ?> (<?php echo $pc['moneda']; ?> <?php echo number_format($pc['monto'], 2); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Notas</label>
                                <textarea name="notas" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                            </div>
                            <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700" type="submit">Guardar venta</button>
                        </form>
                    </div>
                </div>

                <!-- Listas -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-semibold flex items-center gap-2"><i class="fas fa-credit-card text-blue-600"></i>Pagos</h3>
                            <span class="text-xs text-gray-500">Últimos 200</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="px-3 py-2 text-left">Cliente</th>
                                        <th class="px-3 py-2 text-left">Proyecto</th>
                                        <th class="px-3 py-2 text-left">Monto</th>
                                        <th class="px-3 py-2 text-left">Estado</th>
                                        <th class="px-3 py-2 text-left">Fecha</th>
                                        <th class="px-3 py-2 text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($p = $pagos->fetch_assoc()): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-3 py-2"><?php echo $p['cliente']; ?></td>
                                            <td class="px-3 py-2"><?php echo $p['proyecto'] ?: '-'; ?></td>
                                            <td class="px-3 py-2 font-semibold"><?php echo $p['moneda']; ?> <?php echo number_format($p['monto'], 2); ?></td>
                                            <td class="px-3 py-2">
                                                <?php
                                                $estadoClass = [
                                                    'confirmado' => 'bg-green-100 text-green-800',
                                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                    'en_revision' => 'bg-blue-100 text-blue-800',
                                                    'reembolsado' => 'bg-red-100 text-red-800',
                                                    'fallido' => 'bg-gray-200 text-gray-800'
                                                ][$p['estado']] ?? 'bg-gray-100 text-gray-800';
                                                ?>
                                                <span class="<?php echo $estadoClass; ?> px-2 py-1 rounded text-xs capitalize"><?php echo str_replace('_', ' ', $p['estado']); ?></span>
                                            </td>
                                            <td class="px-3 py-2"><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td>
                                            <td class="px-3 py-2 space-x-2">
                                                <a class="text-green-600" href="?accion=confirmar&pago_id=<?php echo $p['id']; ?>&desde=<?php echo $desde; ?>&hasta=<?php echo $hasta; ?>" title="Confirmar"><i class="fas fa-check"></i></a>
                                                <a class="text-yellow-600" href="?accion=en_revision&pago_id=<?php echo $p['id']; ?>&desde=<?php echo $desde; ?>&hasta=<?php echo $hasta; ?>" title="En revisión"><i class="fas fa-eye"></i></a>
                                                <a class="text-red-600" href="?accion=reembolsar&pago_id=<?php echo $p['id']; ?>&desde=<?php echo $desde; ?>&hasta=<?php echo $hasta; ?>" title="Reembolsar"><i class="fas fa-undo"></i></a>
                                                <a class="text-gray-500" href="?accion=eliminar&pago_id=<?php echo $p['id']; ?>&desde=<?php echo $desde; ?>&hasta=<?php echo $hasta; ?>" onclick="return confirm('¿Eliminar este pago?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-semibold flex items-center gap-2"><i class="fas fa-file-invoice-dollar text-amber-600"></i>Gastos</h3>
                            <span class="text-xs text-gray-500">Últimos 200</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="px-3 py-2 text-left">Categoría</th>
                                        <th class="px-3 py-2 text-left">Descripción</th>
                                        <th class="px-3 py-2 text-left">Monto</th>
                                        <th class="px-3 py-2 text-left">Fecha</th>
                                        <th class="px-3 py-2 text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($g = $gastos->fetch_assoc()): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-3 py-2"><?php echo $g['categoria']; ?></td>
                                            <td class="px-3 py-2"><?php echo $g['descripcion'] ?: '-'; ?></td>
                                            <td class="px-3 py-2 font-semibold"><?php echo $g['moneda']; ?> <?php echo number_format($g['monto'] + $g['impuesto'], 2); ?></td>
                                            <td class="px-3 py-2"><?php echo date('d/m/Y', strtotime($g['fecha_gasto'])); ?></td>
                                            <td class="px-3 py-2 space-x-2">
                                                <?php if (!empty($g['comprobante'])): ?>
                                                    <a class="text-blue-600" href="<?php echo $g['comprobante']; ?>" target="_blank" title="Ver comprobante"><i class="fas fa-link"></i></a>
                                                <?php endif; ?>
                                                <a class="text-gray-500" href="?delete_gasto=<?php echo $g['id']; ?>&desde=<?php echo $desde; ?>&hasta=<?php echo $hasta; ?>" onclick="return confirm('¿Eliminar este gasto?')" title="Eliminar"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold flex items-center gap-2"><i class="fas fa-box text-green-600"></i>Inventario de proyectos vendidos</h3>
                        <span class="text-xs text-gray-500">Últimos 200</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="px-3 py-2 text-left">Proyecto</th>
                                    <th class="px-3 py-2 text-left">Cliente</th>
                                    <th class="px-3 py-2 text-left">Precio</th>
                                    <th class="px-3 py-2 text-left">Estado entrega</th>
                                    <th class="px-3 py-2 text-left">Pago</th>
                                    <th class="px-3 py-2 text-left">Fecha</th>
                                    <th class="px-3 py-2 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($v = $ventas->fetch_assoc()): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-3 py-2"><?php echo $v['proyecto']; ?></td>
                                        <td class="px-3 py-2"><?php echo $v['cliente'] ?: '-'; ?></td>
                                        <td class="px-3 py-2 font-semibold">$<?php echo number_format($v['precio'] - $v['descuento'], 2); ?></td>
                                        <td class="px-3 py-2">
                                            <?php
                                            $entregaClass = [
                                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                'entregado' => 'bg-green-100 text-green-800',
                                                'soporte'   => 'bg-blue-100 text-blue-800'
                                            ][$v['estado_entrega']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="<?php echo $entregaClass; ?> px-2 py-1 rounded text-xs capitalize"><?php echo $v['estado_entrega']; ?></span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <?php if (!empty($v['pago_id'])): ?>
                                                <span class="text-sm">#<?php echo $v['pago_id']; ?> (<?php echo $v['estado_pago'] ?: 'n/a'; ?>)</span>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-500">Sin asociar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-2"><?php echo date('d/m/Y', strtotime($v['fecha_venta'])); ?></td>
                                        <td class="px-3 py-2 space-x-2">
                                            <a class="text-gray-500" href="?delete_venta=<?php echo $v['id']; ?>&desde=<?php echo $desde; ?>&hasta=<?php echo $hasta; ?>" onclick="return confirm('¿Eliminar esta venta?')" title="Eliminar"><i class="fas fa-trash"></i></a>
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
</body>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.getElementById('toggleSidebar');
function closeSidebar(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
function openSidebar(){ sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
if (toggleBtn){ toggleBtn.addEventListener('click', ()=> sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar()); }
if (overlay){ overlay.addEventListener('click', closeSidebar); }
</script>
</html>









