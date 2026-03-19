<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section alineado con otras secciones -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white mce-rounded-hero">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-20 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-32 -right-10 w-96 h-96 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-28 lg:py-32">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-left">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-sparkles mr-2 text-yellow-300"></i>
                    Proyectos MCE >·< Software a medida
                </span>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight">
                    Software a medida para que tu operación no se detenga
                </h1>
                <p class="text-lg md:text-xl text-blue-50 max-w-3xl">
                    Planificamos, diseñamos y desarrollamos plataformas web que soportan ventas, inventarios y atención al cliente con control total y visibilidad.
                </p>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-diagram-project mr-2 text-yellow-300"></i>Entendemos tu proceso primero
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-pen-ruler mr-2 text-yellow-300"></i>Diseño gráfico (UX/UI) con maquetas claras
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-bolt mr-2 text-yellow-300"></i>Entregas cortas con pruebas y control
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-database mr-2 text-yellow-300"></i>Conexiones a servidores (APIs) y datos en orden
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center justify-center bg-white text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-slate-900/20 hover:bg-gray-100 transition">
                        <i class="fas fa-rocket mr-2"></i> Armar mi plan
                    </a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition">
                        <i class="fas fa-eye mr-2"></i> Ver casos en vivo
                    </a>
                    <a href="#casos-exito" class="inline-flex items-center justify-center border-2 border-yellow-300 text-yellow-300 px-8 py-4 rounded-xl font-semibold hover:bg-yellow-300 hover:text-slate-900 transition">
                        <i class="fas fa-trophy mr-2"></i> Casos de éxito
                    </a>
                    <a href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center text-blue-100 hover:text-white font-semibold">
                        <span>Servicios</span>
                        <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 pt-6 border-t border-white/10 max-w-4xl">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Gobernanza</p>
                        <p class="text-lg font-semibold">Backlog claro, prioridades y criterios de entrega</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Entrega iterativa</p>
                        <p class="text-lg font-semibold">Demos quincenales, pruebas automáticas y regresiones</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Operabilidad</p>
                        <p class="text-lg font-semibold">Monitoreo, alertas y soporte continuo</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Hoja de ruta MCE</p>
                            <p class="text-2xl font-semibold text-white">Tu proyecto, con control total</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-clipboard-check"></i></span>
                            <div>
                                <p class="font-semibold">Arranque y plan</p>
                                <p class="text-sm text-blue-100">Conversamos de tus procesos y definimos lo más importante primero.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-pen-ruler"></i></span>
                            <div>
                                <p class="font-semibold">Diseño claro</p>
                                <p class="text-sm text-blue-100">Pantallas fáciles de usar para tu equipo y tus clientes.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-bolt"></i></span>
                            <div>
                                <p class="font-semibold">Construcción por etapas</p>
                                <p class="text-sm text-blue-100">Avanzamos en ciclos cortos con avances y pruebas visibles.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-shield-halved"></i></span>
                            <div>
                                <p class="font-semibold">Cuidado y soporte</p>
                                <p class="text-sm text-blue-100">Copias de seguridad, monitoreo y ayuda después de lanzar.</p>
                            </div>
                        </li>
                    </ul>
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20agendar%20un%20discovery%20con%20MCE" target="_blank" rel="noopener" class="flex items-center justify-between p-4 rounded-xl bg-white/10 border border-white/20 hover:bg-white/15 transition">
                        <div>
                            <p class="text-sm text-blue-100">Disponibilidad inmediata</p>
                            <p class="font-semibold">Agenda tu sesión de discovery sin costo</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300 text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Presentación -->
