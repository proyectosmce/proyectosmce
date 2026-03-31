<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php include 'includes/header.php'; ?>


<!-- Hero Section renovado -->
<section class="relative overflow-hidden bg-gradient-to-br from-brand-ink via-[#120c2c] to-brand-dark text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-hero-mesh opacity-80"></div>
    <div class="absolute -top-24 -left-24 w-72 h-72 bg-brand-primary/25 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-32 -right-10 w-96 h-96 bg-brand-accent/20 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-24 lg:py-28">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-7 text-left">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/15 rounded-full backdrop-blur-sm">
                    <i class="fas fa-sparkles mr-2 text-brand-accent"></i>
                    <span class="i18n-hero-badge">Proyectos MCE · Software a medida</span>
                </span>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight font-display i18n-hero-title" data-i18n="hero-title">
                    Software a medida para que tu operación no se detenga
                </h1>
                <p class="text-lg md:text-xl text-white/80 max-w-3xl i18n-hero-sub" data-i18n="hero-sub">
                    Planificamos, diseñamos y desarrollamos plataformas web que soportan ventas, inventarios y atención al cliente con control total y visibilidad.
                </p>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-chip1" data-i18n="chip1">
                        <i class="fas fa-diagram-project mr-2 text-brand-accent"></i><span>Entendemos tu proceso primero</span>
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-chip2" data-i18n="chip2">
                        <i class="fas fa-pen-ruler mr-2 text-brand-accent"></i><span>Diseño gráfico (UX/UI) con maquetas claras</span>
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-chip3" data-i18n="chip3">
                        <i class="fas fa-bolt mr-2 text-brand-accent"></i><span>Entregas cortas con pruebas y control</span>
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm i18n-chip4" data-i18n="chip4">
                        <i class="fas fa-database mr-2 text-brand-accent"></i><span>Conexiones a servidores (APIs) y datos en orden</span>
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>?cta=plan#contacto-form" class="cta-primary inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm md:text-base shadow-glow hover:scale-[1.02] transition i18n-btn-plan whitespace-nowrap" data-i18n="btn-plan">
                        <i class="fas fa-rocket mr-2"></i> Armar mi plan
                    </a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm md:text-base border border-white/40 text-white hover:bg-white/10 transition i18n-btn-portfolio whitespace-nowrap" data-i18n="btn-portfolio">
                        <i class="fas fa-eye mr-2"></i> Ver casos en vivo
                    </a>
                    <a href="<?php echo app_url('portafolio.php#casos-exito-portafolio'); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm md:text-base border border-brand-accent/60 text-brand-accent hover:bg-brand-accent hover:text-brand-ink transition i18n-btn-cases whitespace-nowrap" data-i18n="btn-cases">
                        <i class="fas fa-trophy mr-2"></i> Casos de éxito
                    </a>
                    <a href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-sm md:text-base border border-white/30 text-white hover:bg-white/10 transition i18n-link-services whitespace-nowrap" data-i18n="link-services">
                        <span>Servicios</span>
                        <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>

                <div class="flex flex-wrap items-start gap-6 pt-6 border-t border-white/10 max-w-5xl">
                    <div class="flex items-start gap-3 min-w-[220px]">
                        <span class="w-10 h-10 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent"><i class="fas fa-diagram-project"></i></span>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.24em] text-white/60 i18n-card1-label" data-i18n="card1-label">Gobernanza</p>
                            <p class="font-semibold text-white/90 i18n-card1-text" data-i18n="card1-text">Backlog claro, prioridades y criterios de entrega</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 min-w-[220px]">
                        <span class="w-10 h-10 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent"><i class="fas fa-pen-ruler"></i></span>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.24em] text-white/60 i18n-card2-label" data-i18n="card2-label">Entrega iterativa</p>
                            <p class="font-semibold text-white/90 i18n-card2-text" data-i18n="card2-text">Demos quincenales, pruebas automáticas y regresiones</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 min-w-[220px]">
                        <span class="w-10 h-10 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent"><i class="fas fa-bolt"></i></span>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.24em] text-white/60 i18n-card3-label" data-i18n="card3-label">Operabilidad</p>
                            <p class="font-semibold text-white/90 i18n-card3-text" data-i18n="card3-text">Monitoreo, alertas y soporte continuo</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="relative overflow-hidden rounded-3xl bg-white/10 ring-1 ring-white/15 backdrop-blur-2xl p-8 shadow-glow space-y-6">
                    <div class="absolute inset-0 bg-hero-mesh opacity-60"></div>
                    <div class="relative flex items-start justify-between">
                        <div>
                            <p class="text-sm text-white/70 i18n-card4-label" data-i18n="card4-label">Hoja de ruta MCE</p>
                            <p class="text-2xl font-semibold text-white i18n-card4-title" data-i18n="card4-title">Tu proyecto, con control total</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white shadow-lg overflow-hidden mce-photo-badge">
                            <img src="<?php echo app_url('imag/MCE.jpg'); ?>" alt="MCE" class="w-full h-full object-cover">
                        </span>
                    </div>
                    <ul class="relative space-y-4 text-white/85">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-clipboard-check"></i></span>
                            <div>
                                <p class="font-semibold i18n-card4-step1-title" data-i18n="card4-step1-title">Arranque y plan</p>
                                <p class="text-sm text-white/70 i18n-card4-step1-text" data-i18n="card4-step1-text">Conversamos de tus procesos y definimos lo más importante primero.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-pen-ruler"></i></span>
                            <div>
                                <p class="font-semibold i18n-card4-step2-title" data-i18n="card4-step2-title">Diseño claro</p>
                                <p class="text-sm text-white/70 i18n-card4-step2-text" data-i18n="card4-step2-text">Pantallas fáciles de usar para tu equipo y tus clientes.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-bolt"></i></span>
                            <div>
                                <p class="font-semibold i18n-card4-step3-title" data-i18n="card4-step3-title">Construcción por etapas</p>
                                <p class="text-sm text-white/70 i18n-card4-step3-text" data-i18n="card4-step3-text">Avanzamos en ciclos cortos con avances y pruebas visibles.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-shield-halved"></i></span>
                            <div>
                                <p class="font-semibold i18n-card4-step4-title">Cuidado y soporte</p>
                                <p class="text-sm text-white/70 i18n-card4-step4-text">Copias de seguridad, monitoreo y ayuda después de lanzar.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20agendar%20un%20discovery%20con%20MCE" target="_blank" rel="noopener" class="cta-primary relative flex items-center justify-between p-4 rounded-2xl border border-white/15 hover:scale-[1.01] transition">
                        <div>
                            <p class="text-sm text-white/70 i18n-cta-discovery-label">Disponibilidad inmediata</p>
                            <p class="font-semibold i18n-cta-discovery-text" data-i18n="cta-discovery-text">Agenda tu sesión de discovery sin costo</p>
                        </div>
                        <i class="fas fa-arrow-right text-white text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Presentación / Propuesta de valor -->
