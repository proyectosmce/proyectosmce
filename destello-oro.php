<?php require_once 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
// Configuración de contacto
$WA_DESTELLO = getenv('WA_DESTELLO') ?: '573182687488'; // Reemplaza por el número oficial
$WA_MESSAGE  = urlencode('¡Hola!, vi su información en la página Proyectos MCE y deseo adquirir un producto con ustedes');
$WA_LINK     = "https://wa.me/{$WA_DESTELLO}?text={$WA_MESSAGE}";

// Definición de flujos con capturas para el proyecto "Destello de Oro 18K"
$adminFlows = [
    [
        'title' => 'Acceso y recuperación de cuenta',
        'summary' => 'Pantallas de ingreso y soporte de credenciales para garantizar que solo personal autorizado entre al panel.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/login.png', 'caption' => 'Login de administrador', 'alt' => 'Formulario de ingreso para administrador con usuario y contraseña'],
            ['src' => '/proyectosmce/imag/admin/credenciales-acceso.png', 'caption' => 'Credenciales entregadas', 'alt' => 'Vista donde se muestran las credenciales generadas para el administrador'],
            ['src' => '/proyectosmce/imag/admin/recuperar-contraseña.png', 'caption' => 'Recuperar contraseña', 'alt' => 'Pantalla para solicitar recuperación de contraseña por correo'],
        ],
    ],
    [
        'title' => 'Cambio de contraseña',
        'summary' => 'Flujo guiado para que el administrador actualice su contraseña con validación de seguridad.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/cambiar-contraseña1.png', 'caption' => 'Acceso al cambio', 'alt' => 'Formulario inicial para cambiar la contraseña actual'],
            ['src' => '/proyectosmce/imag/admin/cambiar-contraseña2.png', 'caption' => 'Confirmación', 'alt' => 'Pantalla de confirmación de nueva contraseña'],
        ],
    ],
    [
        'title' => 'Panel principal y perfil',
        'summary' => 'Tablero con métricas rápidas y acceso al perfil personal para mantener datos al día.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/panel-admin.png', 'caption' => 'Panel administrador', 'alt' => 'Dashboard con accesos a ventas, inventario y gastos'],
            ['src' => '/proyectosmce/imag/admin/info-personal.png', 'caption' => 'Información personal', 'alt' => 'Ficha editable con datos de contacto del administrador'],
        ],
    ],
    [
        'title' => 'Inventario y altas de producto',
        'summary' => 'Control total del stock: ver existencias, crear nuevos artículos y surtir nuevamente.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/tabla-inventario.png', 'caption' => 'Tabla de inventario', 'alt' => 'Listado de productos de joyería con existencias y precios'],
            ['src' => '/proyectosmce/imag/admin/agregar-nuevo-producto.png', 'caption' => 'Alta de producto', 'alt' => 'Formulario para agregar un nuevo producto de oro 18K con precio y descripción'],
            ['src' => '/proyectosmce/imag/admin/surtir-inventario.png', 'caption' => 'Surtir inventario', 'alt' => 'Interfaz para aumentar stock de un producto existente'],
        ],
    ],
    [
        'title' => 'Ventas y cobranzas',
        'summary' => 'Proceso en tres pasos para generar la venta, registrar método de pago y dejar todo conciliado.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/realizar-venta1.png', 'caption' => 'Selección de cliente y producto', 'alt' => 'Paso 1 del flujo de venta donde se elige cliente y piezas'],
            ['src' => '/proyectosmce/imag/admin/realizar-venta-2.png', 'caption' => 'Método de pago', 'alt' => 'Paso 2 para indicar forma de pago y abonos'],
            ['src' => '/proyectosmce/imag/admin/realizar-venta-3.png', 'caption' => 'Confirmación de venta', 'alt' => 'Paso 3 con resumen y confirmación final'],
            ['src' => '/proyectosmce/imag/admin/pagos-pendientes.png', 'caption' => 'Pagos pendientes', 'alt' => 'Listado de ventas con saldo pendiente para seguimiento'],
        ],
    ],
    [
        'title' => 'Gastos y control de caja',
        'summary' => 'Registro de gastos operativos y trazabilidad para cortes diarios o semanales.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/registro-gastos.png', 'caption' => 'Registro de gasto', 'alt' => 'Formulario para cargar un gasto con monto y categoría'],
            ['src' => '/proyectosmce/imag/admin/historial-gastos.png', 'caption' => 'Historial general', 'alt' => 'Tabla con todos los gastos registrados'],
            ['src' => '/proyectosmce/imag/admin/historial1.png', 'caption' => 'Detalle por fecha', 'alt' => 'Vista filtrada de movimientos en un periodo'],
            ['src' => '/proyectosmce/imag/admin/historial2.png', 'caption' => 'Detalle por concepto', 'alt' => 'Detalle de cada gasto con monto y responsable'],
        ],
    ],
    [
        'title' => 'Garantías y cambios',
        'summary' => 'Gestión de garantías para piezas de oro laminado: desde la solicitud hasta la reposición.',
        'shots' => [
            ['src' => '/proyectosmce/imag/admin/garantias.png', 'caption' => 'Crear garantía', 'alt' => 'Formulario para abrir una garantía con descripción del daño'],
            ['src' => '/proyectosmce/imag/admin/garantias2.png', 'caption' => 'Seguimiento', 'alt' => 'Estado del caso de garantía y datos del cliente'],
            ['src' => '/proyectosmce/imag/admin/garantias3.png', 'caption' => 'Histórico', 'alt' => 'Historial de garantías previas para control'],
            ['src' => '/proyectosmce/imag/admin/garantias4.png', 'caption' => 'Cierre', 'alt' => 'Pantalla de cierre con confirmación de entrega o reposición'],
        ],
    ],
];

