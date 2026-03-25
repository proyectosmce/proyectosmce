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
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-headset mr-2 text-yellow-300"></i> <span class="i18n-ct-badge" data-i18n="ct-badge">Contacto · Proyectos MCE</span>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight i18n-ct-hero-title" data-i18n="ct-hero-title">Agenda un diagnóstico técnico</h1>
                <p class="text-lg text-blue-50 max-w-3xl i18n-ct-hero-sub" data-i18n="ct-hero-sub">
                    Cuéntanos qué necesitas automatizar o lanzar. Te respondemos con esfuerzo estimado, riesgos visibles, tecnología recomendada y primeros pasos.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-ct-chip1" data-i18n="ct-chip1"><i class="fas fa-bolt mr-2 text-yellow-300"></i><span>Respuesta en &lt; 24h</span></span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-ct-chip2" data-i18n="ct-chip2"><i class="fas fa-diagram-project mr-2 text-yellow-300"></i><span>Discovery + propuesta</span></span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-ct-chip3" data-i18n="ct-chip3"><i class="fas fa-lock mr-2 text-yellow-300"></i><span>Confidencialidad garantizada</span></span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100 i18n-ct-card-title" data-i18n="ct-card-title">Contacta directo</p>
                            <p class="text-2xl font-semibold text-white i18n-ct-card-sub" data-i18n="ct-card-sub">Equipo técnico listo para ayudarte</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-phone"></i></span>
                            <div>
                                <p class="font-semibold i18n-ct-phone" data-i18n="ct-phone">teléfono</p>
                                <p class="text-sm text-blue-100">
                                    <a class="hover:underline" href="tel:+573114125971">+57 311 412 59 71</a>
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-envelope"></i></span>
                            <div>
                                <p class="font-semibold i18n-ct-mail" data-i18n="ct-mail">Correo</p>
                                <p class="text-sm text-blue-100">
                                    <a class="hover:underline" href="mailto:proyectosmceaa@gmail.com">proyectosmceaa@gmail.com</a>
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fab fa-whatsapp"></i></span>
                            <div>
                                <p class="font-semibold i18n-ct-wa" data-i18n="ct-wa">WhatsApp</p>
                                <p class="text-sm text-blue-100">
                                    <a class="hover:underline" href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener">wa.me/573114125971</a>
                                </p>
                            </div>
                        </li>
                    </ul>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100 i18n-ct-hours" data-i18n="ct-hours">Horario</p>
                            <p class="font-semibold i18n-ct-hours-detail" data-i18n="ct-hours-detail">Lunes a Viernes · 8:00 - 17:00 <br>Sábados · 9:00 - 13:00</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
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
                <span class="i18n-ct-error-4" data-i18n="ct-error-4">El formulario se guardó, pero falta configurar el correo del sitio. Inténtalo más tarde.</span>
            <?php elseif ($_GET['error'] == 5): ?>
                <span class="i18n-ct-error-5" data-i18n="ct-error-5">El formulario se guardó, pero no se pudo conectar con el servicio de correo. Revisa SMTP_USER, SMTP_PASS y la App Password.</span>
            <?php elseif ($_GET['error'] == 6): ?>
                <span class="i18n-ct-error-6" data-i18n="ct-error-6">No pudimos validar el envío. Revisa los datos e intenta nuevamente.</span>
            <?php elseif ($_GET['error'] == 7): ?>
                <span class="i18n-ct-error-7" data-i18n="ct-error-7">Has enviado demasiados mensajes en poco tiempo. Espera unos minutos antes de intentar otra vez.</span>
            <?php elseif ($_GET['error'] == 8): ?>
                <span class="i18n-ct-error-8" data-i18n="ct-error-8">Debes completar la verificación reCAPTCHA antes de enviar el formulario.</span>
            <?php elseif ($_GET['error'] == 9): ?>
                <span class="i18n-ct-error-9" data-i18n="ct-error-9">El horario elegido ya no está disponible. Por favor elige otra hora.</span>
            <?php else: ?>
                <span class="i18n-ct-error-default" data-i18n="ct-error-default">Hubo un error. Por favor intenta nuevamente.</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Agenda de llamada -->