<section class="relative bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-16 items-start">
        <div class="space-y-6">
            <p class="text-sm font-semibold text-brand-primary uppercase tracking-wide i18n-pres-label" data-i18n="pres-label">Presentación</p>
            <h2 class="text-4xl font-bold font-display text-slate-900 i18n-pres-title" data-i18n="pres-title">Proyectos MCE</h2>
            <p class="text-lg text-slate-600 leading-relaxed i18n-pres-desc" data-i18n="pres-desc">
                Equipo con experiencia que entiende tu negocio, diseña la experiencia y entrega software confiable listo para producción.
            </p>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <span class="mt-1 text-brand-primary"><i class="fas fa-magnifying-glass-chart"></i></span>
                    <p class="text-slate-800 i18n-pres-b1" data-i18n="pres-b1">Mapeamos objetivos, usuarios y métricas para no programar a ciegas.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="mt-1 text-brand-primary"><i class="fas fa-pen-ruler"></i></span>
                    <p class="text-slate-800 i18n-pres-b2" data-i18n="pres-b2">Diseño gráfico (UX/UI) con flujos claros y pantallas que cualquier persona entiende.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="mt-1 text-brand-primary"><i class="fas fa-server"></i></span>
                    <p class="text-slate-800 i18n-pres-b3" data-i18n="pres-b3">Conexiones a servidores (APIs) y bases de datos para que todo hable entre sí.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="mt-1 text-brand-primary"><i class="fas fa-headset"></i></span>
                    <p class="text-slate-800 i18n-pres-b4" data-i18n="pres-b4">Soporte, monitoreo y mejoras continuas después de lanzar.</p>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-ink via-[#120c2c] to-brand-dark text-white p-10 ring-1 ring-white/10 shadow-soft">
            <div class="absolute inset-0 bg-hero-mesh opacity-70"></div>
            <div class="relative space-y-6">
                <h3 class="text-2xl font-bold i18n-tl-heading">Cómo trabajamos</h3>
                <ul class="space-y-4 timeline">
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-10 h-10 rounded-full bg-white/15 flex items-center justify-center font-semibold border border-white/20">1</span>
                        <div>
                            <p class="font-semibold i18n-tl1-title" data-i18n="tl1-title">Kickoff y plan</p>
                            <p class="text-sm text-white/75 i18n-tl1-text" data-i18n="tl1-text">Revisamos tus objetivos y qué debe hacer la solución.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-10 h-10 rounded-full bg-white/15 flex items-center justify-center font-semibold border border-white/20">2</span>
                        <div>
                            <p class="font-semibold i18n-tl2-title" data-i18n="tl2-title">Diseño y prototipos</p>
                            <p class="text-sm text-white/75 i18n-tl2-text" data-i18n="tl2-text">Te mostramos maquetas para confirmar que el flujo es correcto.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-10 h-10 rounded-full bg-white/15 flex items-center justify-center font-semibold border border-white/20">3</span>
                        <div>
                            <p class="font-semibold i18n-tl3-title" data-i18n="tl3-title">Desarrollo incremental</p>
                            <p class="text-sm text-white/75 i18n-tl3-text" data-i18n="tl3-text">Entregamos en ciclos cortos con pruebas y notas claras.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-10 h-10 rounded-full bg-white/15 flex items-center justify-center font-semibold border border-white/20">4</span>
                        <div>
                            <p class="font-semibold i18n-tl4-title" data-i18n="tl4-title">Lanzamiento y soporte</p>
                            <p class="text-sm text-white/75 i18n-tl4-text" data-i18n="tl4-text">Publicamos, monitoreamos y mejoramos de forma continua.</p>
                        </div>
                    </li>
                </ul>
                <div class="p-5 border border-white/15 rounded-2xl bg-white/10">
                    <p class="text-sm text-white/75 i18n-availability-label" data-i18n="availability-label">Disponibilidad</p>
                    <p class="font-semibold i18n-availability-text" data-i18n="availability-text">Equipo listo para comenzar en menos de 7 días</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA final -->
<section class="relative overflow-hidden bg-gradient-to-r from-brand-primary via-[#6b21a8] to-brand-accent text-white py-10">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.16),transparent_35%)] opacity-60"></div>
    <div class="relative max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <p class="text-sm uppercase tracking-[0.32em] text-white/70" data-i18n="srv-next-label">Siguiente paso</p>
            <h3 class="text-2xl md:text-3xl font-display font-semibold" data-i18n="srv-next-title">Cuéntanos qué quieres automatizar o lanzar</h3>
            <p class="text-white/80" data-i18n="srv-next-desc">Respondemos en menos de 24 horas con un plan de acción y tiempos estimados.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo app_url('contacto.php'); ?>#contacto-form" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-brand-ink font-semibold shadow-soft hover:translate-y-[-2px] transition">
                <i class="fas fa-calendar-check"></i> <span data-i18n="srv-next-call">Agendar llamada</span>
            </a>
            <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-eye"></i> <span data-i18n="srv-next-portfolio">Ver portafolio</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>





