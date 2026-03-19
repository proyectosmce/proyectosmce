<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/payment-helpers.php';
require_once __DIR__ . '/../includes/admin-helpers.php';
require_once __DIR__ . '/../includes/PHPMailer/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

ensureProjectPaymentsSchema($conn);
$csrfToken = admin_get_csrf_token();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$modo = trim((string) ($_GET['modo'] ?? $_POST['modo'] ?? 'html'));
$flash = $_GET['msg'] ?? '';
$mailError = '';
$mailError = '';

function fetch_payment(mysqli $conn, int $id): ?array
{
    if ($id <= 0) return null;
    $sql = "SELECT pp.*, pr.titulo AS proyecto_titulo, pr.cliente AS proyecto_cliente, pr.descripcion AS proyecto_descripcion 
            FROM proyecto_pagos pp 
            LEFT JOIN proyectos pr ON pr.id = pp.proyecto_id 
            WHERE pp.id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = null;
    if ($res instanceof mysqli_result) {
        $row = $res->fetch_assoc();
        $res->free();
    }
    $stmt->close();
    return $row ?: null;
}

$payment = fetch_payment($conn, $id);
if (!$payment) {
    http_response_code(404);
    exit('Pago no encontrado.');
}

function invoice_number(array $payment): string
{
    return 'FAC-' . str_pad((string) $payment['id'], 6, '0', STR_PAD_LEFT);
}

function brand_primary(): array { return [15, 23, 42]; }    // slate-900
function brand_accent(): array { return [245, 158, 11]; }   // amber-500

