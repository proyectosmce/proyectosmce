<?php
// admin/cita-estado.php
require_once '../includes/config.php';
require_once '../includes/admin-helpers.php';
require_once __DIR__ . '/../includes/PHPMailer/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$redirect = isset($_POST['redirect']) ? trim((string) $_POST['redirect']) : 'dashboard.php#agenda-llamadas';
$csrf = $_POST['csrf'] ?? '';

if (!admin_validate_csrf($csrf)) {
    http_response_code(400);
    exit('CSRF token invalido.');
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$estado = strtolower(trim((string) ($_POST['estado'] ?? '')));
$allowed = ['pendiente', 'confirmada', 'cancelada'];

if ($id <= 0 || !in_array($estado, $allowed, true)) {
    header('Location: ' . $redirect);
    exit;
}

ensureCitasSchema($conn);

// Obtener datos de la cita antes de actualizar para notificar al cliente.
$cita = null;
$selectError = null;
if ($stmt = $conn->prepare('SELECT id, nombre, email, telefono, servicio, fecha, hora, COALESCE(estado, \"pendiente\") AS estado FROM citas WHERE id = ?')) {
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $stmt->bind_result($cid, $cnombre, $cemail, $ctelefono, $cservicio, $cfecha, $chora, $cestado);
        if ($stmt->fetch()) {
            $cita = [
                'id' => $cid,
                'nombre' => $cnombre,
                'email' => $cemail,
                'telefono' => $ctelefono,
                'servicio' => $cservicio,
                'fecha' => $cfecha,
                'hora' => $chora,
                'estado' => $cestado,
            ];
        }
    } else {
        $selectError = $stmt->error;
    }
    $stmt->close();
} else {
    $selectError = $conn->error;
}

if (!$cita) {
    $_SESSION['agenda_flash'] = [
        'ok' => false,
        'estado' => $estado,
        'email' => '',
        'id' => $id,
        'error' => 'Cita no encontrada. ' . ($selectError ? 'SQL: ' . $selectError : ''),
    ];
    header('Location: ' . $redirect);
    exit;
}

if ($stmt = $conn->prepare('UPDATE citas SET estado = ? WHERE id = ?')) {
    $stmt->bind_param('si', $estado, $id);
    $stmt->execute();
    $stmt->close();
}

// Config SMTP (igual que contacto).
$secretPath = __DIR__ . '/../includes/secrets.php';
if (file_exists($secretPath)) {
    require $secretPath; // Debe definir $SMTP_USER y $SMTP_PASS
}
$smtpUser = $SMTP_USER ?? getenv('SMTP_USER') ?? 'proyectosmceaa@gmail.com';
$smtpPass = $SMTP_PASS ?? getenv('SMTP_PASS') ?? '';
$smtpHost = $SMTP_HOST ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com';
$smtpPort = (int) ($SMTP_PORT ?? getenv('SMTP_PORT') ?? 587);
$smtpSecure = strtolower((string) ($SMTP_SECURE ?? getenv('SMTP_SECURE') ?? 'tls'));
$smtpFromEmail = $SMTP_FROM_EMAIL ?? getenv('SMTP_FROM_EMAIL') ?? $smtpUser;
$smtpFromName = $SMTP_FROM_NAME ?? getenv('SMTP_FROM_NAME') ?? 'Proyectos MCE';
$smtpDebug = (string) ($SMTP_DEBUG ?? getenv('SMTP_DEBUG') ?? '0') === '1';

if (stripos($smtpHost, 'gmail.com') !== false) {
    $smtpPass = str_replace(' ', '', $smtpPass);
}

