<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php
$projects = fetchPortfolioProjects($conn);
$categories = fetchPortfolioCategories($projects);
$featuredProject = $projects[0] ?? null;
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Portafolio -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-28 -right-10 w-96 h-96 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-briefcase mr-2 text-yellow-300"></i> <span class="i18n-pf-badge" data-i18n="pf-badge">Portafolio · Proyectos MCE</span>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight i18n-pf-hero-title" data-i18n="pf-hero-title">Productos digitales y sistemas en producción</h1>
                <p class="text-lg text-blue-50 max-w-3xl i18n-pf-hero-sub" data-i18n="pf-hero-sub">
                    Proyectos reales con usuarios y datos en vivo: paneles internos, portales, automatizaciones y sitios de marca conectados a pasarelas y APIs.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-pf-chip1" data-i18n="pf-chip1"><i class="fas fa-industry mr-2 text-yellow-300"></i><span>Retail · Logística · Servicios</span></span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-pf-chip2" data-i18n="pf-chip2"><i class="fas fa-code-branch mr-2 text-yellow-300"></i><span>APIs, colas y webhooks</span></span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-pf-chip3" data-i18n="pf-chip3"><i class="fas fa-lock mr-2 text-yellow-300"></i><span>Seguridad y pruebas completas</span></span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition i18n-pf-btn-demo" data-i18n="pf-btn-demo">
                        <i class="fas fa-rocket mr-2"></i> Solicitar demo
                    </a>
                    <a href="#proyectos-grid" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition i18n-pf-btn-see" data-i18n="pf-btn-see">
                        <i class="fas fa-eye mr-2"></i> Ver proyectos
                    </a>
                    <a href="#casos-exito-portafolio" class="inline-flex items-center justify-center border-2 border-amber-300 text-amber-100 px-8 py-4 rounded-xl font-semibold hover:bg-amber-200 hover:text-slate-900 transition i18n-pf-btn-cases" data-i18n="pf-btn-cases">
                        <i class="fas fa-trophy mr-2"></i> Casos de éxito
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100 i18n-pf-what" data-i18n="pf-what">Qué verás</p>
                            <p class="text-2xl font-semibold text-white i18n-pf-what-title" data-i18n="pf-what-title">Casos listos para inspirar tu proyecto</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-laptop-code"></i></span>
                            <div>
                                <p class="font-semibold i18n-pf-b1-title" data-i18n="pf-b1-title">Aplicaciones internas</p>
                                <p class="text-sm text-blue-100 i18n-pf-b1-text" data-i18n="pf-b1-text">Paneles internos con roles y flujos simples.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-globe"></i></span>
                            <div>
                                <p class="font-semibold i18n-pf-b2-title" data-i18n="pf-b2-title">Sitios y landing pages</p>
                                <p class="text-sm text-blue-100 i18n-pf-b2-text" data-i18n="pf-b2-text">Catálogos, carrito de compra, pagos y medición de ventas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-gears"></i></span>
                            <div>
                                <p class="font-semibold i18n-pf-b3-title" data-i18n="pf-b3-title">Automatizaciones</p>
                                <p class="text-sm text-blue-100 i18n-pf-b3-text" data-i18n="pf-b3-text">Integraciones que conectan sistemas y reducen tareas manuales.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="#proyectos-grid" class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between hover:bg-white/10 transition">
                        <div>
                            <p class="text-sm text-blue-100 i18n-pf-cta-small" data-i18n="pf-cta-small">Entrega con visibilidad</p>
                            <p class="font-semibold i18n-pf-cta-big" data-i18n="pf-cta-big">Demos frecuentes y documentación</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Caso de éxito destacado -->
