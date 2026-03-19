<?php require_once 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Servicios -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-layer-group mr-2 text-yellow-300"></i> Servicios · Proyectos MCE
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Software a medida para operaciones, ventas y servicio</h1>
                <p class="text-lg text-blue-50 max-w-3xl">
                    Te acompañamos de punta a punta: entendemos el proceso, diseñamos la experiencia, desarrollamos, probamos, desplegamos y dejamos todo documentado.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Sistemas de gestión alineados a procesos y métricas del negocio.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Sitios, tiendas y landing pages pensadas para vender y posicionar tu marca.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Automatización de procesos (bots) e integraciones con servidores (APIs) para reducir tareas manuales.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Software personalizado con monitoreo, seguridad y control de accesos por rol.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition mce-call-ringing">
                        <span class="call-ico-wrap mr-2 text-slate-900">
                            <i class="fas fa-phone-alt"></i>
                            <span class="call-ring call-ring--1"></span>
                            <span class="call-ring call-ring--2"></span>
                            <span class="call-ring call-ring--3"></span>
                        </span>
                        Agenda una reunión
                    </a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition">
                        <i class="fas fa-eye mr-2"></i> Ver proyectos
                    </a>
                    <a href="<?php echo app_url('portafolio.php#casos-exito-portafolio'); ?>" class="inline-flex items-center justify-center border-2 border-amber-300 text-amber-200 px-8 py-4 rounded-xl font-semibold hover:bg-amber-200 hover:text-slate-900 transition">
                        <i class="fas fa-trophy mr-2"></i> Casos de éxito
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Qué obtienes</p>
                            <p class="text-2xl font-semibold text-white">Paquetes completos, sin sorpresas</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-clipboard-list"></i></span>
                            <div>
                                <p class="font-semibold">Plan inicial</p>
                                <p class="text-sm text-blue-100">Definimos objetivos y el orden de las entregas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-pen-ruler"></i></span>
                            <div>
                                <p class="font-semibold">Diseño fácil de usar</p>
                                <p class="text-sm text-blue-100">Prototipos que probamos contigo antes de construir.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-server"></i></span>
                            <div>
                                <p class="font-semibold">Construcción técnica</p>
                                <p class="text-sm text-blue-100">Usamos tecnología actual e integramos tus sistemas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-vial-circle-check"></i></span>
                            <div>
                                <p class="font-semibold">Pruebas y despliegue</p>
                                <p class="text-sm text-blue-100">Probamos y publicamos de forma controlada para evitar sorpresas.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-headset"></i></span>
                            <div>
                                <p class="font-semibold">Soporte y mejoras</p>
                                <p class="text-sm text-blue-100">Monitoreamos, atendemos incidencias y planificamos mejoras.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20una%20consultoria%20para%20mi%20proyecto" target="_blank" rel="noopener" class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between hover:bg-white/10 transition">
                        <div>
                            <p class="text-sm text-blue-100">Consultoría inicial</p>
                            <p class="font-semibold">Agenda un diagnóstico sin costo</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
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
            <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Catálogo de servicios</p>
            <h2 class="text-3xl font-bold text-slate-900">Soluciones diseñadas para automatizar y crecer</h2>
            <p class="text-gray-700 mt-2 max-w-3xl">Elige un servicio o combínalos en un solo plan. Entregamos especificaciones claras, ambiente de pruebas, despliegues controlados y documentación para tu equipo.</p>
        </div>
        <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
            <i class="fas fa-handshake mr-2"></i> Hablemos de tu proyecto
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $result = $conn->query("SELECT * FROM servicios ORDER BY orden");
        while ($row = $result->fetch_assoc()):
        ?>
        <div class="group bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 relative overflow-hidden animate-on-scroll">
            <!-- Barra decorativa superior -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            
            <!-- Icono con animación -->
            <div class="text-5xl text-blue-600 mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                <i class="fas fa-<?php echo $row['icono'] ?? 'code'; ?>"></i>
            </div>
            
            <!-- Contenido -->
            <h3 class="text-2xl font-bold mb-3 group-hover:text-blue-600 transition"><?php echo $row['titulo']; ?></h3>
            <p class="text-gray-600 mb-4 line-clamp-2"><?php echo $row['descripcion']; ?></p>
            
            <!-- Precio y CTA -->
            <div class="flex justify-between items-center mb-4">
                <span class="text-3xl font-bold text-blue-600">$<?php echo number_format($row['precio_desde']); ?></span>
                <span class="text-gray-500 text-sm">+IVA</span>
            </div>
            
            <!-- Botón con efecto -->
            <a href="<?php echo app_url('contacto.php'); ?>?servicio=<?php echo urlencode($row['titulo']); ?>" 
               class="mt-4 inline-flex items-center text-blue-600 group-hover:text-blue-800 font-semibold">
                <span>Solicitar presupuesto</span>
                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition"></i>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- CTA final -->
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl mce-rounded-panel p-10 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">Siguiente paso</p>
            <h3 class="text-2xl font-bold">Cuéntanos qué quieres automatizar o lanzar</h3>
            <p class="text-blue-100 mt-2">Respondemos en menos de 24 horas con un plan de acción y tiempos estimados.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada" class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-blue-50 transition mce-call-ringing">
                <span class="call-ico-wrap mr-2 text-slate-900">
                    <i class="fas fa-phone-alt"></i>
                    <span class="call-ring call-ring--1"></span>
                    <span class="call-ring call-ring--2"></span>
                    <span class="call-ring call-ring--3"></span>
                </span>
                Agendar llamada
            </a>
            <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-eye mr-2"></i> Ver portafolio
            </a>
            <a href="<?php echo app_url('portafolio.php#casos-exito-portafolio'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-amber-300 text-amber-100 font-semibold hover:bg-amber-200 hover:text-slate-900 transition">
                <i class="fas fa-trophy mr-2"></i> Casos de éxito
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
