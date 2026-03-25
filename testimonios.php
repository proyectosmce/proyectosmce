<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php require_once 'includes/form-guard.php'; ?>
<?php require_once 'includes/testimonial-helpers.php'; ?>
<?php
ensureTestimonialsSchema($conn);

// Manejo de envío de testimonios (solo alta)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'nuevo_testimonio') {
    if (!form_guard_honeypot_is_clear($_POST['company_website'] ?? '')) {
        redirect('testimonios.php?error=validation#form-testimonio');
    }

    $guardCheck = form_guard_verify('testimonios', $_POST['form_token'] ?? null, 3);
    if (!$guardCheck['ok']) {
        redirect('testimonios.php?error=validation#form-testimonio');
    }

    $nombre  = form_guard_normalize_whitespace($_POST['nombre'] ?? '');
    $mensaje = form_guard_normalize_multiline($_POST['mensaje'] ?? '');
    $proyId  = (int) ($_POST['proyecto_id'] ?? 0);
    $valor   = (int) ($_POST['valoracion'] ?? 5);
    $valor   = max(1, min(5, $valor));

    if (!form_guard_validate_name($nombre, 2, 100) || !form_guard_validate_message($mensaje, 30, 1200) || $proyId <= 0) {
        redirect('testimonios.php?error=validation#form-testimonio');
    }

    if (!form_guard_verify_recaptcha($_POST['g-recaptcha-response'] ?? null)) {
        redirect('testimonios.php?error=captcha#form-testimonio');
    }

    $ipLimit = form_guard_rate_limit('testimonial_form_ip', form_guard_client_ip(), 3, 3600);
    if (!$ipLimit['allowed']) {
        redirect('testimonios.php?error=rate#form-testimonio');
    }

    $signatureName = function_exists('mb_strtolower')
        ? mb_strtolower($nombre, 'UTF-8')
        : strtolower($nombre);
    $signatureLimit = form_guard_rate_limit(
        'testimonial_form_signature',
        $signatureName . '|' . $proyId,
        2,
        86400
    );
    if (!$signatureLimit['allowed']) {
        redirect('testimonios.php?error=rate#form-testimonio');
    }

    $empresa = '';
    if ($proyId > 0) {
        if ($stProj = $conn->prepare('SELECT titulo FROM proyectos WHERE id = ? LIMIT 1')) {
            $stProj->bind_param('i', $proyId);
            $stProj->execute();
            $stProj->bind_result($projTitulo);
            if ($stProj->fetch()) {
                $empresa = $projTitulo;
            }
            $stProj->close();
        }
    }

    if ($empresa === '') {
        redirect('testimonios.php?error=validation#form-testimonio');
    }

    $nombre = trim(strip_tags($nombre));
    $mensaje = trim(strip_tags($mensaje));

    if ($nombre !== '' && $mensaje !== '' && $proyId > 0) {
        if ($stmt = $conn->prepare('INSERT INTO testimonios (nombre, testimonio, proyecto_id, empresa, valoracion, destacado, aprobado) VALUES (?, ?, ?, ?, ?, 0, 0)')) {
            $stmt->bind_param('ssisi', $nombre, $mensaje, $proyId, $empresa, $valor);
            $stmt->execute();
            $stmt->close();
            header('Location: testimonios.php?testimonio=ok#testimonios');
            exit;
        }
    }

    redirect('testimonios.php?error=validation#form-testimonio');
}

