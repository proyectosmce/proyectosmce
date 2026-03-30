<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/form-guard.php'; ?>
<?php
$contactFormGuard = form_guard_issue('contacto');
$contactRecaptchaEnabled = form_guard_recaptcha_enabled();
$selectedService = trim((string) ($_GET['servicio'] ?? ''));

// Crear tabla de citas si no existe (agenda de llamadas)
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

// Asegurar columnas nuevas en instalaciones previas
$colCheck = $conn->query("SHOW COLUMNS FROM citas LIKE 'tipo_llamada'");
if (!$colCheck || $colCheck->num_rows === 0) {
    $conn->query("ALTER TABLE citas ADD COLUMN tipo_llamada VARCHAR(20) NOT NULL DEFAULT 'telefono' AFTER estado");
}
$colCheck = $conn->query("SHOW COLUMNS FROM citas LIKE 'enlace_reunion'");
if (!$colCheck || $colCheck->num_rows === 0) {
    $conn->query("ALTER TABLE citas ADD COLUMN enlace_reunion VARCHAR(255) NULL AFTER tipo_llamada");
}

// Cargar citas ocupadas proximos 14 dias
$bookedSlotsByDate = [];
$bookedQuery = $conn->query("
    SELECT fecha, DATE_FORMAT(hora, '%H:%i') AS hora
    FROM citas
    WHERE fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
      AND (estado IS NULL OR estado <> 'cancelada')
");
if ($bookedQuery) {
    while ($row = $bookedQuery->fetch_assoc()) {
        $d = $row['fecha'];
        $h = $row['hora'];
        if (!isset($bookedSlotsByDate[$d])) {
            $bookedSlotsByDate[$d] = [];
        }
        $bookedSlotsByDate[$d][] = $h;
    }
}
$availableHours = ['08:00','09:00','10:00','11:00','12:00','14:00','15:00','16:00','17:00'];
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Contacto -->
<section class="relative overflow-hidden bg-gradient-to-br from-brand-ink via-[#120c2c] to-brand-dark text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-hero-mesh opacity-80"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-brand-primary/25 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-brand-accent/20 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-headset mr-2 text-brand-accent"></i> <span class="i18n-ct-badge" data-i18n="ct-badge">Contacto � Proyectos MCE</span>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight i18n-ct-hero-title" data-i18n="ct-hero-title">Agenda un diagn�stico t�cnico</h1>
                <p class="text-lg text-white/80 max-w-3xl i18n-ct-hero-sub" data-i18n="ct-hero-sub">
                    Cu�ntanos qu� necesitas automatizar o lanzar. Te respondemos con esfuerzo estimado, riesgos visibles, tecnolog�a recomendada y primeros pasos.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-ct-chip1" data-i18n="ct-chip1"><i class="fas fa-bolt mr-2 text-brand-accent"></i><span>Respuesta en &lt; 24h</span></span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-ct-chip2" data-i18n="ct-chip2"><i class="fas fa-diagram-project mr-2 text-brand-accent"></i><span>Discovery + propuesta</span></span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-ct-chip3" data-i18n="ct-chip3"><i class="fas fa-lock mr-2 text-brand-accent"></i><span>Confidencialidad garantizada</span></span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="relative overflow-hidden rounded-3xl bg-white/10 ring-1 ring-white/15 backdrop-blur-2xl p-8 shadow-glow space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/70 i18n-ct-card-title" data-i18n="ct-card-title">Contacta directo</p>
                            <p class="text-2xl font-semibold text-white i18n-ct-card-sub" data-i18n="ct-card-sub">Equipo t�cnico listo para ayudarte</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white shadow-lg overflow-hidden mce-photo-badge">
                            <img src="<?php echo app_url('imag/MCE.jpg'); ?>" alt="MCE" class="w-full h-full object-cover">
                        </span>
                    </div>
                    <ul class="space-y-3 text-white/80">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-brand-accent"><i class="fas fa-phone"></i></span>
                            <div>
                                <p class="font-semibold i18n-ct-phone" data-i18n="ct-phone">tel�fono</p>
                                <p class="text-sm text-white/70">
                                    <a class="hover:underline" href="tel:+573114125971">+57 311 412 59 71</a>
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-brand-accent"><i class="fas fa-envelope"></i></span>
                            <div>
                                <p class="font-semibold i18n-ct-mail" data-i18n="ct-mail">Correo</p>
                                <p class="text-sm text-white/70">
                                    <a class="hover:underline" href="mailto:proyectosmceaa@gmail.com">proyectosmceaa@gmail.com</a>
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-brand-accent"><i class="fab fa-whatsapp"></i></span>
                            <div>
                                <p class="font-semibold i18n-ct-wa" data-i18n="ct-wa">WhatsApp</p>
                                <p class="text-sm text-white/70">
                                    <a class="hover:underline" href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener">wa.me/573114125971</a>
                                </p>
                            </div>
                        </li>
                    </ul>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/70 i18n-ct-hours" data-i18n="ct-hours">Horario</p>
                            <p class="font-semibold i18n-ct-hours-detail" data-i18n="ct-hours-detail">Lunes a Viernes � 8:00 - 17:00 <br>S�bados � 9:00 - 13:00</p>
                        </div>
                        <i class="fas fa-arrow-right text-brand-accent"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feedback de formularios -->
<section id="form-feedback" class="max-w-7xl mx-auto px-4 mt-8">
    <?php if (isset($_GET['success'])): ?>
        <div
            data-auto-dismiss="5000"
            data-query-flag="success"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 transition-opacity duration-500"
        >
            <span class="i18n-ct-alert-success" data-i18n="ct-alert-success">Mensaje enviado. Te contactaremos a la brevedad.</span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php if ($_GET['error'] == 4): ?>
                <span class="i18n-ct-error-4" data-i18n="ct-error-4">El formulario se guard�, pero falta configurar el correo del sitio. Int�ntalo m�s tarde.</span>
            <?php elseif ($_GET['error'] == 5): ?>
                <span class="i18n-ct-error-5" data-i18n="ct-error-5">El formulario se guard�, pero no se pudo conectar con el servicio de correo. Revisa SMTP_USER, SMTP_PASS y la App Password.</span>
            <?php elseif ($_GET['error'] == 6): ?>
                <span class="i18n-ct-error-6" data-i18n="ct-error-6">No pudimos validar el env�o. Revisa los datos e intenta nuevamente.</span>
            <?php elseif ($_GET['error'] == 7): ?>
                <span class="i18n-ct-error-7" data-i18n="ct-error-7">Has enviado demasiados mensajes en poco tiempo. Espera unos minutos antes de intentar otra vez.</span>
            <?php elseif ($_GET['error'] == 8): ?>
                <span class="i18n-ct-error-8" data-i18n="ct-error-8">Debes completar la verificaci�n reCAPTCHA antes de enviar el formulario.</span>
            <?php elseif ($_GET['error'] == 9): ?>
                <span class="i18n-ct-error-9" data-i18n="ct-error-9">El horario elegido ya no est� disponible. Por favor elige otra hora.</span>
            <?php else: ?>
                <span class="i18n-ct-error-default" data-i18n="ct-error-default">Hubo un error. Por favor intenta nuevamente.</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Agenda de llamada -->
<section id="agenda-llamada" class="max-w-7xl mx-auto px-4 mt-10 lg:mt-14">
    <div class="grid lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-5 space-y-4 bg-gradient-to-br from-brand-ink via-[#120c2c] to-brand-dark text-white rounded-2xl shadow-2xl p-8 border border-white/10">
            <p class="text-sm font-semibold text-white/70 uppercase tracking-wide i18n-ct-call-label" data-i18n="ct-call-label">Coordina tu llamada</p>
            <h2 class="text-3xl font-bold leading-tight i18n-ct-call-title" data-i18n="ct-call-title">Elige fecha y hora para hablar</h2>
            <p class="text-white/80 i18n-ct-call-desc" data-i18n="ct-call-desc">Agendamos una llamada corta para revisar tu necesidad y darte siguientes pasos. Confirmamos por correo con el enlace de la reuni�n.</p>
            <ul class="space-y-3 text-white/70">
                <li class="flex items-start gap-3"><span class="mt-1 text-brand-accent"><i class="fas fa-clock"></i></span><span class="i18n-ct-call-b1" data-i18n="ct-call-b1">Duraci�n estimada: 20 minutos.</span></li>
                <li class="flex items-start gap-3"><span class="mt-1 text-brand-accent"><i class="fas fa-video"></i></span><span class="i18n-ct-call-b2" data-i18n="ct-call-b2">Formato: videollamada o tel�fono, seg�n prefieras.</span></li>
                <li class="flex items-start gap-3"><span class="mt-1 text-brand-accent"><i class="fas fa-bolt"></i></span><span class="i18n-ct-call-b3" data-i18n="ct-call-b3">Confirmaci�n r�pida con link y agenda en tu correo.</span></li>
            </ul>
        </div>

        <div class="lg:col-span-7 order-2 lg:order-1">
            <form id="agenda-form" action="enviar-contacto.php" method="POST" class="bg-white p-8 rounded-2xl mce-rounded-panel shadow-2xl border border-slate-100 overflow-hidden space-y-6">
                <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($contactFormGuard['token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="form_context" value="agenda">
                <input type="hidden" name="redirect_anchor" value="form-feedback">
                <input type="hidden" name="lang" id="agenda-lang" value="es">
                <div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
                    <label for="agenda_company_website">No llenes este campo</label>
                    <input id="agenda_company_website" type="text" name="company_website" tabindex="-1" autocomplete="off">
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-name" data-i18n="ct-form-name">Nombre *</label>
                        <input type="text" name="nombre" required minlength="2" maxlength="100" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-name-ph" placeholder="Tu nombre completo">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-email" data-i18n="ct-form-email">Email *</label>
                        <input type="email" name="email" required maxlength="120" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-email-ph" placeholder="tucorreo@ejemplo.com">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-phone" data-i18n="ct-form-phone">tel�fono</label>
                        <input type="tel" name="telefono" maxlength="25" inputmode="tel" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-phone-ph" placeholder="Opcional">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-service" data-i18n="ct-form-service">Servicio de inter�s (opcional)</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="" class="i18n-ct-form-service-opt0" data-i18n="ct-form-service-opt0">Solo llamada de exploraci�n</option>
                            <?php
                            $servicios = $conn->query("SELECT titulo FROM servicios WHERE LOWER(titulo) <> 'tiendas online' ORDER BY orden");
                            while ($s = $servicios->fetch_assoc()) {
                                $serviceTitle = (string) ($s['titulo'] ?? '');
                                ?>
                                <option value="<?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedService !== '' && strcasecmp($selectedService, $serviceTitle) === 0 ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-date" data-i18n="ct-form-date">Fecha de la llamada *</label>
                        <input type="date" id="agenda-fecha" name="fecha_llamada" required min="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-time" data-i18n="ct-form-time">Hora disponible *</label>
                        <select id="agenda-hora" name="hora_llamada" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <p id="agenda-hora-msg" class="text-sm text-red-600 mt-1 hidden i18n-ct-form-time-msg" data-i18n="ct-form-time-msg">No hay horarios disponibles para esta fecha.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-pref" data-i18n="ct-form-pref">�Prefieres videollamada o tel�fono?</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <input type="radio" name="modo_llamada" value="video" class="h-4 w-4 text-blue-600" checked>
                                <span class="i18n-ct-form-pref-video" data-i18n="ct-form-pref-video">Videollamada (te enviamos el enlace)</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <input type="radio" name="modo_llamada" value="telefono" class="h-4 w-4 text-blue-600">
                                <span class="i18n-ct-form-pref-phone" data-i18n="ct-form-pref-phone">Solo llamada telef�nica</span>
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-obj" data-i18n="ct-form-obj">Objetivo de la llamada *</label>
                        <?php
                            $ctaParam = strtolower(trim((string)($_GET['cta'] ?? '')));
                            $agendaPrefill = $ctaParam === 'agenda'
                                ? "Hola, quiero agendar una llamada de asesoría.\n\nTema a revisar:\nObjetivos que quiero lograr:\nFecha y hora preferidas:\nPreferencia (teléfono o videollamada):\n¿Enlace o documentos relevantes?:"
                                : '';
                        ?>
                        <textarea name="mensaje" rows="8" required minlength="10" maxlength="2000" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" style="min-height:200px;" data-i18n-placeholder="ct-form-obj-ph" placeholder="Cuéntanos en breve qué necesitas revisar en la llamada."><?php echo htmlspecialchars($agendaPrefill, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>

                <?php if ($contactRecaptchaEnabled): ?>
                <div class="pt-2">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                </div>
                <?php else: ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 i18n-ct-form-recaptcha-missing" data-i18n="ct-form-recaptcha-missing">
                    reCAPTCHA es obligatorio, pero no est� configurado correctamente en este entorno.
                </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-600 i18n-ct-form-note" data-i18n="ct-form-note">Confirmaremos tu llamada por correo con el enlace de reuni�n.</p>
                    <button type="submit" id="agenda-submit" <?php echo $contactRecaptchaEnabled ? '' : 'disabled'; ?> class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition w-full sm:w-auto disabled:cursor-not-allowed disabled:bg-slate-400 i18n-ct-form-submit" data-i18n="ct-form-submit">
                        <i class="fas fa-calendar-check mr-2"></i> <span class="i18n-ct-form-submit" data-i18n="ct-form-submit">Confirmar llamada</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Separador visual -->
<div class="max-w-7xl mx-auto px-4 mt-14">
    <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
</div>

<!-- Formulario de contacto (correo) -->
<section id="contacto-form" class="max-w-7xl mx-auto px-4 mt-12 pb-16">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-8 lg:p-10 mb-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide i18n-ct-mail-label" data-i18n="ct-mail-label">Contacto por correo</p>
                <h2 class="text-3xl font-bold text-slate-900 i18n-ct-mail-title" data-i18n="ct-mail-title">Prefieres escribirnos</h2>
                <p class="text-gray-600 mt-2 i18n-ct-mail-desc" data-i18n="ct-mail-desc">Env�anos detalles y te respondemos por el mismo medio en menos de 24h.</p>
            </div>
            <a href="#agenda-llamada" class="inline-flex items-center text-blue-700 font-semibold hover:text-blue-900 i18n-ct-mail-altlink" data-i18n="ct-mail-altlink">
                <i class="fas fa-phone-alt mr-2"></i> �Mejor una llamada? Agenda aqu�
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 order-2 lg:order-1">
            <form id="contact-form" action="enviar-contacto.php" method="POST" class="bg-white p-8 rounded-2xl mce-rounded-panel shadow-2xl border border-slate-100 overflow-hidden space-y-6">
                <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($contactFormGuard['token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="form_context" value="contacto">
                <input type="hidden" name="redirect_anchor" value="form-feedback">
                <input type="hidden" name="lang" id="contact-lang" value="es">
                <div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
                    <label for="company_website">No llenes este campo</label>
                    <input id="company_website" type="text" name="company_website" tabindex="-1" autocomplete="off">
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-name" data-i18n="ct-mail-name">Nombre *</label>
                        <input type="text" name="nombre" required minlength="2" maxlength="100" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-name-ph" placeholder="Tu nombre completo">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-email" data-i18n="ct-mail-email">Email *</label>
                        <input type="email" name="email" required maxlength="120" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-email-ph" placeholder="tucorreo@ejemplo.com">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-phone" data-i18n="ct-mail-phone">Tel�fono</label>
                        <input type="tel" name="telefono" maxlength="25" inputmode="tel" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-service" data-i18n="ct-mail-service">�Qu� servicio te interesa?</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="" class="i18n-ct-mail-service-opt0" data-i18n="ct-mail-service-opt0">Seleccionar...</option>
                            <?php
                            $servicios = $conn->query("SELECT titulo FROM servicios WHERE LOWER(titulo) <> 'tiendas online' ORDER BY orden");
                            while ($s = $servicios->fetch_assoc()) {
                                $serviceTitle = (string) ($s['titulo'] ?? '');
                                ?>
                                <option value="<?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selectedService !== '' && strcasecmp($selectedService, $serviceTitle) === 0 ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-msg" data-i18n="ct-mail-msg">Mensaje *</label>
                        <textarea name="mensaje" rows="8" required minlength="20" maxlength="2000" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus-border-blue-600" style="min-height:200px;" data-i18n-placeholder="ct-mail-msg-ph" placeholder="Cu�ntanos los detalles y c�mo podemos ayudarte."></textarea>
                    </div>
                </div>

                <?php if ($contactRecaptchaEnabled): ?>
                <div class="pt-2">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                </div>
                <?php else: ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 i18n-ct-mail-recaptcha-missing" data-i18n="ct-mail-recaptcha-missing">
                    reCAPTCHA es obligatorio, pero no est� configurado correctamente en este entorno.
                </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-600 i18n-ct-mail-disclaimer" data-i18n="ct-mail-disclaimer">Al enviar aceptas ser contactado por nuestro equipo.</p>
                    <button type="submit" id="contact-submit" <?php echo $contactRecaptchaEnabled ? '' : 'disabled'; ?> class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition w-full sm:w-auto disabled:cursor-not-allowed disabled:bg-slate-400">
                        <i class="fas fa-paper-plane mr-2"></i> <span class="i18n-ct-mail-submit" data-i18n="ct-mail-submit">Enviar mensaje</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-5 order-1 lg:order-2">
            <div class="bg-white rounded-2xl mce-rounded-panel shadow-2xl border border-slate-100 overflow-hidden p-8 space-y-4">
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide i18n-ct-info-label" data-i18n="ct-info-label">Informaci�n clave</p>
                <h3 class="text-2xl font-bold text-slate-900 i18n-ct-info-title" data-i18n="ct-info-title">�Qu� recibes al escribirnos?</h3>
                <ul class="space-y-3 text-gray-800 mt-3">
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span class="i18n-ct-info-b1" data-i18n="ct-info-b1">Respuesta personalizada con una ruta inicial y esfuerzos aproximados.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span class="i18n-ct-info-b2" data-i18n="ct-info-b2">Reuni�n virtual de discovery para entender procesos y objetivos.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span class="i18n-ct-info-b3" data-i18n="ct-info-b3">Documento de alcance con los pr�ximos pasos para aprobar o iterar.</span></li>
                </ul>
                <div class="mt-6 p-4 rounded-xl bg-blue-50 text-blue-800 flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <p class="text-sm i18n-ct-info-nda" data-i18n="ct-info-nda">Si necesitas NDA antes de compartir detalles, ind�calo en el mensaje y lo enviamos.</p>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-green-500 text-white font-semibold shadow hover:bg-green-600 transition">
                        <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                    </a>
                    <a href="mailto:proyectosmceaa@gmail.com" class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-slate-200 text-slate-900 font-semibold hover:bg-slate-50 transition">
                        <i class="fas fa-envelope mr-2"></i> Correo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// sincroniza el idioma actual con los formularios para enviarlo al backend
(function() {
    const getLang = () => (window.mceCurrentLang || localStorage.getItem('siteLang') || 'es');
    const setLangInputs = () => {
        const lang = getLang();
        const agenda = document.getElementById('agenda-lang');
        const contact = document.getElementById('contact-lang');
        if (agenda) agenda.value = lang;
        if (contact) contact.value = lang;
    };
    setLangInputs();
    window.addEventListener('mce-lang-changed', setLangInputs);
})();
</script>

<script>
// Horarios y validaci�n de formularios (reCAPTCHA se maneja en assets/js/mce-recaptcha.js)
(() => {
    const forms = ['contact-form', 'agenda-form'].map(id => document.getElementById(id)).filter(Boolean);

    // Horarios de agenda
    const availableHours = <?php echo json_encode($availableHours, JSON_UNESCAPED_UNICODE); ?>;
    const bookedSlots = <?php echo json_encode($bookedSlotsByDate, JSON_UNESCAPED_UNICODE); ?>;
    const horaSelect = document.getElementById('agenda-hora');
    const fechaInput = document.getElementById('agenda-fecha');
    const horaMsg = document.getElementById('agenda-hora-msg');

    const renderHours = (dateStr) => {
        if (!horaSelect || !fechaInput) return;
        const booked = bookedSlots[dateStr] || [];
        let options = availableHours.filter(h => !booked.includes(h));
        const todayStr = new Date().toISOString().slice(0, 10);
        if (dateStr === todayStr) {
            const now = new Date();
            const currentHHMM = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            options = options.filter(h => h > currentHHMM);
        }
        horaSelect.innerHTML = '';
        if (!options.length) {
            horaSelect.disabled = true;
            horaMsg?.classList.remove('hidden');
            return;
        }
        horaSelect.disabled = false;
        horaMsg?.classList.add('hidden');
        options.forEach(h => {
            const opt = document.createElement('option');
            opt.value = h;
            opt.textContent = h;
            horaSelect.appendChild(opt);
        });
    };
    if (fechaInput) {
        fechaInput.addEventListener('change', (e) => renderHours(e.target.value));
        renderHours(fechaInput.value || fechaInput.getAttribute('min'));
    }

    // Mensajes de validaci�n en el idioma seleccionado
    const attachValidationMessages = () => {
        forms.forEach((form) => {
            form?.querySelectorAll('input, textarea, select').forEach((el) => {
                if (el.dataset.mceValidationAttached) return;
                el.dataset.mceValidationAttached = '1';
                el.addEventListener('invalid', () => {
                    const t = window.mceTranslations || {};
                    const req = t['ct-field-required'] || 'Completa este campo.';
                    const email = t['ct-field-email'] || req;
                    const minTpl = t['ct-field-minlength'] || req;
                    if (el.validity.valueMissing) {
                        el.setCustomValidity(req);
                    } else if (el.validity.typeMismatch && el.type === 'email') {
                        el.setCustomValidity(email);
                    } else if (el.validity.tooShort) {
                        const min = el.getAttribute('minlength') || '';
                        el.setCustomValidity(minTpl.replace('{min}', min));
                    } else {
                        el.setCustomValidity('');
                    }
                });
                el.addEventListener('input', () => el.setCustomValidity(''));
            });
        });
    };
    attachValidationMessages();
    window.addEventListener('mce-lang-changed', attachValidationMessages);
})();
</script>

<script>
// Prefill mensaje seg�n servicio o CTA (plan / agenda) en el idioma activo
(() => {
    const qs = new URLSearchParams(window.location.search);
    const rawService = qs.get('servicio');
    const ctaKey = (qs.get('cta') || '').trim().toLowerCase();
    if (!rawService && !ctaKey) return;

    const normalize = (str) => (str || '').normalize('NFKD').replace(/\p{M}+/gu, '').trim().toLowerCase();
    const lang = (window.mceCurrentLang || localStorage.getItem('siteLang') || 'es');

    // Plantillas por servicio
    const tplService = {
        es: {
            'desarrollo web a medida': 'Hola, quiero un desarrollo web a medida.\n\nObjetivo principal:\nP�blico objetivo:\nFunciones clave:\nIntegraciones (APIs/pagos):\nPlazo ideal y presupuesto aproximado:',
            'sistemas de inventario': 'Hola, necesito un sistema de inventario.\n\nN�mero de productos/SKUs:\nPuntos de venta o canales:\nAlertas y reportes deseados:\nIntegraciones con contabilidad/tiendas:\nPlazo ideal y presupuesto aproximado:',
            'landing pages': 'Hola, necesito una landing page.\n\nObjetivo de la campa�a (leads/ventas):\nP�blico y propuesta de valor:\nSecciones requeridas:\nIntegraciones (formularios/CRM/pagos):\nFecha de lanzamiento y presupuesto:',
            'mantenimiento web': 'Hola, busco mantenimiento web.\n\nTipo de sitio y tecnolog�a:\nAlcance (monitoreo, soporte, mejoras):\nFrecuencia de actualizaciones:\nAccesos disponibles (hosting/Git):\nPresupuesto mensual o por horas:'
        },
        en: {
            'desarrollo web a medida': 'Hi, I need a custom web development project.\n\nMain goal:\nTarget audience:\nKey features:\nIntegrations (APIs/payments):\nIdeal timeline and budget:',
            'sistemas de inventario': 'Hi, I need an inventory system.\n\nNumber of products/SKUs:\nSales channels or POS:\nAlerts and reports needed:\nIntegrations with accounting/stores:\nIdeal timeline and budget:',
            'landing pages': 'Hi, I need a landing page.\n\nCampaign goal (leads/sales):\nAudience and value proposition:\nRequired sections:\nIntegrations (forms/CRM/payments):\nLaunch date and budget:',
            'mantenimiento web': 'Hi, I need website maintenance.\n\nSite type and tech stack:\nScope (monitoring, support, improvements):\nUpdate frequency:\nAccess available (hosting/Git):\nMonthly or hourly budget:'
        },
        de: {
            'desarrollo web a medida': 'Hallo, ich brauche eine ma�geschneiderte Webentwicklung.\n\nHauptziel:\nZielgruppe:\nSchl�sselfunktionen:\nIntegrationen (APIs/Zahlungen):\nGew�nschter Zeitplan und Budget:',
            'sistemas de inventario': 'Hallo, ich ben�tige ein Warenwirtschaftssystem.\n\nAnzahl Produkte/SKUs:\nVertriebskan�le oder POS:\nGew�nschte Alarme und Berichte:\nIntegrationen mit Buchhaltung/Shops:\nGew�nschter Zeitplan und Budget:',
            'landing pages': 'Hallo, ich brauche eine Landing Page.\n\nKampagnenziel (Leads/Verk�ufe):\nZielgruppe und Value Proposition:\nBen�tigte Abschnitte:\nIntegrationen (Formulare/CRM/Zahlungen):\nLaunch-Datum und Budget:',
            'mantenimiento web': 'Hallo, ich suche Website-Wartung.\n\nSeitentyp und Tech-Stack:\nUmfang (Monitoring, Support, Verbesserungen):\nUpdate-H�ufigkeit:\nZug�nge vorhanden (Hosting/Git):\nMonats- oder Stundensatz:'
        },
        fr: {
            'desarrollo web a medida': 'Bonjour, je souhaite un d�veloppement web sur mesure.\n\nObjectif principal :\nAudience cible :\nFonctionnalit�s cl�s :\nInt�grations (APIs/paiements) :\nD�lai id�al et budget :',
            'sistemas de inventario': 'Bonjour, j�ai besoin d�un syst�me d�inventaire.\n\nNombre de produits/SKU :\nCanaux de vente ou PDV :\nAlertes et rapports souhait�s :\nInt�grations avec comptabilit�/boutiques :\nD�lai id�al et budget :',
            'landing pages': 'Bonjour, j�ai besoin d�une landing page.\n\nObjectif de la campagne (leads/ventes) :\nAudience et proposition de valeur :\nSections requises :\nInt�grations (formulaires/CRM/paiements) :\nDate de lancement et budget :',
            'mantenimiento web': 'Bonjour, je cherche de la maintenance web.\n\nType de site et techno :\nPort�e (monitoring, support, am�liorations) :\nFr�quence des mises � jour :\nAcc�s disponibles (h�bergement/Git) :\nBudget mensuel ou horaire :'
        },
        pt: {
            'desarrollo web a medida': 'Ol�, preciso de um desenvolvimento web sob medida.\n\nObjetivo principal:\nP�blico-alvo:\nFuncionalidades-chave:\nIntegra��es (APIs/pagamentos):\nPrazo ideal e or�amento:',
            'sistemas de inventario': 'Ol�, preciso de um sistema de invent�rio.\n\nQuantidade de produtos/SKUs:\nCanais de venda ou PDV:\nAlertas e relat�rios desejados:\nIntegra��es com contabilidade/lojas:\nPrazo ideal e or�amento:',
            'landing pages': 'Ol�, preciso de uma landing page.\n\nObjetivo da campanha (leads/vendas):\nP�blico e proposta de valor:\nSe��es necess�rias:\nIntegra��es (formul�rios/CRM/pagamentos):\nData de lan�amento e or�amento:',
            'mantenimiento web': 'Ol�, preciso de manuten��o web.\n\nTipo de site e stack:\nEscopo (monitoramento, suporte, melhorias):\nFrequ�ncia de atualiza��es:\nAcessos dispon�veis (hosting/Git):\nOr�amento mensal ou por hora:'
        },
        it: {
            'desarrollo web a medida': 'Ciao, ho bisogno di uno sviluppo web su misura.\n\nObiettivo principale:\nPubblico di riferimento:\nFunzionalit� chiave:\nIntegrazioni (API/pagamenti):\nTempistica ideale e budget:',
            'sistemas de inventario': 'Ciao, mi serve un sistema di inventario.\n\nNumero di prodotti/SKU:\nCanali di vendita o POS:\nAvvisi e report desiderati:\nIntegrazioni con contabilit�/negozi:\nTempistica ideale e budget:',
            'landing pages': 'Ciao, mi serve una landing page.\n\nObiettivo della campagna (lead/vendite):\nPubblico e proposta di valore:\nSezioni richieste:\nIntegrazioni (moduli/CRM/pagamenti):\nData di lancio e budget:',
            'mantenimiento web': 'Ciao, cerco manutenzione web.\n\nTipo di sito e stack:\nAmbito (monitoraggio, supporto, miglioramenti):\nFrequenza degli aggiornamenti:\nAccessi disponibili (hosting/Git):\nBudget mensile o a ore:'
        }
    };

    // Plantillas por CTA
        const tplCta = {
        plan: {
            es: 'Hola, quiero armar mi plan de proyecto.\n\nTipo de proyecto (web/app):\nObjetivo principal:\nAlcance deseado y entregables:\nPlazo ideal y presupuesto aproximado:\nPreferencia de comunicación:',
            en: 'Hi, I want to build a project plan.\n\nProject type (web/app):\nMain goal:\nDesired scope and deliverables:\nIdeal timeline and rough budget:\nPreferred communication channel:',
            de: 'Hallo, ich möchte meinen Projektplan erstellen.\n\nProjekttyp (Web/App):\nHauptziel:\nGewünschter Umfang und Deliverables:\nWunschtermin und grobes Budget:\nBevorzugter Kommunikationskanal:',
            fr: 'Bonjour, je veux élaborer mon plan de projet.\n\nType de projet (web/app) :\nObjectif principal :\nPérimètre souhaité et livrables :\nDélai idéal et budget estimé :\nCanal de communication préféré :',
            pt: 'Olá, quero montar meu plano de projeto.\n\nTipo de projeto (web/app):\nObjetivo principal:\nEscopo desejado e entregáveis:\nPrazo ideal e orçamento aproximado:\nCanal de comunicação preferido:',
            it: 'Ciao, voglio creare il mio piano di progetto.\n\nTipo di progetto (web/app):\nObiettivo principale:\nAmbito desiderato e deliverable:\nTempistica ideale e budget indicativo:\nCanale di comunicazione preferito:'
        },
        agenda: {
            es: 'Hola, quiero agendar una llamada de asesoría.\n\nTema a revisar:\nObjetivos que quiero lograr:\nFecha y hora preferidas:\nPreferencia (teléfono o videollamada):\n¿Enlace o documentos relevantes?:',
            en: 'Hi, I want to schedule a consulting call.\n\nTopic to review:\nGoals I want to achieve:\nPreferred date and time:\nPreferred channel (phone or video):\nAny relevant link or document?:',
            de: 'Hallo, ich möchte einen Beratungstermin buchen.\n\nThema zur Besprechung:\nZiele, die ich erreichen möchte:\nBevorzugtes Datum und Uhrzeit:\nKanal (Telefon oder Video):\nRelevanter Link oder Dokument?:',
            fr: 'Bonjour, je souhaite planifier un appel de conseil.\n\nSujet à traiter :\nObjectifs à atteindre :\nDate et heure préférées :\nCanal préféré (téléphone ou visio) :\nLien ou document pertinent ? :',
            pt: 'Olá, quero agendar uma chamada de consultoria.\n\nTema a revisar:\nObjetivos que quero atingir:\nData e hora preferidas:\nCanal preferido (telefone ou vídeo):\nAlgum link ou documento relevante?:',
            it: 'Ciao, voglio programmare una call di consulenza.\n\nTema da rivedere:\nObiettivi che voglio raggiungere:\nData e ora preferite:\nCanale preferito (telefono o video):\nLink o documenti rilevanti?:'
        }
    };

    const templatesAllLangs = [];

    const applyTemplate = (langCurrent) => {
        let messageTemplate = null;
        let targetForms = ['contact-form', 'agenda-form'];
        let selectedServiceSlug = null;

        if (rawService) {
            const slug = normalize(rawService);
            selectedServiceSlug = slug;
            messageTemplate = tplService[langCurrent]?.[slug] || tplService.es?.[slug];
        } else if (ctaKey && tplCta[ctaKey]) {
            messageTemplate = tplCta[ctaKey][langCurrent] || tplCta[ctaKey].es;
            targetForms = ctaKey === 'plan' ? ['contact-form'] : ['agenda-form'];
        }

        // Si no hay CTA relevante, no hacemos nada
        if (!messageTemplate) return;

        const fillField = (formId) => {
            const form = document.getElementById(formId);
            if (!form) return;
            const msg = form.querySelector('textarea[name="mensaje"]');
            if (msg && ctaKey === 'agenda') {
                // Siempre forzamos el texto de agenda según el idioma activo
                msg.value = messageTemplate;
            } else if (msg) {
                const current = msg.value.trim();
                if (current.length === 0) msg.value = messageTemplate;
            }
            if (selectedServiceSlug) {
                const select = form.querySelector('select[name="servicio"]');
                if (select) {
                    const target = Array.from(select.options).find(opt => normalize(opt.textContent) === selectedServiceSlug || normalize(opt.value) === selectedServiceSlug);
                    if (target) select.value = target.value;
                }
            }
        };

        targetForms.forEach(fillField);
    };

    applyTemplate(lang);
    window.addEventListener('mce-lang-changed', (e) => {
        const newLang = e.detail?.lang || 'es';
        applyTemplate(newLang);
    });
</script>

<?php include 'includes/footer.php'; ?>








































