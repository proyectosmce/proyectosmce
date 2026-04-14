<?php
require_once 'includes/config.php';
require_once 'includes/form-guard.php';
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
$defaultSmtpEmail = 'contacto@proyectosmce.com';
$smtpUser = $SMTP_USER ?? getenv('SMTP_USER') ?? $defaultSmtpEmail;
$smtpPass = $SMTP_PASS ?? getenv('SMTP_PASS') ?? '';
$smtpHost = $SMTP_HOST ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com';
$smtpPort = (int) ($SMTP_PORT ?? getenv('SMTP_PORT') ?? 587);
$smtpSecure = strtolower((string) ($SMTP_SECURE ?? getenv('SMTP_SECURE') ?? 'tls'));
$smtpFromEmail = $SMTP_FROM_EMAIL ?? getenv('SMTP_FROM_EMAIL') ?? $defaultSmtpEmail;
$smtpFromName = $SMTP_FROM_NAME ?? getenv('SMTP_FROM_NAME') ?? 'Proyectos MCE';
$smtpToEmail = $SMTP_TO_EMAIL ?? getenv('SMTP_TO_EMAIL') ?? $defaultSmtpEmail;
$smtpDebug = (string) ($SMTP_DEBUG ?? getenv('SMTP_DEBUG') ?? '0') === '1';

// Helper para generar enlace de reunión (Teams por defecto)
function mce_generate_meet_link(): string
{
    $token = uniqid('mce-', true);
    $template = getenv('TEAMS_MEET_URL') ?: ($GLOBALS['TEAMS_MEET_URL'] ?? '');
    if ($template) {
        return str_replace('{ID}', $token, $template);
    }
    return 'https://teams.live.com/meet/' . $token;
}

