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

// Obtener datos de la cita antes de actualizar
$cita = null;
$selectError = null;
$stmt = $conn->prepare('SELECT id, nombre, email, telefono, servicio, fecha, hora, COALESCE(estado, "pendiente") AS estado, COALESCE(tipo_llamada, "telefono") AS tipo_llamada, enlace_reunion FROM citas WHERE id = ?');
if ($stmt) {
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $stmt->bind_result($cid, $cnombre, $cemail, $ctelefono, $cservicio, $cfecha, $chora, $cestado, $ctipo, $cenlace);
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
                'tipo_llamada' => $ctipo,
                'enlace_reunion' => $cenlace,
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

// Actualizar estado
if ($stmt = $conn->prepare('UPDATE citas SET estado = ? WHERE id = ?')) {
    $stmt->bind_param('si', $estado, $id);
    $stmt->execute();
    $stmt->close();
}

// SMTP config
$secretPath = __DIR__ . '/../includes/secrets.php';
if (file_exists($secretPath)) {
    require $secretPath; // define $SMTP_USER, $SMTP_PASS, etc.
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
    $destino = trim((string) ($cita['email'] ?? ''));
    if ($destino === '') {
        $_SESSION['agenda_flash'] = [
            'ok' => false,
            'estado' => $estadoNuevo,
            'email' => '',
            'id' => $cita['id'] ?? null,
            'error' => 'Email del cliente vacío.',
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
        if (!empty($smtp['from_email'])) {
            $mail->addBCC($smtp['from_email']);
        }
        $mail->addReplyTo($smtp['from_email'], $smtp['from_name']);
        $mail->isHTML(true);

        $fecha = $cita['fecha'] ?? '';
        $hora = $cita['hora'] ?? '';
        $servicio = $cita['servicio'] ?? 'Llamada';
        $nombre = $cita['nombre'] ?? 'Cliente';
        $tipo = strtolower(trim((string) ($cita['tipo_llamada'] ?? 'telefono'))) === 'video' ? 'Videollamada' : 'Teléfono';
        $enlace = trim((string) ($cita['enlace_reunion'] ?? ''));

        $fechaLabel = $fecha !== '' ? date('d/m/Y', strtotime($fecha)) : 'por definir';
        $horaLabel = $hora !== '' ? date('H:i', strtotime($hora)) : 'por definir';

        if ($estadoNuevo === 'confirmada') {
            $subject = 'Tu cita fue confirmada · Proyectos MCE';
            $hero = 'Tu llamada está confirmada';
            $lead = 'Gracias por agendar. Te esperamos en el horario elegido.';
            $ctaText = $enlace !== '' ? 'Entrar a la videollamada' : 'Agregar al calendario';
            $ctaUrl = $enlace !== '' ? $enlace : '#';
            $statusColor = '#16a34a';
        } else {
            $subject = 'Tu cita fue cancelada · Proyectos MCE';
            $hero = 'Hemos cancelado la cita';
            $lead = 'Podemos reprogramar cuando te convenga. Reagenda en el enlace siguiente.';
            $ctaText = 'Reagendar ahora';
            $ctaUrl = app_absolute_url('contacto.php#agenda-llamada');
            $statusColor = '#dc2626';
        }

        $logoText = 'Proyectos MCE';
        $estadoUpper = strtoupper($estadoNuevo);
        $heroEsc = htmlspecialchars($hero, ENT_QUOTES, 'UTF-8');
        $leadEsc = htmlspecialchars($lead, ENT_QUOTES, 'UTF-8');
        $nombreEsc = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $fechaEsc = htmlspecialchars($fechaLabel, ENT_QUOTES, 'UTF-8');
        $horaEsc = htmlspecialchars($horaLabel, ENT_QUOTES, 'UTF-8');
        $servicioEsc = htmlspecialchars($servicio, ENT_QUOTES, 'UTF-8');
        $tipoEsc = htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8');
        $enlaceEsc = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');
        $enlaceHtml = $enlaceEsc !== '' ? '<a href="' . $enlaceEsc . '" style="color:#2563eb;">' . $enlaceEsc . '</a>' : 'No aplica / se compartirá si es necesario';
        $ctaEsc = htmlspecialchars($ctaUrl, ENT_QUOTES, 'UTF-8');
        $ctaTextEsc = htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8');
        $statusColorEsc = htmlspecialchars($statusColor, ENT_QUOTES, 'UTF-8');
        $logoTextEsc = htmlspecialchars($logoText, ENT_QUOTES, 'UTF-8');

        $mail->Subject = $subject;
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$logoTextEsc}</title>
</head>
<body style="margin:0;padding:0;background:#eef2ff;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(180deg,#eef2ff 0%,#f8fafc 100%);padding:28px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 18px 40px rgba(37,99,235,0.12);">
          <tr>
            <td style="background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 48%,#06b6d4 100%);padding:34px;">
              <div style="font-size:12px;letter-spacing:0.24em;text-transform:uppercase;color:#bfdbfe;margin-bottom:12px;">{$logoTextEsc}</div>
              <div style="font-size:28px;line-height:1.2;font-weight:700;color:#ffffff;margin-bottom:12px;">{$heroEsc}</div>
              <div style="font-size:15px;line-height:1.6;color:#dbeafe;">{$leadEsc}</div>
            </td>
          </tr>
          <tr>
            <td style="padding:26px 30px 10px;">
              <div style="background:#f8fafc;border:1px solid #dbeafe;border-radius:18px;padding:18px 20px;margin-bottom:18px;">
                <div style="font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:{$statusColorEsc};margin-bottom:10px;">{$estadoUpper}</div>
                <p style="margin:4px 0;color:#0f172a;"><strong>Hola:</strong> {$nombreEsc}</p>
                <p style="margin:4px 0;color:#0f172a;"><strong>Fecha:</strong> {$fechaEsc}</p>
                <p style="margin:4px 0;color:#0f172a;"><strong>Hora:</strong> {$horaEsc}</p>
                <p style="margin:4px 0;color:#0f172a;"><strong>Servicio:</strong> {$servicioEsc}</p>
                <p style="margin:4px 0;color:#0f172a;"><strong>Modalidad:</strong> {$tipoEsc}</p>
                <p style="margin:4px 0;color:#0f172a;"><strong>Enlace:</strong> {$enlaceHtml}</p>
              </div>
              <div style="margin:14px 0;">
                <a href="{$ctaEsc}" style="display:inline-block;padding:12px 18px;border-radius:12px;background:{$statusColorEsc};color:#ffffff;text-decoration:none;font-weight:700;">{$ctaTextEsc}</a>
              </div>
              <p style="color:#94a3b8;font-size:12px;margin:14px 0 4px;">Si no solicitaste esta actualización, responde a este correo.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        $mail->AltBody = "Estado de tu cita: {$estadoNuevo}\nFecha: {$fechaLabel}\nHora: {$horaLabel}\nServicio: {$servicio}\nModalidad: {$tipo}\nEnlace: " . ($enlace !== '' ? $enlace : 'No aplica / se enviará si es necesario') . "\nCTA: {$ctaUrl}\n\nSi no solicitaste esta actualización, responde este correo.";

        $mail->send();
        $_SESSION['agenda_flash'] = [
            'ok' => true,
            'estado' => $estadoNuevo,
            'email' => $destino,
            'id' => $cita['id'] ?? null,
        ];
    } catch (Exception $e) {
        error_log('No se pudo enviar correo de cita: ' . $mail->ErrorInfo);
        $_SESSION['agenda_flash'] = [
            'ok' => false,
            'estado' => $estadoNuevo,
            'email' => $destino,
            'id' => $cita['id'] ?? null,
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
?>