function render_html(array $payment): void
{
    $monto = payment_format_amount((float) $payment['monto'], (string) $payment['moneda']);
    $invoice = invoice_number($payment);
    $fecha = date('d/m/Y', strtotime($payment['fecha_pago']));
    $project = $payment['proyecto_titulo'] ?: 'Proyecto sin título';
    $client = $payment['proyecto_cliente'] ?: 'Cliente sin nombre';
    $ref = $payment['referencia'] ?: '-';
    $notas = nl2br(htmlspecialchars(trim((string) ($payment['notas'] ?? '')), ENT_QUOTES, 'UTF-8'));
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo htmlspecialchars($invoice, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        :root { --dark:#0f172a; --accent:#f59e0b; --muted:#475569; --bg:#f8fafc; }
        body { font-family:'Segoe UI', Arial, sans-serif; margin:0; padding:24px; background:var(--bg); color:#0f172a; }
        .card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; max-width:900px; margin:0 auto; box-shadow:0 10px 30px rgba(15,23,42,0.08); }
        .header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
        .brand { color:#fff; background:linear-gradient(135deg, var(--dark), #111827); padding:16px 18px; border-radius:12px; }
        .brand h1 { margin:0; font-size:20px; letter-spacing:0.3px; }
        .brand p { margin:4px 0 0; font-size:12px; color:#cbd5e1; }
        .invoice-meta { text-align:right; }
        .pill { display:inline-flex; align-items:center; gap:8px; background:rgba(245,158,11,0.12); color:var(--dark); border:1px solid rgba(245,158,11,0.35); border-radius:999px; padding:6px 12px; font-weight:600; }
        table { width:100%; border-collapse:collapse; margin-top:18px; }
        th { text-align:left; padding:10px; background:#f1f5f9; color:var(--muted); font-size:13px; }
        td { padding:10px; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1f2937; }
        .total { font-size:18px; font-weight:700; color:var(--dark); }
        .muted { color:var(--muted); font-size:13px; }
        .actions { margin-top:18px; display:flex; gap:12px; }
        .btn { padding:10px 14px; border-radius:10px; border:1px solid #cbd5e1; background:#fff; color:var(--dark); text-decoration:none; font-weight:600; }
        .btn-primary { background:var(--dark); color:#fff; border:none; }
        @media print { .actions { display:none; } body { background:#fff; padding:0; } .card { box-shadow:none; border:0; } }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="brand">
                <h1>Proyectos MCE</h1>
                <p>Software a medida · proyectosmceaa@gmail.com · +57 311 412 59 71</p>
            </div>
            <div class="invoice-meta">
                <div class="pill">Factura <?php echo htmlspecialchars($invoice, ENT_QUOTES, 'UTF-8'); ?></div>
                <p class="muted" style="margin:8px 0 0;">Fecha: <?php echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <div style="margin-top:18px; display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:16px;">
            <div>
                <p class="muted" style="margin:0 0 4px;">Cliente</p>
                <p style="margin:0; font-weight:700;"><?php echo htmlspecialchars($client, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div>
                <p class="muted" style="margin:0 0 4px;">Proyecto</p>
                <p style="margin:0; font-weight:700;"><?php echo htmlspecialchars($project, ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="muted" style="margin:4px 0 0;">Referencia: <?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th style="text-align:right;">Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($payment['concepto'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($payment['metodo'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($payment['estado'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td style="text-align:right;" class="total"><?php echo htmlspecialchars($monto, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            </tbody>
        </table>

        <?php if (!empty(trim((string) $payment['notas']))): ?>
            <div style="margin-top:12px;">
                <p class="muted" style="margin:0 0 6px;">Notas</p>
                <p style="margin:0;"><?php echo $notas; ?></p>
            </div>
        <?php endif; ?>

        <p class="muted" style="margin-top:18px;">Gracias por confiar en Proyectos MCE. Si tienes dudas sobre este comprobante o requieres soporte, contáctanos en proyectosmceaa@gmail.com.</p>

        <div class="actions">
            <a class="btn" href="pagos.php">Volver</a>
            <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
        </div>
    </div>
</body>
</html>
<?php
}

function send_invoice_email(array $payment, string $toEmail, mysqli $conn, ?string &$errorMsg = null): bool
{
    $secretPath = __DIR__ . '/../includes/secrets.php';
    if (file_exists($secretPath)) {
        require $secretPath;
    }
    $smtpUser = $SMTP_USER ?? getenv('SMTP_USER') ?? 'proyectosmceaa@gmail.com';
    $smtpPass = $SMTP_PASS ?? getenv('SMTP_PASS') ?? '';
    $smtpHost = $SMTP_HOST ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com';
    $smtpPort = (int) ($SMTP_PORT ?? getenv('SMTP_PORT') ?? 587);
    $smtpSecure = strtolower((string) ($SMTP_SECURE ?? getenv('SMTP_SECURE') ?? 'tls'));
    $smtpFromEmail = $SMTP_FROM_EMAIL ?? getenv('SMTP_FROM_EMAIL') ?? $smtpUser;
    $smtpFromName = $SMTP_FROM_NAME ?? getenv('SMTP_FROM_NAME') ?? 'Proyectos MCE';
    $smtpToAdmin = $SMTP_TO_EMAIL ?? getenv('SMTP_TO_EMAIL') ?? $smtpUser;

    // Ajuste Gmail: quitar espacios en app password
    if (stripos($smtpHost, 'gmail.com') !== false) {
        $smtpPass = str_replace(' ', '', $smtpPass);
    }

    if (empty($smtpUser) || empty($smtpPass)) {
        $errorMsg = 'Faltan credenciales SMTP (SMTP_USER o SMTP_PASS).';
        error_log('Factura: ' . $errorMsg);
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = function ($str) { error_log('PHPMailer: ' . $str); };
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = $smtpSecure;
        $mail->Port = $smtpPort;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($smtpFromEmail, $smtpFromName);
        $mail->addAddress($toEmail);
        if (!empty($smtpToAdmin) && strtolower($smtpToAdmin) !== strtolower($toEmail)) {
            $mail->addBcc($smtpToAdmin);
        }
        $mail->addReplyTo($smtpFromEmail, $smtpFromName);

        $cliente = htmlspecialchars($payment['proyecto_cliente'] ?: 'cliente', ENT_QUOTES, 'UTF-8');
        $concepto = htmlspecialchars($payment['concepto'], ENT_QUOTES, 'UTF-8');
        $monto = payment_format_amount((float) $payment['monto'], (string) $payment['moneda']);
        $fecha = date('d/m/Y', strtotime($payment['fecha_pago']));

        $mail->Subject = "Factura - Proyectos MCE";
        $mail->isHTML(true);
        $mail->Body = "
            <p>Hola {$cliente},</p>
            <p>Te compartimos el resumen del pago correspondiente a:</p>
            <ul>
                <li><strong>Concepto:</strong> {$concepto}</li>
                <li><strong>Monto:</strong> {$monto}</li>
                <li><strong>Fecha de pago:</strong> {$fecha}</li>
            </ul>
            <p>Gracias por tu confianza.</p>
            <p>Proyectos MCE</p>
        ";
        $mail->AltBody = "Factura - Concepto: {$concepto} - Monto: {$monto} - Fecha: {$fecha}";
        $mail->send();
        return true;
    } catch (Exception $e) {
        $errorMsg = 'No se pudo enviar la factura: ' . $e->getMessage() . ' | ' . $mail->ErrorInfo;
        error_log($errorMsg);
        return false;
    }
}

// Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $modo === 'send') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        exit('CSRF inválido, recarga la página.');
    }
    $emailDestino = trim((string) ($_POST['email_destino'] ?? ''));
    if (!filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
        $flash = 'bademail';
    } else {
        $mailError = '';
        $ok = send_invoice_email($payment, $emailDestino, $conn, $mailError);

        // Log sencillo
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
        $logFile = $logDir . '/facturas.log';
        $status = $ok ? 'OK' : 'FAIL';
        $msgLog = date('c') . " | {$status} | pago_id={$payment['id']} | to={$emailDestino}";
        if (!$ok && !empty($mailError)) {
            $msgLog .= " | err={$mailError}";
        }
        $msgLog .= "\n";
        @file_put_contents($logFile, $msgLog, FILE_APPEND);

        if ($ok) {
            header('Location: pagos.php?msg=factura_enviada');
            exit;
        } else {
            $flash = (stripos((string)$mailError, 'credenciales') !== false) ? 'smtp' : 'sendfail';
        }
    }
    // Si falla, seguimos renderizando el formulario con $flash
    $modo = 'sendform';
}

// Render según modo
if ($modo === 'html') {
    render_html($payment);
    exit;
}

// Form para enviar por correo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enviar factura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl border border-slate-100 p-8 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Factura</p>
                <p class="text-xl font-bold text-slate-900"><?php echo htmlspecialchars(invoice_number($payment), ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="text-sm text-slate-600">Concepto: <?php echo htmlspecialchars($payment['concepto'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <a href="pagos.php" class="text-sm text-blue-600 hover:underline">Volver</a>
        </div>

        <?php if ($flash === 'bademail'): ?>
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">Correo inválido, intenta nuevamente.</div>
        <?php elseif ($flash === 'sendfail'): ?>
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
                No pudimos enviar la factura. Revisa SMTP o intenta de nuevo.
                <?php if (!empty($mailError ?? '')): ?>
                    <div class="mt-1 text-xs text-red-600"><?php echo htmlspecialchars($mailError, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        <?php elseif ($flash === 'smtp'): ?>
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
                Faltan SMTP_USER / SMTP_PASS. Configúralos en <code>includes/secrets.php</code> o variables de entorno.
                <?php if (!empty($mailError ?? '')): ?>
                    <div class="mt-1 text-xs text-red-600"><?php echo htmlspecialchars($mailError, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        <?php elseif ($flash === 'sent'): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">Factura enviada con éxito.</div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo admin_escape($csrfToken); ?>">
            <input type="hidden" name="id" value="<?php echo (int) $payment['id']; ?>">
            <input type="hidden" name="modo" value="send">
            <label class="block text-sm font-semibold text-slate-800 mb-1">Correo de destino</label>
            <input type="email" name="email_destino" required placeholder="cliente@dominio.com" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-blue-600 focus:outline-none">
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-slate-800">
                    <i class="fas fa-paper-plane"></i>
                    <span>Enviar factura</span>
                </button>
            </div>
            <p class="text-xs text-slate-500">La factura se enviará por correo usando la configuración SMTP definida en secrets.php o variables de entorno.</p>
        </form>
    </div>
</body>
</html>