if (stripos($smtpHost, 'gmail.com') !== false) {
    $smtpPass = str_replace(' ', '', $smtpPass);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formContextRaw = strtolower(trim((string) ($_POST['form_context'] ?? 'contacto')));
    $formContext = $formContextRaw === 'agenda' ? 'agenda' : 'contacto';
    $allowedAnchors = ['form-feedback', 'contacto-form', 'agenda-llamada'];
    $redirectAnchorInput = trim((string) ($_POST['redirect_anchor'] ?? ''));
    $redirectAnchor = in_array($redirectAnchorInput, $allowedAnchors, true) ? $redirectAnchorInput : 'form-feedback';
    $redirectHash = '#' . $redirectAnchor;
    $isAgendaForm = $formContext === 'agenda';

    if (!form_guard_honeypot_is_clear($_POST['company_website'] ?? '')) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    $guardCheck = form_guard_verify('contacto', $_POST['form_token'] ?? null, 3);
    if (!$guardCheck['ok']) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    $ipLimit = form_guard_rate_limit('contact_form_ip', form_guard_client_ip(), 5, 900);
    if (!$ipLimit['allowed']) {
        redirect('contacto.php?error=7' . $redirectHash);
    }

    $nombreRaw = form_guard_normalize_whitespace($_POST['nombre'] ?? '');
    $emailRaw = trim((string) ($_POST['email'] ?? ''));
    $telefonoRaw = trim((string) ($_POST['telefono'] ?? ''));
    $servicioRaw = form_guard_normalize_whitespace($_POST['servicio'] ?? '');
    $mensajeRaw = form_guard_normalize_multiline($_POST['mensaje'] ?? '');
    $fechaCitaRaw = trim((string) ($_POST['fecha_llamada'] ?? ''));
    $horaCitaRaw = trim((string) ($_POST['hora_llamada'] ?? ''));
    $modoLlamadaRaw = strtolower(trim((string) ($_POST['modo_llamada'] ?? 'telefono')));
    $modoLlamada = in_array($modoLlamadaRaw, ['video', 'telefono'], true) ? $modoLlamadaRaw : 'telefono';
    $enlaceReunion = '';
    if ($modoLlamada === 'video') {
        $enlaceReunion = mce_generate_meet_link();
    }

    if ($nombreRaw === '' || $emailRaw === '' || $mensajeRaw === '') {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    if (!form_guard_validate_name($nombreRaw)) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    if (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL) || strlen($emailRaw) > 120) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    if (!form_guard_validate_phone($telefonoRaw)) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    if ($servicioRaw !== '' && (strlen($servicioRaw) > 120 || preg_match('~https?://|www\.~i', $servicioRaw) === 1)) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    $fechaCitaLabel = 'No aplica';
    $horaCitaLabel = 'No aplica';
    $zonaHorariaLabel = '';

    if ($isAgendaForm) {
        if ($fechaCitaRaw === '' || $horaCitaRaw === '') {
            redirect('contacto.php?error=6' . $redirectHash);
        }

        $fechaObj = DateTime::createFromFormat('Y-m-d', $fechaCitaRaw);
        $horaObj = DateTime::createFromFormat('H:i', $horaCitaRaw);

        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fechaCitaRaw) {
            redirect('contacto.php?error=6' . $redirectHash);
        }

        if (!$horaObj || $horaObj->format('H:i') !== $horaCitaRaw) {
            redirect('contacto.php?error=6' . $redirectHash);
        }

        $fechaCitaLabel = $fechaObj->format('Y-m-d');
        $horaCitaLabel = $horaObj->format('H:i');
    }

    $messageMin = $isAgendaForm ? 10 : 20;

    if (!form_guard_validate_message($mensajeRaw, $messageMin, 2000)) {
        redirect('contacto.php?error=6' . $redirectHash);
    }

    $emailLimit = form_guard_rate_limit('contact_form_email', strtolower($emailRaw), 3, 900);
    if (!$emailLimit['allowed']) {
        redirect('contacto.php?error=7' . $redirectHash);
    }

    if (!form_guard_verify_recaptcha($_POST['g-recaptcha-response'] ?? null)) {
        redirect('contacto.php?error=8' . $redirectHash);
    }

    $nombre = trim(strip_tags($nombreRaw));
    $email = trim(strip_tags($emailRaw));
    $telefono = trim(strip_tags($telefonoRaw));
    $servicio = trim(strip_tags($servicioRaw));
    if ($isAgendaForm && $servicio === '') {
        $servicio = 'Agenda de llamada';
    }
    $mensaje = trim(strip_tags($mensajeRaw));

    $horaCitaPlain = $horaCitaLabel;
    $mensajeParaGuardar = $mensaje;
    if ($isAgendaForm) {
        $mensajeParaGuardar = trim($mensaje . "\n\nAgenda solicitada:\nFecha: {$fechaCitaLabel}\nHora: {$horaCitaPlain}\nModalidad: " . ($modoLlamada === 'video' ? 'Videollamada' : 'Teléfono') . ($enlaceReunion !== '' ? "\nEnlace: {$enlaceReunion}" : ''));
    }

    // Crear tabla de citas si no existe
    $conn->query("
        CREATE TABLE IF NOT EXISTS citas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(120) NOT NULL,
            telefono VARCHAR(50),
            servicio VARCHAR(120),
            notas TEXT,
            estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
            tipo_llamada VARCHAR(20) NOT NULL DEFAULT 'telefono',
            enlace_reunion VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY idx_fecha_hora (fecha, hora)
        ) ENGINE=InnoDB
    ");

    $checkTipo = $conn->query("SHOW COLUMNS FROM citas LIKE 'tipo_llamada'");
    if (!$checkTipo || $checkTipo->num_rows === 0) {
        $conn->query("ALTER TABLE citas ADD COLUMN tipo_llamada VARCHAR(20) NOT NULL DEFAULT 'telefono' AFTER estado");
    }
    $checkLink = $conn->query("SHOW COLUMNS FROM citas LIKE 'enlace_reunion'");
    if (!$checkLink || $checkLink->num_rows === 0) {
        $conn->query("ALTER TABLE citas ADD COLUMN enlace_reunion VARCHAR(255) NULL AFTER tipo_llamada");
    }

    // Verificar disponibilidad
    if ($isAgendaForm) {
        $checkSlot = $conn->prepare("
            SELECT COUNT(*) 
            FROM citas 
            WHERE fecha = ? AND hora = ? 
              AND (estado IS NULL OR estado <> 'cancelada')
        ");
        if ($checkSlot) {
            $checkSlot->bind_param("ss", $fechaCitaLabel, $horaCitaLabel);
            $checkSlot->execute();
            $checkSlot->bind_result($slotCount);
            $checkSlot->fetch();
            $checkSlot->close();
            if ($slotCount > 0) {
                redirect('contacto.php?error=9' . $redirectHash);
            }
        }
    }

    // Guardar en BD mensajes
    $sql = "INSERT INTO mensajes (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        redirect('contacto.php?error=3' . $redirectHash);
    }
    $stmt->bind_param("ssss", $nombre, $email, $telefono, $mensajeParaGuardar);
    
    if ($stmt->execute()) {
        // Guardar cita si aplica
        if ($isAgendaForm) {
            $insertCita = $conn->prepare("INSERT INTO citas (fecha, hora, nombre, email, telefono, servicio, notas, estado, tipo_llamada, enlace_reunion) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', ?, ?)");
            if ($insertCita) {
                $insertCita->bind_param("sssssssss", $fechaCitaLabel, $horaCitaLabel, $nombre, $email, $telefono, $servicio, $mensaje, $modoLlamada, $enlaceReunion);
                if (!$insertCita->execute()) {
                    // si falla por duplicado, redirige a horario ocupado
                    if ($conn->errno === 1062) {
                        redirect('contacto.php?error=9' . $redirectHash);
                    }
                }
                $insertCita->close();
            }
        }

        // Enviar email con PHPMailer
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'quoted-printable';
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

    // Idioma seleccionado (enviado desde el front). Fallback: es.
    $langRaw = strtolower(trim((string)($_POST['lang'] ?? 'es')));
    $allowedLangs = ['es','en','fr','de','pt','it'];
    $lang = in_array($langRaw, $allowedLangs, true) ? $langRaw : 'es';

    $brandNames = [
        'es' => 'Proyectos MCE',
        'en' => 'MCE Projects',
        'fr' => 'Projets MCE',
        'de' => 'MCE Projekte',
        'pt' => 'Projetos MCE',
        'it' => 'Progetti MCE',
    ];
    $brand = $brandNames[$lang] ?? $brandNames['es'];
    // Sobrescribe el nombre mostrado del remitente con la marca en el idioma seleccionado
    $smtpFromName = $brand;

    $emailCopyContact = [
        'es' => [
            'subject' => "Recibimos tu solicitud en {$brand}",
            'hero' => "Hola %s, ya recibimos tu solicitud",
            'heroText' => 'Gracias por escribirnos. Revisaremos tu caso y te responderemos por este mismo medio.',
            'summaryTitle' => 'Resumen de tu consulta',
            'messageTitle' => 'Lo que nos compartiste',
            'nextTitle' => 'Qué sigue ahora',
            'nextText' => 'Revisaremos la información y te responderemos con los siguientes pasos o una propuesta inicial.',
            'footerText' => 'Este es un correo automático de confirmación. Puedes responder si quieres agregar más detalles.',
            'ctaText' => 'Ver portafolio',
            'plainIntro' => "Recibimos tu solicitud en {$brand}.",
            'plainNext' => 'Revisaremos la información y te responderemos con los siguientes pasos.'
        ],
        'en' => [
            'subject' => "We received your request at {$brand}",
            'hero' => "Hi %s, we’ve got your request",
            'heroText' => 'Thanks for reaching out. We’ll review your case and reply to you by email.',
            'summaryTitle' => 'Your request summary',
            'messageTitle' => 'What you shared',
            'nextTitle' => 'What happens next',
            'nextText' => 'We’ll review the info and reply with next steps or an initial proposal.',
            'footerText' => 'This is an automatic confirmation email. Reply here if you want to add more details.',
            'ctaText' => 'See portfolio',
            'plainIntro' => "We received your request at {$brand}.",
            'plainNext' => 'We’ll review the info and reply with next steps.'
        ],
        'fr' => [
            'subject' => "Nous avons bien reçu votre demande chez {$brand}",
            'hero' => "Bonjour %s, nous avons reçu votre demande",
            'heroText' => 'Merci de nous avoir écrit. Nous allons étudier votre besoin et vous répondre par email.',
            'summaryTitle' => 'Résumé de votre demande',
            'messageTitle' => 'Ce que vous avez partagé',
            'nextTitle' => 'Prochaine étape',
            'nextText' => 'Nous analyserons les infos et vous répondrons avec les étapes suivantes ou une proposition initiale.',
            'footerText' => 'Ceci est un email automatique de confirmation. Répondez si vous souhaitez ajouter des détails.',
            'ctaText' => 'Voir le portfolio',
            'plainIntro' => "Nous avons reçu votre demande chez {$brand}.",
            'plainNext' => 'Nous analyserons les infos et répondrons avec les prochaines étapes.'
        ],
        'de' => [
            'subject' => "Wir haben deine Anfrage bei {$brand} erhalten",
            'hero' => "Hallo %s, wir haben deine Anfrage erhalten",
            'heroText' => 'Danke für deine Nachricht. Wir prüfen dein Anliegen und melden uns per E-Mail.',
            'summaryTitle' => 'Zusammenfassung deiner Anfrage',
            'messageTitle' => 'Deine Nachricht',
            'nextTitle' => 'Wie es weitergeht',
            'nextText' => 'Wir prüfen die Infos und melden uns mit nächsten Schritten oder einem ersten Vorschlag.',
            'footerText' => 'Dies ist eine automatische Bestätigung. Antworte, wenn du weitere Details hinzufügen möchtest.',
            'ctaText' => 'Portfolio ansehen',
            'plainIntro' => "Wir haben deine Anfrage bei {$brand} erhalten.",
            'plainNext' => 'Wir prüfen die Infos und melden uns mit nächsten Schritten.'
        ],
        'pt' => [
            'subject' => "Recebemos sua solicitação na {$brand}",
            'hero' => "Olá %s, recebemos sua solicitação",
            'heroText' => 'Obrigado por escrever. Vamos analisar seu caso e responder por este e-mail.',
            'summaryTitle' => 'Resumo da sua solicitação',
            'messageTitle' => 'O que você nos contou',
            'nextTitle' => 'Próximo passo',
            'nextText' => 'Vamos revisar as informações e responder com próximos passos ou uma proposta inicial.',
            'footerText' => 'Este é um e-mail automático de confirmação. Responda se quiser acrescentar mais detalhes.',
            'ctaText' => 'Ver portfólio',
            'plainIntro' => "Recebemos sua solicitação na {$brand}.",
            'plainNext' => 'Vamos revisar as informações e responder com próximos passos.'
        ],
        'it' => [
            'subject' => "Abbiamo ricevuto la tua richiesta su {$brand}",
            'hero' => "Ciao %s, abbiamo ricevuto la tua richiesta",
            'heroText' => 'Grazie per averci scritto. Analizzeremo il tuo caso e ti risponderemo via email.',
            'summaryTitle' => 'Riepilogo della tua richiesta',
            'messageTitle' => 'Cosa ci hai scritto',
            'nextTitle' => 'Cosa succede ora',
            'nextText' => 'Rivedremo le informazioni e ti risponderemo con i prossimi passi o una proposta iniziale.',
            'footerText' => 'Questo è un email automatica di conferma. Rispondi se vuoi aggiungere altri dettagli.',
            'ctaText' => 'Vedi portfolio',
            'plainIntro' => "Abbiamo ricevuto la tua richiesta su {$brand}.",
            'plainNext' => 'Rivedremo le informazioni e ti risponderemo con i prossimi passi.'
        ],
    ];

    $emailCopyAgenda = [
        'es' => [
            'subject' => "Confirmamos tu llamada con {$brand}",
            'hero' => "Hola %s, agenda recibida",
            'heroText' => 'Revisaremos el horario solicitado y te enviaremos la confirmación con el enlace.',
            'summaryTitle' => 'Detalle de la llamada',
            'messageTitle' => 'Notas que nos compartiste',
            'nextTitle' => 'Qué sigue ahora',
            'nextText' => 'Validaremos el horario y te responderemos con la confirmación o una alternativa cercana.',
            'footerText' => 'Este es un correo automático de confirmación de agenda. Puedes responder si necesitas ajustar el horario.',
            'ctaText' => 'Ver portafolio',
            'plainIntro' => "Recibimos tu solicitud para agendar una llamada.",
            'plainNext' => 'Validaremos el horario y te responderemos con la confirmación o una alternativa.'
        ],
        'en' => [
            'subject' => "We’re confirming your call with {$brand}",
            'hero' => "Hi %s, your call request is in",
            'heroText' => 'We’ll review the time you chose and send the confirmation with the meeting link.',
            'summaryTitle' => 'Call details',
            'messageTitle' => 'Notes you shared',
            'nextTitle' => 'Next step',
            'nextText' => 'We’ll validate the slot and reply with confirmation or a nearby option.',
            'footerText' => 'This is an automatic call confirmation. Reply if you need to adjust the time.',
            'ctaText' => 'See portfolio',
            'plainIntro' => "We received your call scheduling request.",
            'plainNext' => 'We’ll validate the time and reply with confirmation or an alternative.'
        ],
        'fr' => [
            'subject' => "Nous confirmons votre appel avec {$brand}",
            'hero' => "Bonjour %s, demande d’appel reçue",
            'heroText' => 'Nous vérifierons l’horaire choisi et enverrons la confirmation avec le lien.',
            'summaryTitle' => 'Détails de l’appel',
            'messageTitle' => 'Notes partagées',
            'nextTitle' => 'Étape suivante',
            'nextText' => 'Nous validerons le créneau et répondrons avec la confirmation ou une alternative proche.',
            'footerText' => 'Ceci est un email automatique de confirmation d’appel. Répondez si vous devez ajuster l’horaire.',
            'ctaText' => 'Voir le portfolio',
            'plainIntro' => "Nous avons reçu votre demande d’appel.",
            'plainNext' => 'Nous validerons l’horaire et répondrons avec la confirmation ou une alternative.'
        ],
        'de' => [
            'subject' => "Wir bestätigen deinen Anruf mit {$brand}",
            'hero' => "Hallo %s, dein Call-Wunsch ist eingegangen",
            'heroText' => 'Wir prüfen den gewünschten Termin und senden die Bestätigung mit Meeting-Link.',
            'summaryTitle' => 'Call-Details',
            'messageTitle' => 'Notizen von dir',
            'nextTitle' => 'Nächster Schritt',
            'nextText' => 'Wir validieren den Slot und melden uns mit Bestätigung oder einer Alternative.',
            'footerText' => 'Dies ist eine automatische Call-Bestätigung. Antworte, falls du die Zeit ändern musst.',
            'ctaText' => 'Portfolio ansehen',
            'plainIntro' => "Wir haben deine Anfrage für einen Call erhalten.",
            'plainNext' => 'Wir prüfen den Slot und melden uns mit Bestätigung oder Alternative.'
        ],
        'pt' => [
            'subject' => "Confirmamos sua ligação com {$brand}",
            'hero' => "Olá %s, recebemos seu pedido de ligação",
            'heroText' => 'Vamos revisar o horário escolhido e enviar a confirmação com o link.',
            'summaryTitle' => 'Detalhes da ligação',
            'messageTitle' => 'Notas que você compartilhou',
            'nextTitle' => 'Próximo passo',
            'nextText' => 'Vamos validar o horário e responder com a confirmação ou uma alternativa próxima.',
            'footerText' => 'Este é um e-mail automático de confirmação. Responda se precisar ajustar o horário.',
            'ctaText' => 'Ver portfólio',
            'plainIntro' => "Recebemos seu pedido para agendar uma ligação.",
            'plainNext' => 'Vamos validar o horário e responder com a confirmação ou alternativa.'
        ],
        'it' => [
            'subject' => "Confermiamo la tua chiamata con {$brand}",
            'hero' => "Ciao %s, richiesta di chiamata ricevuta",
            'heroText' => 'Controlleremo l’orario scelto e ti invieremo la conferma con il link.',
            'summaryTitle' => 'Dettagli della chiamata',
            'messageTitle' => 'Note che hai condiviso',
            'nextTitle' => 'Prossimo passo',
            'nextText' => 'Valideremo lo slot e ti risponderemo con la conferma o un’alternativa vicina.',
            'footerText' => 'Questa è una conferma automatica. Rispondi se devi cambiare l’orario.',
            'ctaText' => 'Vedi portfolio',
            'plainIntro' => "Abbiamo ricevuto la tua richiesta di chiamata.",
            'plainNext' => 'Valideremo l’orario e risponderemo con la conferma o un’alternativa.'
        ],
    ];

    $copySet = $isAgendaForm ? ($emailCopyAgenda[$lang] ?? $emailCopyAgenda['es']) : ($emailCopyContact[$lang] ?? $emailCopyContact['es']);

    $labelsByLang = [
        'es' => [
            'name' => 'Nombre',
            'email' => 'Correo',
            'phone' => 'Teléfono',
            'service' => 'Servicio de interés',
            'response_channel' => 'Canal de respuesta',
            'mode' => 'Modalidad',
            'modePhone' => 'Teléfono',
            'modeVideo' => 'Videollamada',
            'date' => 'Fecha solicitada',
            'time' => 'Hora',
            'link' => 'Enlace (si aplica)',
            'linkFallback' => 'Se compartirá en la confirmación',
            'notProvided' => 'No proporcionado',
            'notSpecified' => 'No especificado',
            'notApplicable' => 'No aplica',
        ],
        'en' => [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'service' => 'Service of interest',
            'response_channel' => 'Reply channel',
            'mode' => 'Mode',
            'modePhone' => 'Phone call',
            'modeVideo' => 'Video call',
            'date' => 'Requested date',
            'time' => 'Time',
            'link' => 'Link (if applies)',
            'linkFallback' => 'Will be shared in the confirmation',
            'notProvided' => 'Not provided',
            'notSpecified' => 'Not specified',
            'notApplicable' => 'Not applicable',
        ],
        'fr' => [
            'name' => 'Nom',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'service' => "Service d'intérêt",
            'response_channel' => 'Canal de réponse',
            'mode' => 'Mode',
            'modePhone' => 'Appel téléphonique',
            'modeVideo' => 'Appel vidéo',
            'date' => 'Date demandée',
            'time' => 'Heure',
            'link' => 'Lien (le cas échéant)',
            'linkFallback' => 'Sera partagé dans la confirmation',
            'notProvided' => 'Non fourni',
            'notSpecified' => 'Non précisé',
            'notApplicable' => 'Non applicable',
        ],
        'de' => [
            'name' => 'Name',
            'email' => 'E-Mail',
            'phone' => 'Telefon',
            'service' => 'Interessierter Service',
            'response_channel' => 'Antwortkanal',
            'mode' => 'Modus',
            'modePhone' => 'Telefonat',
            'modeVideo' => 'Videoanruf',
            'date' => 'Wunschtermin',
            'time' => 'Uhrzeit',
            'link' => 'Link (falls zutreffend)',
            'linkFallback' => 'Wird in der Bestätigung geteilt',
            'notProvided' => 'Nicht angegeben',
            'notSpecified' => 'Nicht spezifiziert',
            'notApplicable' => 'Nicht anwendbar',
        ],
        'pt' => [
            'name' => 'Nome',
            'email' => 'Email',
            'phone' => 'Telefone',
            'service' => 'Serviço de interesse',
            'response_channel' => 'Canal de resposta',
            'mode' => 'Modalidade',
            'modePhone' => 'Ligação telefônica',
            'modeVideo' => 'Videochamada',
            'date' => 'Data solicitada',
            'time' => 'Horário',
            'link' => 'Link (se aplicável)',
            'linkFallback' => 'Será compartilhado na confirmação',
            'notProvided' => 'Não informado',
            'notSpecified' => 'Não especificado',
            'notApplicable' => 'Não se aplica',
        ],
        'it' => [
            'name' => 'Nome',
            'email' => 'Email',
            'phone' => 'Telefono',
            'service' => 'Servizio di interesse',
            'response_channel' => 'Canale di risposta',
            'mode' => 'Modalità',
            'modePhone' => 'Telefonata',
            'modeVideo' => 'Videochiamata',
            'date' => 'Data richiesta',
            'time' => 'Ora',
            'link' => 'Link (se applicabile)',
            'linkFallback' => 'Verrà condiviso nella conferma',
            'notProvided' => 'Non fornito',
            'notSpecified' => 'Non specificato',
            'notApplicable' => 'Non applicabile',
        ],
    ];

    $labels = $labelsByLang[$lang] ?? $labelsByLang['es'];

    $nombreHtml = htmlspecialchars($nombreMail, ENT_QUOTES, 'UTF-8');
    $emailHtml = htmlspecialchars($emailMail, ENT_QUOTES, 'UTF-8');
    $telefonoHtml = htmlspecialchars($telefonoMail !== '' ? $telefonoMail : $labels['notProvided'], ENT_QUOTES, 'UTF-8');
    $servicioHtml = htmlspecialchars($servicioMail !== '' ? $servicioMail : $labels['notSpecified'], ENT_QUOTES, 'UTF-8');
    $mensajeHtml = nl2br(htmlspecialchars($mensajeMail, ENT_QUOTES, 'UTF-8'));
    $fechaCitaHtml = htmlspecialchars($fechaCitaLabel !== '' ? $fechaCitaLabel : $labels['notApplicable'], ENT_QUOTES, 'UTF-8');
    $horaCitaHtml = htmlspecialchars($horaCitaLabel !== '' ? $horaCitaLabel : $labels['notApplicable'], ENT_QUOTES, 'UTF-8');
    $portfolioAbsoluteUrl = htmlspecialchars(app_absolute_url('portafolio.php'), ENT_QUOTES, 'UTF-8');
    $modoLlamadaHtml = htmlspecialchars($modoLlamada === 'video' ? $labels['modeVideo'] : ($isAgendaForm ? $labels['modePhone'] : $labels['notApplicable']), ENT_QUOTES, 'UTF-8');
    $enlaceReunionHtml = $enlaceReunion !== '' ? '<a href="' . htmlspecialchars($enlaceReunion, ENT_QUOTES, 'UTF-8') . '" style="color:#2563eb;">' . htmlspecialchars($enlaceReunion, ENT_QUOTES, 'UTF-8') . '</a>' : $labels['linkFallback'];

    $serviceKey = function_exists('mb_strtolower')
        ? mb_strtolower($servicioMail, 'UTF-8')
        : strtolower($servicioMail);

    $clientSubject = $copySet['subject'];
    $clientHeroTitle = sprintf($copySet['hero'], $nombreHtml);
    $clientHeroText = $copySet['heroText'];
    $clientSummaryTitle = $copySet['summaryTitle'];
    $clientMessageTitle = $copySet['messageTitle'];
    $clientNextTitle = $copySet['nextTitle'];
    $clientNextText = $copySet['nextText'];
    $clientFooterText = $copySet['footerText'];
    $clientCtaText = $copySet['ctaText'];
    $clientPlainIntro = $copySet['plainIntro'];
    $clientPlainNext = $copySet['plainNext'];

    $langTag = strtoupper($lang);

    $internalSubject = "Nuevo lead web - {$nombreMail} [{$langTag}]";
            $internalHeroTitle = "Nuevo lead registrado: {$nombreHtml}";
            $internalHeroText = 'Llegó una nueva solicitud desde el formulario de contacto. Aquí tienes el resumen para revisar el alcance y responder con contexto.';
            $internalSummaryTitle = 'Resumen del lead';
            $internalMessageTitle = 'Mensaje del cliente';
            $internalActionTitle = 'Siguiente acción sugerida';
            $internalActionText = "Revisa la necesidad inicial y responde a {$nombreHtml} para continuar la conversación.";
            $internalFooterText = "Correo interno generado automáticamente desde el formulario de contacto de {$brand}.";
            $internalCtaText = 'Responder al cliente';
            $internalPlainIntro = 'Llegó un nuevo lead desde el formulario web.';
            $internalPlainAction = "Siguiente acción sugerida: responde a {$nombreMail} y valida el alcance inicial.";

            if ($isAgendaForm) {
                $internalSubject = "Nueva agenda de llamada - {$nombreMail} [{$langTag}]";
                $internalHeroTitle = "Solicitud de llamada: {$nombreHtml}";
                $internalHeroText = 'Se registr&oacute; una agenda de llamada. Revisa horario solicitado y responde con la confirmaci&oacute;n.';
                $internalSummaryTitle = 'Resumen de la llamada';
                $internalMessageTitle = 'Notas del cliente';
                $internalActionTitle = 'Siguiente acci&oacute;n';
                $internalActionText = "Confirma el horario solicitado y env&iacute;a el enlace de reuni&oacute;n a {$nombreHtml}.";
                $internalFooterText = 'Lead interno generado autom&aacute;ticamente desde el formulario de agenda de llamada.';
                $internalCtaText = 'Responder y confirmar';
                $internalPlainIntro = 'Nueva solicitud de llamada recibida.';
                $internalPlainAction = "Confirma horario y envia enlace a {$nombreMail}.";
            } elseif (strpos($serviceKey, 'tienda') !== false || strpos($serviceKey, 'e-commerce') !== false || strpos($serviceKey, 'ecommerce') !== false) {
                $internalSubject = "Nuevo lead de tienda online - {$nombreMail} [{$langTag}]";
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
                $internalSubject = "Nuevo lead de sistema de inventario - {$nombreMail} [{$langTag}]";
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
                $internalSubject = "Nuevo lead de landing page - {$nombreMail} [{$langTag}]";
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
                $internalSubject = "Nuevo lead de desarrollo web - {$nombreMail} [{$langTag}]";
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
                            <div style="font-size:12px; letter-spacing:0.26em; text-transform:uppercase; color:#bfdbfe; margin-bottom:10px;">{$brand}</div>
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
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['name']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$nombreHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 12px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['email']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$emailHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 14px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['phone']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$telefonoHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['service']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$servicioHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 14px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['date']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$fechaCitaHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['time']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$horaCitaHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 14px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['date']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$fechaCitaHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['time']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$horaCitaHtml}</div>
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
                . "{$labels['name']}: {$nombreMail}\n"
                . "{$labels['email']}: {$emailMail}\n"
                . "{$labels['phone']}: " . ($telefonoMail !== '' ? $telefonoMail : $labels['notProvided']) . "\n"
                . "{$labels['service']}: " . ($servicioMail !== '' ? $servicioMail : $labels['notSpecified']) . "\n"
                . "{$labels['date']}: " . ($fechaCitaLabel !== '' ? $fechaCitaLabel : $labels['notApplicable']) . "\n"
                . "{$labels['time']}: " . ($horaCitaLabel !== '' ? $horaCitaLabel : $labels['notApplicable']) . "\n"
                . "{$labels['mode']}: " . ($modoLlamada === 'video' ? $labels['modeVideo'] : ($isAgendaForm ? $labels['modePhone'] : $labels['notApplicable'])) . "\n"
                . "{$labels['link']}: " . ($enlaceReunion !== '' ? $enlaceReunion : $labels['linkFallback']) . "\n\n"
                . "{$internalMessageTitle}:\n{$mensajeMail}\n\n"
                . "{$internalPlainAction}";
            
            $mail->send();

            try {
                $mail->clearAllRecipients();
                $mail->clearReplyTos();

                $mail->addAddress($emailMail, $nombreMail);
                $mail->addReplyTo($smtpUser, $brand);
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
                            <div style="font-size:12px; letter-spacing:0.26em; text-transform:uppercase; color:#bfdbfe; margin-bottom:10px;">{$brand}</div>
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
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['service']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$servicioHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['response_channel']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$emailHtml}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" valign="top" style="padding:0 8px 14px 0;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['mode']}</div>
                                                <div style="font-size:16px; font-weight:700; color:#0f172a;">{$modoLlamadaHtml}</div>
                                            </div>
                                        </td>
                                        <td width="50%" valign="top" style="padding:0 0 14px 8px;">
                                            <div style="background-color:#ffffff; border:1px solid #dbeafe; border-radius:16px; padding:16px;">
                                                <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#64748b; margin-bottom:8px;">{$labels['link']}</div>
                                                <div style="font-size:14px; font-weight:700; color:#0f172a;">{$enlaceReunionHtml}</div>
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
                $mail->AltBody =
                    "{$clientPlainIntro}\n\n"
                    . "{$labels['service']}: " . ($servicioMail !== '' ? $servicioMail : $labels['notSpecified']) . "\n"
                    . "{$labels['response_channel']}: {$emailMail}\n"
                    . "{$labels['mode']}: " . ($modoLlamada === 'video' ? $labels['modeVideo'] : ($isAgendaForm ? $labels['modePhone'] : $labels['notApplicable'])) . "\n"
                    . "{$labels['link']}: " . ($enlaceReunion !== '' ? $enlaceReunion : $labels['linkFallback']) . "\n"
                    . "{$labels['date']}: " . ($fechaCitaLabel !== '' ? $fechaCitaLabel : $labels['notApplicable']) . "\n"
                    . "{$labels['time']}: " . ($horaCitaLabel !== '' ? $horaCitaLabel : $labels['notApplicable']) . "\n\n"
                    . "{$clientSummaryTitle}:\n{$mensajeMail}\n\n"
                    . "{$clientPlainNext}\n\n"
                    . "— {$brand}";

                $mail->send();
            } catch (Exception $clientMailException) {
                error_log('Error al enviar confirmacion al cliente: ' . $clientMailException->getMessage());
            }

            redirect('contacto.php?success=1' . $redirectHash);
        } catch (Exception $e) {
            error_log('Error al enviar correo: ' . $e->getMessage());
            if (!empty($mail->ErrorInfo)) {
                error_log('PHPMailer ErrorInfo: ' . $mail->ErrorInfo);
            }
            if (!empty($smtpDebugLog)) {
                error_log("SMTP debug:\n" . implode("\n", $smtpDebugLog));
            }
            $code = (empty($smtpUser) || empty($smtpPass)) ? 4 : 5;
            redirect('contacto.php?error=' . $code . $redirectHash);
        }
    } else {
        redirect('contacto.php?error=3' . $redirectHash);
    }
    
    $stmt->close();
} else {
    redirect('contacto.php');
}
?>