<section id="agenda-llamada" class="max-w-7xl mx-auto px-4 mt-10 lg:mt-14">
    <div class="grid lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-5 space-y-4 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl shadow-2xl p-8 border border-white/10">
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide i18n-ct-call-label" data-i18n="ct-call-label">Coordina tu llamada</p>
            <h2 class="text-3xl font-bold leading-tight i18n-ct-call-title" data-i18n="ct-call-title">Elige fecha y hora para hablar</h2>
            <p class="text-blue-50 i18n-ct-call-desc" data-i18n="ct-call-desc">Agendamos una llamada corta para revisar tu necesidad y darte siguientes pasos. Confirmamos por correo con el enlace de la reunión.</p>
            <ul class="space-y-3 text-blue-100">
                <li class="flex items-start gap-3"><span class="mt-1 text-yellow-300"><i class="fas fa-clock"></i></span><span class="i18n-ct-call-b1" data-i18n="ct-call-b1">Duración estimada: 20 minutos.</span></li>
                <li class="flex items-start gap-3"><span class="mt-1 text-yellow-300"><i class="fas fa-video"></i></span><span class="i18n-ct-call-b2" data-i18n="ct-call-b2">Formato: videollamada o teléfono, según prefieras.</span></li>
                <li class="flex items-start gap-3"><span class="mt-1 text-yellow-300"><i class="fas fa-bolt"></i></span><span class="i18n-ct-call-b3" data-i18n="ct-call-b3">Confirmación rápida con link y agenda en tu correo.</span></li>
            </ul>
        </div>

        <div class="lg:col-span-7 order-2 lg:order-1">
            <form id="agenda-form" action="enviar-contacto.php" method="POST" class="bg-white p-8 rounded-2xl mce-rounded-panel shadow-2xl border border-slate-100 overflow-hidden space-y-6">
                <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($contactFormGuard['token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="form_context" value="agenda">
                <input type="hidden" name="redirect_anchor" value="form-feedback">
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
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-phone" data-i18n="ct-form-phone">teléfono</label>
                        <input type="tel" name="telefono" maxlength="25" inputmode="tel" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-phone-ph" placeholder="Opcional">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-service" data-i18n="ct-form-service">Servicio de interés (opcional)</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="" class="i18n-ct-form-service-opt0" data-i18n="ct-form-service-opt0">Solo llamada de exploración</option>
                            <?php
                            $servicios = $conn->query("SELECT titulo FROM servicios ORDER BY orden");
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
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-pref" data-i18n="ct-form-pref">¿Prefieres videollamada o teléfono?</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <input type="radio" name="modo_llamada" value="video" class="h-4 w-4 text-blue-600" checked>
                                <span class="i18n-ct-form-pref-video" data-i18n="ct-form-pref-video">Videollamada (te enviamos el enlace)</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <input type="radio" name="modo_llamada" value="telefono" class="h-4 w-4 text-blue-600">
                                <span class="i18n-ct-form-pref-phone" data-i18n="ct-form-pref-phone">Solo llamada telefónica</span>
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-form-obj" data-i18n="ct-form-obj">Objetivo de la llamada *</label>
                        <textarea name="mensaje" rows="4" required minlength="10" maxlength="2000" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" data-i18n-placeholder="ct-form-obj-ph" placeholder="Cuéntanos en breve qué necesitas revisar en la llamada."></textarea>
                    </div>
                </div>

                <?php if ($contactRecaptchaEnabled): ?>
                <div class="pt-2">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                </div>
                <?php else: ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 i18n-ct-form-recaptcha-missing" data-i18n="ct-form-recaptcha-missing">
                    reCAPTCHA es obligatorio, pero no está configurado correctamente en este entorno.
                </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-600 i18n-ct-form-note" data-i18n="ct-form-note">Confirmaremos tu llamada por correo con el enlace de reunión.</p>
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
                <p class="text-gray-600 mt-2 i18n-ct-mail-desc" data-i18n="ct-mail-desc">Envíanos detalles y te respondemos por el mismo medio en menos de 24h.</p>
            </div>
            <a href="#agenda-llamada" class="inline-flex items-center text-blue-700 font-semibold hover:text-blue-900 i18n-ct-mail-altlink" data-i18n="ct-mail-altlink">
                <i class="fas fa-phone-alt mr-2"></i> ¿Mejor una llamada? Agenda aquí
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 order-2 lg:order-1">
            <form id="contact-form" action="enviar-contacto.php" method="POST" class="bg-white p-8 rounded-2xl mce-rounded-panel shadow-2xl border border-slate-100 overflow-hidden space-y-6">
                <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($contactFormGuard['token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="form_context" value="contacto">
                <input type="hidden" name="redirect_anchor" value="form-feedback">
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
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-phone" data-i18n="ct-mail-phone">Teléfono</label>
                        <input type="tel" name="telefono" maxlength="25" inputmode="tel" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold i18n-ct-mail-service" data-i18n="ct-mail-service">¿Qué servicio te interesa?</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="" class="i18n-ct-mail-service-opt0" data-i18n="ct-mail-service-opt0">Seleccionar...</option>
                            <?php
                            $servicios = $conn->query("SELECT titulo FROM servicios ORDER BY orden");
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
                        <textarea name="mensaje" rows="5" required minlength="20" maxlength="2000" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus-border-blue-600" data-i18n-placeholder="ct-mail-msg-ph" placeholder="Cuéntanos los detalles y cómo podemos ayudarte."></textarea>
                    </div>
                </div>

                <?php if ($contactRecaptchaEnabled): ?>
                <div class="pt-2">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                </div>
                <?php else: ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 i18n-ct-mail-recaptcha-missing" data-i18n="ct-mail-recaptcha-missing">
                    reCAPTCHA es obligatorio, pero no está configurado correctamente en este entorno.
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
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide i18n-ct-info-label" data-i18n="ct-info-label">Información clave</p>
                <h3 class="text-2xl font-bold text-slate-900 i18n-ct-info-title" data-i18n="ct-info-title">¿Qué recibes al escribirnos?</h3>
                <ul class="space-y-3 text-gray-800 mt-3">
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span class="i18n-ct-info-b1" data-i18n="ct-info-b1">Respuesta personalizada con una ruta inicial y esfuerzos aproximados.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span class="i18n-ct-info-b2" data-i18n="ct-info-b2">Reunión virtual de discovery para entender procesos y objetivos.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span class="i18n-ct-info-b3" data-i18n="ct-info-b3">Documento de alcance con los próximos pasos para aprobar o iterar.</span></li>
                </ul>
                <div class="mt-6 p-4 rounded-xl bg-blue-50 text-blue-800 flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <p class="text-sm i18n-ct-info-nda" data-i18n="ct-info-nda">Si necesitas NDA antes de compartir detalles, indícalo en el mensaje y lo enviamos.</p>
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

<?php if ($contactRecaptchaEnabled): ?>
<script>
(() => {
    const forms = ['contact-form', 'agenda-form'].map(id => document.getElementById(id)).filter(Boolean);
    let placeholders = Array.from(document.querySelectorAll('.g-recaptcha[data-sitekey]'));

    function renderAll() {
        if (!window.grecaptcha) return;
        placeholders.forEach(el => {
            if (el.dataset.recaptchaId) return;
            const id = grecaptcha.render(el, { sitekey: el.dataset.sitekey });
            el.dataset.recaptchaId = id;
        });
        document.querySelectorAll('.recaptcha-old').forEach(old => old.remove());
    }
    function loadRecaptcha(lang) {
        const existing = document.querySelector('script[data-mce-recaptcha]');
        if (existing && existing.dataset.lang === lang) {
            renderAll();
            return;
        }
        if (existing) existing.remove();
        delete window.grecaptcha;
        delete window.___grecaptcha_cfg;
        const s = document.createElement('script');
        s.src = `https://www.google.com/recaptcha/api.js?onload=mceRenderRecaptcha&render=explicit&hl=${lang}`;
        s.async = true;
        s.defer = true;
        s.dataset.mceRecaptcha = '1';
        s.dataset.lang = lang;
        document.head.appendChild(s);
        window.mceRenderRecaptcha = renderAll;
    }
    loadRecaptcha(localStorage.getItem('siteLang') || 'es');
    window.addEventListener('mce-lang-changed', (e) => {
        const lang = e.detail?.lang || 'es';
        const fresh = [];
        placeholders.forEach(el => {
            const clone = document.createElement('div');
            clone.className = el.className;
            clone.dataset.sitekey = el.dataset.sitekey;
            el.insertAdjacentElement('afterend', clone);
            el.classList.add('recaptcha-old');
            fresh.push(clone);
        });
        placeholders = fresh;
        loadRecaptcha(lang);
    });

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

    // Validación de envío con reCAPTCHA
    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (typeof window.grecaptcha === 'undefined') {
                event.preventDefault();
                alert('reCAPTCHA aun no termina de cargar. Intenta nuevamente en unos segundos.');
                return;
            }
            const widgetId = form.querySelector('.g-recaptcha')?.dataset.recaptchaId;
            const response = widgetId !== undefined ? grecaptcha.getResponse(Number(widgetId)) : '';
            if (!response) {
                event.preventDefault();
                alert('Completa la verificación reCAPTCHA antes de enviar.');
            }
        });
    });

    // Mensajes de validación en el idioma seleccionado
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
<?php endif; ?>

<?php include 'includes/footer.php'; ?>


