<section class="relative max-w-7xl mx-auto px-4 -mt-10 lg:-mt-16">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="p-10 space-y-4">
                <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Presentación</p>
                <h2 class="text-3xl font-bold text-slate-900">Proyectos MCE </h2>
                <p class="text-gray-700 leading-relaxed">
                    Equipo con experiencia que entiende tu negocio, diseña la experiencia y entrega software confiable listo para producción.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-magnifying-glass-chart"></i></span>
                        <p class="text-gray-800">Mapeamos objetivos, usuarios y métricas para no programar a ciegas.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-pen-ruler"></i></span>
                        <p class="text-gray-800">Diseño gráfico (UX/UI) con flujos claros y pantallas que cualquier persona entiende.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-server"></i></span>
                        <p class="text-gray-800">Conexiones a servidores (APIs) y bases de datos para que todo hable entre sí.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-headset"></i></span>
                        <p class="text-gray-800">Soporte, monitoreo y mejoras continuas después de lanzar.</p>
                    </div>
                </div>
            </div>
            <div class="p-10 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white space-y-5">
                <h3 class="text-2xl font-bold">Cómo trabajamos</h3>
                <ul class="space-y-4 timeline">
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">1</span>
                        <div>
                            <p class="font-semibold">Kickoff y plan</p>
                            <p class="text-sm text-blue-100">Revisamos tus objetivos y qué debe hacer la solución.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">2</span>
                        <div>
                            <p class="font-semibold">Diseño y prototipos</p>
                            <p class="text-sm text-blue-100">Te mostramos maquetas para confirmar que el flujo es correcto.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">3</span>
                        <div>
                            <p class="font-semibold">Desarrollo incremental</p>
                            <p class="text-sm text-blue-100">Entregamos en ciclos cortos con pruebas y notas claras.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 timeline-item">
                        <span class="timeline-bullet w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">4</span>
                        <div>
                            <p class="font-semibold">Lanzamiento y soporte</p>
                            <p class="text-sm text-blue-100">Publicamos, monitoreamos y mejoramos de forma continua.</p>
                        </div>
                    </li>
                </ul>
                <div class="p-4 border border-white/20 rounded-xl bg-white/10">
                    <p class="text-sm text-blue-100">Disponibilidad</p>
                    <p class="font-semibold">Equipo listo para comenzar en menos de 7 días</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Caso de éxito -->
<section id="casos-exito" class="max-w-7xl mx-auto px-4 py-14">
    <div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-orange-400 text-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-amber-200">
        <div class="grid md:grid-cols-12 gap-0">
            <div class="md:col-span-7 p-10 space-y-5 bg-white/60 backdrop-blur">
                <p class="text-sm font-semibold uppercase tracking-wide text-amber-700">Caso de éxito</p>
                <h3 class="text-3xl font-bold">Destello de Oro 18K</h3>
                <p class="text-lg text-slate-800">Sistema integral para joyería: ventas en tienda, inventario, garantías y control de caja en tiempo real.</p>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Reto</p>
                        <p class="text-sm text-slate-800">Unificar ventas, stock y garantías en una sola plataforma fácil de usar en tienda física.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Resultado</p>
                        <p class="text-sm text-slate-800">Ventas y stock consolidados, garantías trazables y cierres de caja diarios sin hojas de cálculo.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold"><i class="fas fa-store mr-2"></i>Punto de venta</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold"><i class="fas fa-boxes-stacked mr-2"></i>Inventario</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold"><i class="fas fa-shield-heart mr-2"></i>Garantías</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold"><i class="fas fa-cash-register mr-2"></i>Caja y pagos</span>
                </div>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="<?php echo app_url('destello-oro.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
                        <i class="fas fa-play mr-2"></i> Ver caso completo
                    </a>
                    <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada" class="inline-flex items-center px-5 py-3 rounded-xl border border-slate-900 text-slate-900 font-semibold hover:bg-slate-900 hover:text-white transition">
                        <i class="fas fa-phone-alt mr-2"></i> Agenda una llamada
                    </a>
                </div>
            </div>
            <div class="md:col-span-5 relative min-h-[260px] bg-[radial-gradient(circle_at_30%_30%,rgba(255,255,255,0.35),transparent_45%),radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.22),transparent_40%),radial-gradient(circle_at_60%_80%,rgba(255,255,255,0.28),transparent_45%)]">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(15,23,42,0.25),rgba(15,23,42,0.12))]"></div>
                <div class="relative h-full w-full flex items-center justify-center p-8">
                    <div class="bg-white/85 backdrop-blur-lg rounded-2xl shadow-2xl border border-amber-100 p-6 max-w-sm w-full space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Joyería</p>
                                <p class="text-lg font-bold text-slate-900">Destello de Oro 18K</p>
                            </div>
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-500 text-white font-bold">DO</span>
                        </div>
                        <ul class="space-y-2 text-slate-800 text-sm">
                            <li class="flex items-start gap-2"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Venta en tienda con cobro y saldos.</span></li>
                            <li class="flex items-start gap-2"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Inventario en tiempo real por pieza.</span></li>
                            <li class="flex items-start gap-2"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Garantías trazables y reposición.</span></li>
                            <li class="flex items-start gap-2"><i class="fas fa-circle-check text-amber-600 mt-0.5"></i><span>Reportes de caja y gastos diarios.</span></li>
                        </ul>
                        <a href="<?php echo app_url('destello-oro.php'); ?>" class="inline-flex items-center justify-center w-full mt-2 px-4 py-2 rounded-xl bg-amber-500 text-white font-semibold shadow hover:bg-amber-600 transition">
                            Ver pantallas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