<section id="casos-exito-portafolio" class="max-w-7xl mx-auto px-4 pb-10 pt-10">
    <div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-orange-400 text-slate-900 rounded-2xl shadow-2xl border border-amber-200 overflow-hidden">
        <div class="grid md:grid-cols-12 gap-0">
            <div class="md:col-span-7 p-8 lg:py-10 lg:px-10 space-y-6 bg-white/70 backdrop-blur">
                <div class="inline-flex items-center px-3 py-1 text-xs font-semibold uppercase tracking-wide bg-amber-100 text-amber-800 rounded-full border border-amber-200 i18n-pf-case-badge" data-i18n="pf-case-badge">Caso de éxito</div>
                <h3 class="text-3xl font-bold i18n-pf-case-title" data-i18n="pf-case-title">Destello de Oro 18K – Sistema de gestión para joyería</h3>
                <p class="text-lg text-slate-800 i18n-pf-case-desc" data-i18n="pf-case-desc">El dueño enfrentaba descontrol total: inventario incierto, ventas en papel, garantías sin orden, gastos sin registrar y cierres de caja que tomaban horas.</p>

                <div class="grid lg:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 i18n-pf-case-need-title" data-i18n="pf-case-need-title">Necesidades del cliente</p>
                        <ul class="mt-2 space-y-2 text-sm text-slate-800">
                            <li class="i18n-pf-need1" data-i18n="pf-need1"><span class="font-semibold text-amber-700">1.</span> Descontrol de inventario: sin claridad de stock y garantías.</li>
                            <li class="i18n-pf-need2" data-i18n="pf-need2"><span class="font-semibold text-amber-700">2.</span> Pérdida de información en cuadernos/Excel.</li>
                            <li class="i18n-pf-need3" data-i18n="pf-need3"><span class="font-semibold text-amber-700">3.</span> Garantías desordenadas: plazos y costos sin trazabilidad.</li>
                            <li class="i18n-pf-need4" data-i18n="pf-need4"><span class="font-semibold text-amber-700">4.</span> Dos precios (mayorista/detal) manejados a mano con errores.</li>
                            <li class="i18n-pf-need5" data-i18n="pf-need5"><span class="font-semibold text-amber-700">5.</span> Gastos diarios sin registrar ni controlar.</li>
                            <li class="i18n-pf-need6" data-i18n="pf-need6"><span class="font-semibold text-amber-700">6.</span> Falta de roles: necesitaba limitar accesos de empleados.</li>
                            <li class="i18n-pf-need7" data-i18n="pf-need7"><span class="font-semibold text-amber-700">7.</span> Cierres de caja tediosos y manuales.</li>
                            <li class="i18n-pf-need8" data-i18n="pf-need8"><span class="font-semibold text-amber-700">8.</span> Sin respaldos: riesgo de perder toda la información.</li>
                        </ul>
                    </div>
                    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 i18n-pf-sol-title" data-i18n="pf-sol-title">Soluciones implementadas</p>
                        <ul class="mt-2 space-y-2 text-sm text-slate-800">
                            <li class="i18n-pf-sol1" data-i18n="pf-sol1"><span class="font-semibold text-amber-700">1.</span> Inventario en tiempo real por pieza.</li>
                            <li class="i18n-pf-sol2" data-i18n="pf-sol2"><span class="font-semibold text-amber-700">2.</span> Base de datos en la nube (Hostinger) con backups automáticos.</li>
                            <li class="i18n-pf-sol3" data-i18n="pf-sol3"><span class="font-semibold text-amber-700">3.</span> Módulo de garantías trazable (motivo, costos, estado, historial).</li>
                            <li class="i18n-pf-sol4" data-i18n="pf-sol4"><span class="font-semibold text-amber-700">4.</span> Precios mayorista/detal automáticos según tipo de cliente.</li>
                            <li class="i18n-pf-sol5" data-i18n="pf-sol5"><span class="font-semibold text-amber-700">5.</span> Módulo de gastos diarios con filtros y totales por período.</li>
                            <li class="i18n-pf-sol6" data-i18n="pf-sol6"><span class="font-semibold text-amber-700">6.</span> Roles: Administrador (total) y Trabajador (ventas/consultas).</li>
                            <li class="i18n-pf-sol7" data-i18n="pf-sol7"><span class="font-semibold text-amber-700">7.</span> Cierre de caja automático con ventas, medios de pago, gastos y utilidad.</li>
                            <li class="i18n-pf-sol8" data-i18n="pf-sol8"><span class="font-semibold text-amber-700">8.</span> Respaldos diarios con restauración rápida.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 i18n-pf-res-title" data-i18n="pf-res-title">Resultados</p>
                    <div class="mt-2 grid sm:grid-cols-2 gap-3 text-sm text-slate-800">
                        <div>
                            <p class="font-semibold text-amber-700 i18n-pf-res1-title" data-i18n="pf-res1-title">Inventario</p>
                            <p class="i18n-pf-res1-text" data-i18n="pf-res1-text">Antes: se perdían piezas. Después: control exacto por pieza en tiempo real.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-700 i18n-pf-res2-title" data-i18n="pf-res2-title">Ventas</p>
                            <p class="i18n-pf-res2-text" data-i18n="pf-res2-text">Antes: papel/Excel. Después: registro digital consultable siempre.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-700 i18n-pf-res3-title" data-i18n="pf-res3-title">Garantías</p>
                            <p class="i18n-pf-res3-text" data-i18n="pf-res3-text">Antes: sin orden. Después: trazabilidad completa y costos controlados.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-700 i18n-pf-res4-title" data-i18n="pf-res4-title">Gastos</p>
                            <p class="i18n-pf-res4-text" data-i18n="pf-res4-text">Antes: no se medían. Después: reportes y detección de fugas.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-700 i18n-pf-res5-title" data-i18n="pf-res5-title">Tiempo</p>
                            <p class="i18n-pf-res5-text" data-i18n="pf-res5-text">Antes: 2 horas diarias en papeleo. Después: cierres automáticos.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-700 i18n-pf-res6-title" data-i18n="pf-res6-title">Tranquilidad</p>
                            <p class="i18n-pf-res6-text" data-i18n="pf-res6-text">Antes: riesgo de perder datos. Después: información segura y respaldada.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 i18n-pf-test-title" data-i18n="pf-test-title">Testimonio</p>
                    <p class="text-sm text-slate-800 mt-2 italic i18n-pf-test-text" data-i18n="pf-test-text">“Superó mis expectativas, lo recomiendo 100%” – Jose Delgado, Destello de Oro 18K.</p>
                </div>

                <div class="flex flex-wrap gap-3 pt-1">
                    <a href="<?php echo app_url('destello-oro.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
                        <i class="fas fa-play mr-2"></i> <span class="i18n-pf-case-btn-full" data-i18n="pf-case-btn-full">Ver caso completo</span>
                    </a>
                    <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada" class="inline-flex items-center px-5 py-3 rounded-xl border border-slate-900 text-slate-900 font-semibold hover:bg-slate-900 hover:text-white transition">
                        <i class="fas fa-phone-alt mr-2"></i> <span class="i18n-pf-case-btn-call" data-i18n="pf-case-btn-call">Agenda una llamada</span>
                    </a>
                </div>
            </div>
            <div class="md:col-span-5 relative min-h-[260px] bg-[radial-gradient(circle_at_30%_30%,rgba(255,255,255,0.35),transparent_45%),radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.22),transparent_40%),radial-gradient(circle_at_60%_80%,rgba(255,255,255,0.28),transparent_45%)]">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(15,23,42,0.25),rgba(15,23,42,0.12))]"></div>
                <div class="relative h-full w-full flex items-center justify-center p-8">
                    <div class="bg-white/85 backdrop-blur-lg rounded-2xl shadow-2xl border border-amber-100 p-6 max-w-sm w-full space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide i18n-pf-card-industry" data-i18n="pf-card-industry">Joyería</p>
                                <p class="text-lg font-bold text-slate-900">Destello de Oro 18K</p>
                            </div>
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-500 text-white font-bold">DO</span>
                        </div>
                        <ul class="space-y-2 text-slate-800 text-sm">
                            <li class="flex items-start gap-2 i18n-pf-card-b1" data-i18n="pf-card-b1"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Ventas con control de cobros y saldos.</span></li>
                            <li class="flex items-start gap-2 i18n-pf-card-b2" data-i18n="pf-card-b2"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Inventario en tiempo real por pieza.</span></li>
                            <li class="flex items-start gap-2 i18n-pf-card-b3" data-i18n="pf-card-b3"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Garantías trazables y reposición.</span></li>
                            <li class="flex items-start gap-2 i18n-pf-card-b4" data-i18n="pf-card-b4"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Reportes de caja y gastos diarios.</span></li>
                        </ul>
                        <a href="<?php echo app_url('destello-oro.php'); ?>" class="inline-flex items-center justify-center w-full mt-2 px-4 py-2 rounded-xl bg-amber-500 text-white font-semibold shadow hover:bg-amber-600 transition">
                            <span class="i18n-pf-card-btn" data-i18n="pf-card-btn">Ver pantallas</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($featuredProject): ?>
