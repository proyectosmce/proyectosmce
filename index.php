<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php
$portfolioProjects = fetchPortfolioProjects($conn);
$featuredProject = $portfolioProjects[0] ?? null;

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
                    Software a medida que impulsa la operación de tu empresa
                </h1>
                <p class="text-lg md:text-xl text-blue-50 max-w-3xl">
                    Creamos software y páginas web a medida para empresas que quieren automatizar y crecer, con equipos senior que acompañan desde la estrategia hasta el despliegue.
                </p>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-diagram-project mr-2 text-yellow-300"></i>Sistemas de gestión
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-globe mr-2 text-yellow-300"></i>Páginas web profesionales
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-gears mr-2 text-yellow-300"></i>Automatización de procesos
                    </span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm">
                        <i class="fas fa-code mr-2 text-yellow-300"></i>Software personalizado
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition">
                        <i class="fas fa-rocket mr-2"></i> Agenda un diagnóstico
                    </a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition">
                        <i class="fas fa-eye mr-2"></i> Ver portafolio
                    </a>
                    <a href="#servicios" class="inline-flex items-center text-blue-100 hover:text-white font-semibold">
                        <span>Ver servicios</span>
                        <i class="fas fa-arrow-down ml-2"></i>
                    </a>
                </div>

                <div class="grid sm:grid-cols-3 gap-4 pt-6 border-t border-white/10 max-w-4xl">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Acompañamiento end-to-end</p>
                        <p class="text-lg font-semibold">Discovery, UX, desarrollo y soporte</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Entrega iterativa</p>
                        <p class="text-lg font-semibold">Sprints quincenales con visibilidad</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <p class="text-sm text-blue-100">Stack moderno</p>
                        <p class="text-lg font-semibold">Cloud, APIs seguras e integraciones</p>
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
                    Somos un equipo multidisciplinario que une estrategia digital, diseño y desarrollo para lanzar productos y plataformas que resuelven procesos reales. Trabajamos con empresas que buscan automatizar, escalar ventas y ofrecer experiencias digitales de alto nivel.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-magnifying-glass-chart"></i></span>
                        <p class="text-gray-800">Discovery estratégico y priorización junto a stakeholders del negocio.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-laptop-code"></i></span>
                        <p class="text-gray-800">Arquitecturas modernas, APIs seguras e integraciones con tus sistemas existentes.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-people-group"></i></span>
                        <p class="text-gray-800">UX/UI centrado en usuarios internos y clientes para acelerar adopción.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 text-blue-600"><i class="fas fa-headset"></i></span>
                        <p class="text-gray-800">Acompañamiento post-lanzamiento con monitoreo, soporte y roadmap evolutivo.</p>
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

<!-- Servicios Destacados -->
<section id="servicios" class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid md:grid-cols-2 gap-12 items-start mb-12">
        <div class="space-y-4">
            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full">Servicios</span>
            <h2 class="text-3xl font-bold text-slate-900 leading-tight">Creamos software y páginas web a medida para empresas que quieren automatizar y crecer.</h2>
            <p class="text-gray-700">Integramos estrategia, diseño y desarrollo para que cada entrega se conecte con tus metas operativas, comerciales y de servicio. Trabajamos con procesos claros, demos constantes y acompañamiento post lanzamiento.</p>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center px-3 py-2 rounded-full bg-slate-900 text-white text-sm"><i class="fas fa-link mr-2"></i>Integraciones con ERP/CRM</span>
                <span class="inline-flex items-center px-3 py-2 rounded-full bg-blue-50 text-blue-700 text-sm"><i class="fas fa-file-alt mr-2"></i>Documentación y handoff</span>
                <span class="inline-flex items-center px-3 py-2 rounded-full bg-emerald-50 text-emerald-700 text-sm"><i class="fas fa-shield-alt mr-2"></i>Seguridad y observabilidad</span>
            </div>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
            <h3 class="text-xl font-semibold text-slate-900 mb-4">Nuestro foco</h3>
            <ul class="space-y-3 text-gray-800">
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span>
                    <span>Sistemas de gestión hechos a tu medida para controlar operaciones.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span>
                    <span>Páginas web profesionales orientadas a convertir y posicionar tu marca.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span>
                    <span>Automatización de procesos para reducir tiempos operativos y errores.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span>
                    <span>Software personalizado con integraciones API y arquitectura escalable.</span>
                </li>
            </ul>
            <div class="mt-6 p-4 rounded-xl bg-blue-50 text-blue-800 flex items-center justify-between">
                <span>Agenda una sesión y recibe una propuesta en pocos días.</span>
                <i class="fas fa-arrow-right"></i>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <?php
        $result = $conn->query("SELECT * FROM servicios WHERE destacado = 1 ORDER BY orden LIMIT 3");
        while ($row = $result->fetch_assoc()):
        ?>
        <!-- En servicios.php, dentro del grid -->
        <div class="group bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 relative overflow-hidden animate-on-scroll">
            <!-- Barra decorativa superior -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
            
            <!-- Icono con animación -->
            <div class="text-5xl text-blue-600 mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                <i class="fas fa-<?php echo $row['icono'] ?? 'code'; ?>"></i>
            </div>
            
            <!-- Contenido -->
            <h3 class="text-2xl font-bold mb-3 group-hover:text-blue-600 transition"><?php echo $row['titulo']; ?></h3>
            <p class="text-gray-600 mb-4"><?php echo $row['descripcion']; ?></p>
            
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

<!-- Proyecto Destacado -->
<section class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Proyecto Destacado</h2>
        <?php if ($featuredProject): ?>
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
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="md:flex">
                    <div class="md:w-1/2">
                        <img
                            src="<?php echo htmlspecialchars($featuredProject['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($featuredProject['titulo'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="w-full h-64 md:h-full object-cover"
                        >
                    </div>
                    <div class="md:w-1/2 p-8">
                        <span class="text-blue-600 font-semibold"><?php echo htmlspecialchars($featuredProject['categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="text-2xl font-bold mt-2 mb-4"><?php echo htmlspecialchars($featuredProject['titulo'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($featuredDescription, ENT_QUOTES, 'UTF-8'); ?></p>

                        <div class="grid sm:grid-cols-2 gap-3 text-gray-600 mb-6">
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
                                <span>Proyecto destacado</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <?php if ($featuredHasLink): ?>
                                <a
                                    href="<?php echo htmlspecialchars($featuredUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $featuredIsExternal ? 'target="_blank" rel="noopener"' : ''; ?>
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
                                >
                                    Ver proyecto
                                </a>
                            <?php endif; ?>

                            <?php if ($repoUrl !== ''): ?>
                                <a
                                    href="<?php echo htmlspecialchars($repoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    target="_blank"
                                    rel="noopener"
                                    class="border border-slate-300 text-slate-700 px-6 py-3 rounded-lg hover:bg-slate-50 transition"
                                >
                                    Ver repositorio
                                </a>
                            <?php endif; ?>

                            <a href="<?php echo app_url('portafolio.php'); ?>" class="border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition">
                                Ver más proyectos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-xl p-10 text-center text-gray-600">
                Aún no hay proyectos destacados publicados. Revisa el portafolio completo para ver los trabajos disponibles.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
