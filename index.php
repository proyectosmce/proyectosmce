<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
/* Botones flotantes y asistente */
.floating-buttons {
    position: fixed;
    bottom: 100px;
    right: 18px;
    display: grid;
    gap: 10px;
    justify-items: end;
    grid-auto-rows: min-content;
    grid-auto-flow: row;
    z-index: 99999;
}
.float-btn {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    border: 2px solid #0a1630;
    cursor: pointer;
    color: #0f274b;
    display: grid;
    place-items: center;
    box-shadow: 0 12px 24px rgba(0,0,0,0.3);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    font-size: 1.3rem;
}
.float-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 26px rgba(0,0,0,0.32); }
.float-btn.assistant {
    position: relative;
    background: transparent;
    border: none;
    box-shadow: none;
    width: auto;
    height: auto;
    border-radius: 0;
    padding: 0;
}
.float-btn.assistant img.bot-img {
    display: block;
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 0;
    animation: botWaveCycle 14s ease-in-out infinite;
}
.float-btn.whatsapp {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #25D366;
    color: #fff;
    border: 2px solid #128C7E;
    box-shadow: 0 10px 20px rgba(0,0,0,0.25);
    font-size: 1.2rem;
}
.float-btn.assistant:hover img.bot-img,
.float-btn.assistant.paused img.bot-img {
    animation-play-state: paused;
}

@keyframes botWaveCycle {
    0%,70%   { transform: rotate(0deg); }
    80%      { transform: rotate(10deg); }
    88%      { transform: rotate(-10deg); }
    95%      { transform: rotate(6deg); }
    100%     { transform: rotate(0deg); }
}
.assistant-panel {
    position: fixed;
    bottom: 180px;
    right: 18px;
    width: 320px;
    max-height: 420px;
    background: #ffffff;
    border: 1px solid #e3e9f3;
    box-shadow: 0 18px 36px rgba(0,0,0,0.25);
    border-radius: 14px;
    display: none;
    flex-direction: column;
    overflow: visible;
    z-index: 99998;
}
.assistant-panel.open { display: flex; }
.assistant-header {
    background: linear-gradient(135deg, #0a1630, #12325f);
    color: white;
    padding: 10px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 700;
    font-size: 0.95rem;
}
.assistant-header .left { display: flex; align-items: center; gap: 10px; }
.assistant-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #ffd700;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
}
.assistant-body { padding: 12px; display: grid; gap: 10px; font-size: 0.9rem; color: #1b2b48; }
.assistant-answer {
    background: #f5f7fb;
    border: 1px solid #e3e9f3;
    border-radius: 10px;
    padding: 10px;
    min-height: 60px;
    line-height: 1.4;
}
.assistant-lang {
    display: flex;
    justify-content: flex-end;
    position: relative;
}
.assistant-lang select { display: none; }
.lang-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 10px;
    border: 1px solid #d4dce7;
    font-size: 0.85rem;
    background: #fff;
    color: #1b2b48;
    cursor: pointer;
}
.lang-toggle img {
    width: 18px;
    height: 14px;
    object-fit: cover;
    border-radius: 2px;
}
.lang-list {
    position: absolute;
    right: 0;
    top: 110%;
    background: #fff;
    border: 1px solid #d4dce7;
    border-radius: 10px;
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
    padding: 8px 8px 10px;
    display: none;
    z-index: 5;
    max-height: 200px;
    overflow-y: auto;
    min-width: 180px;
}
.lang-list::-webkit-scrollbar {
    width: 8px;
}
.lang-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 6px;
}
.lang-list::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 6px;
}
.lang-list.open { display: block; }
.lang-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    color: #1b2b48;
}
.lang-option:hover { background: #f1f5f9; }
.lang-option img {
    width: 18px;
    height: 14px;
    object-fit: cover;
    border-radius: 2px;
}
.assistant-input { display: flex; gap: 8px; }
.assistant-input input {
    flex: 1; padding: 10px 12px; border-radius: 10px;
    border: 1px solid #d4dce7; font-size: 0.9rem;
}
.assistant-input button {
    padding: 10px 12px; border-radius: 10px; border: none;
    background: #0f274b; color: #ffd700; font-weight: 700; cursor: pointer;
}
</style>

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
                    Proyectos MCE · Software a medida
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
                    <a href="<?php echo app_url('portafolio.php#casos-exito-portafolio'); ?>" class="inline-flex items-center justify-center border-2 border-yellow-300 text-yellow-300 px-8 py-4 rounded-xl font-semibold hover:bg-yellow-300 hover:text-slate-900 transition">
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

<?php include 'includes/footer.php'; ?>