<section class="max-w-7xl mx-auto px-4 py-12">
    <?php
    $featuredUrl = $featuredProject['public_url'];
    $featuredHasLink = $featuredUrl !== '#';
    $featuredIsExternal = $featuredHasLink && isExternalProjectUrl($featuredUrl);
    $featuredDescription = trim((string) ($featuredProject['descripcion'] ?? '')) ?: 'Proyecto destacado del portafolio de Proyectos MCE.';
    $featuredClient = trim((string) ($featuredProject['cliente'] ?? '')) ?: 'Cliente privado';
    $featuredDate = null;
    if (!empty($featuredProject['fecha_completado'])) {
        $timestamp = strtotime((string) $featuredProject['fecha_completado']);
        if ($timestamp) {
            $featuredDate = date('d/m/Y', $timestamp);
        }
    }
    $repoUrl = trim((string) ($featuredProject['url_repo'] ?? ''));
    if ($repoUrl !== '' && preg_match('~^(https?://|/)~i', $repoUrl) !== 1) {
        $repoUrl = 'https://' . $repoUrl;
    }
    ?>
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <div class="md:flex">
            <div class="md:w-1/2">
                    <img
                        src="<?php echo htmlspecialchars($featuredProject['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($featuredProject['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full h-64 md:h-full object-cover"
                        loading="lazy"
                    >
            </div>
            <div class="md:w-1/2 p-8 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wide text-blue-700 i18n-pf-featured-badge" data-i18n="pf-featured-badge">Proyecto destacado</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold"><?php echo htmlspecialchars($featuredProject['categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900"><?php echo htmlspecialchars($featuredProject['titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-gray-700"><?php echo htmlspecialchars($featuredDescription, ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="grid sm:grid-cols-2 gap-3 text-gray-700">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-tie text-blue-600"></i>
                        <span><?php echo htmlspecialchars($featuredClient, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-600"></i>
                        <span><?php echo htmlspecialchars($featuredProject['categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php if ($featuredDate): ?>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                            <span><?php echo htmlspecialchars($featuredDate, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-star text-blue-600"></i>
                        <span class="i18n-pf-featured-selected" data-i18n="pf-featured-selected">Seleccionado por el equipo MCE</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <?php if ($featuredHasLink): ?>
                        <a
                            href="<?php echo htmlspecialchars($featuredUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $featuredIsExternal ? 'target="_blank" rel="noopener"' : ''; ?>
                            class="bg-slate-900 text-white px-6 py-3 rounded-lg hover:bg-slate-800 transition"
                        >
                            <span class="i18n-pf-featured-view" data-i18n="pf-featured-view">Ver proyecto</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($repoUrl !== ''): ?>
                        <a
                            href="<?php echo htmlspecialchars($repoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            target="_blank"
                            rel="noopener"
                            class="border border-slate-300 text-slate-700 px-6 py-3 rounded-lg hover:bg-slate-50 transition"
                        >
                            <span class="i18n-pf-featured-repo" data-i18n="pf-featured-repo">Ver repositorio</span>
                        </a>
                    <?php endif; ?>

                    <a href="#proyectos-grid" class="border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition">
                        <span class="i18n-pf-featured-more" data-i18n="pf-featured-more">Ver más proyectos</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Filtros -->
<section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide i18n-pf-filter-label" data-i18n="pf-filter-label">Filtrar proyectos</p>
            <p class="text-gray-700 i18n-pf-filter-desc" data-i18n="pf-filter-desc">Explora por categoría o mira todo el portafolio.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="filter-btn active px-4 py-2 rounded-full bg-blue-600 text-white i18n-pf-filter-all" data-i18n="pf-filter-all" data-filter="all">Todos</button>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn px-4 py-2 rounded-full bg-gray-200 hover:bg-gray-300" data-filter="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Grid de proyectos -->
<section class="max-w-7xl mx-auto px-4 py-12" id="proyectos-grid">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!$projects): ?>
            <div class="md:col-span-2 lg:col-span-3 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-600 i18n-pf-no-projects" data-i18n="pf-no-projects">
                Aún no hay proyectos publicados en el portafolio.
            </div>
        <?php endif; ?>

        <?php foreach ($projects as $project): ?>
            <?php
            $projectUrl = $project['public_url'];
            $hasLink = $projectUrl !== '#';
            $isExternal = $hasLink && isExternalProjectUrl($projectUrl);
            $description = trim((string) ($project['descripcion'] ?? ''));
            if ($description === '') {
                $description = 'Proyecto publicado en el portafolio de Proyectos MCE.';
            }
            if (function_exists('mb_strimwidth')) {
                $descriptionPreview = mb_strimwidth($description, 0, 110, '...');
            } else {
                $descriptionPreview = strlen($description) > 110 ? substr($description, 0, 110) . '...' : $description;
            }
            $clientLabel = trim((string) ($project['cliente'] ?? '')) ?: 'Cliente privado';
            ?>
            <div class="proyecto-item bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border border-slate-100" data-categoria="<?php echo htmlspecialchars($project['categoria'], ENT_QUOTES, 'UTF-8'); ?>">
                <a
                    href="<?php echo htmlspecialchars($projectUrl, ENT_QUOTES, 'UTF-8'); ?>"
                    <?php echo $isExternal ? 'target="_blank" rel="noopener"' : ''; ?>
                    class="block <?php echo $hasLink ? '' : 'pointer-events-none'; ?>"
                >
                    <img
                        src="<?php echo htmlspecialchars($project['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                        alt="<?php echo htmlspecialchars($project['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full h-52 object-cover hover:scale-[1.01] transition-transform duration-200"
                        loading="lazy"
                    >
                </a>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-blue-600 font-semibold"><?php echo htmlspecialchars($project['categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold i18n-pf-real-case" data-i18n="pf-real-case">Caso real</span>
                    </div>
                    <h3 class="text-xl font-bold">
                        <a
                            href="<?php echo htmlspecialchars($projectUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $isExternal ? 'target="_blank" rel="noopener"' : ''; ?>
                            class="hover:text-blue-600 transition <?php echo $hasLink ? '' : 'pointer-events-none'; ?>"
                        >
                            <?php echo htmlspecialchars($project['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 line-clamp-2"><?php echo htmlspecialchars($descriptionPreview, ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="flex justify-between items-center gap-4 pt-1">
                        <span class="text-gray-500 text-sm"><?php echo htmlspecialchars($clientLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if ($hasLink): ?>
                            <a
                                href="<?php echo htmlspecialchars($projectUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $isExternal ? 'target="_blank" rel="noopener"' : ''; ?>
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold"
                            >
                                <span class="i18n-pf-see-more" data-i18n="pf-see-more">Ver más</span> <i class="fas fa-arrow-right ml-2 text-sm"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-sm text-gray-400 i18n-pf-no-link" data-i18n="pf-no-link">Sin enlace disponible</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA final -->
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl mce-rounded-panel p-10 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide i18n-pf-cta2-label" data-i18n="pf-cta2-label">¿Listo para tu caso?</p>
            <h3 class="text-2xl font-bold i18n-pf-cta2-title" data-i18n="pf-cta2-title">Te mostramos un demo similar a tu industria</h3>
            <p class="text-blue-100 mt-2 i18n-pf-cta2-desc" data-i18n="pf-cta2-desc">Agenda una llamada y en 24 horas preparamos un recorrido de referencia.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada" class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-blue-50 transition mce-call-ringing">
                <span class="call-ico-wrap mr-2 text-slate-900">
                    <i class="fas fa-phone-alt"></i>
                    <span class="call-ring call-ring--1"></span>
                    <span class="call-ring call-ring--2"></span>
                    <span class="call-ring call-ring--3"></span>
                </span>
                <span class="i18n-pf-cta2-call" data-i18n="pf-cta2-call">Agendar llamada</span>
            </a>
            <a href="#proyectos-grid" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-eye mr-2"></i> <span class="i18n-pf-cta2-see" data-i18n="pf-cta2-see">Ver proyectos</span>
            </a>
            <a href="#casos-exito-portafolio" class="inline-flex items-center px-5 py-3 rounded-xl border border-amber-300 text-amber-100 font-semibold hover:bg-amber-200 hover:text-slate-900 transition">
                <i class="fas fa-trophy mr-2"></i> <span class="i18n-pf-cta2-cases" data-i18n="pf-cta2-cases">Casos de éxito</span>
            </a>
        </div>
    </div>
</section>

<script>
// Filtro simple con JavaScript
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Actualizar botones activos
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-blue-600', 'text-white');
            b.classList.add('bg-gray-200');
        });
        this.classList.add('bg-blue-600', 'text-white');
        this.classList.remove('bg-gray-200');
        
        // Filtrar proyectos
        const filter = this.dataset.filter;
        document.querySelectorAll('.proyecto-item').forEach(item => {
            if (filter === 'all' || item.dataset.categoria === filter) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>