function admin_send_cita_email(array $cita, string $estadoNuevo, array $smtp): void
{
    $destino = filter_var(trim((string) ($cita['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    if (!$destino) {
        $_SESSION['agenda_flash'] = [
            'ok' => false,
            'estado' => $estadoNuevo,
            'email' => $cita['email'] ?? '',
            'error' => 'Email del cliente vacío o inválido.',
        ];
        return;
    }

    $mail = new PHPMailer(true);
    $smtpDebugLog = [];
    try {
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->Port = $smtp['port'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['user'];
        $mail->Password = $smtp['pass'];
        $mail->Timeout = 30;
        $mail->SMTPDebug = $smtp['debug'] ? 2 : 0;
        $mail->Debugoutput = static function ($str, $level) use (&$smtpDebugLog) {
            $smtpDebugLog[] = "[{$level}] {$str}";
        };

        if ($smtp['secure'] === 'ssl' || $smtp['secure'] === 'smtps' || (int) $smtp['port'] === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($smtp['secure'] === 'none' || $smtp['secure'] === '') {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
        $mail->addAddress($destino, $cita['nombre'] ?? '');
        // Copia interna para confirmar envío
        if (!empty($smtp['from_email'])) {
            $mail->addBCC($smtp['from_email']);
        }
        $mail->addReplyTo($smtp['from_email'], $smtp['from_name']);
        $mail->isHTML(true);

        $fecha = $cita['fecha'] ?? '';
        $hora = $cita['hora'] ?? '';
        $servicio = $cita['servicio'] ?? 'Llamada';
        $nombre = $cita['nombre'] ?? 'Cliente';
        $fechaLabel = $fecha !== '' ? date('d/m/Y', strtotime($fecha)) : 'por definir';
        $horaLabel = $hora !== '' ? date('H:i', strtotime($hora)) : 'por definir';

        if ($estadoNuevo === 'confirmada') {
            $subject = 'Tu cita fue confirmada · Proyectos MCE';
            $hero = 'Tu llamada está confirmada';
            $lead = 'Gracias por agendar. Te esperamos en el horario elegido.';
            $ctaText = 'Agregar al calendario';
            $ctaUrl = '#';
            $statusColor = '#16a34a';
        } else { // cancelada
            $subject = 'Tu cita fue cancelada · Proyectos MCE';
            $hero = 'Hemos cancelado la cita';
            $lead = 'Podemos reprogramar cuando te convenga. Responde a este correo para coordinar otro horario.';
            $ctaText = 'Reagendar';
            $ctaUrl = 'mailto:' . $smtp['from_email'];
            $statusColor = '#dc2626';
        }

        $mail->Subject = $subject;
        $mail->Body = '
            <div style="font-family:Arial,sans-serif;max-width:560px;margin:0 auto;padding:20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                <h2 style="margin-top:0;color:#0f172a;">' . htmlspecialchars($hero, ENT_QUOTES, 'UTF-8') . '</h2>
                <p style="color:#334155;">Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>
                <p style="color:#334155;">' . htmlspecialchars($lead, ENT_QUOTES, 'UTF-8') . '</p>
                <div style="margin:16px 0;padding:14px;border:1px solid #e2e8f0;border-radius:10px;background:#fff;">
                    <p style="margin:0 0 8px 0;font-weight:bold;color:' . $statusColor . ';text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">' . strtoupper($estadoNuevo) . '</p>
                    <p style="margin:4px 0;color:#0f172a;"><strong>Fecha:</strong> ' . htmlspecialchars($fechaLabel, ENT_QUOTES, 'UTF-8') . '</p>
                    <p style="margin:4px 0;color:#0f172a;"><strong>Hora:</strong> ' . htmlspecialchars($horaLabel, ENT_QUOTES, 'UTF-8') . '</p>
                    <p style="margin:4px 0;color:#0f172a;"><strong>Servicio:</strong> ' . htmlspecialchars($servicio, ENT_QUOTES, 'UTF-8') . '</p>
                </div>
                <p><a href="' . htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:10px 16px;border-radius:8px;background:' . $statusColor . ';color:white;text-decoration:none;font-weight:600;">' . htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8') . '</a></p>
                <p style="color:#64748b;font-size:12px;margin-top:20px;">Si no solicitaste esta actualización, responde este correo.</p>
            </div>';

        $mail->AltBody = "Estado de tu cita: {$estadoNuevo}\nFecha: {$fechaLabel}\nHora: {$horaLabel}\nServicio: {$servicio}\n\nSi no solicitaste esta actualización, responde este correo.";

        $mail->send();
        $_SESSION['agenda_flash'] = [
            'ok' => true,
            'estado' => $estadoNuevo,
            'email' => $destino,
            'id' => $cita['id'] ?? $id,
        ];
    } catch (Exception $e) {
        error_log('No se pudo enviar correo de cita: ' . $mail->ErrorInfo);
        $_SESSION['agenda_flash'] = [
            'ok' => false,
            'estado' => $estadoNuevo,
            'email' => $destino,
            'id' => $cita['id'] ?? $id,
            'error' => $mail->ErrorInfo ?: 'Error desconocido al enviar correo.',
            'debug' => implode(' | ', $smtpDebugLog),
        ];
    }
}

admin_send_cita_email($cita ?? [], $estado, [
    'host' => $smtpHost,
    'port' => $smtpPort,
    'user' => $smtpUser,
    'pass' => $smtpPass,
    'secure' => $smtpSecure,
    'from_email' => $smtpFromEmail,
    'from_name' => $smtpFromName,
    'debug' => $smtpDebug,
]);

admin_log_action($conn, 'Actualizar cita', 'cita', $id, 'Estado: ' . $estado);

header('Location: ' . $redirect);
exit;
