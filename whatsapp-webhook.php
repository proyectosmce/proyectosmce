<?php
// Webhook para recibir mensajes de WhatsApp y reenviarlos por email.
// Soporta Twilio (POST urlencoded) y el Webhook de Meta Cloud (POST JSON / GET verify).

require_once __DIR__ . '/includes/config.php';

// Lee el token desde secrets.php o variables de entorno.
$verifyToken = $WHATSAPP_VERIFY_TOKEN ?? getenv('WHATSAPP_VERIFY_TOKEN') ?? 'cambia-este-token';

// Correo de destino
$notifyEmail = 'proyectosmceaa@gmail.com';

// 1) Verificacion del webhook (solo Meta Cloud)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hub_mode'])) {
    if ($verifyToken === 'cambia-este-token') {
        http_response_code(500);
        exit('Configura WHATSAPP_VERIFY_TOKEN antes de usar el webhook.');
    }

    if ($_GET['hub_mode'] === 'subscribe' && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $verifyToken) {
        echo $_GET['hub_challenge'] ?? '';
        exit;
    }
    http_response_code(403);
    exit;
}

// 2) Detectar origen y extraer datos
$from = '';
$body = '';

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$raw = file_get_contents('php://input');

if (stripos($contentType, 'application/json') !== false) {
    // Meta Cloud: JSON
    $payload = json_decode($raw, true);
    $msg = $payload['entry'][0]['changes'][0]['value']['messages'][0] ?? null;
    if ($msg) {
        $from = $msg['from'] ?? '';
        $body = $msg['text']['body'] ?? '';
    }
} else {
    // Twilio: application/x-www-form-urlencoded
    $from = $_POST['From'] ?? $_POST['WaId'] ?? '';
    $body = $_POST['Body'] ?? '';
}

// 3) Si hay datos, enviar correo y responder
if ($from && $body) {
    $subject = 'Nuevo WhatsApp de ' . $from;
    $message = "Remitente: {$from}\nMensaje:\n{$body}\n\nFecha: " . date('Y-m-d H:i:s');
    @mail($notifyEmail, $subject, $message);

    // Auto-respuesta (solo para Twilio; Meta ignora TwiML)
    if (isset($_POST['From']) || isset($_POST['WaId'])) {
        header('Content-Type: text/xml');
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo '<Response><Message>Claro que si! Cuentame de que se trata tu proyecto</Message></Response>';
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    }
    exit;
}

http_response_code(400);
echo 'Faltan datos';