$workerFlows = [
    [
        'title' => 'Acceso y perfil',
        'summary' => 'Ingreso del trabajador y actualización de sus datos para operar en tienda.',
        'shots' => [
            ['src' => '/proyectosmce/imag/trabajador/login.png', 'caption' => 'Login trabajador', 'alt' => 'Formulario de ingreso para usuario trabajador'],
            ['src' => '/proyectosmce/imag/trabajador/credenciales-acceso.png', 'caption' => 'Credenciales recibidas', 'alt' => 'Vista con usuario y contraseña asignados'],
            ['src' => '/proyectosmce/imag/trabajador/info-personal.png', 'caption' => 'Perfil personal', 'alt' => 'Edición de datos básicos del trabajador'],
        ],
    ],
    [
        'title' => 'Panel e inventario',
        'summary' => 'Inicio rápido para visualizar tareas y revisar existencias disponibles.',
        'shots' => [
            ['src' => '/proyectosmce/imag/trabajador/panel.png', 'caption' => 'Panel trabajador', 'alt' => 'Dashboard con accesos a ventas e inventario'],
            ['src' => '/proyectosmce/imag/trabajador/inventario.png', 'caption' => 'Inventario', 'alt' => 'Listado de productos con stock visible para el vendedor'],
        ],
    ],
    [
        'title' => 'Proceso de venta',
        'summary' => 'Flujo guiado para cerrar ventas en piso de tienda, registrando pago y saldo.',
        'shots' => [
            ['src' => '/proyectosmce/imag/trabajador/realizar-venta1.png', 'caption' => 'Selección de productos', 'alt' => 'Paso 1: elegir artículos para la venta'],
            ['src' => '/proyectosmce/imag/trabajador/realizar-venta-2.png', 'caption' => 'Pago del cliente', 'alt' => 'Paso 2: definir método de pago y abono'],
            ['src' => '/proyectosmce/imag/trabajador/realizar-venta-3.png', 'caption' => 'Confirmación', 'alt' => 'Paso 3: confirmar y generar recibo'],
        ],
    ],
];

$normalizeFlowShots = function (array $flows) {
    foreach ($flows as &$flow) {
        foreach ($flow['shots'] as &$shot) {
            $relativeSrc = preg_replace('~^/proyectosmce/~', '', (string) ($shot['src'] ?? ''));
            $shot['src'] = app_url($relativeSrc);
        }
        unset($shot);
    }
    unset($flow);

    return $flows;
};

$adminFlows = $normalizeFlowShots($adminFlows);
$workerFlows = $normalizeFlowShots($workerFlows);
?>