$testimonios     = $conn->query("SELECT t.id, t.nombre, t.testimonio, t.valoracion, t.likes, COALESCE(p.titulo, t.empresa, 'Proyecto MCE') AS proyecto FROM testimonios t LEFT JOIN proyectos p ON t.proyecto_id = p.id WHERE t.aprobado = 1 ORDER BY t.destacado DESC, t.created_at DESC LIMIT 9");
$projectOptions  = fetchProjectDropdownOptions($conn);
$testimonioOk    = isset($_GET['testimonio']) && $_GET['testimonio'] === 'ok';
$testimonioError = $_GET['error'] ?? '';
$testError       = !$testimonios instanceof mysqli_result;
$hasTestimonios  = !$testError && $testimonios->num_rows > 0;
$hasProjectOptions = !empty($projectOptions);
$testimonialFormGuard = form_guard_issue('testimonios');
$testimonialRecaptchaEnabled = form_guard_recaptcha_enabled();
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Testimonios -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-comment-dots mr-2 text-yellow-300"></i> <span class="i18n-ts-badge" data-i18n="ts-badge">Testimonios · Proyectos MCE</span>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight i18n-ts-hero-title" data-i18n="ts-hero-title">Lo que dicen quienes operan con nuestro software</h1>
                <p class="text-lg text-blue-50 max-w-3xl i18n-ts-hero-sub" data-i18n="ts-hero-sub">
                    Comentarios de equipos que hoy usan las soluciones en producción: releases frecuentes, soporte real y métricas visibles.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50 i18n-ts-b1" data-i18n="ts-b1">Acompañamiento end-to-end en cada release.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50 i18n-ts-b2" data-i18n="ts-b2">Equipos de negocio y tecnología siempre informados.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50 i18n-ts-b3" data-i18n="ts-b3">Feedback directo que usamos para mejorar el producto.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="#testimonios" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition i18n-ts-btn-read" data-i18n="ts-btn-read">
                        <i class="fas fa-comments mr-2"></i> Leer testimonios
                    </a>
                    <a href="#form-testimonio" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition i18n-ts-btn-share" data-i18n="ts-btn-share">
                        <i class="fas fa-handshake mr-2"></i> Compartir mi experiencia
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100 i18n-ts-side-title" data-i18n="ts-side-title">Lo que valoran</p>
                            <p class="text-2xl font-semibold text-white i18n-ts-side-desc" data-i18n="ts-side-desc">Claridad, tiempos y soporte cercano</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-clock"></i></span>
                            <div>
                                <p class="font-semibold i18n-ts-side-b1-title" data-i18n="ts-side-b1-title">Visibilidad constante</p>
                                <p class="text-sm text-blue-100 i18n-ts-side-b1-text" data-i18n="ts-side-b1-text">Demos en cada sprint, tableros y comunicación directa.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-shield-alt"></i></span>
                            <div>
                                <p class="font-semibold i18n-ts-side-b2-title" data-i18n="ts-side-b2-title">Cuidado de la operación</p>
                                <p class="text-sm text-blue-100 i18n-ts-side-b2-text" data-i18n="ts-side-b2-text">Backups, roles, permisos y monitoreo activo.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-face-smile"></i></span>
                            <div>
                                <p class="font-semibold i18n-ts-side-b3-title" data-i18n="ts-side-b3-title">Experiencias claras</p>
                                <p class="text-sm text-blue-100 i18n-ts-side-b3-text" data-i18n="ts-side-b3-text">UX/UI pensado para equipos operativos y clientes finales.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="#form-testimonio" class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between hover:bg-white/10 transition">
                        <div>
                            <p class="text-sm text-blue-100 i18n-ts-side-cta-label" data-i18n="ts-side-cta-label">Participa</p>
                            <p class="font-semibold i18n-ts-side-cta-text" data-i18n="ts-side-cta-text">Deja tu testimonio y ayuda a otros equipos</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="testimonios" class="max-w-7xl mx-auto px-4 py-12">
<?php if ($testimonioOk): ?>
    <div id="alert-testimonio" class="mb-8 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 i18n-ts-alert-ok" data-i18n="ts-alert-ok">
        Gracias. Tu testimonio fue recibido y quedó pendiente de aprobación.
    </div>
<?php endif; ?>

