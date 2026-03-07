<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php
$projects = fetchPortfolioProjects($conn);
$categories = fetchPortfolioCategories($projects);
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Portafolio -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-28 -right-10 w-96 h-96 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-briefcase mr-2 text-yellow-300"></i> Portafolio · Proyectos MCE
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Productos digitales y sistemas que ya están funcionando</h1>
                <p class="text-lg text-blue-50 max-w-3xl">
                    Mostramos implementaciones reales: sistemas de gestión, portales transaccionales, automatizaciones y sitios web de marca que impulsan operaciones y ventas.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-industry mr-2 text-yellow-300"></i>Retail · Logística · Servicios</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-code-branch mr-2 text-yellow-300"></i>APIs e integraciones</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-lock mr-2 text-yellow-300"></i>Enfoque en seguridad y calidad</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition">
                        <i class="fas fa-rocket mr-2"></i> Solicitar demo
                    </a>
                    <a href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition">
                        <i class="fas fa-layer-group mr-2"></i> Ver servicios
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Qué verás</p>
                            <p class="text-2xl font-semibold text-white">Casos listos para inspirar tu proyecto</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-laptop-code"></i></span>
                            <div>
                                <p class="font-semibold">Aplicaciones y backoffice</p>
                                <p class="text-sm text-blue-100">Portales internos, paneles y flujos operativos.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-globe"></i></span>
                            <div>
                                <p class="font-semibold">Sitios y eCommerce</p>
                                <p class="text-sm text-blue-100">Brand sites, catálogos y funnels de conversión.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-gears"></i></span>
                            <div>
                                <p class="font-semibold">Automatizaciones</p>
                                <p class="text-sm text-blue-100">Integraciones API y reducción de tareas manuales.</p>
                            </div>
                        </li>
                    </ul>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Entrega con visibilidad</p>
                            <p class="font-semibold">Demos frecuentes y documentación</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtros -->
<section class="max-w-7xl mx-auto px-4 -mt-10 lg:-mt-14">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Filtrar proyectos</p>
            <p class="text-gray-700">Explora por categoría o mira todo el portafolio.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="filter-btn active px-4 py-2 rounded-full bg-blue-600 text-white" data-filter="all">Todos</button>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn px-4 py-2 rounded-full bg-gray-200 hover:bg-gray-300" data-filter="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Grid de proyectos -->
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="proyectos-grid">
        <?php if (!$projects): ?>
            <div class="md:col-span-2 lg:col-span-3 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-600">
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
                    >
                </a>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-blue-600 font-semibold"><?php echo htmlspecialchars($project['categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">Caso real</span>
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
                    <p class="text-gray-600"><?php echo htmlspecialchars($descriptionPreview, ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="flex justify-between items-center gap-4 pt-1">
                        <span class="text-gray-500 text-sm"><?php echo htmlspecialchars($clientLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if ($hasLink): ?>
                            <a
                                href="<?php echo htmlspecialchars($projectUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $isExternal ? 'target="_blank" rel="noopener"' : ''; ?>
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold"
                            >
                                Ver más <i class="fas fa-arrow-right ml-2 text-sm"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-sm text-gray-400">Sin enlace disponible</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA final -->
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl p-10 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">¿Listo para tu caso?</p>
            <h3 class="text-2xl font-bold">Te mostramos un demo similar a tu industria</h3>
            <p class="text-blue-100 mt-2">Agenda una llamada y en 24 horas preparamos un recorrido de referencia.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-blue-50 transition">
                <i class="fas fa-comments mr-2"></i> Agendar llamada
            </a>
            <a href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-layer-group mr-2"></i> Ver servicios
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