<!-- Hero -->
<section id="do-hero" class="relative bg-gradient-to-r from-[#f2b416] via-[#f7d370] to-[#d99a0a] text-white overflow-hidden mce-rounded-hero">
    <div class="absolute inset-0 bg-white/10 mix-blend-overlay"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
    
    <div class="relative max-w-6xl mx-auto px-4 py-20 text-center">
        <p id="do-hero-badge" class="uppercase tracking-widest text-sm mb-3">Caso real</p>
        <h1 id="do-hero-title" class="text-4xl md:text-5xl font-extrabold mb-4">Destello de Oro 18K</h1>
        <p id="do-hero-desc" class="text-lg md:text-xl text-[#fff9db] max-w-3xl mx-auto">
            Documentación visual del sistema para joyería: flujos de administrador y trabajador,
            con capturas numeradas y descripción de cada proceso clave.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a id="do-btn-admin" href="#admin" class="bg-white text-[#d99a0a] px-6 py-3 rounded-lg font-semibold shadow-lg hover:-translate-y-1 transition">Flujos de administrador</a>
            <a id="do-btn-worker" href="#trabajador" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-[#d99a0a] transition">Flujos de trabajador</a>
        </div>
    </div>
</section>

<!-- Descripción comercial -->
<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl border border-[#f7d370] p-8 md:p-10">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <p id="do-desc-tag" class="text-sm uppercase tracking-[0.2em] text-[#d99a0a] font-semibold">Destello de Oro 18K</p>
                <h2 id="do-desc-title" class="text-3xl font-bold text-gray-900">Joyería laminada con respaldo real</h2>
                <p id="do-desc-p1" class="text-gray-700 leading-relaxed">
                    Ofrecemos piezas y artículos de oro laminado con acabado premium, garantía escrita de 1 año,
                    envíos gratis en compras superiores a $250.000 y un programa de fidelidad con promociones
                    y descuentos escalonados para clientes recurrentes.
                </p>
                <p id="do-desc-p2" class="text-gray-700 leading-relaxed">
                    Nuestro equipo te asesora en tallas, estilos y cuidado de las piezas para que tu inversión
                    conserve su brillo. Si necesitas catálogos o una cotización puntual, contáctanos y respondemos al instante.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a id="do-btn-wa" href="<?php echo $WA_LINK; ?>" target="_blank" rel="noopener" class="bg-[#25D366] text-white px-6 py-3 rounded-lg shadow hover:bg-[#1ebe5a] transition">
                        Escríbenos por WhatsApp
                    </a>
                    <a id="do-btn-ig" href="https://www.instagram.com/destellodeoro18k/" target="_blank" rel="noopener" class="px-6 py-3 rounded-lg text-white font-semibold shadow transition" style="background: linear-gradient(135deg, #f58529, #dd2a7b 45%, #8134af 70%, #515bd4);">
                        Síguenos en Instagram
                    </a>
                </div>
            </div>
            <div id="do-features" class="bg-[#fff6dd] border border-[#f7d370] rounded-xl p-6 space-y-3">
                <div class="flex items-start space-x-3" data-feature-index="0">
                    <i class="fas fa-shield-alt text-[#f2b416] text-xl"></i>
                    <div>
                        <p class="font-semibold text-gray-900 do-feature-title">Garantía de 1 año</p>
                        <p class="text-gray-600 text-sm do-feature-text">Cobertura frente a defectos de fabricación y desgaste anormal.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3" data-feature-index="1">
                    <i class="fas fa-shipping-fast text-[#f2b416] text-xl"></i>
                    <div>
                        <p class="font-semibold text-gray-900 do-feature-title">Envío gratis</p>
                        <p class="text-gray-600 text-sm do-feature-text">Sin costo en pedidos mayores a $250.000.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3" data-feature-index="2">
                    <i class="fas fa-gift text-[#f2b416] text-xl"></i>
                    <div>
                        <p class="font-semibold text-gray-900 do-feature-title">Promociones y fidelidad</p>
                        <p class="text-gray-600 text-sm do-feature-text">Descuentos progresivos y beneficios exclusivos para clientes frecuentes.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3" data-feature-index="3">
                    <i class="fas fa-headset text-[#f2b416] text-xl"></i>
                    <div>
                        <p class="font-semibold text-gray-900 do-feature-title">Asesoría directa</p>
                        <p class="text-gray-600 text-sm do-feature-text">Atención personalizada vía WhatsApp para elegir y cuidar tus piezas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Admin flows -->
<section id="admin" class="max-w-7xl mx-auto px-4 py-14 space-y-10">
    <div class="flex items-center justify-between">
        <div>
            <p id="do-admin-badge" class="text-sm uppercase tracking-widest text-[#d99a0a] font-semibold">Panel administrador</p>
            <h2 id="do-admin-title" class="text-3xl font-bold text-gray-900">Procesos y capturas</h2>
        </div>
        <span id="do-admin-total" class="text-sm text-gray-500">Total: <?php echo count($adminFlows); ?> flujos</span>
    </div>

    <?php foreach ($adminFlows as $flowIndex => $flow): ?>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-[#f7d370]" data-flow-type="admin" data-flow-index="<?php echo $flowIndex; ?>">
        <div class="p-6 md:p-8 border-b border-gray-100">
            <h3 class="text-2xl font-bold text-gray-900 mb-2 do-flow-title"><?php echo $flow['title']; ?></h3>
            <p class="text-gray-600 do-flow-summary"><?php echo $flow['summary']; ?></p>
        </div>
        <div class="p-6 md:p-8">
            <div class="grid md:grid-cols-<?php echo min(3, count($flow['shots'])); ?> gap-6">
                <?php foreach ($flow['shots'] as $shotIndex => $shot): ?>
                <figure
                    class="bg-gray-50 rounded-xl overflow-hidden shadow-sm border border-gray-100 cursor-pointer hover:shadow-md transition"
                    data-lightbox="true"
                    data-flow-type="admin"
                    data-flow-index="<?php echo $flowIndex; ?>"
                    data-shot-index="<?php echo $shotIndex; ?>"
                    data-src="<?php echo $shot['src']; ?>"
                    data-title="<?php echo htmlspecialchars($flow['title'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-summary="<?php echo htmlspecialchars($flow['summary'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-caption="<?php echo htmlspecialchars($shot['caption'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-alt="<?php echo htmlspecialchars($shot['alt'], ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <img src="<?php echo $shot['src']; ?>" alt="<?php echo $shot['alt']; ?>" class="w-full h-56 object-cover">
                    <figcaption class="p-4">
                        <p class="text-sm font-semibold text-gray-800 do-shot-caption"><?php echo $shot['caption']; ?></p>
                        <p class="text-xs text-gray-500 mt-1 do-shot-alt"><?php echo $shot['alt']; ?></p>
                    </figcaption>
                </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<!-- Worker flows -->
<section id="trabajador" class="bg-gray-100 py-14">
    <div class="max-w-7xl mx-auto px-4 space-y-10">
        <div class="flex items-center justify-between">
            <div>
                <p id="do-worker-badge" class="text-sm uppercase tracking-widest text-[#d99a0a] font-semibold">Panel trabajador</p>
                <h2 id="do-worker-title" class="text-3xl font-bold text-gray-900">Procesos y capturas</h2>
            </div>
            <span id="do-worker-total" class="text-sm text-gray-500">Total: <?php echo count($workerFlows); ?> flujos</span>
        </div>

        <?php foreach ($workerFlows as $flowIndex => $flow): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-[#f7d370]" data-flow-type="worker" data-flow-index="<?php echo $flowIndex; ?>">
            <div class="p-6 md:p-8 border-b border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 do-flow-title"><?php echo $flow['title']; ?></h3>
                <p class="text-gray-600 do-flow-summary"><?php echo $flow['summary']; ?></p>
            </div>
            <div class="p-6 md:p-8">
                <div class="grid md:grid-cols-<?php echo min(3, count($flow['shots'])); ?> gap-6">
                    <?php foreach ($flow['shots'] as $shotIndex => $shot): ?>
                    <figure
                        class="bg-gray-50 rounded-xl overflow-hidden shadow-sm border border-gray-100 cursor-pointer hover:shadow-md transition"
                        data-lightbox="true"
                        data-flow-type="worker"
                        data-flow-index="<?php echo $flowIndex; ?>"
                        data-shot-index="<?php echo $shotIndex; ?>"
                        data-src="<?php echo $shot['src']; ?>"
                        data-title="<?php echo htmlspecialchars($flow['title'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-summary="<?php echo htmlspecialchars($flow['summary'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-caption="<?php echo htmlspecialchars($shot['caption'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-alt="<?php echo htmlspecialchars($shot['alt'], ENT_QUOTES, 'UTF-8'); ?>"
                    >
                        <img src="<?php echo $shot['src']; ?>" alt="<?php echo $shot['alt']; ?>" class="w-full h-56 object-cover">
                        <figcaption class="p-4">
                            <p class="text-sm font-semibold text-gray-800 do-shot-caption"><?php echo $shot['caption']; ?></p>
                            <p class="text-xs text-gray-500 mt-1 do-shot-alt"><?php echo $shot['alt']; ?></p>
                        </figcaption>
                    </figure>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Lightbox modal -->
<div id="lightbox" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center px-4">
    <div class="relative max-w-6xl w-full text-white">
        <button id="lightbox-close" class="absolute top-3 right-3 text-white bg-black/50 hover:bg-black/70 rounded-full p-2">
            <span class="sr-only">Cerrar</span>
            <i class="fas fa-times"></i>
        </button>
        <button id="lightbox-prev" class="absolute left-3 top-1/2 -translate-y-1/2 text-white bg-black/50 hover:bg-black/70 rounded-full p-3">
            <span class="sr-only">Anterior</span>
            <i class="fas fa-chevron-left"></i>
        </button>
        <button id="lightbox-next" class="absolute right-3 top-1/2 -translate-y-1/2 text-white bg-black/50 hover:bg-black/70 rounded-full p-3">
            <span class="sr-only">Siguiente</span>
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="flex flex-col items-center space-y-4">
            <div class="w-full bg-black/40 border border-white/10 rounded-xl p-3">
                <img id="lightbox-img" src="" alt="" class="max-h-[80vh] w-full object-contain rounded-lg">
            </div>
            <div class="text-center space-y-2 px-2 md:px-6">
                <p id="lightbox-tag" class="text-xs font-semibold text-[#f7d370] uppercase tracking-widest"></p>
                <h3 id="lightbox-title" class="text-2xl font-bold text-white"></h3>
                <p id="lightbox-caption" class="text-sm text-white/90"></p>
                <p id="lightbox-summary" class="text-sm text-white/80 leading-relaxed"></p>
                <p id="lightbox-count" class="text-xs text-white/60"></p>
            </div>
        </div>
    </div>
</div>

<script>
// Traducción específica del caso Destello de Oro 18K
(() => {
    const translations = {
        es: {
            heroBadge: 'Caso real',
            heroTitle: 'Destello de Oro 18K',
            heroDesc: 'Documentación visual del sistema para joyería: flujos de administrador y trabajador, con capturas numeradas y descripción de cada proceso clave.',
            btnAdmin: 'Flujos de administrador',
            btnWorker: 'Flujos de trabajador',
            descTag: 'Destello de Oro 18K',
            descTitle: 'Joyería laminada con respaldo real',
            descP1: 'Ofrecemos piezas y artículos de oro laminado con acabado premium, garantía escrita de 1 año, envíos gratis en compras superiores a $250.000 y un programa de fidelidad con promociones y descuentos escalonados para clientes recurrentes.',
            descP2: 'Nuestro equipo te asesora en tallas, estilos y cuidado de las piezas para que tu inversión conserve su brillo. Si necesitas catálogos o una cotización puntual, contáctanos y respondemos al instante.',
            btnWa: 'Escríbenos por WhatsApp',
            btnIg: 'Síguenos en Instagram',
            features: [
                { title: 'Garantía de 1 año', text: 'Cobertura frente a defectos de fabricación y desgaste anormal.' },
                { title: 'Envío gratis', text: 'Sin costo en pedidos mayores a $250.000.' },
                { title: 'Promociones y fidelidad', text: 'Descuentos progresivos y beneficios exclusivos para clientes frecuentes.' },
                { title: 'Asesoría directa', text: 'Atención personalizada vía WhatsApp para elegir y cuidar tus piezas.' },
            ],
            adminBadge: 'Panel administrador',
            adminTitle: 'Procesos y capturas',
            workerBadge: 'Panel trabajador',
            workerTitle: 'Procesos y capturas',
            total: (n) => `Total: ${n} flujos`,
            lightboxCount: (i, total) => `Imagen ${i} de ${total}`,
            adminFlows: [
                {
                    title: 'Acceso y recuperación de cuenta',
                    summary: 'Pantallas de ingreso y soporte de credenciales para garantizar que solo personal autorizado entre al panel.',
                    shots: [
                        { caption: 'Login de administrador', alt: 'Formulario de ingreso para administrador con usuario y contraseña' },
                        { caption: 'Credenciales entregadas', alt: 'Vista donde se muestran las credenciales generadas para el administrador' },
                        { caption: 'Recuperar contraseña', alt: 'Pantalla para solicitar recuperación de contraseña por correo' },
                    ],
                },
                {
                    title: 'Cambio de contraseña',
                    summary: 'Flujo guiado para que el administrador actualice su contraseña con validación de seguridad.',
                    shots: [
                        { caption: 'Acceso al cambio', alt: 'Formulario inicial para cambiar la contraseña actual' },
                        { caption: 'Confirmación', alt: 'Pantalla de confirmación de nueva contraseña' },
                    ],
                },
                {
                    title: 'Panel principal y perfil',
                    summary: 'Tablero con métricas rápidas y acceso al perfil personal para mantener datos al día.',
                    shots: [
                        { caption: 'Panel administrador', alt: 'Dashboard con accesos a ventas, inventario y gastos' },
                        { caption: 'Información personal', alt: 'Ficha editable con datos de contacto del administrador' },
                    ],
                },
                {
                    title: 'Inventario y altas de producto',
                    summary: 'Control total del stock: ver existencias, crear nuevos artículos y surtir nuevamente.',
                    shots: [
                        { caption: 'Tabla de inventario', alt: 'Listado de productos de joyería con existencias y precios' },
                        { caption: 'Alta de producto', alt: 'Formulario para agregar un nuevo producto de oro 18K con precio y descripción' },
                        { caption: 'Surtir inventario', alt: 'Interfaz para aumentar stock de un producto existente' },
                    ],
                },
                {
                    title: 'Ventas y cobranzas',
                    summary: 'Proceso en tres pasos para generar la venta, registrar método de pago y dejar todo conciliado.',
                    shots: [
                        { caption: 'Selección de cliente y producto', alt: 'Paso 1 del flujo de venta donde se elige cliente y piezas' },
                        { caption: 'Método de pago', alt: 'Paso 2 para indicar forma de pago y abonos' },
                        { caption: 'Confirmación de venta', alt: 'Paso 3 con resumen y confirmación final' },
                        { caption: 'Pagos pendientes', alt: 'Listado de ventas con saldo pendiente para seguimiento' },
                    ],
                },
                {
                    title: 'Gastos y control de caja',
                    summary: 'Registro de gastos operativos y trazabilidad para cortes diarios o semanales.',
                    shots: [
                        { caption: 'Registro de gasto', alt: 'Formulario para cargar un gasto con monto y categoría' },
                        { caption: 'Historial general', alt: 'Tabla con todos los gastos registrados' },
                        { caption: 'Detalle por fecha', alt: 'Vista filtrada de movimientos en un periodo' },
                        { caption: 'Detalle por concepto', alt: 'Detalle de cada gasto con monto y responsable' },
                    ],
                },
                {
                    title: 'Garantías y cambios',
                    summary: 'Gestión de garantías para piezas de oro laminado: desde la solicitud hasta la reposición.',
                    shots: [
                        { caption: 'Crear garantía', alt: 'Formulario para abrir una garantía con descripción del daño' },
                        { caption: 'Seguimiento', alt: 'Estado del caso de garantía y datos del cliente' },
                        { caption: 'Histórico', alt: 'Historial de garantías previas para control' },
                        { caption: 'Cierre', alt: 'Pantalla de cierre con confirmación de entrega o reposición' },
                    ],
                },
            ],
            workerFlows: [
                {
                    title: 'Acceso y perfil',
                    summary: 'Ingreso del trabajador y actualización de sus datos para operar en tienda.',
                    shots: [
                        { caption: 'Login trabajador', alt: 'Formulario de ingreso para usuario trabajador' },
                        { caption: 'Credenciales recibidas', alt: 'Vista con usuario y contraseña asignados' },
                        { caption: 'Perfil personal', alt: 'Edición de datos básicos del trabajador' },
                    ],
                },
                {
                    title: 'Panel e inventario',
                    summary: 'Inicio rápido para visualizar tareas y revisar existencias disponibles.',
                    shots: [
                        { caption: 'Panel trabajador', alt: 'Dashboard con accesos a ventas e inventario' },
                        { caption: 'Inventario', alt: 'Listado de productos con stock visible para el vendedor' },
                    ],
                },
                {
                    title: 'Proceso de venta',
                    summary: 'Flujo guiado para cerrar ventas en piso de tienda, registrando pago y saldo.',
                    shots: [
                        { caption: 'Selección de productos', alt: 'Paso 1: elegir artículos para la venta' },
                        { caption: 'Pago del cliente', alt: 'Paso 2: definir método de pago y abono' },
                        { caption: 'Confirmación', alt: 'Paso 3: confirmar y generar recibo' },
                    ],
                },
            ],
        },
        en: {
            heroBadge: 'Real case',
            heroTitle: 'Destello de Oro 18K',
            heroDesc: 'Visual documentation of the jewelry system: admin and worker flows with numbered screenshots and step-by-step descriptions.',
            btnAdmin: 'Admin flows',
            btnWorker: 'Worker flows',
            descTag: 'Destello de Oro 18K',
            descTitle: 'Gold-plated jewelry with real backing',
            descP1: 'We offer gold-plated pieces with premium finish, a 1-year written warranty, free shipping over $250,000, and a loyalty program with tiered discounts for repeat customers.',
            descP2: 'Our team advises on sizes, styles and care so your investment keeps its shine. Need catalogs or a quote? Message us and we reply instantly.',
            btnWa: 'Message us on WhatsApp',
            btnIg: 'Follow us on Instagram',
            features: [
                { title: '1-year warranty', text: 'Coverage for manufacturing defects and abnormal wear.' },
                { title: 'Free shipping', text: 'No cost on orders over $250,000.' },
                { title: 'Promos & loyalty', text: 'Tiered discounts and exclusive perks for recurring customers.' },
                { title: 'Direct guidance', text: 'Personal WhatsApp assistance to choose and care for your pieces.' },
            ],
            adminBadge: 'Admin panel',
            adminTitle: 'Processes and screenshots',
            workerBadge: 'Worker panel',
            workerTitle: 'Processes and screenshots',
            total: (n) => `Total: ${n} flows`,
            lightboxCount: (i, total) => `Image ${i} of ${total}`,
            adminFlows: [
                {
                    title: 'Account access & recovery',
                    summary: 'Login and credential support to ensure only authorized staff enter the panel.',
                    shots: [
                        { caption: 'Admin login', alt: 'Login form for admin with user and password' },
                        { caption: 'Delivered credentials', alt: 'View showing generated credentials for the admin' },
                        { caption: 'Password recovery', alt: 'Screen to request password reset via email' },
                    ],
                },
                {
                    title: 'Password change',
                    summary: 'Guided flow for the admin to update password with security validation.',
                    shots: [
                        { caption: 'Open password change', alt: 'Initial form to change current password' },
                        { caption: 'Confirmation', alt: 'Confirmation screen for new password' },
                    ],
                },
                {
                    title: 'Main dashboard & profile',
                    summary: 'Quick metrics dashboard and access to the personal profile to keep data updated.',
                    shots: [
                        { caption: 'Admin dashboard', alt: 'Dashboard with shortcuts to sales, inventory and expenses' },
                        { caption: 'Personal info', alt: 'Editable card with admin contact data' },
                    ],
                },
                {
                    title: 'Inventory & product intake',
                    summary: 'Full stock control: view inventory, create new items and restock.',
                    shots: [
                        { caption: 'Inventory table', alt: 'Product list with stock and prices' },
                        { caption: 'New product intake', alt: 'Form to add a new 18K gold item with price and description' },
                        { caption: 'Restock inventory', alt: 'Interface to increase stock of an existing product' },
                    ],
                },
                {
                    title: 'Sales & payments',
                    summary: 'Three-step process to create the sale, register payment method and reconcile.',
                    shots: [
                        { caption: 'Select customer and products', alt: 'Step 1 of sale flow choosing customer and pieces' },
                        { caption: 'Payment method', alt: 'Step 2 to set payment method and deposits' },
                        { caption: 'Sale confirmation', alt: 'Step 3 with summary and final confirmation' },
                        { caption: 'Pending payments', alt: 'List of sales with outstanding balance for follow-up' },
                    ],
                },
                {
                    title: 'Expenses & cash control',
                    summary: 'Log operational expenses and traceability for daily or weekly closes.',
                    shots: [
                        { caption: 'Expense entry', alt: 'Form to record an expense with amount and category' },
                        { caption: 'General history', alt: 'Table with all recorded expenses' },
                        { caption: 'Detail by date', alt: 'Filtered view of movements in a period' },
                        { caption: 'Detail by concept', alt: 'Detail of each expense with amount and responsible' },
                    ],
                },
                {
                    title: 'Warranties & exchanges',
                    summary: 'Warranty management for gold-plated pieces: from request to replacement.',
                    shots: [
                        { caption: 'Create warranty', alt: 'Form to open a warranty with damage description' },
                        { caption: 'Follow-up', alt: 'Warranty case status and customer data' },
                        { caption: 'History', alt: 'History of previous warranties for control' },
                        { caption: 'Closure', alt: 'Close screen with confirmation of delivery or replacement' },
                    ],
                },
            ],
            workerFlows: [
                {
                    title: 'Access & profile',
                    summary: 'Worker login and update of personal data to operate in store.',
                    shots: [
                        { caption: 'Worker login', alt: 'Login form for worker user' },
                        { caption: 'Received credentials', alt: 'View with assigned user and password' },
                        { caption: 'Personal profile', alt: 'Edit basic worker data' },
                    ],
                },
                {
                    title: 'Dashboard & inventory',
                    summary: 'Quick start to view tasks and check available stock.',
                    shots: [
                        { caption: 'Worker dashboard', alt: 'Dashboard with shortcuts to sales and inventory' },
                        { caption: 'Inventory', alt: 'Product list with visible stock for the seller' },
                    ],
                },
                {
                    title: 'Sales process',
                    summary: 'Guided flow to close store sales, recording payment and balance.',
                    shots: [
                        { caption: 'Select products', alt: 'Step 1: choose items for sale' },
                        { caption: 'Customer payment', alt: 'Step 2: define payment method and deposit' },
                        { caption: 'Confirmation', alt: 'Step 3: confirm and generate receipt' },
                    ],
                },
            ],
        },
        fr: {}, de: {}, pt: {}, it: {} // placeholder for brevity above
    };

    const setText = (id, text) => {
        const el = document.getElementById(id);
        if (el && typeof text === 'string') el.textContent = text;
    };

    const applyLangToFlows = (lang, type, data) => {
        const flows = Array.from(document.querySelectorAll(`[data-flow-type=\"${type}\"][data-flow-index]`));
        flows.forEach(flowEl => {
            const idx = parseInt(flowEl.dataset.flowIndex || '0', 10);
            const tflow = data[idx];
            if (!tflow) return;
            const titleEl = flowEl.querySelector('.do-flow-title');
            const summaryEl = flowEl.querySelector('.do-flow-summary');
            if (titleEl) titleEl.textContent = tflow.title;
            if (summaryEl) summaryEl.textContent = tflow.summary;

            const shots = Array.from(flowEl.querySelectorAll('[data-shot-index]'));
            shots.forEach(fig => {
                const sidx = parseInt(fig.dataset.shotIndex || '0', 10);
                const shot = tflow.shots?.[sidx];
                if (!shot) return;
                fig.dataset.title = tflow.title;
                fig.dataset.summary = tflow.summary;
                fig.dataset.caption = shot.caption;
                fig.dataset.alt = shot.alt;
                const capEl = fig.querySelector('.do-shot-caption');
                const altEl = fig.querySelector('.do-shot-alt');
                const imgEl = fig.querySelector('img');
                if (capEl) capEl.textContent = shot.caption;
                if (altEl) altEl.textContent = shot.alt;
                if (imgEl && shot.alt) imgEl.alt = shot.alt;
            });
        });
    };

    const apply = (lang) => {
        const dict = translations[lang] || translations.es;
        setText('do-hero-badge', dict.heroBadge);
        setText('do-hero-title', dict.heroTitle);
        setText('do-hero-desc', dict.heroDesc);
        setText('do-btn-admin', dict.btnAdmin);
        setText('do-btn-worker', dict.btnWorker);
        setText('do-desc-tag', dict.descTag);
        setText('do-desc-title', dict.descTitle);
        setText('do-desc-p1', dict.descP1);
        setText('do-desc-p2', dict.descP2);
        setText('do-btn-wa', dict.btnWa);
        setText('do-btn-ig', dict.btnIg);

        const featureItems = Array.from(document.querySelectorAll('#do-features [data-feature-index]'));
        featureItems.forEach(item => {
            const idx = parseInt(item.dataset.featureIndex || '0', 10);
            const f = dict.features?.[idx];
            if (!f) return;
            const tEl = item.querySelector('.do-feature-title');
            const pEl = item.querySelector('.do-feature-text');
            if (tEl) tEl.textContent = f.title;
            if (pEl) pEl.textContent = f.text;
        });

        setText('do-admin-badge', dict.adminBadge);
        setText('do-admin-title', dict.adminTitle);
        setText('do-worker-badge', dict.workerBadge);
        setText('do-worker-title', dict.workerTitle);

        const adminCount = document.querySelectorAll('[data-flow-type=\"admin\"][data-flow-index]').length;
        const workerCount = document.querySelectorAll('[data-flow-type=\"worker\"][data-flow-index]').length;
        setText('do-admin-total', typeof dict.total === 'function' ? dict.total(adminCount) : dict.total);
        setText('do-worker-total', typeof dict.total === 'function' ? dict.total(workerCount) : dict.total);

        applyLangToFlows(lang, 'admin', dict.adminFlows || []);
        applyLangToFlows(lang, 'worker', dict.workerFlows || []);
    };

    const current = window.mceCurrentLang || localStorage.getItem('siteLang') || 'es';
    apply(current);
    window.addEventListener('mce-lang-changed', (e) => {
        apply(e.detail?.lang || 'es');
    });

    window.doLightboxCount = (index, total, lang) => {
        const dict = translations[lang] || translations.es;
        return dict.lightboxCount ? dict.lightboxCount(index + 1, total) : `Imagen ${index + 1} de ${total}`;
    };
})();
</script>

<script>
(() => {
    const overlay = document.getElementById('lightbox');
    const img = document.getElementById('lightbox-img');
    const title = document.getElementById('lightbox-title');
    const caption = document.getElementById('lightbox-caption');
    const summary = document.getElementById('lightbox-summary');
    const tag = document.getElementById('lightbox-tag');
    const count = document.getElementById('lightbox-count');
    const closeBtn = document.getElementById('lightbox-close');
    const nextBtn = document.getElementById('lightbox-next');
    const prevBtn = document.getElementById('lightbox-prev');
    const items = Array.from(document.querySelectorAll('[data-lightbox="true"]'));
    let current = -1;

    // Mantener el scroll siempre habilitado
    const lockScroll = () => {};
    const unlockScroll = () => {};

    function render(index) {
        const el = items[index];
        if (!el) return;
        current = index;
        img.src = el.dataset.src;
        img.alt = el.dataset.alt || '';
        title.textContent = el.dataset.caption || '';
        caption.textContent = el.dataset.alt || '';
        summary.textContent = el.dataset.summary || '';
        tag.textContent = el.dataset.title || '';
        count.textContent = `Imagen ${index + 1} de ${items.length}`;
        prevBtn.disabled = index === 0;
        nextBtn.disabled = index === items.length - 1;
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        lockScroll();
    }

    function openLightbox(el) {
        const index = items.indexOf(el);
        if (index >= 0) render(index);
    }

    function closeLightbox() {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        img.src = '';
        current = -1;
        unlockScroll();
    }

    function next() {
        if (current < items.length - 1) render(current + 1);
    }
    function prev() {
        if (current > 0) render(current - 1);
    }

    document.querySelectorAll('[data-lightbox="true"]').forEach(el => {
        el.addEventListener('click', () => openLightbox(el));
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeLightbox();
    });
    closeBtn.addEventListener('click', closeLightbox);
    nextBtn.addEventListener('click', next);
    prevBtn.addEventListener('click', prev);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') next();
        if (e.key === 'ArrowLeft') prev();
    });
})();
</script>

<?php include 'includes/footer.php'; ?>
