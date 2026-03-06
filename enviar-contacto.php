<?php
require_once 'includes/config.php';
require_once __DIR__ . '/includes/PHPMailer/Exception.php';
require_once __DIR__ . '/includes/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar credenciales seguras desde variable de entorno o archivo ignorado (includes/secrets.php)
$secretPath = __DIR__ . '/includes/secrets.php';
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
$smtpToEmail = $SMTP_TO_EMAIL ?? getenv('SMTP_TO_EMAIL') ?? $smtpUser;
$smtpDebug = (string) ($SMTP_DEBUG ?? getenv('SMTP_DEBUG') ?? '0') === '1';

if (stripos($smtpHost, 'gmail.com') !== false) {
    $smtpPass = str_replace(' ', '', $smtpPass);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitize($_POST['nombre']);
    $email = sanitize($_POST['email']);
    $telefono = sanitize($_POST['telefono'] ?? '');
    $servicio = sanitize($_POST['servicio'] ?? '');
    $mensaje = sanitize($_POST['mensaje']);
    
    // Validaciones basicas
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        redirect('contacto.php?error=1');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect('contacto.php?error=2');
    }
    
    // Guardar en BD
    $sql = "INSERT INTO mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensaje);
    
    if ($stmt->execute()) {
        // Enviar email con PHPMailer
        $mail = new PHPMailer(true);
        try {
            if (empty($smtpUser) || empty($smtpPass)) {
                throw new Exception('Falta SMTP_USER o SMTP_PASS');
            }

            $smtpDebugLog = [];

            $nombreMail = html_entity_decode($nombre, ENT_QUOTES, 'UTF-8');
            $emailMail = html_entity_decode($email, ENT_QUOTES, 'UTF-8');
            $telefonoMail = html_entity_decode($telefono, ENT_QUOTES, 'UTF-8');
            $servicioMail = html_entity_decode($servicio, ENT_QUOTES, 'UTF-8');
            $mensajeMail = html_entity_decode($mensaje, ENT_QUOTES, 'UTF-8');

            $nombreHtml = htmlspecialchars($nombreMail, ENT_QUOTES, 'UTF-8');
            $emailHtml = htmlspecialchars($emailMail, ENT_QUOTES, 'UTF-8');
            $telefonoHtml = htmlspecialchars($telefonoMail !== '' ? $telefonoMail : 'No proporcionado', ENT_QUOTES, 'UTF-8');
            $servicioHtml = htmlspecialchars($servicioMail !== '' ? $servicioMail : 'No especificado', ENT_QUOTES, 'UTF-8');
            $mensajeHtml = nl2br(htmlspecialchars($mensajeMail, ENT_QUOTES, 'UTF-8'));
            $portfolioAbsoluteUrl = htmlspecialchars(app_absolute_url('portafolio.php'), ENT_QUOTES, 'UTF-8');

            $serviceKey = function_exists('mb_strtolower')
                ? mb_strtolower($servicioMail, 'UTF-8')
                : strtolower($servicioMail);

            $clientSubject = 'Recibimos tu solicitud en Proyectos MCE';
            $clientHeroTitle = "Hola {$nombreHtml}, ya recibimos tu solicitud";
            $clientHeroText = 'Gracias por escribirnos. Tu información quedó registrada correctamente y revisaremos tu caso para responderte por este mismo medio.';
            $clientSummaryTitle = 'Resumen de tu consulta';
            $clientMessageTitle = 'Lo que nos compartiste';
            $clientNextTitle = 'Qué sigue ahora';
            $clientNextText = 'Revisaremos la información que enviaste y te responderemos con los siguientes pasos, una orientación inicial o una propuesta según el tipo de proyecto.';
            $clientFooterText = 'Este es un correo automático de confirmación. Si deseas agregar información adicional, puedes responder directamente a este mensaje.';
            $clientCtaText = 'Ver portafolio';
            $clientPlainIntro = 'Recibimos tu solicitud en Proyectos MCE y la revisaremos para responderte por este mismo medio.';
            $clientPlainNext = 'Revisaremos la información que enviaste y te responderemos con los siguientes pasos.';

            if (strpos($serviceKey, 'tienda') !== false || strpos($serviceKey, 'e-commerce') !== false || strpos($serviceKey, 'ecommerce') !== false) {
                $clientSubject = 'Recibimos tu solicitud para tu tienda online';
                $clientHeroTitle = "Hola {$nombreHtml}, tu solicitud para tienda online ya está en revisión";
                $clientHeroText = 'Gracias por contarnos sobre tu idea de venta en línea. Revisaremos tu solicitud para orientarte sobre estructura, catálogo, pagos y el siguiente paso más conveniente.';
                $clientSummaryTitle = 'Resumen de tu tienda online';
                $clientMessageTitle = 'Detalles que nos compartiste';
                $clientNextTitle = 'Próximo paso para tu tienda';
                $clientNextText = 'Vamos a revisar el alcance de tu tienda, el tipo de productos y la forma de cobro que podrías necesitar para responderte con una orientación clara.';
                $clientFooterText = 'Este correo confirma que tu solicitud para tienda online ya fue recibida. Si deseas agregar productos, referencias o ideas visuales, responde a este mensaje.';
                $clientCtaText = 'Ver proyectos web';
                $clientPlainIntro = 'Recibimos tu solicitud para tienda online y vamos a revisarla para responderte con una orientación clara.';
                $clientPlainNext = 'Revisaremos productos, estructura y necesidades de pago para indicarte el siguiente paso.';
            } elseif (strpos($serviceKey, 'inventario') !== false) {
                $clientSubject = 'Recibimos tu solicitud para sistema de inventario';
                $clientHeroTitle = "Hola {$nombreHtml}, ya recibimos tu solicitud para sistema de inventario";
                $clientHeroText = 'Gracias por escribirnos. Vamos a revisar tu necesidad para entender mejor el control de stock, ventas, reportes o procesos internos que buscas mejorar.';
                $clientSummaryTitle = 'Resumen de tu sistema';
                $clientMessageTitle = 'Necesidades que nos compartiste';
                $clientNextTitle = 'Próximo paso para tu sistema';
                $clientNextText = 'Analizaremos los procesos que mencionaste para responderte con una orientación inicial sobre módulos, flujo de trabajo y nivel de personalización.';
                $clientFooterText = 'Tu solicitud para sistema de inventario ya quedó registrada. Si quieres agregar más detalles sobre tu operación, puedes responder directamente a este correo.';
                $clientCtaText = 'Ver sistemas publicados';
                $clientPlainIntro = 'Recibimos tu solicitud para sistema de inventario y revisaremos tus necesidades para responderte con una orientación inicial.';
                $clientPlainNext = 'Analizaremos tus procesos y te responderemos con una propuesta de enfoque.';
            } elseif (strpos($serviceKey, 'landing') !== false) {
                $clientSubject = 'Recibimos tu solicitud para landing page';
                $clientHeroTitle = "Hola {$nombreHtml}, tu solicitud para landing page ya fue recibida";
                $clientHeroText = 'Gracias por escribirnos. Revisaremos tu mensaje para entender el objetivo de la página, el tipo de conversión que buscas y cómo enfocar mejor la propuesta.';
                $clientSummaryTitle = 'Resumen de tu landing';
                $clientMessageTitle = 'Objetivo que nos compartiste';
                $clientNextTitle = 'Próximo paso para tu página';
                $clientNextText = 'Vamos a revisar el enfoque de tu landing, el mensaje principal y la acción esperada para responderte con una guía inicial más aterrizada.';
                $clientFooterText = 'Tu solicitud para landing page ya quedó registrada. Si deseas enviar referencias visuales o ejemplos, responde directamente a este mensaje.';
                $clientCtaText = 'Ver trabajos publicados';
                $clientPlainIntro = 'Recibimos tu solicitud para landing page y revisaremos el objetivo de tu página para responderte con una guía inicial.';
                $clientPlainNext = 'Revisaremos tu enfoque, mensaje y necesidad de conversión para indicarte el siguiente paso.';
            } elseif (strpos($serviceKey, 'desarrollo') !== false || strpos($serviceKey, 'medida') !== false || strpos($serviceKey, 'web') !== false || strpos($serviceKey, 'sistema') !== false) {
                $clientSubject = 'Recibimos tu solicitud de desarrollo web';
                $clientHeroTitle = "Hola {$nombreHtml}, ya estamos revisando tu solicitud de desarrollo web";
                $clientHeroText = 'Gracias por escribirnos. Tu mensaje ya quedó registrado y revisaremos el alcance de tu idea para responderte con una orientación inicial más precisa.';
                $clientSummaryTitle = 'Resumen de tu proyecto';
                $clientMessageTitle = 'Idea que nos compartiste';
                $clientNextTitle = 'Próximo paso para tu desarrollo';
                $clientNextText = 'Vamos a revisar lo que necesitas construir para responderte con una guía inicial sobre enfoque, alcance y siguientes pasos.';
                $clientFooterText = 'Tu solicitud de desarrollo web ya fue recibida. Si quieres agregar funcionalidades o referencias, puedes responder directamente a este mensaje.';
                $clientCtaText = 'Explorar portafolio';
                $clientPlainIntro = 'Recibimos tu solicitud de desarrollo web y vamos a revisar el alcance para responderte con una orientación inicial.';
                $clientPlainNext = 'Analizaremos tu idea y te responderemos con enfoque, alcance y siguientes pasos.';
            }

            $internalSubject = "Nuevo lead web - {$nombreMail}";
            $internalHeroTitle = "Nuevo lead registrado: {$nombreHtml}";
            $internalHeroText = 'Llegó una nueva solicitud desde el formulario de contacto. Aquí tienes el resumen para revisar el alcance y responder con contexto.';
            $internalSummaryTitle = 'Resumen del lead';
            $internalMessageTitle = 'Mensaje del cliente';
            $internalActionTitle = 'Siguiente acción sugerida';
            $internalActionText = "Revisa la necesidad inicial y responde a {$nombreHtml} para continuar la conversación.";
            $internalFooterText = 'Correo interno generado automáticamente desde el formulario de contacto de Proyectos MCE.';
            $internalCtaText = 'Responder al cliente';
            $internalPlainIntro = 'Llegó un nuevo lead desde el formulario web.';
            $internalPlainAction = "Siguiente acción sugerida: responde a {$nombreMail} y valida el alcance inicial.";

            if (strpos($serviceKey, 'tienda') !== false || strpos($serviceKey, 'e-commerce') !== false || strpos($serviceKey, 'ecommerce') !== false) {
                $internalSubject = "Nuevo lead de tienda online - {$nombreMail}";
                $internalHeroTitle = "Nuevo lead para tienda online: {$nombreHtml}";
                $internalHeroText = 'El cliente quiere avanzar con una solución de venta en línea. Revisa productos, pagos y alcance comercial antes de responder.';
                $internalSummaryTitle = 'Resumen del lead e-commerce';
                $internalMessageTitle = 'Necesidades del canal de venta';
                $internalActionTitle = 'Enfoque sugerido';
                $internalActionText = "Valida catálogo, medios de pago, envíos y panel administrativo antes de enviar la primera respuesta a {$nombreHtml}.";
                $internalFooterText = 'Lead interno de tienda online generado automáticamente desde el formulario de contacto.';
                $internalPlainIntro = 'Llegó un nuevo lead interesado en una tienda online.';
                $internalPlainAction = 'Siguiente acción sugerida: valida catálogo, pagos, envíos y panel administrativo.';
            } elseif (strpos($serviceKey, 'inventario') !== false) {
                $internalSubject = "Nuevo lead de sistema de inventario - {$nombreMail}";
                $internalHeroTitle = "Nuevo lead para sistema de inventario: {$nombreHtml}";
                $internalHeroText = 'El mensaje apunta a operación interna, control de stock o reportes. Conviene revisar procesos, roles y puntos críticos antes de responder.';
                $internalSummaryTitle = 'Resumen del lead operativo';
                $internalMessageTitle = 'Procesos que quiere mejorar';
                $internalActionTitle = 'Enfoque sugerido';
                $internalActionText = "Revisa flujos de inventario, ventas, reportes y usuarios para responderle a {$nombreHtml} con un enfoque más aterrizado.";
                $internalFooterText = 'Lead interno de sistema de inventario generado automáticamente desde el formulario de contacto.';
                $internalPlainIntro = 'Llegó un nuevo lead interesado en un sistema de inventario.';
                $internalPlainAction = 'Siguiente acción sugerida: valida procesos, reportes, usuarios y necesidades operativas.';
            } elseif (strpos($serviceKey, 'landing') !== false) {
                $internalSubject = "Nuevo lead de landing page - {$nombreMail}";
                $internalHeroTitle = "Nuevo lead para landing page: {$nombreHtml}";
                $internalHeroText = 'El cliente está buscando una página orientada a conversión. Revisa el objetivo comercial, campaña o público antes de responder.';
                $internalSummaryTitle = 'Resumen del lead de captación';
                $internalMessageTitle = 'Objetivo de la página';
                $internalActionTitle = 'Enfoque sugerido';
                $internalActionText = "Identifica la acción principal que quiere lograr y responde a {$nombreHtml} con foco en mensaje, oferta y conversión.";
                $internalFooterText = 'Lead interno de landing page generado automáticamente desde el formulario de contacto.';
                $internalPlainIntro = 'Llegó un nuevo lead interesado en una landing page.';
                $internalPlainAction = 'Siguiente acción sugerida: valida objetivo, oferta, campaña y acción de conversión principal.';
            } elseif (strpos($serviceKey, 'desarrollo') !== false || strpos($serviceKey, 'medida') !== false || strpos($serviceKey, 'web') !== false || strpos($serviceKey, 'sistema') !== false) {
                $internalSubject = "Nuevo lead de desarrollo web - {$nombreMail}";
                $internalHeroTitle = "Nuevo lead de desarrollo web: {$nombreHtml}";
                $internalHeroText = 'El cliente necesita una solución más abierta o personalizada. Revisa alcance, funcionalidades y nivel de complejidad antes de contestar.';
                $internalSummaryTitle = 'Resumen del proyecto a medida';
                $internalMessageTitle = 'Alcance que plantea el cliente';
                $internalActionTitle = 'Enfoque sugerido';
                $internalActionText = "Valida módulos, funcionalidades, integraciones y expectativas del proyecto para responder a {$nombreHtml} con mejor contexto.";
                $internalFooterText = 'Lead interno de desarrollo web generado automáticamente desde el formulario de contacto.';
                $internalPlainIntro = 'Llegó un nuevo lead interesado en desarrollo web a medida.';
                $internalPlainAction = 'Siguiente acción sugerida: valida alcance, módulos, integraciones y complejidad del proyecto.';
            }

            $replyMailtoHtml = htmlspecialchars('mailto:' . $emailMail, ENT_QUOTES, 'UTF-8');

            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            $mail->Port = $smtpPort;
            $mail->Timeout = 30;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = $smtpDebug ? 2 : 0;
            $mail->Debugoutput = static function ($str, $level) use (&$smtpDebugLog) {
                $smtpDebugLog[] = "[{$level}] {$str}";
            };

            if ($smtpSecure === 'ssl' || $smtpSecure === 'smtps') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtpSecure === 'none' || $smtpSecure === '') {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
             
            $mail->setFrom($smtpFromEmail, $smtpFromName);
            $mail->addAddress($smtpToEmail);
            if (!empty($email)) {
                $mail->addReplyTo($email, $nombre);
            }
            
            $mail->isHTML(true);
            $mail->Subject = $internalSubject;
            $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="margin:0; padding:0; background-color:#eff6ff; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%); margin:0; padding:26px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; background-color:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 20px 48px rgba(37, 99, 235, 0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg, #0f172a 0%, #1d4ed8 48%, #06b6d4 100%); padding:36px;">
                            <div style="font-size:12px; letter-spacing:0.26em; text-transform:uppercase; color:#bfdbfe; margin-bottom:10px;">Proyectos MCE</div>
                            <div style="font-size:30px; line-height:1.2; font-weight:700; color:#ffffff; margin-bottom:12px;">{$internalHeroTitle}</div>
                            <div style="font-size:15px; line-height:1.7; color:#dbeafe;">
                                {$internalHeroText}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 36px 14px;">
                            <div style="background-color:#f8fafc; border:1px solid #dbeafe; border-radius:20px; padding:22px 24px; margin-bottom:20px;">
                                <div style="font-size:13px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#2563eb; margin-bottom:12px;">{$internalSummaryTitle}</div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 12px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Nombre</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$nombreHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 12px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Correo</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$emailHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 14px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Teléfono</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$telefonoHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Servicio</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$servicioHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div style="background:linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%); border:1px solid #bfdbfe; border-radius:18px; padding:18px 20px;">
                                    <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#2563eb; font-weight:700; margin-bottom:10px;">{$internalMessageTitle}</div>
                                    <div style="font-size:15px; line-height:1.8; color:#1e293b;">{$mensajeHtml}</div>
                                </div>
                            </div>

                            <div style="background-color:#0f172a; border-radius:20px; padding:22px 24px; margin-bottom:20px;">
                                <div style="font-size:14px; font-weight:700; color:#ffffff; margin-bottom:10px;">{$internalActionTitle}</div>
                                <div style="font-size:14px; line-height:1.8; color:#cbd5e1;">
                                    {$internalActionText}
                                </div>
                            </div>

                            <div style="text-align:center; padding:4px 0 14px;">
                                <a href="{$replyMailtoHtml}" style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:14px 24px; border-radius:999px; font-weight:700;">
                                    {$internalCtaText}
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 30px; color:#64748b; font-size:12px; line-height:1.7;">
                            {$internalFooterText}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
            $mail->AltBody = "{$internalPlainIntro}\n\n"
                . "Nombre: {$nombreMail}\n"
                . "Correo: {$emailMail}\n"
                . "Telefono: " . ($telefonoMail !== '' ? $telefonoMail : 'No proporcionado') . "\n"
                . "Servicio: " . ($servicioMail !== '' ? $servicioMail : 'No especificado') . "\n\n"
                . "Mensaje:\n{$mensajeMail}\n\n"
                . "{$internalPlainAction}";
            
            $mail->send();

            try {
                $mail->clearAllRecipients();
                $mail->clearReplyTos();

                $mail->addAddress($emailMail, $nombreMail);
                $mail->addReplyTo($smtpUser, 'Proyectos MCE');
                $mail->Subject = $clientSubject;
                $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de contacto</title>
</head>
<body style="margin:0; padding:0; background-color:#eff6ff; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%); margin:0; padding:26px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; background-color:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 20px 48px rgba(37, 99, 235, 0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg, #0f172a 0%, #1d4ed8 48%, #06b6d4 100%); padding:36px;">
                            <div style="font-size:12px; letter-spacing:0.26em; text-transform:uppercase; color:#bfdbfe; margin-bottom:10px;">Proyectos MCE</div>
                            <div style="font-size:30px; line-height:1.2; font-weight:700; color:#ffffff; margin-bottom:12px;">{$clientHeroTitle}</div>
                            <div style="font-size:15px; line-height:1.7; color:#dbeafe;">
                                {$clientHeroText}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 36px 14px;">
                            <div style="background-color:#f8fafc; border:1px solid #dbeafe; border-radius:20px; padding:22px 24px; margin-bottom:20px;">
                                <div style="font-size:13px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#2563eb; margin-bottom:12px;">{$clientSummaryTitle}</div>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 14px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Servicio de interés</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$servicioHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">Canal de respuesta</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$emailHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div style="background:linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%); border:1px solid #bfdbfe; border-radius:18px; padding:18px 20px;">
                                    <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#2563eb; font-weight:700; margin-bottom:10px;">{$clientMessageTitle}</div>
                                    <div style="font-size:15px; line-height:1.8; color:#1e293b;">{$mensajeHtml}</div>
                                </div>
                            </div>

                            <div style="background-color:#0f172a; border-radius:20px; padding:22px 24px; margin-bottom:20px;">
                                <div style="font-size:14px; font-weight:700; color:#ffffff; margin-bottom:10px;">{$clientNextTitle}</div>
                                <div style="font-size:14px; line-height:1.8; color:#cbd5e1;">
                                    {$clientNextText}
                                </div>
                            </div>

                            <div style="text-align:center; padding:4px 0 14px;">
                                <a href="{$portfolioAbsoluteUrl}" style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:14px 24px; border-radius:999px; font-weight:700;">
                                    {$clientCtaText}
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 30px; color:#64748b; font-size:12px; line-height:1.7;">
                            {$clientFooterText}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
                $mail->AltBody = "Hola {$nombreMail},\n\n"
                    . "{$clientPlainIntro}\n\n"
                    . "Servicio de interes: " . ($servicioMail !== '' ? $servicioMail : 'No especificado') . "\n"
                    . "Canal de respuesta: {$emailMail}\n\n"
                    . "Resumen de tu consulta:\n{$mensajeMail}\n\n"
                    . "{$clientPlainNext}\n\n"
                    . "Si quieres agregar mas informacion, puedes responder a este correo.";

                $mail->send();
            } catch (Exception $clientMailException) {
                error_log('Error al enviar confirmacion al cliente: ' . $clientMailException->getMessage());
            }

            redirect('contacto.php?success=1');
        } catch (Exception $e) {
            error_log('Error al enviar correo: ' . $e->getMessage());
            if (!empty($mail->ErrorInfo)) {
                error_log('PHPMailer ErrorInfo: ' . $mail->ErrorInfo);
            }
            if (!empty($smtpDebugLog)) {
                error_log("SMTP debug:\n" . implode("\n", $smtpDebugLog));
            }
            $code = (empty($smtpUser) || empty($smtpPass)) ? 4 : 5;
            redirect('contacto.php?error=' . $code);
        }
    } else {
        redirect('contacto.php?error=3');
    }
    
    $stmt->close();
} else {
    redirect('contacto.php');
}
?>
