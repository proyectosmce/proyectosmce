<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php
// Manejo de envío de testimonios (solo alta, sin edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'nuevo_testimonio') {
    $nombre  = trim($_POST['nombre'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $proyId  = (int) ($_POST['proyecto_id'] ?? 0);

    if ($nombre !== '' && $mensaje !== '' && $proyId > 0) {
        if ($stmt = $conn->prepare('INSERT INTO testimonios (nombre, testimonio, proyecto_id, valoracion, destacado) VALUES (?, ?, ?, 5, 0)')) {
            $stmt->bind_param('ssi', $nombre, $mensaje, $proyId);
            $stmt->execute();
            $stmt->close();
            header('Location: index.php?testimonio=ok#testimonios');
            exit;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-20 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-32 -right-10 w-96 h-96 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-28 lg:py-32">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-left">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-sparkles mr-2 text-yellow-300"></i>
                    Proyectos MCE · Fábrica de software
                </span>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight">
                    Arquitectura y software a medida para operaciones críticas
                </h1>
                <p class="text-lg md:text-xl text-blue-50 max-w-3xl">
                    Diseñamos, construimos y operamos plataformas web y backoffice que sostienen ventas, inventario, logística y servicio al cliente con métricas y trazabilidad.
                </p>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-diagram-project mr-2 text-yellow-300"></i>Modelado de dominio y procesos
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-pen-ruler mr-2 text-yellow-300"></i>Discovery, UX/UI y prototipos validados
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-bolt mr-2 text-yellow-300"></i>Sprints con QA, CI/CD y monitoreo
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-database mr-2 text-yellow-300"></i>Integraciones y calidad de datos
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition">
                        <i class="fas fa-rocket mr-2"></i> Plan técnico inicial
                    </a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition">
                        <i class="fas fa-eye mr-2"></i> Ver casos en producción
                    </a>
                    <a href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center text-blue-100 hover:text-white font-semibold">
                        <span>Servicios</span>
                        <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 pt-6 border-t border-white/10 max-w-4xl">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Gobernanza</p>
                        <p class="text-lg font-semibold">Historias priorizadas, Definition of Done y revisiones</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Entrega iterativa</p>
                        <p class="text-lg font-semibold">Demos quincenales, QA automatizado y pruebas regresivas</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Operabilidad</p>
                        <p class="text-lg font-semibold">Observabilidad, alertas y soporte continuo (SRE)</p>
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
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-clipboard-check"></i></span>
                            <div>
                                <p class="font-semibold">Kickoff y definición</p>
                                <p class="text-sm text-blue-100">Entendemos procesos clave y priorizamos entregables.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-pen-ruler"></i></span>
                            <div>
                                <p class="font-semibold">UX/UI profesional</p>
                                <p class="text-sm text-blue-100">Interfaces claras para equipos operativos y clientes.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-bolt"></i></span>
                            <div>
                                <p class="font-semibold">Desarrollo ágil</p>
                                <p class="text-sm text-blue-100">Sprints de desarrollo con demos y QA continuo.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-shield-halved"></i></span>
                            <div>
                                <p class="font-semibold">Seguridad y soporte</p>
                                <p class="text-sm text-blue-100">Observabilidad, backups y soporte posterior al lanzamiento.</p>
                            </div>
                        </li>
                    </ul>
                    <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-white/10">
                        <div>
                            <p class="text-sm text-blue-100">Disponibilidad inmediata</p>
                            <p class="font-semibold">Agenda tu sesión de discovery sin costo</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300 text-xl"></i>
                    </div>
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
                <h2 class="text-3xl font-bold text-slate-900">Proyectos MCE en pocas palabras</h2>
                <p class="text-gray-700 leading-relaxed">
                    Equipo senior de ingeniería y producto que arma soluciones end-to-end: entendemos el dominio, diseñamos la experiencia y entregamos software estable listo para producción.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-magnifying-glass-chart"></i></span>
                        <p class="text-gray-800">Discovery técnico/funcional con KPIs, backlog priorizado y dependencias mapeadas.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-laptop-code"></i></span>
                        <p class="text-gray-800">Arquitecturas modernas, APIs seguras, integración con ERP/CRM y servicios externos.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-people-group"></i></span>
                        <p class="text-gray-800">UX/UI centrado en usuarios internos y clientes para acelerar adopción.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-headset"></i></span>
                        <p class="text-gray-800">Soporte post-lanzamiento con monitoreo, alertas y roadmap evolutivo.</p>
                    </div>
                </div>
            </div>
            <div class="p-10 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white space-y-5">
                <h3 class="text-2xl font-bold">Cómo trabajamos</h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">1</span>
                        <div>
                            <p class="font-semibold">Kickoff y plan</p>
                            <p class="text-sm text-blue-100">Revisamos objetivos, KPIs y alcances técnicos.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">2</span>
                        <div>
                            <p class="font-semibold">Diseño y prototipos</p>
                            <p class="text-sm text-blue-100">Validamos flujos con tu equipo antes de codificar.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">3</span>
                        <div>
                            <p class="font-semibold">Desarrollo incremental</p>
                            <p class="text-sm text-blue-100">Sprints con demos, QA y documentación clara.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center font-semibold">4</span>
                        <div>
                            <p class="font-semibold">Lanzamiento y soporte</p>
                            <p class="text-sm text-blue-100">Despliegue, observabilidad y mejoras continuas.</p>
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

<?php include 'includes/footer.php'; ?>
