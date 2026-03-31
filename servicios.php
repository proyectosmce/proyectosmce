<?php require_once 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Servicios -->
<section class="relative overflow-hidden bg-gradient-to-br from-brand-ink via-[#120c2c] to-brand-dark text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-hero-mesh opacity-80"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-brand-primary/25 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-brand-accent/20 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-layer-group mr-2 text-brand-accent"></i> <span class="i18n-srv-badge" data-i18n="srv-badge">Servicios · Proyectos MCE</span>
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight i18n-srv-hero-title" data-i18n="srv-hero-title">Software a medida para operaciones, ventas y servicio</h1>
                <p class="text-lg text-blue-50 max-w-3xl i18n-srv-hero-sub" data-i18n="srv-hero-sub">
                    Te acompañamos de punta a punta: entendemos el proceso, diseñamos la experiencia, desarrollamos, probamos, desplegamos y dejamos todo documentado.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-brand-accent mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-white/80 i18n-srv-b1" data-i18n="srv-b1">Sistemas de gestión alineados a procesos y métricas del negocio.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-brand-accent mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-white/80 i18n-srv-b2" data-i18n="srv-b2">Sitios y landing pages pensadas para vender y posicionar tu marca.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-brand-accent mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-white/80 i18n-srv-b3" data-i18n="srv-b3">Automatización de procesos (bots) e integraciones con servidores (APIs) para reducir tareas manuales.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-brand-accent mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-white/80 i18n-srv-b4" data-i18n="srv-b4">Software personalizado con monitoreo, seguridad y control de accesos por rol.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>?cta=agenda#agenda-llamada" class="cta-primary inline-flex items-center justify-center text-white px-8 py-4 rounded-xl font-semibold shadow-glow hover:scale-[1.02] transition mce-call-ringing i18n-srv-btn-call" data-i18n="srv-btn-call">
                        <span class="call-ico-wrap mr-2 text-slate-900">
                            <i class="fas fa-phone-alt"></i>
                            <span class="call-ring call-ring--1"></span>
                            <span class="call-ring call-ring--2"></span>
                            <span class="call-ring call-ring--3"></span>
                        </span>
                        Agenda una reunión
                    </a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center justify-center border border-white/40 text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/10 transition i18n-srv-btn-portfolio" data-i18n="srv-btn-portfolio">
                        <i class="fas fa-eye mr-2"></i> Ver proyectos
                    </a>
                    <a href="<?php echo app_url('portafolio.php#casos-exito-portafolio'); ?>" class="inline-flex items-center justify-center border border-brand-accent/70 text-brand-accent px-8 py-4 rounded-xl font-semibold hover:bg-brand-accent hover:text-brand-ink transition i18n-srv-btn-cases" data-i18n="srv-btn-cases">
                        <i class="fas fa-trophy mr-2"></i> <span>Casos de éxito</span>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="relative overflow-hidden rounded-3xl bg-white/10 ring-1 ring-white/15 backdrop-blur-2xl p-8 shadow-glow space-y-5">
                    <div class="absolute inset-0 bg-hero-mesh opacity-50"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white/70 i18n-srv-side-label" data-i18n="srv-side-label">Qué obtienes</p>
                            <p class="text-2xl font-semibold text-white i18n-srv-side-title" data-i18n="srv-side-title">Paquetes completos, sin sorpresas</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white shadow-lg overflow-hidden mce-photo-badge">
                            <img src="<?php echo app_url('imag/MCE.jpg'); ?>" alt="MCE" class="w-full h-full object-cover">
                        </span>
                    </div>
                    <ul class="relative space-y-3 text-white/85">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-clipboard-list"></i></span>
                            <div>
                                <p class="font-semibold i18n-srv-side-plan-title" data-i18n="srv-side-plan-title">Plan inicial</p>
                                <p class="text-sm text-white/70 i18n-srv-side-plan-text" data-i18n="srv-side-plan-text">Definimos objetivos y el orden de las entregas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-pen-ruler"></i></span>
                            <div>
                                <p class="font-semibold i18n-srv-side-design-title" data-i18n="srv-side-design-title">Diseño fácil de usar</p>
                                <p class="text-sm text-white/70 i18n-srv-side-design-text" data-i18n="srv-side-design-text">Prototipos que probamos contigo antes de construir.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-server"></i></span>
                            <div>
                                <p class="font-semibold i18n-srv-side-build-title" data-i18n="srv-side-build-title">Construcción técnica</p>
                                <p class="text-sm text-white/70 i18n-srv-side-build-text" data-i18n="srv-side-build-text">Usamos tecnología actual e integramos tus sistemas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-vial-circle-check"></i></span>
                            <div>
                                <p class="font-semibold i18n-srv-side-test-title" data-i18n="srv-side-test-title">Pruebas y despliegue</p>
                                <p class="text-sm text-white/70 i18n-srv-side-test-text" data-i18n="srv-side-test-text">Probamos y publicamos de forma controlada para evitar sorpresas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-brand-primary/20 text-brand-accent border border-white/15"><i class="fas fa-headset"></i></span>
                            <div>
                                <p class="font-semibold i18n-srv-side-support-title" data-i18n="srv-side-support-title">Soporte y mejoras</p>
                                <p class="text-sm text-white/70 i18n-srv-side-support-text" data-i18n="srv-side-support-text">Monitoreamos, atendemos incidencias y planificamos mejoras.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20una%20consultoria%20para%20mi%20proyecto" target="_blank" rel="noopener" class="cta-primary relative p-4 rounded-xl border border-white/15 flex items-center justify-between hover:scale-[1.01] transition">
                        <div>
                            <p class="text-sm text-white/70 i18n-srv-side-cta-label" data-i18n="srv-side-label">Consultoría inicial</p>
                            <p class="font-semibold i18n-srv-side-cta-text" data-i18n="srv-side-cta-text">Agenda un diagnóstico sin costo</p>
                        </div>
                        <i class="fas fa-arrow-right text-white text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lista de servicios -->
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
        <div>
            <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide i18n-srv-cat-label" data-i18n="srv-cat-label">Catálogo de servicios</p>
            <h2 class="text-3xl font-bold text-slate-900 i18n-srv-cat-title" data-i18n="srv-cat-title">Soluciones diseñadas para automatizar y crecer</h2>
            <p class="text-gray-700 mt-2 max-w-3xl i18n-srv-cat-desc" data-i18n="srv-cat-desc">Elige un servicio o combínalos en un solo plan. Entregamos especificaciones claras, ambiente de pruebas, despliegues controlados y documentación para tu equipo.</p>
        </div>
        <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
            <i class="fas fa-handshake mr-2"></i> <span class="i18n-srv-cat-btn" data-i18n="srv-cat-btn">Hablemos de tu proyecto</span>
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $result = $conn->query("SELECT * FROM servicios WHERE LOWER(titulo) <> 'tiendas online' ORDER BY orden");
        $i18nMap = [
            'desarrollo web a medida' => 'srv-card1',
            'sistemas de inventario'  => 'srv-card2',
            'landing pages'           => 'srv-card3',
        ];
        while ($row = $result->fetch_assoc()):
            $slug = strtolower(trim($row['titulo'] ?? ''));
            $i18nKey = $i18nMap[$slug] ?? null;
        ?>
        <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-soft hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 relative overflow-hidden animate-on-scroll p-8 text-white">
            <!-- Barra decorativa superior -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-primary to-brand-accent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            
            <!-- Icono con animación -->
            <div class="text-5xl text-brand-accent mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                <i class="fas fa-<?php echo $row['icono'] ?? 'code'; ?>"></i>
            </div>
            
            <!-- Contenido -->
            <h3 class="text-2xl font-bold mb-3 group-hover:text-brand-primary transition <?php echo $i18nKey ? 'i18n-' . $i18nKey . '-title' : ''; ?>" <?php echo $i18nKey ? 'data-i18n="'.$i18nKey.'-title"' : ''; ?>>
                <?php echo $row['titulo']; ?>
            </h3>
            <p class="text-white/75 mb-4 line-clamp-2 <?php echo $i18nKey ? 'i18n-' . $i18nKey . '-desc' : ''; ?>" <?php echo $i18nKey ? 'data-i18n="'.$i18nKey.'-desc"' : ''; ?>>
                <?php echo $row['descripcion']; ?>
            </p>
            
            <!-- Precio y CTA en USD -->
            <div class="flex flex-col mb-6 bg-white/5 p-4 rounded-2xl border border-white/10 shadow-sm">
                <?php
                $usdPrice = "Consultar";
                if (stripos($row['titulo'], 'landing') !== false) $usdPrice = "100";
                elseif (stripos($row['titulo'], 'desarrollo') !== false) $usdPrice = "450"; // Promedio junior
                elseif (stripos($row['titulo'], 'inventario') !== false) $usdPrice = "800";
                ?>
                <div class="flex flex-col">
                    <span class="text-xs font-black uppercase tracking-widest text-[#7C3AED] mb-1 i18n-srv-from" data-i18n="srv-from">Desde</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-[#7C3AED] tracking-tighter">$<?php echo $usdPrice; ?></span>
                        <span class="text-2xl font-bold text-[#7C3AED]">USD</span>
                    </div>
                </div>
            </div>
            
            <!-- Botón con efecto directo a WhatsApp (Color conservado en hover) -->
            <a href="https://wa.me/573114125971?text=Hola!%20Vengo%20de%20la%20web%20y%20quiero%20consultar%20por%20el%20servicio%20de%20<?php echo urlencode($row['titulo']); ?>" 
               target="_blank"
               rel="noopener"
               data-track-lead="<?php echo htmlspecialchars($row['titulo']); ?>"
               class="mt-4 inline-flex items-center text-brand-accent font-semibold transition-colors duration-300">
                <span class="i18n-srv-ask-quote" data-i18n="srv-ask-quote">Solicitar presupuesto</span>
                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition"></i>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- CTA final -->
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-r from-brand-primary via-[#6b21a8] to-brand-accent text-white rounded-2xl mce-rounded-panel p-10 shadow-glow flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-sm font-semibold text-white/80 uppercase tracking-wide i18n-srv-next-label" data-i18n="srv-next-label">Siguiente paso</p>
            <h3 class="text-2xl font-bold i18n-srv-next-title" data-i18n="srv-next-title">Cuéntanos qué quieres automatizar o lanzar</h3>
            <p class="text-white/85 mt-2 i18n-srv-next-desc" data-i18n="srv-next-desc">Respondemos en menos de 24 horas con un plan de acción y tiempos estimados.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo app_url('contacto.php'); ?>?cta=agenda#agenda-llamada" class="cta-primary inline-flex items-center px-5 py-3 rounded-xl font-semibold shadow-soft hover:-translate-y-[2px] transition mce-call-ringing">
                <span class="call-ico-wrap mr-2 text-slate-900">
                    <i class="fas fa-phone-alt"></i>
                    <span class="call-ring call-ring--1"></span>
                    <span class="call-ring call-ring--2"></span>
                    <span class="call-ring call-ring--3"></span>
                </span>
                <span class="i18n-srv-next-call" data-i18n="srv-next-call">Agendar llamada</span>
            </a>
            <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-eye mr-2"></i> <span class="i18n-srv-next-portfolio" data-i18n="srv-next-portfolio">Ver portafolio</span>
            </a>
            <a href="<?php echo app_url('portafolio.php#casos-exito-portafolio'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-trophy mr-2"></i> <span class="i18n-srv-next-cases" data-i18n="srv-next-cases">Casos de éxito</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