<?php if ($testimonioError !== ''): ?>
    <div id="alert-testimonio-error" class="mb-8 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3">
        <?php if ($testimonioError === 'rate'): ?>
            <span class="i18n-ts-alert-rate" data-i18n="ts-alert-rate">Has enviado demasiados testimonios en poco tiempo. Espera antes de intentar nuevamente.</span>
        <?php elseif ($testimonioError === 'captcha'): ?>
            <span class="i18n-ts-alert-captcha" data-i18n="ts-alert-captcha">Debes completar la verificación reCAPTCHA antes de enviar el testimonio.</span>
        <?php else: ?>
            <span class="i18n-ts-alert-generic" data-i18n="ts-alert-generic">No pudimos validar el testimonio. Revisa los datos del formulario e intenta otra vez.</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div>
            <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide i18n-ts-exp-label" data-i18n="ts-exp-label">Experiencias reales</p>
            <h2 class="text-3xl font-bold text-slate-900 i18n-ts-exp-title" data-i18n="ts-exp-title">Historias de equipos que confiaron en Proyectos MCE</h2>
            <p class="text-gray-700 mt-2 max-w-3xl i18n-ts-exp-desc" data-i18n="ts-exp-desc">Lee cómo usamos entregas iterativas, acompañamiento y soporte para llevar sus proyectos a producción.</p>
        </div>
        <a href="#form-testimonio" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
            <i class="fas fa-comments mr-2"></i> <span class="i18n-ts-exp-btn" data-i18n="ts-exp-btn">Quiero contar mi experiencia</span>
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php if ($testError): ?>
            <div class="md:col-span-3 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 i18n-ts-alert-load" data-i18n="ts-alert-load">
                No pudimos cargar los testimonios en este momento.
            </div>
        <?php elseif (!$hasTestimonios): ?>
            <div class="md:col-span-3 bg-white border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-600 i18n-ts-empty" data-i18n="ts-empty">
                Aún no hay testimonios. ¡Sé el primero en dejar el tuyo!
            </div>
        <?php else: ?>
            <?php while ($t = $testimonios->fetch_assoc()):
                $initial = strtoupper(mb_substr($t['nombre'] ?? 'U', 0, 1, 'UTF-8'));
            ?>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-semibold"><?php echo $initial; ?></span>
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="text-sm text-blue-600"><?php echo htmlspecialchars($t['proyecto'] ?? 'Proyecto MCE', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex text-yellow-400 justify-end">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i < (int) $t['valoracion'] ? '' : '-o'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <?php
                            $v = (int) $t['valoracion'];
                            if ($v <= 1) { $lbl = 'No recomiendo'; $cls = 'text-red-600'; }
                            else if ($v == 2) { $lbl = 'Poco recomendable'; $cls = 'text-red-600'; }
                            else if ($v == 3) { $lbl = 'Neutral'; $cls = 'text-gray-600'; }
                            else if ($v == 4) { $lbl = 'Recomiendo'; $cls = 'text-green-600'; }
                            else { $lbl = 'Sí recomiendo'; $cls = 'text-green-600'; }
                        ?>
                        <p class="text-xs font-semibold <?php echo $cls; ?> mt-1 ts-rating-card" data-rating="<?php echo $v; ?>"><?php echo $v; ?> / 5 · <?php echo $lbl; ?></p>
                    </div>
                </div>
                <?php
                    $projName = $t['proyecto'] ?? 'su proyecto';
                    $textoFinal = "Yo, {$t['nombre']} dueño de {$projName}, {$t['testimonio']}";
                ?>
                <p class="text-gray-700 leading-relaxed text-sm mb-4">"<?php echo nl2br(htmlspecialchars($textoFinal, ENT_QUOTES, 'UTF-8')); ?>"</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-500"></i> <span class="i18n-ts-verified" data-i18n="ts-verified">Testimonio verificado</span>
                    </span>
                    <button type="button" class="flex items-center gap-2 rounded-full border border-red-100 bg-red-50 px-3 py-1 text-sm font-semibold text-red-600 hover:bg-red-100 transition like-btn" data-like-id="<?php echo (int) $t['id']; ?>">
                        <i class="fas fa-heart"></i>
                        <span class="like-count"><?php echo (int) ($t['likes'] ?? 0); ?></span>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-8" id="form-testimonio">
        <h3 class="text-2xl font-bold text-gray-900 mb-2 i18n-ts-form-title" data-i18n="ts-form-title">Deja tu testimonio</h3>
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm flex items-start gap-3">
            <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-500 text-white rounded-full text-xs mt-0.5">
                <i class="fas fa-exclamation"></i>
            </span>
            <span class="i18n-ts-form-warning" data-i18n="ts-form-warning">Advertencia: si el nombre no coincide con el propietario del proyecto, el testimonio podrá ser eliminado.</span>
        </div>
        <form id="testimonio-form" method="POST" action="#testimonios" class="grid md:grid-cols-2 gap-6">
            <input type="hidden" name="action" value="nuevo_testimonio">
            <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($testimonialFormGuard['token'], ENT_QUOTES, 'UTF-8'); ?>">
            <div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
                <label for="testimonial_company_website">No llenes este campo</label>
                <input id="testimonial_company_website" type="text" name="company_website" tabindex="-1" autocomplete="off">
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 i18n-ts-form-proj-label" data-i18n="ts-form-proj-label">Selecciona tu proyecto</label>
                    <input id="t-proyecto-search" type="text" <?php echo $hasProjectOptions ? '' : 'disabled'; ?> class="w-full border border-gray-200 rounded-lg px-4 py-2 mb-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-100 disabled:text-gray-400 i18n-ts-form-proj-search" data-i18n="ts-form-proj-search" data-i18n-placeholder="ts-form-proj-search" placeholder="Escribe para buscar un proyecto">
                    <select id="t-proyecto" name="proyecto_id" required <?php echo $hasProjectOptions ? '' : 'disabled'; ?> class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="" class="i18n-ts-form-proj-option" data-i18n="ts-form-proj-option">Elige un proyecto</option>
                        <?php foreach ($projectOptions as $projectOption): ?>
                            <option value="<?php echo $projectOption['id']; ?>"><?php echo htmlspecialchars($projectOption['titulo'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$hasProjectOptions): ?>
                        <p class="mt-2 text-sm text-amber-700 i18n-ts-form-proj-empty" data-i18n="ts-form-proj-empty">No hay proyectos publicados en el portafolio para seleccionar.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 i18n-ts-form-name-label" data-i18n="ts-form-name-label">Nombre completo</label>
                    <input id="t-nombre" name="nombre" required type="text" minlength="2" maxlength="100" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 i18n-ts-form-name-ph" data-i18n="ts-form-name-ph" data-i18n-placeholder="ts-form-name-ph" placeholder="Ej. Ana Martínez">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 i18n-ts-form-msg-label" data-i18n="ts-form-msg-label">Escribe tu experiencia</label>
                    <textarea id="t-mensaje" name="mensaje" required rows="5" minlength="30" maxlength="1200" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus-border-blue-500 i18n-ts-form-msg-ph" data-i18n="ts-form-msg-ph" data-i18n-placeholder="ts-form-msg-ph" placeholder="Cuenta cómo te fue con el proyecto"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 i18n-ts-form-rating-label" data-i18n="ts-form-rating-label">Calificación (1 = no recomiendo, 5 = sí recomiendo)</label>
                    <div class="flex items-center gap-2" id="rating-group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="cursor-pointer text-2xl transition text-gray-300" data-star="<?php echo $i; ?>">
                            <input type="radio" class="sr-only" name="valoracion" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                            <i class="fas fa-star"></i>
                        </label>
                        <?php endfor; ?>
                    </div>
                    <p id="rating-text" class="text-sm text-green-600 mt-2 i18n-ts-form-rating-text" data-i18n="ts-form-rating-text">5 / 5 (sí recomiendo)</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="t-prev-btn" class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition i18n-ts-form-prev" data-i18n="ts-form-prev">Ver vista previa</button>
                    <button type="submit" id="testimonial-submit" <?php echo ($hasProjectOptions && $testimonialRecaptchaEnabled) ? '' : 'disabled'; ?> class="border border-blue-600 text-blue-600 px-5 py-3 rounded-lg hover:bg-blue-50 transition disabled:border-gray-300 disabled:text-gray-400 disabled:bg-gray-100 i18n-ts-form-submit" data-i18n="ts-form-submit">
                        Enviar testimonio
                    </button>
                </div>

                <?php if ($testimonialRecaptchaEnabled): ?>
                    <div class="pt-2">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(form_guard_recaptcha_site_key(), ENT_QUOTES, 'UTF-8'); ?>"></div>
                    </div>
                <?php else: ?>
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 i18n-ts-form-recaptcha-missing" data-i18n="ts-form-recaptcha-missing">
                        reCAPTCHA es obligatorio, pero no está configurado correctamente en este entorno.
                    </div>
                <?php endif; ?>
            </div>
            <div class="space-y-3">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-sm font-semibold text-gray-700 mb-2 i18n-ts-live-raw-title" data-i18n="ts-live-raw-title">Lo que estás escribiendo</p>
                    <p id="t-live-raw" class="text-gray-600 whitespace-pre-line min-h-[120px] i18n-ts-live-raw-text" data-i18n="ts-live-raw-text">Aquí verás tu texto a medida que escribes…</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                    <p class="text-sm font-semibold text-blue-700 mb-2 i18n-ts-live-final-title" data-i18n="ts-live-final-title">Así se verá publicado</p>
                    <p id="t-live-final" class="text-gray-800 leading-relaxed min-h-[120px] i18n-ts-live-final-text" data-i18n="ts-live-final-text">
                        Yo, [tu nombre] dueño de [tu proyecto], aquí aparecerá tu testimonio final.
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- CTA final -->
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl mce-rounded-panel p-10 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide i18n-ts-cta-label" data-i18n="ts-cta-label">Hablemos</p>
            <h3 class="text-2xl font-bold i18n-ts-cta-title" data-i18n="ts-cta-title">¿Quieres aparecer en esta sección?</h3>
            <p class="text-blue-100 mt-2 i18n-ts-cta-desc" data-i18n="ts-cta-desc">Cuéntanos cómo te fue con tu proyecto y te contactamos para publicarlo.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="#form-testimonio" class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-blue-50 transition">
                <i class="fas fa-paper-plane mr-2"></i> <span class="i18n-ts-cta-btn1" data-i18n="ts-cta-btn1">Dejar testimonio</span>
            </a>
            <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-comments mr-2"></i> <span class="i18n-ts-cta-btn2" data-i18n="ts-cta-btn2">Hablar con el equipo</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php if ($testimonialRecaptchaEnabled): ?>
<script>
(() => {
    let placeholders = Array.from(document.querySelectorAll('.g-recaptcha[data-sitekey]'));
    if (!placeholders.length) return;
    function renderAll() {
        if (!window.grecaptcha) return;
        placeholders.forEach((el) => {
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
        window.grecaptcha = undefined;
        const s = document.createElement('script');
        s.src = `https://www.google.com/recaptcha/api.js?onload=mceRenderRecaptcha&render=explicit&hl=${lang}`;
        s.async = true;
        s.defer = true;
        s.dataset.mceRecaptcha = '1';
        s.dataset.lang = lang;
        document.head.appendChild(s);
        window.mceRenderRecaptcha = renderAll;
    }
    const currentLang = localStorage.getItem('siteLang') || 'es';
    loadRecaptcha(currentLang);
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
        loadRecaptcha(e.detail?.lang || 'es');
    });
})();
</script>
<?php endif; ?>

<script>
(() => {
    const nombre = document.getElementById('t-nombre');
    const proyecto = document.getElementById('t-proyecto');
    const proyectoSearch = document.getElementById('t-proyecto-search');
    const mensaje = document.getElementById('t-mensaje');
    const liveRaw = document.getElementById('t-live-raw');
    const liveFinal = document.getElementById('t-live-final');
    const form = document.getElementById('testimonio-form');
    const prevBtn = document.getElementById('t-prev-btn');
    let submitting = false;
    const ratingInputs = document.querySelectorAll('input[name=\"valoracion\"]');
    const starLabels = document.querySelectorAll('[data-star]');
    const ratingText = document.getElementById('rating-text');
    const likeButtons = document.querySelectorAll('.like-btn');
    const cardRatings = document.querySelectorAll('.ts-rating-card');

    // Likes testimonios
    likeButtons.forEach((btn) => {
        const id = btn.dataset.likeId;
        const countEl = btn.querySelector('.like-count');
        const storageKey = 'mce_like_' + id;
        const markLiked = () => {
            btn.classList.add('bg-red-600', 'text-white', 'border-red-600', 'liked');
            btn.classList.remove('bg-red-50', 'text-red-600');
        };
        if (localStorage.getItem(storageKey)) {
            markLiked();
        }
        btn.addEventListener('click', () => {
            if (btn.classList.contains('liked')) return;
            btn.disabled = true;
            fetch('ajax/testimonio-like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id),
            })
                .then((res) => res.ok ? res.json() : null)
                .then((data) => {
                    if (data && data.ok) {
                        countEl.textContent = data.likes;
                        localStorage.setItem(storageKey, '1');
                        markLiked();
                    }
                })
                .catch(() => {})
                .finally(() => { btn.disabled = false; });
        });
    });

    function tr(key, fallback) {
        const dict = window.mceTranslations || {};
        return dict[key] || fallback;
    }

    function updatePreview() {
        const n = (nombre.value || tr('ts-preview-your-name', 'tu nombre')).trim();
        const p = (proyecto.selectedOptions[0]?.text || tr('ts-preview-your-project', 'tu proyecto')).trim();
        const msg = mensaje.value.trim() || tr('ts-preview-default-msg', 'escribe aquí tu experiencia con el proyecto');
        liveRaw.textContent = mensaje.value || tr('ts-live-raw-text', 'Aquí verás tu texto a medida que escribes…');
        liveFinal.textContent = `${tr('ts-preview-prefix', 'Yo')}, ${n} ${tr('ts-preview-owner-of', 'dueño de')} ${p}, ${msg}`;
    }

    nombre.addEventListener('input', updatePreview);
    proyecto.addEventListener('change', updatePreview);
    mensaje.addEventListener('input', updatePreview);
    prevBtn.addEventListener('click', updatePreview);

    const updateStars = () => {
        const selected = document.querySelector('input[name=\"valoracion\"]:checked');
        const value = selected ? parseInt(selected.value, 10) : 0;
        starLabels.forEach(label => {
            const v = parseInt(label.dataset.star, 10);
            label.classList.toggle('text-yellow-400', v <= value);
            label.classList.toggle('text-gray-300', v > value);
        });
        if (ratingText) {
            ratingText.classList.remove('text-red-600','text-green-600','text-gray-600');
            if (value === 1) {
                ratingText.textContent = tr('ts-rating-1', '1 / 5 (no recomiendo)');
                ratingText.classList.add('text-red-600');
            } else if (value === 2) {
                ratingText.textContent = tr('ts-rating-2', '2 / 5 (poco recomendable)');
                ratingText.classList.add('text-red-600');
            } else if (value === 3) {
                ratingText.textContent = tr('ts-rating-3', '3 / 5 (neutral)');
                ratingText.classList.add('text-gray-600');
            } else if (value === 4) {
                ratingText.textContent = tr('ts-rating-4', '4 / 5 (recomiendo)');
                ratingText.classList.add('text-green-600');
            } else if (value === 5) {
                ratingText.textContent = tr('ts-rating-5', '5 / 5 (sí recomiendo)');
                ratingText.classList.add('text-green-600');
            } else {
                ratingText.textContent = `${value} / 5`;
                ratingText.classList.add('text-gray-600');
            }
        }
    };

    const updateCardRatings = () => {
        const dict = window.mceTranslations || {};
        cardRatings.forEach(el => {
            const val = parseInt(el.dataset.rating || '0', 10);
            const key = 'ts-rating-' + (val || 0);
            const txt = dict[key] || `${val} / 5`;
            el.textContent = txt;
        });
    };

    ratingInputs.forEach(r => {
        r.addEventListener('change', () => {
            updateStars();
            updatePreview();
        });
    });
    updateStars();
    updateCardRatings();

    // Si el usuario cambia de idioma en caliente, actualiza textos dinámicos sin recargar
    window.addEventListener('mce-lang-changed', () => {
        updateStars();
        updatePreview();
        updateCardRatings();
    });

    // Búsqueda rápida en el select de proyectos
    if (proyectoSearch) {
        proyectoSearch.addEventListener('input', () => {
            const term = proyectoSearch.value.toLowerCase();
            let matched = null;
            Array.from(proyecto.options).forEach(opt => {
                if (!opt.value) return;
                const show = opt.text.toLowerCase().includes(term);
                opt.hidden = term.length ? !show : false;
                if (!matched && show) matched = opt;
            });
            if (matched) {
                proyecto.value = matched.value;
            } else if (term.length) {
                proyecto.value = '';
            }
            updatePreview();
        });
    }

    // Ocultar aviso y limpiar querystring
    const alertTestimonio = document.getElementById('alert-testimonio');
    const alertTestimonioError = document.getElementById('alert-testimonio-error');
    const feedbackBanner = alertTestimonio || alertTestimonioError;
    if (feedbackBanner) {
        setTimeout(() => feedbackBanner.classList.add('hidden'), 4000);
        const url = new URL(window.location);
        url.searchParams.delete('testimonio');
        url.searchParams.delete('error');
        window.history.replaceState({}, '', url);
    }

    form.addEventListener('submit', (e) => {
        if (submitting) return;
        e.preventDefault();
        updatePreview();

        if (typeof window.grecaptcha === 'undefined') {
            alert('reCAPTCHA aun no termina de cargar. Intenta nuevamente en unos segundos.');
            return;
        }

        if (!window.grecaptcha.getResponse()) {
            alert('Completa la verificacion reCAPTCHA antes de enviar.');
            return;
        }

        const ok = confirm(`Enviar este testimonio?\n\n${liveFinal.textContent}`);
        if (ok) {
            submitting = true;
            form.submit();
        }
    });

    updatePreview();
})();
</script>
