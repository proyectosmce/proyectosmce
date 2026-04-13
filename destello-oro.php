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
        fr: {
            heroBadge: 'Cas réel',
            heroTitle: 'Destello de Oro 18K',
            heroDesc: 'Documentation visuelle du système pour une bijouterie : parcours admin et vendeur avec captures numérotées et description de chaque étape.',
            btnAdmin: 'Parcours admin',
            btnWorker: 'Parcours vendeur',
            descTag: 'Destello de Oro 18K',
            descTitle: 'Bijoux plaqué or avec vrai suivi',
            descP1: 'Nous proposons des pièces plaquées or finition premium, garantie écrite d’un an, livraison gratuite dès 250 000 et programme fidélité avec remises progressives.',
            descP2: 'Notre équipe conseille sur les tailles, styles et l’entretien pour garder l’éclat. Besoin d’un catalogue ou d’un devis ? Écrivez-nous, réponse immédiate.',
            btnWa: 'Écris-nous sur WhatsApp',
            btnIg: 'Suis-nous sur Instagram',
            features: [
                { title: 'Garantie 1 an', text: 'Couverture des défauts de fabrication et usure anormale.' },
                { title: 'Livraison gratuite', text: 'Sans frais pour les commandes supérieures à 250 000.' },
                { title: 'Promos et fidélité', text: 'Remises progressives et avantages exclusifs pour clients fidèles.' },
                { title: 'Conseil direct', text: 'Assistance personnalisée via WhatsApp pour choisir et entretenir vos pièces.' },
            ],
            adminBadge: 'Panneau admin',
            adminTitle: 'Processus et captures',
            workerBadge: 'Panneau vendeur',
            workerTitle: 'Processus et captures',
            total: (n) => `Total : ${n} flux`,
            lightboxCount: (i, total) => `Image ${i} sur ${total}`,
            adminFlows: [
                {
                    title: 'Accès et récupération de compte',
                    summary: 'Écrans de connexion et support d’identifiants pour garantir un accès autorisé uniquement.',
                    shots: [
                        { caption: 'Login administrateur', alt: 'Formulaire de connexion avec utilisateur et mot de passe' },
                        { caption: 'Identifiants remis', alt: 'Vue des identifiants générés pour l’administrateur' },
                        { caption: 'Récupérer le mot de passe', alt: 'Écran pour demander la réinitialisation par e-mail' },
                    ],
                },
                {
                    title: 'Changement de mot de passe',
                    summary: 'Parcours guidé pour mettre à jour le mot de passe avec validation de sécurité.',
                    shots: [
                        { caption: 'Accès au changement', alt: 'Formulaire initial pour modifier le mot de passe actuel' },
                        { caption: 'Confirmation', alt: 'Écran de confirmation du nouveau mot de passe' },
                    ],
                },
                {
                    title: 'Tableau de bord et profil',
                    summary: 'Tableau avec métriques rapides et accès au profil personnel pour garder les données à jour.',
                    shots: [
                        { caption: 'Dashboard admin', alt: 'Tableau de bord avec accès ventes, inventaire et dépenses' },
                        { caption: 'Informations personnelles', alt: 'Fiche éditable avec données de contact de l’admin' },
                    ],
                },
                {
                    title: 'Inventaire et créations de produit',
                    summary: 'Contrôle complet du stock : voir, créer des articles et réapprovisionner.',
                    shots: [
                        { caption: 'Table d’inventaire', alt: 'Liste des produits de bijouterie avec stock et prix' },
                        { caption: 'Nouvel article', alt: 'Formulaire pour ajouter un article 18K avec prix et description' },
                        { caption: 'Réassort d’inventaire', alt: 'Interface pour augmenter le stock d’un produit existant' },
                    ],
                },
                {
                    title: 'Ventes et encaissements',
                    summary: 'Processus en trois étapes : vente, mode de paiement et rapprochement.',
                    shots: [
                        { caption: 'Sélection client et produits', alt: 'Étape 1 du flux de vente : choix du client et des pièces' },
                        { caption: 'Mode de paiement', alt: 'Étape 2 pour définir mode de paiement et acomptes' },
                        { caption: 'Confirmation de vente', alt: 'Étape 3 avec résumé et confirmation finale' },
                        { caption: 'Paiements en attente', alt: 'Liste des ventes avec solde à suivre' },
                    ],
                },
                {
                    title: 'Dépenses et caisse',
                    summary: 'Enregistrement des dépenses et traçabilité pour clôtures quotidiennes ou hebdomadaires.',
                    shots: [
                        { caption: 'Saisie de dépense', alt: 'Formulaire pour enregistrer une dépense avec montant et catégorie' },
                        { caption: 'Historique général', alt: 'Tableau de toutes les dépenses enregistrées' },
                        { caption: 'Détail par date', alt: 'Vue filtrée des mouvements sur une période' },
                        { caption: 'Détail par concept', alt: 'Détail de chaque dépense avec montant et responsable' },
                    ],
                },
                {
                    title: 'Garanties et échanges',
                    summary: 'Gestion des garanties pour pièces plaquées or : de la demande à la remise.',
                    shots: [
                        { caption: 'Créer une garantie', alt: 'Formulaire pour ouvrir une garantie avec description du dommage' },
                        { caption: 'Suivi', alt: 'Statut du dossier de garantie et données client' },
                        { caption: 'Historique', alt: 'Historique des garanties précédentes' },
                        { caption: 'Clôture', alt: 'Écran de clôture avec confirmation de remise ou remplacement' },
                    ],
                },
            ],
            workerFlows: [
                {
                    title: 'Accès et profil',
                    summary: 'Connexion de l’employé et mise à jour de ses données pour travailler en boutique.',
                    shots: [
                        { caption: 'Login employé', alt: 'Formulaire de connexion pour utilisateur vendeur' },
                        { caption: 'Identifiants reçus', alt: 'Vue avec utilisateur et mot de passe assignés' },
                        { caption: 'Profil personnel', alt: 'Édition des données de base du vendeur' },
                    ],
                },
                {
                    title: 'Tableau et inventaire',
                    summary: 'Démarrage rapide pour voir les tâches et vérifier le stock disponible.',
                    shots: [
                        { caption: 'Dashboard vendeur', alt: 'Tableau de bord avec accès ventes et inventaire' },
                        { caption: 'Inventaire', alt: 'Liste de produits avec stock visible pour le vendeur' },
                    ],
                },
                {
                    title: 'Processus de vente',
                    summary: 'Parcours guidé pour conclure les ventes en boutique, en enregistrant paiement et solde.',
                    shots: [
                        { caption: 'Sélection de produits', alt: 'Étape 1 : choisir les articles à vendre' },
                        { caption: 'Paiement client', alt: 'Étape 2 : définir mode de paiement et acompte' },
                        { caption: 'Confirmation', alt: 'Étape 3 : confirmer et générer le reçu' },
                    ],
                },
            ],
        },
        de: {
            heroBadge: 'Echter Fall',
            heroTitle: 'Destello de Oro 18K',
            heroDesc: 'Visuelle Dokumentation des Schmucksystems: Admin- und Mitarbeiter-Flows mit nummerierten Screenshots und Schrittbeschreibungen.',
            btnAdmin: 'Admin-Abläufe',
            btnWorker: 'Mitarbeiter-Abläufe',
            descTag: 'Destello de Oro 18K',
            descTitle: 'Vergoldeter Schmuck mit echtem Support',
            descP1: 'Wir bieten vergoldete Stücke mit Premium-Finish, einjähriger Garantie, kostenlosen Versand ab 250.000 und ein Treueprogramm mit gestaffelten Rabatten.',
            descP2: 'Unser Team berät zu Größen, Styles und Pflege, damit deine Investition glänzt. Brauchst du Kataloge oder ein Angebot? Schreib uns, wir antworten sofort.',
            btnWa: 'Schreib uns per WhatsApp',
            btnIg: 'Folge uns auf Instagram',
            features: [
                { title: '1 Jahr Garantie', text: 'Abdeckung bei Herstellungsfehlern und abnormaler Abnutzung.' },
                { title: 'Gratis Versand', text: 'Keine Kosten für Bestellungen über 250.000.' },
                { title: 'Aktionen & Treue', text: 'Gestaffelte Rabatte und exklusive Vorteile für Stammkunden.' },
                { title: 'Direkte Beratung', text: 'Persönliche WhatsApp-Betreuung bei Auswahl und Pflege.' },
            ],
            adminBadge: 'Admin-Panel',
            adminTitle: 'Prozesse und Screens',
            workerBadge: 'Mitarbeiter-Panel',
            workerTitle: 'Prozesse und Screens',
            total: (n) => `Summe: ${n} Abläufe`,
            lightboxCount: (i, total) => `Bild ${i} von ${total}`,
            adminFlows: [
                {
                    title: 'Zugang & Kontowiederherstellung',
                    summary: 'Login und Zugangsdaten-Support, damit nur autorisiertes Personal ins Panel kommt.',
                    shots: [
                        { caption: 'Admin-Login', alt: 'Login-Formular für Admin mit Benutzer und Passwort' },
                        { caption: 'Zugangsdaten bereitgestellt', alt: 'Ansicht der generierten Zugangsdaten für den Admin' },
                        { caption: 'Passwort zurücksetzen', alt: 'Seite zum Anfordern des Passwort-Resets per E-Mail' },
                    ],
                },
                {
                    title: 'Passwort ändern',
                    summary: 'Geführter Ablauf zum sicheren Passwort-Update.',
                    shots: [
                        { caption: 'Passwortwechsel öffnen', alt: 'Erstes Formular zum Ändern des aktuellen Passworts' },
                        { caption: 'Bestätigung', alt: 'Bestätigungsseite für das neue Passwort' },
                    ],
                },
                {
                    title: 'Haupt-Dashboard & Profil',
                    summary: 'Schnelle Kennzahlen und Zugriff auf das persönliche Profil, um Daten aktuell zu halten.',
                    shots: [
                        { caption: 'Admin-Dashboard', alt: 'Dashboard mit Shortcuts zu Verkäufen, Inventar und Ausgaben' },
                        { caption: 'Persönliche Daten', alt: 'Bearbeitbare Karte mit Kontaktdaten des Admins' },
                    ],
                },
                {
                    title: 'Inventar & Wareneingang',
                    summary: 'Komplette Lagerkontrolle: Bestand sehen, neue Artikel anlegen und nachfüllen.',
                    shots: [
                        { caption: 'Inventartabelle', alt: 'Produktliste mit Bestand und Preisen' },
                        { caption: 'Neuer Artikel', alt: 'Formular zum Hinzufügen eines 18K-Artikels mit Preis und Beschreibung' },
                        { caption: 'Bestand auffüllen', alt: 'Interface zum Erhöhen des Bestands eines bestehenden Produkts' },
                    ],
                },
                {
                    title: 'Verkäufe & Zahlungen',
                    summary: 'Drei Schritte: Verkauf anlegen, Zahlungsart erfassen und abgleichen.',
                    shots: [
                        { caption: 'Kunde und Produkte wählen', alt: 'Schritt 1: Kunde und Stücke auswählen' },
                        { caption: 'Zahlungsart', alt: 'Schritt 2: Zahlungsart und Anzahlungen setzen' },
                        { caption: 'Verkaufsbestätigung', alt: 'Schritt 3: Zusammenfassung und finale Bestätigung' },
                        { caption: 'Offene Zahlungen', alt: 'Liste der Verkäufe mit ausstehendem Saldo' },
                    ],
                },
                {
                    title: 'Ausgaben & Kasse',
                    summary: 'Erfassung operativer Ausgaben und Nachvollziehbarkeit für tägliche oder wöchentliche Abschlüsse.',
                    shots: [
                        { caption: 'Ausgabe erfassen', alt: 'Formular zum Erfassen einer Ausgabe mit Betrag und Kategorie' },
                        { caption: 'Gesamthistorie', alt: 'Tabelle aller erfassten Ausgaben' },
                        { caption: 'Detail nach Datum', alt: 'Gefilterte Ansicht der Bewegungen in einem Zeitraum' },
                        { caption: 'Detail nach Konzept', alt: 'Detail jeder Ausgabe mit Betrag und Verantwortlichem' },
                    ],
                },
                {
                    title: 'Garantien & Umtausch',
                    summary: 'Garantiemanagement für vergoldete Stücke: von der Anfrage bis zur Ersatzlieferung.',
                    shots: [
                        { caption: 'Garantie erstellen', alt: 'Formular zum Öffnen einer Garantie mit Schadensbeschreibung' },
                        { caption: 'Nachverfolgung', alt: 'Status des Garantie-Falls und Kundendaten' },
                        { caption: 'Historie', alt: 'Verlauf früherer Garantien zur Kontrolle' },
                        { caption: 'Abschluss', alt: 'Schlussbild mit Bestätigung von Übergabe oder Ersatz' },
                    ],
                },
            ],
            workerFlows: [
                {
                    title: 'Zugang & Profil',
                    summary: 'Mitarbeiter-Login und Aktualisierung persönlicher Daten für den Shopbetrieb.',
                    shots: [
                        { caption: 'Mitarbeiter-Login', alt: 'Login-Formular für Verkaufsmitarbeiter' },
                        { caption: 'Zugangsdaten erhalten', alt: 'Ansicht mit zugewiesenem Benutzer und Passwort' },
                        { caption: 'Persönliches Profil', alt: 'Bearbeitung grundlegender Mitarbeiterdaten' },
                    ],
                },
                {
                    title: 'Dashboard & Inventar',
                    summary: 'Schneller Start, um Aufgaben zu sehen und verfügbaren Bestand zu prüfen.',
                    shots: [
                        { caption: 'Dashboard Verkäufer', alt: 'Dashboard mit Shortcuts zu Verkäufen und Inventar' },
                        { caption: 'Inventar', alt: 'Produktliste mit sichtbarem Bestand für den Verkäufer' },
                    ],
                },
                {
                    title: 'Verkaufsprozess',
                    summary: 'Geführter Ablauf zum Abschließen von Verkäufen im Laden, Zahlung und Saldo erfassen.',
                    shots: [
                        { caption: 'Produkte auswählen', alt: 'Schritt 1: Artikel für den Verkauf wählen' },
                        { caption: 'Zahlung Kunde', alt: 'Schritt 2: Zahlungsart und Anzahlung festlegen' },
                        { caption: 'Bestätigung', alt: 'Schritt 3: Bestätigen und Beleg erzeugen' },
                    ],
                },
            ],
        },
        pt: {
            heroBadge: 'Caso real',
            heroTitle: 'Destello de Oro 18K',
            heroDesc: 'Documentação visual do sistema de joalheria: fluxos de administrador e vendedor com capturas numeradas e descrição passo a passo.',
            btnAdmin: 'Fluxos do administrador',
            btnWorker: 'Fluxos do vendedor',
            descTag: 'Destello de Oro 18K',
            descTitle: 'Joias folheadas com respaldo real',
            descP1: 'Oferecemos peças folheadas a ouro com acabamento premium, garantia escrita de 1 ano, frete grátis acima de 250.000 e programa de fidelidade com descontos progressivos.',
            descP2: 'Nossa equipe orienta sobre tamanhos, estilos e cuidados para manter o brilho. Precisa de catálogos ou orçamento? Fale conosco e respondemos na hora.',
            btnWa: 'Fale no WhatsApp',
            btnIg: 'Siga no Instagram',
            features: [
                { title: 'Garantia de 1 ano', text: 'Cobertura para defeitos de fabricação e desgaste anormal.' },
                { title: 'Frete grátis', text: 'Sem custo em pedidos acima de 250.000.' },
                { title: 'Promoções e fidelidade', text: 'Descontos progressivos e benefícios exclusivos para clientes recorrentes.' },
                { title: 'Assessoria direta', text: 'Atendimento personalizado via WhatsApp para escolher e cuidar das peças.' },
            ],
            adminBadge: 'Painel admin',
            adminTitle: 'Processos e capturas',
            workerBadge: 'Painel vendedor',
            workerTitle: 'Processos e capturas',
            total: (n) => `Total: ${n} fluxos`,
            lightboxCount: (i, total) => `Imagem ${i} de ${total}`,
            adminFlows: [
                {
                    title: 'Acesso e recuperação de conta',
                    summary: 'Telas de login e suporte de credenciais para garantir acesso apenas autorizado.',
                    shots: [
                        { caption: 'Login do administrador', alt: 'Formulário de login para admin com usuário e senha' },
                        { caption: 'Credenciais entregues', alt: 'Tela com credenciais geradas para o administrador' },
                        { caption: 'Recuperar senha', alt: 'Tela para solicitar redefinição de senha por e-mail' },
                    ],
                },
                {
                    title: 'Troca de senha',
                    summary: 'Fluxo guiado para o admin atualizar a senha com validação de segurança.',
                    shots: [
                        { caption: 'Abrir troca de senha', alt: 'Formulário inicial para mudar a senha atual' },
                        { caption: 'Confirmação', alt: 'Tela de confirmação da nova senha' },
                    ],
                },
                {
                    title: 'Dashboard e perfil',
                    summary: 'Métricas rápidas e acesso ao perfil pessoal para manter dados atualizados.',
                    shots: [
                        { caption: 'Dashboard admin', alt: 'Painel com atalhos para vendas, inventário e despesas' },
                        { caption: 'Informações pessoais', alt: 'Ficha editável com dados de contato do admin' },
                    ],
                },
                {
                    title: 'Inventário e cadastro',
                    summary: 'Controle total do estoque: ver, criar itens novos e reabastecer.',
                    shots: [
                        { caption: 'Tabela de inventário', alt: 'Lista de produtos de joalheria com estoque e preços' },
                        { caption: 'Novo produto', alt: 'Formulário para adicionar peça 18K com preço e descrição' },
                        { caption: 'Reabastecer', alt: 'Interface para aumentar estoque de um produto existente' },
                    ],
                },
                {
                    title: 'Vendas e cobranças',
                    summary: 'Processo em três passos: venda, método de pagamento e conciliação.',
                    shots: [
                        { caption: 'Selecionar cliente e produtos', alt: 'Passo 1 da venda escolhendo cliente e peças' },
                        { caption: 'Método de pagamento', alt: 'Passo 2 para definir forma de pagamento e entradas' },
                        { caption: 'Confirmação da venda', alt: 'Passo 3 com resumo e confirmação final' },
                        { caption: 'Pagamentos pendentes', alt: 'Lista de vendas com saldo pendente para acompanhamento' },
                    ],
                },
                {
                    title: 'Gastos e caixa',
                    summary: 'Registro de gastos operacionais e rastreabilidade para fechamentos diários ou semanais.',
                    shots: [
                        { caption: 'Registrar gasto', alt: 'Formulário para lançar gasto com valor e categoria' },
                        { caption: 'Histórico geral', alt: 'Tabela com todos os gastos registrados' },
                        { caption: 'Detalhe por data', alt: 'Vista filtrada dos movimentos em um período' },
                        { caption: 'Detalhe por conceito', alt: 'Detalhe de cada gasto com valor e responsável' },
                    ],
                },
                {
                    title: 'Garantias e trocas',
                    summary: 'Gestão de garantias para peças folheadas: da solicitação à reposição.',
                    shots: [
                        { caption: 'Criar garantia', alt: 'Formulário para abrir garantia com descrição do dano' },
                        { caption: 'Acompanhamento', alt: 'Status do caso de garantia e dados do cliente' },
                        { caption: 'Histórico', alt: 'Histórico de garantias anteriores para controle' },
                        { caption: 'Encerramento', alt: 'Tela de fechamento com confirmação de entrega ou reposição' },
                    ],
                },
            ],
            workerFlows: [
                {
                    title: 'Acesso e perfil',
                    summary: 'Login do vendedor e atualização de dados pessoais para atuar na loja.',
                    shots: [
                        { caption: 'Login vendedor', alt: 'Formulário de login para usuário vendedor' },
                        { caption: 'Credenciais recebidas', alt: 'Tela com usuário e senha atribuídos' },
                        { caption: 'Perfil pessoal', alt: 'Edição dos dados básicos do vendedor' },
                    ],
                },
                {
                    title: 'Dashboard e inventário',
                    summary: 'Início rápido para ver tarefas e checar estoque disponível.',
                    shots: [
                        { caption: 'Dashboard vendedor', alt: 'Painel com atalhos para vendas e inventário' },
                        { caption: 'Inventário', alt: 'Lista de produtos com estoque visível para o vendedor' },
                    ],
                },
                {
                    title: 'Processo de venda',
                    summary: 'Fluxo guiado para fechar vendas na loja, registrando pagamento e saldo.',
                    shots: [
                        { caption: 'Selecionar produtos', alt: 'Passo 1: escolher itens da venda' },
                        { caption: 'Pagamento do cliente', alt: 'Passo 2: definir forma de pagamento e entrada' },
                        { caption: 'Confirmação', alt: 'Passo 3: confirmar e gerar recibo' },
                    ],
                },
            ],
        },
        it: {
            heroBadge: 'Caso reale',
            heroTitle: 'Destello de Oro 18K',
            heroDesc: 'Documentazione visiva del sistema per gioielleria: flussi admin e commesso con screenshot numerati e descrizione passo-passo.',
            btnAdmin: 'Flussi admin',
            btnWorker: 'Flussi commesso',
            descTag: 'Destello de Oro 18K',
            descTitle: 'Gioielli placcati oro con garanzia reale',
            descP1: 'Offriamo pezzi placcati oro con finitura premium, garanzia scritta di 1 anno, spedizione gratuita sopra 250.000 e programma fedeltà con sconti progressivi.',
            descP2: 'Il nostro team consiglia su taglie, stili e cura per mantenere la lucentezza. Hai bisogno di cataloghi o preventivo? Scrivici, rispondiamo subito.',
            btnWa: 'Scrivici su WhatsApp',
            btnIg: 'Seguici su Instagram',
            features: [
                { title: 'Garanzia 1 anno', text: 'Copertura per difetti di fabbrica e usura anomala.' },
                { title: 'Spedizione gratuita', text: 'Senza costo per ordini sopra 250.000.' },
                { title: 'Promo e fedeltà', text: 'Sconti progressivi e vantaggi esclusivi per clienti abituali.' },
                { title: 'Consulenza diretta', text: 'Assistenza personalizzata via WhatsApp per scegliere e curare i tuoi pezzi.' },
            ],
            adminBadge: 'Pannello admin',
            adminTitle: 'Processi e schermate',
            workerBadge: 'Pannello commesso',
            workerTitle: 'Processi e schermate',
            total: (n) => `Totale: ${n} flussi`,
            lightboxCount: (i, total) => `Immagine ${i} di ${total}`,
            adminFlows: [
                {
                    title: 'Accesso e recupero account',
                    summary: 'Schermate di login e supporto credenziali per garantire accesso solo autorizzato.',
                    shots: [
                        { caption: 'Login amministratore', alt: 'Form di accesso per admin con utente e password' },
                        { caption: 'Credenziali consegnate', alt: 'Vista delle credenziali generate per l’amministratore' },
                        { caption: 'Recupero password', alt: 'Schermata per richiedere il reset via email' },
                    ],
                },
                {
                    title: 'Cambio password',
                    summary: 'Flusso guidato per aggiornare la password con validazione di sicurezza.',
                    shots: [
                        { caption: 'Apri cambio password', alt: 'Form iniziale per cambiare la password attuale' },
                        { caption: 'Conferma', alt: 'Schermata di conferma della nuova password' },
                    ],
                },
                {
                    title: 'Dashboard principale e profilo',
                    summary: 'Dashboard con metriche rapide e accesso al profilo personale per mantenere i dati aggiornati.',
                    shots: [
                        { caption: 'Dashboard admin', alt: 'Dashboard con accessi a vendite, inventario e spese' },
                        { caption: 'Info personali', alt: 'Scheda modificabile con dati di contatto dell’admin' },
                    ],
                },
                {
                    title: 'Inventario e nuovi prodotti',
                    summary: 'Controllo completo dello stock: vedere, creare articoli e rifornire.',
                    shots: [
                        { caption: 'Tabella inventario', alt: 'Elenco prodotti con stock e prezzi' },
                        { caption: 'Nuovo prodotto', alt: 'Form per aggiungere un articolo 18K con prezzo e descrizione' },
                        { caption: 'Rifornire inventario', alt: 'Interfaccia per aumentare lo stock di un prodotto esistente' },
                    ],
                },
                {
                    title: 'Vendite e incassi',
                    summary: 'Processo in tre passi: creare la vendita, registrare pagamento e riconciliare.',
                    shots: [
                        { caption: 'Seleziona cliente e prodotti', alt: 'Passo 1 della vendita scegliendo cliente e pezzi' },
                        { caption: 'Metodo di pagamento', alt: 'Passo 2 per impostare metodo di pagamento e acconti' },
                        { caption: 'Conferma vendita', alt: 'Passo 3 con riepilogo e conferma finale' },
                        { caption: 'Pagamenti pendenti', alt: 'Elenco vendite con saldo in sospeso da seguire' },
                    ],
                },
                {
                    title: 'Spese e cassa',
                    summary: 'Registrazione spese operative e tracciabilità per chiusure giornaliere o settimanali.',
                    shots: [
                        { caption: 'Registrare spesa', alt: 'Form per registrare una spesa con importo e categoria' },
                        { caption: 'Storico generale', alt: 'Tabella con tutte le spese registrate' },
                        { caption: 'Dettaglio per data', alt: 'Vista filtrata dei movimenti in un periodo' },
                        { caption: 'Dettaglio per voce', alt: 'Dettaglio di ogni spesa con importo e responsabile' },
                    ],
                },
                {
                    title: 'Garanzie e cambi',
                    summary: 'Gestione delle garanzie per pezzi placcati oro: dalla richiesta alla sostituzione.',
                    shots: [
                        { caption: 'Creare garanzia', alt: 'Form per aprire una garanzia con descrizione del danno' },
                        { caption: 'Follow-up', alt: 'Stato del caso di garanzia e dati del cliente' },
                        { caption: 'Storico', alt: 'Storico delle garanzie precedenti per controllo' },
                        { caption: 'Chiusura', alt: 'Schermata di chiusura con conferma di consegna o sostituzione' },
                    ],
                },
            ],
            workerFlows: [
                {
                    title: 'Accesso e profilo',
                    summary: 'Login del commesso e aggiornamento dati personali per operare in negozio.',
                    shots: [
                        { caption: 'Login commesso', alt: 'Form di accesso per utente venditore' },
                        { caption: 'Credenziali ricevute', alt: 'Vista con utente e password assegnati' },
                        { caption: 'Profilo personale', alt: 'Modifica dei dati base del venditore' },
                    ],
                },
                {
                    title: 'Dashboard e inventario',
                    summary: 'Avvio rapido per vedere attività e controllare lo stock disponibile.',
                    shots: [
                        { caption: 'Dashboard venditore', alt: 'Dashboard con scorciatoie a vendite e inventario' },
                        { caption: 'Inventario', alt: 'Lista prodotti con stock visibile al venditore' },
                    ],
                },
                {
                    title: 'Processo di vendita',
                    summary: 'Flusso guidato per chiudere vendite in negozio registrando pagamento e saldo.',
                    shots: [
                        { caption: 'Seleziona prodotti', alt: 'Passo 1: scegliere gli articoli da vendere' },
                        { caption: 'Pagamento cliente', alt: 'Passo 2: definire metodo di pagamento e acconto' },
                        { caption: 'Conferma', alt: 'Passo 3: confermare e generare ricevuta' },
                    ],
                },
            ],
        }
    };
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
        const lang = window.mceCurrentLang || localStorage.getItem('siteLang') || 'es';
        count.textContent = window.doLightboxCount
            ? window.doLightboxCount(index, items.length, lang)
            : `Imagen ${index + 1} de ${items.length}`;
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
