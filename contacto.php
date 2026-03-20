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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY idx_fecha_hora (fecha, hora)
    ) ENGINE=InnoDB
");

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
                    <i class="fas fa-headset mr-2 text-yellow-300"></i> Contacto · Proyectos MCE
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Agenda un diagnóstico técnico</h1>
                <p class="text-lg text-blue-50 max-w-3xl">
                    Cuéntanos qué necesitas automatizar o lanzar. Te respondemos con esfuerzo estimado, riesgos visibles, tecnología recomendada y primeros pasos.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-bolt mr-2 text-yellow-300"></i>Respuesta en &lt; 24h</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-diagram-project mr-2 text-yellow-300"></i>Discovery + propuesta</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-lock mr-2 text-yellow-300"></i>Confidencialidad garantizada</span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Contacta directo</p>
                            <p class="text-2xl font-semibold text-white">Equipo técnico listo para ayudarte</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-phone"></i></span>
                            <div>
                                <p class="font-semibold">Teléfono</p>
                                <p class="text-sm text-blue-100">
                                    <a class="hover:underline" href="tel:+573114125971">+57 311 412 59 71</a>
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-envelope"></i></span>
                            <div>
                                <p class="font-semibold">Correo</p>
                                <p class="text-sm text-blue-100">
                                    <a class="hover:underline" href="mailto:proyectosmceaa@gmail.com">proyectosmceaa@gmail.com</a>
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fab fa-whatsapp"></i></span>
                            <div>
                                <p class="font-semibold">WhatsApp</p>
                                <p class="text-sm text-blue-100">
                                    <a class="hover:underline" href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener">wa.me/573114125971</a>
                                </p>
                            </div>
                        </li>
                    </ul>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Horario</p>
                            <p class="font-semibold">Lunes a Viernes · 8:00 - 17:00 <br>Sabados · 9:00 - 13:00</p>
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
            Mensaje enviado. Te contactaremos a la brevedad.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php if ($_GET['error'] == 4): ?>
                El formulario se guardo, pero falta configurar el correo del sitio. Intentalo mas tarde.
            <?php elseif ($_GET['error'] == 5): ?>
                El formulario se guardo, pero no se pudo conectar con el servicio de correo. Revisa SMTP_USER, SMTP_PASS y la App Password.
            <?php elseif ($_GET['error'] == 6): ?>
                No pudimos validar el envio. Revisa los datos e intenta nuevamente.
            <?php elseif ($_GET['error'] == 7): ?>
                Has enviado demasiados mensajes en poco tiempo. Espera unos minutos antes de intentar otra vez.
            <?php elseif ($_GET['error'] == 8): ?>
                Debes completar la verificacion reCAPTCHA antes de enviar el formulario.
            <?php elseif ($_GET['error'] == 9): ?>
                El horario elegido ya no está disponible. Por favor elige otra hora.
            <?php else: ?>
                Hubo un error. Por favor intenta nuevamente.
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Agenda de llamada -->
<section id="agenda-llamada" class="max-w-7xl mx-auto px-4 mt-10 lg:mt-14">
    <div class="grid lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-5 space-y-4 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl shadow-2xl p-8 border border-white/10">
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">Coordina tu llamada</p>
            <h2 class="text-3xl font-bold leading-tight">Elige fecha y hora para hablar</h2>
            <p class="text-blue-50">Agendamos una llamada corta para revisar tu necesidad y darte siguientes pasos. Confirmamos por correo con el enlace de la reuni&oacute;n.</p>
            <ul class="space-y-3 text-blue-100">
                <li class="flex items-start gap-3"><span class="mt-1 text-yellow-300"><i class="fas fa-clock"></i></span><span>Duraci&oacute;n estimada: 20 minutos.</span></li>
                <li class="flex items-start gap-3"><span class="mt-1 text-yellow-300"><i class="fas fa-video"></i></span><span>Formato: videollamada o tel&eacute;fono, seg&uacute;n prefieras.</span></li>
                <li class="flex items-start gap-3"><span class="mt-1 text-yellow-300"><i class="fas fa-bolt"></i></span><span>Confirmaci&oacute;n r&aacute;pida con link y agenda en tu correo.</span></li>
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
                        <label class="block text-gray-800 mb-2 font-semibold">Nombre *</label>
                        <input type="text" name="nombre" required minlength="2" maxlength="100" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Email *</label>
                        <input type="email" name="email" required maxlength="120" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Tel&eacute;fono</label>
                        <input type="tel" name="telefono" maxlength="25" inputmode="tel" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Servicio de inter&eacute;s (opcional)</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Solo llamada de exploraci&oacute;n</option>
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
                        <label class="block text-gray-800 mb-2 font-semibold">Fecha de la llamada *</label>
                        <input type="date" id="agenda-fecha" name="fecha_llamada" required min="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Hora disponible *</label>
                        <select id="agenda-hora" name="hora_llamada" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        </select>
                        <p id="agenda-hora-msg" class="text-sm text-red-600 mt-2 hidden">No hay horarios disponibles para esta fecha.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-800 mb-2 font-semibold">Objetivo de la llamada *</label>
                        <textarea name="mensaje" rows="4" required minlength="10" maxlength="2000" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600" placeholder="Cu&eacute;ntanos en breve qu&eacute; necesitas revisar en la llamada."></textarea>
                    </div>
                </div>

                <?php if ($contactRecaptchaEnabled): ?>
                    <div class="pt-2">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                        reCAPTCHA es obligatorio, pero no est&aacute; configurado correctamente en este entorno.
                    </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-600">Confirmaremos tu llamada por correo con el enlace de reuni&oacute;n.</p>
                    <button type="submit" id="agenda-submit" <?php echo $contactRecaptchaEnabled ? '' : 'disabled'; ?> class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition w-full sm:w-auto disabled:cursor-not-allowed disabled:bg-slate-400">
                        <i class="fas fa-calendar-check mr-2"></i> Confirmar llamada
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
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Contacto por correo</p>
                <h2 class="text-3xl font-bold text-slate-900">Prefieres escribirnos</h2>
                <p class="text-gray-600 mt-2">Envíanos detalles y te respondemos por el mismo medio en menos de 24h.</p>
            </div>
            <a href="#agenda-llamada" class="inline-flex items-center text-blue-700 font-semibold hover:text-blue-900">
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
                        <label class="block text-gray-800 mb-2 font-semibold">Nombre *</label>
                        <input type="text" name="nombre" required minlength="2" maxlength="100" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Email *</label>
                        <input type="email" name="email" required maxlength="120" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Teléfono</label>
                        <input type="tel" name="telefono" maxlength="25" inputmode="tel" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">¿Qué servicio te interesa?</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Seleccionar...</option>
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
                        <label class="block text-gray-800 mb-2 font-semibold">Mensaje *</label>
                        <textarea name="mensaje" rows="5" required minlength="20" maxlength="2000" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"></textarea>
                    </div>
                </div>

                <?php if ($contactRecaptchaEnabled): ?>
                    <div class="pt-2">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                        reCAPTCHA es obligatorio, pero no est\u00e1 configurado correctamente en este entorno.
                    </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-600">Al enviar aceptas ser contactado por nuestro equipo.</p>
                    <button type="submit" id="contact-submit" <?php echo $contactRecaptchaEnabled ? '' : 'disabled'; ?> class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition w-full sm:w-auto disabled:cursor-not-allowed disabled:bg-slate-400">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar mensaje
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-5 order-1 lg:order-2">
            <div class="bg-white rounded-2xl mce-rounded-panel shadow-2xl border border-slate-100 overflow-hidden p-8 space-y-4">
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Información clave</p>
                <h3 class="text-2xl font-bold text-slate-900">¿Qué recibes al escribirnos?</h3>
                <ul class="space-y-3 text-gray-800 mt-3">
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span>Respuesta personalizada con una ruta inicial y esfuerzos aproximados.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span>Reunión virtual de discovery para entender procesos y objetivos.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span>Documento de alcance con los próximos pasos para aprobar o iterar.</span></li>
                </ul>
                <div class="mt-6 p-4 rounded-xl bg-blue-50 text-blue-800 flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <p class="text-sm">Si necesitas NDA antes de compartir detalles, indícalo en el mensaje y lo enviamos.</p>
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
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(() => {
    const forms = ['contact-form', 'agenda-form']
        .map(id => document.getElementById(id))
        .filter(Boolean);

    // Slots disponibles
    const availableHours = <?php echo json_encode($availableHours, JSON_UNESCAPED_UNICODE); ?>;
    const bookedSlots = <?php echo json_encode($bookedSlotsByDate, JSON_UNESCAPED_UNICODE); ?>;
    const horaSelect = document.getElementById('agenda-hora');
    const fechaInput = document.getElementById('agenda-fecha');
    const horaMsg = document.getElementById('agenda-hora-msg');

    const renderHours = (dateStr) => {
        if (!horaSelect || !fechaInput) return;
        const booked = bookedSlots[dateStr] || [];
        const options = availableHours.filter(h => !booked.includes(h));
        horaSelect.innerHTML = '';
        if (options.length === 0) {
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
        fechaInput.addEventListener('change', (e) => {
            renderHours(e.target.value);
        });
        renderHours(fechaInput.value || fechaInput.getAttribute('min'));
    }

    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (typeof window.grecaptcha === 'undefined') {
                event.preventDefault();
                alert('reCAPTCHA aun no termina de cargar. Intenta nuevamente en unos segundos.');
                return;
            }

            const widget = form.querySelector('.g-recaptcha');
            const widgetId = widget?.getAttribute('data-widget-id');
            const response = typeof window.grecaptcha.getResponse === 'function'
                ? window.grecaptcha.getResponse(widgetId ? Number(widgetId) : undefined)
                : '';

            if (!response) {
                event.preventDefault();
                alert('Completa la verificacion reCAPTCHA antes de enviar.');
            }
        });
    });
})();
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
