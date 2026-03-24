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
    overflow: hidden;
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

<!-- Panel de asistente -->
<div class="assistant-panel" id="assistant-panel">
    <div class="assistant-header">
        <div class="left">
            <img src="<?php echo app_url('asstv.webp'); ?>" alt="Asistente MCE" class="assistant-avatar">
            <span>Asistente MCE</span>
        </div>
        <button id="assistant-close" style="background:none;border:none;color:#ffd700;font-weight:800;font-size:1rem;cursor:pointer;">×</button>
    </div>
    <div class="assistant-body">
        <div class="assistant-answer" id="assistant-answer">Hola, ¿en qué puedo ayudarte sobre nuestros servicios?</div>
        <div class="assistant-input">
            <input id="assistant-question" type="text" placeholder="Escribe tu pregunta..." />
            <button id="assistant-send">Enviar</button>
        </div>
    </div>
</div>

<!-- Botón flotante del asistente (botón de WhatsApp original permanece en footer) -->
<div class="floating-buttons" id="floating-buttons">
    <button class="float-btn assistant" id="assistant-toggle" aria-label="Asistente virtual">
        <img src="<?php echo app_url('asstv.webp'); ?>" alt="Abrir asistente MCE" class="bot-img">
        <span style="position:absolute;opacity:0;">Asistente</span>
    </button>
</div>

<script>
(() => {
    const panel = document.getElementById('assistant-panel');
    const toggle = document.getElementById('assistant-toggle');
    const closeBtn = document.getElementById('assistant-close');
    const sendBtn = document.getElementById('assistant-send');
    const questionInput = document.getElementById('assistant-question');
    const answerBox = document.getElementById('assistant-answer');

    if (!panel || !toggle) return;

    const faqs = [
        { keywords: ['hola', 'buenas', 'saludo', 'hello', 'hi', 'hey', 'bonjour', 'salut', 'hallo', 'ola', 'olá', 'ciao'], answers: {
            es: 'Hola, ¿cómo estás? ¿En qué puedo ayudarte hoy?',
            en: 'Hi! How are you? How can I help you today?',
            fr: 'Salut ! Comment ça va ? Comment puis-je t’aider aujourd’hui ?',
            de: 'Hallo! Wie geht’s? Wobei kann ich dir heute helfen?',
            pt: 'Oi! Tudo bem? Como posso ajudar você hoje?',
            it: 'Ciao! Come stai? Come posso aiutarti oggi?'
        }},
        { keywords: ['desarrollo', 'web', 'medida'], answers: {
            es: 'Creamos software y sitios web a medida, alineados a tus procesos y objetivos.',
            en: 'We build custom software and websites tailored to your processes and goals.',
            fr: 'Nous créons des logiciels et sites web sur mesure, adaptés à vos processus et objectifs.',
            de: 'Wir entwickeln maßgeschneiderte Software und Websites, abgestimmt auf deine Prozesse und Ziele.',
            pt: 'Criamos software e sites sob medida, alinhados aos seus processos e objetivos.',
            it: 'Creiamo software e siti web su misura, allineati ai tuoi processi e obiettivi.'
        }},
        { keywords: ['tienda', 'ecommerce', 'online', 'shop', 'store'], answers: {
            es: 'Construimos tiendas online integradas con inventarios, pagos y logística.',
            en: 'We build online stores integrated with inventory, payments, and logistics.',
            fr: 'Nous créons des boutiques en ligne intégrées aux stocks, paiements et logistique.',
            de: 'Wir bauen Online-Shops mit Integration von Lager, Zahlungen und Logistik.',
            pt: 'Construímos lojas online integradas a estoque, pagamentos e logística.',
            it: 'Realizziamo e-commerce integrati con inventario, pagamenti e logistica.'
        }},
        { keywords: ['inventario', 'stock', 'bodega'], answers: {
            es: 'Implementamos sistemas de inventario con control de stock y trazabilidad en tiempo real.',
            en: 'We implement inventory systems with real-time stock control and traceability.',
            fr: 'Nous mettons en place des systèmes d’inventaire avec contrôle et traçabilité en temps réel.',
            de: 'Wir implementieren Warenwirtschaft mit Echtzeit-Bestand und Rückverfolgbarkeit.',
            pt: 'Implementamos sistemas de inventário com controle e rastreabilidade em tempo real.',
            it: 'Implementiamo sistemi di inventario con controllo stock e tracciabilità in tempo reale.'
        }},
        { keywords: ['diseño', 'ux', 'ui', 'design'], answers: {
            es: 'Realizamos diseño UX/UI con flujos claros y pantallas fáciles de usar.',
            en: 'We deliver UX/UI design with clear flows and easy-to-use screens.',
            fr: 'Nous réalisons des designs UX/UI avec des parcours clairs et des écrans simples.',
            de: 'Wir erstellen UX/UI-Design mit klaren Flows und leicht bedienbaren Screens.',
            pt: 'Fazemos UX/UI com fluxos claros e telas fáceis de usar.',
            it: 'Facciamo UX/UI con flussi chiari e schermate facili da usare.'
        }},
        { keywords: ['api', 'integración', 'erp', 'pasarela', 'pago', 'crm', 'api', 'integration'], answers: {
            es: 'Conectamos tu sistema con ERPs, pasarelas de pago y CRMs mediante APIs seguras.',
            en: 'We connect your system with ERPs, payment gateways, and CRMs through secure APIs.',
            fr: 'Nous connectons votre système aux ERP, passerelles de paiement et CRM via des API sécurisées.',
            de: 'Wir verbinden dein System per sicheren APIs mit ERP, Payment-Gateways und CRM.',
            pt: 'Conectamos seu sistema a ERPs, gateways de pagamento e CRMs por APIs seguras.',
            it: 'Colleghiamo il tuo sistema a ERP, gateway di pagamento e CRM tramite API sicure.'
        }},
        { keywords: ['soporte', '24/7', 'monitoreo', 'support'], answers: {
            es: 'Brindamos soporte continuo, monitoreo y mesa de ayuda para que tu operación no se detenga.',
            en: 'We provide continuous support, monitoring, and a help desk so your operation doesn’t stop.',
            fr: 'Nous assurons support continu, monitoring et helpdesk pour que votre opération ne s’arrête pas.',
            de: 'Wir bieten kontinuierlichen Support, Monitoring und Helpdesk, damit dein Betrieb nicht stoppt.',
            pt: 'Oferecemos suporte contínuo, monitoramento e help desk para que sua operação não pare.',
            it: 'Forniamo supporto continuo, monitoraggio e help desk perché la tua operazione non si fermi.'
        }},
        { keywords: ['discovery', 'agenda', 'sesión', 'session'], answers: {
            es: 'Discovery es la sesión inicial para entender tu negocio y priorizar el primer entregable.',
            en: 'Discovery is the first session to understand your business and prioritize the first deliverable.',
            fr: 'Le Discovery est la première session pour comprendre votre activité et prioriser le premier livrable.',
            de: 'Discovery ist die erste Session, um dein Business zu verstehen und das erste Deliverable zu priorisieren.',
            pt: 'Discovery é a sessão inicial para entender seu negócio e priorizar o primeiro entregável.',
            it: 'La Discovery è la prima sessione per capire il tuo business e priorizzare il primo deliverable.'
        }},
        { keywords: ['mvp'], answers: {
            es: 'Un MVP es la versión mínima viable: funciones esenciales para operar y validar rápido con usuarios reales.',
            en: 'An MVP is the minimum viable product: essential functions to operate and validate quickly with real users.',
            fr: 'Un MVP est le produit minimum viable : fonctions essentielles pour opérer et valider rapidement avec des utilisateurs réels.',
            de: 'Ein MVP ist das minimal brauchbare Produkt mit Kernfunktionen, um schnell mit echten Nutzern zu validieren.',
            pt: 'Um MVP é o produto mínimo viável: funções essenciais para operar e validar rápido com usuários reais.',
            it: 'Un MVP è il prodotto minimo viabile: funzioni essenziali per operare e validare rapidamente con utenti reali.'
        }},
        { keywords: ['roadmap', 'hoja de ruta'], answers: {
            es: 'El roadmap es la hoja de ruta del proyecto: qué se hace primero y qué entregables tiene cada fase.',
            en: 'The roadmap is the project path: what gets done first and which deliverables each phase has.',
            fr: 'La roadmap est la feuille de route du projet : ce qui est fait en premier et les livrables de chaque phase.',
            de: 'Die Roadmap ist der Fahrplan: was zuerst kommt und welche Deliverables jede Phase hat.',
            pt: 'O roadmap é a trilha do projeto: o que vem primeiro e quais entregáveis cada fase tem.',
            it: 'La roadmap è il percorso del progetto: cosa si fa per primo e quali deliverable ha ogni fase.'
        }},
        { keywords: ['backlog'], answers: {
            es: 'El backlog es la lista priorizada de tareas/funciones pendientes que alimenta cada sprint.',
            en: 'The backlog is the prioritized list of pending tasks/features feeding each sprint.',
            fr: 'Le backlog est la liste priorisée des tâches/fonctions en attente pour chaque sprint.',
            de: 'Das Backlog ist die priorisierte Liste offener Aufgaben/Funktionen für jeden Sprint.',
            pt: 'O backlog é a lista priorizada de tarefas/funções que alimenta cada sprint.',
            it: 'Il backlog è la lista prioritaria di task/funzioni che alimenta ogni sprint.'
        }},
        { keywords: ['sprint'], answers: {
            es: 'Un sprint es un ciclo corto de trabajo (1-2 semanas) con objetivos claros y demo al final.',
            en: 'A sprint is a short work cycle (1-2 weeks) with clear goals and a demo at the end.',
            fr: 'Un sprint est un cycle court (1-2 semaines) avec objectifs clairs et démo finale.',
            de: 'Ein Sprint ist ein kurzer Arbeitszyklus (1-2 Wochen) mit klaren Zielen und Demo am Ende.',
            pt: 'Um sprint é um ciclo curto (1-2 semanas) com metas claras e demo no final.',
            it: 'Uno sprint è un ciclo breve (1-2 settimane) con obiettivi chiari e una demo finale.'
        }},
        { keywords: ['qa', 'pruebas', 'quality'], answers: {
            es: 'QA son pruebas funcionales/técnicas para asegurar que todo funciona antes de publicar.',
            en: 'QA means functional/technical testing to ensure everything works before going live.',
            fr: 'La QA, ce sont des tests fonctionnels/techniques pour garantir que tout marche avant la mise en ligne.',
            de: 'QA sind funktionale/technische Tests, damit alles funktioniert, bevor wir live gehen.',
            pt: 'QA são testes funcionais/técnicos para garantir que tudo funciona antes de publicar.',
            it: 'QA sono test funzionali/tecnici per assicurare che tutto funzioni prima di andare live.'
        }},
        { keywords: ['staging'], answers: {
            es: 'Staging es el ambiente de pruebas idéntico a producción donde validamos antes de subir cambios.',
            en: 'Staging is the test environment identical to production where we validate before deploying.',
            fr: 'Le staging est l’environnement de test identique à la production pour valider avant déploiement.',
            de: 'Staging ist die Testumgebung wie Produktion, in der wir vor dem Deploy validieren.',
            pt: 'Staging é o ambiente de testes idêntico à produção para validar antes de publicar.',
            it: 'Lo staging è l’ambiente di test identico alla produzione per validare prima del deploy.'
        }},
        { keywords: ['hosting', 'servidor'], answers: {
            es: 'El hosting/servidor es donde vive tu sitio o app para que esté disponible 24/7.',
            en: 'Hosting/server is where your site or app lives so it’s available 24/7.',
            fr: 'Le hosting/serveur héberge votre site ou app pour la rendre disponible 24/7.',
            de: 'Hosting/Server ist, wo deine Site/App lebt, damit sie 24/7 erreichbar ist.',
            pt: 'Hosting/servidor é onde seu site ou app fica disponível 24/7.',
            it: 'Hosting/server è dove il tuo sito o app vive per essere disponibile 24/7.'
        }},
        { keywords: ['ssl', 'https', 'candado'], answers: {
            es: 'SSL/HTTPS cifra la conexión y muestra el candado para proteger los datos de tus usuarios.',
            en: 'SSL/HTTPS encrypts the connection and shows the lock to protect your users’ data.',
            fr: 'SSL/HTTPS chiffre la connexion et affiche le cadenas pour protéger les données.',
            de: 'SSL/HTTPS verschlüsselt die Verbindung und zeigt das Schloss zum Schutz der Nutzerdaten.',
            pt: 'SSL/HTTPS criptografa a conexão e mostra o cadeado para proteger os dados.',
            it: 'SSL/HTTPS cifra la connessione e mostra il lucchetto per proteggere i dati.'
        }},
        { keywords: ['performance', 'rápido', 'velocidad', 'speed'], answers: {
            es: 'Performance web es qué tan rápido carga y responde tu sitio; clave para retención y SEO.',
            en: 'Web performance is how fast your site loads and responds—key for retention and SEO.',
            fr: 'La performance web est la vitesse de chargement/réponse, clé pour la rétention et le SEO.',
            de: 'Web-Performance ist, wie schnell deine Seite lädt und reagiert—wichtig für Retention und SEO.',
            pt: 'Performance web é quão rápido seu site carrega e responde — chave para retenção e SEO.',
            it: 'La performance web è la velocità di caricamento/risposta, cruciale per retention e SEO.'
        }},
        { keywords: ['automatización', 'automation'], answers: {
            es: 'Automatización: reemplazar tareas manuales por flujos automáticos (notificaciones, reportes, sincronizaciones).',
            en: 'Automation: replace manual tasks with automatic flows (notifications, reports, syncs).',
            fr: 'Automatisation : remplacer les tâches manuelles par des flux automatiques (notifications, rapports, synchro).',
            de: 'Automatisierung: manuelle Aufgaben durch automatische Abläufe ersetzen (Benachrichtigungen, Reports, Sync).',
            pt: 'Automação: trocar tarefas manuais por fluxos automáticos (notificações, relatórios, sincronizações).',
            it: 'Automazione: sostituire compiti manuali con flussi automatici (notifiche, report, sincronizzazioni).'
        }},
        { keywords: ['observabilidad', 'alertas', 'monitoreo'], answers: {
            es: 'Observabilidad/alertas: métricas y avisos automáticos si algo falla o se vuelve lento.',
            en: 'Observability/alerts: metrics and automatic notices if something fails or slows down.',
            fr: 'Observabilité/alertes : métriques et notifications automatiques en cas de panne ou lenteur.',
            de: 'Observability/Alerts: Metriken und automatische Hinweise, wenn etwas ausfällt oder langsam wird.',
            pt: 'Observabilidade/alertas: métricas e avisos automáticos se algo falhar ou ficar lento.',
            it: 'Osservabilità/alert: metriche e avvisi automatici se qualcosa fallisce o rallenta.'
        }},
        { keywords: ['escalabilidad', 'scalability'], answers: {
            es: 'Escalabilidad: que la solución crezca en usuarios/datos sin caerse ni volverse lenta.',
            en: 'Scalability: your solution grows in users/data without crashing or slowing down.',
            fr: 'Scalabilité : la solution grandit en utilisateurs/données sans tomber ni ralentir.',
            de: 'Skalierbarkeit: mehr Nutzer/Daten ohne Abstürze oder Verlangsamung.',
            pt: 'Escalabilidade: crescer em usuários/dados sem cair ou ficar lento.',
            it: 'Scalabilità: crescere in utenti/dati senza cadere o rallentare.'
        }},
        { keywords: ['ux writing', 'microcopy'], answers: {
            es: 'UX writing/microcopys: textos cortos y claros en botones y mensajes para guiar al usuario.',
            en: 'UX writing/microcopy: short, clear texts in buttons and messages to guide users.',
            fr: 'UX writing/microcopy : textes courts et clairs dans les boutons/messages pour guider l’utilisateur.',
            de: 'UX Writing/Microcopy: kurze, klare Texte in Buttons/Nachrichten zur Nutzerführung.',
            pt: 'UX writing/microcopy: textos curtos e claros em botões e mensagens para guiar o usuário.',
            it: 'UX writing/microcopy: testi brevi e chiari in pulsanti e messaggi per guidare l’utente.'
        }},
        { keywords: ['mantenimiento', 'evolutivo'], answers: {
            es: 'Mantenimiento evolutivo: nuevas mejoras y funciones después del lanzamiento, no solo soporte.',
            en: 'Evolutionary maintenance: new improvements and features after launch, not just bug fixes.',
            fr: 'Maintenance évolutive : nouvelles améliorations et fonctions après le lancement, pas seulement du support.',
            de: 'Evolutive Wartung: neue Verbesserungen/Funktionen nach dem Launch, nicht nur Support.',
            pt: 'Manutenção evolutiva: novas melhorias e funções após o lançamento, não só suporte.',
            it: 'Manutenzione evolutiva: nuove migliorie e funzioni dopo il lancio, non solo supporto.'
        }},
        { keywords: ['integración', 'whatsapp', 'telegram', 'mensajería', 'messaging'], answers: {
            es: 'Integramos mensajería (WhatsApp/Telegram) para enviar/recibir mensajes y notificaciones automáticas.',
            en: 'We integrate messaging (WhatsApp/Telegram) to send/receive messages and automatic notifications.',
            fr: 'Nous intégrons la messagerie (WhatsApp/Telegram) pour envoyer/recevoir des messages et notifications.',
            de: 'Wir integrieren Messaging (WhatsApp/Telegram) für Nachrichten und automatische Benachrichtigungen.',
            pt: 'Integramos mensageria (WhatsApp/Telegram) para enviar/receber mensagens e notificações automáticas.',
            it: 'Integriamo messaggistica (WhatsApp/Telegram) per inviare/ricevere messaggi e notifiche automatiche.'
        }},
        { keywords: ['dashboard'], answers: {
            es: 'Un dashboard es un panel con métricas y gráficos clave para tomar decisiones rápidas.',
            en: 'A dashboard is a panel with key metrics and charts for quick decisions.',
            fr: 'Un dashboard est un tableau avec métriques et graphiques clés pour décider vite.',
            de: 'Ein Dashboard ist ein Panel mit Kennzahlen und Charts für schnelle Entscheidungen.',
            pt: 'Um dashboard é um painel com métricas e gráficos-chave para decidir rápido.',
            it: 'Una dashboard è un pannello con metriche e grafici chiave per decisioni rapide.'
        }},
        { keywords: ['responsive', 'móvil', 'tablet', 'mobile'], answers: {
            es: 'Responsive: que la web/app se adapte y se vea bien en móvil, tablet y desktop.',
            en: 'Responsive means the site/app adapts and looks good on mobile, tablet, and desktop.',
            fr: 'Responsive signifie que le site/app s’adapte et rend bien sur mobile, tablette et desktop.',
            de: 'Responsive heißt, dass die Site/App auf Mobile, Tablet und Desktop gut aussieht.',
            pt: 'Responsivo: o site/app se adapta e fica bom em mobile, tablet e desktop.',
            it: 'Responsive: il sito/app si adatta e si vede bene su mobile, tablet e desktop.'
        }},
        { keywords: ['seo'], answers: {
            es: 'SEO básico: títulos, descripciones, estructura y velocidad para que los buscadores entiendan y posicionen mejor tu sitio.',
            en: 'Basic SEO: titles, descriptions, structure, and speed so search engines can rank you better.',
            fr: 'SEO de base : titres, descriptions, structure et vitesse pour mieux vous positionner.',
            de: 'Basis-SEO: Titel, Beschreibungen, Struktur und Speed für besseres Ranking.',
            pt: 'SEO básico: títulos, descrições, estrutura e velocidade para melhor ranqueamento.',
            it: 'SEO base: titoli, descrizioni, struttura e velocità per posizionarti meglio.'
        }},
        { keywords: ['backup', 'respaldo'], answers: {
            es: 'Backup: copias de seguridad programadas para restaurar rápido ante errores o incidentes.',
            en: 'Backup: scheduled copies to restore quickly after errors or incidents.',
            fr: 'Backup : copies programmées pour restaurer rapidement en cas d’erreur ou incident.',
            de: 'Backup: geplante Sicherungen für schnelle Wiederherstellung bei Fehlern/Incidents.',
            pt: 'Backup: cópias programadas para restaurar rápido após erros ou incidentes.',
            it: 'Backup: copie programmate per ripristinare rapidamente in caso di errori o incidenti.'
        }},
        { keywords: ['inicio', 'home', 'start'], answers: {
            es: 'Inicio: portada con el resumen de Proyectos MCE, propuesta de valor y llamados a agendar o ver portafolio.',
            en: 'Home: the landing page with Proyectos MCE value prop and CTAs to book or view portfolio.',
            fr: 'Accueil : page d’entrée avec la proposition de valeur MCE et CTA pour réserver ou voir le portfolio.',
            de: 'Startseite: Überblick mit Value Proposition von MCE und CTAs zum Buchen oder Portfolio ansehen.',
            pt: 'Início: página principal com a proposta de valor da MCE e CTAs para agendar ou ver portfólio.',
            it: 'Home: pagina di ingresso con value proposition MCE e CTA per prenotare o vedere il portfolio.'
        }},
        { keywords: ['servicios', 'services'], answers: {
            es: 'Servicios: lista de lo que hacemos (desarrollo a medida, ecommerce, inventarios, UX/UI, integraciones, soporte 24/7).',
            en: 'Services: what we deliver (custom dev, ecommerce, inventory, UX/UI, integrations, 24/7 support).',
            fr: 'Services : ce que nous faisons (dev sur mesure, ecommerce, inventaires, UX/UI, intégrations, support 24/7).',
            de: 'Services: unsere Leistungen (Custom Dev, E‑Commerce, Inventar, UX/UI, Integrationen, 24/7 Support).',
            pt: 'Serviços: o que fazemos (dev sob medida, ecommerce, inventário, UX/UI, integrações, suporte 24/7).',
            it: 'Servizi: cosa offriamo (sviluppo su misura, ecommerce, inventari, UX/UI, integrazioni, supporto 24/7).'
        }},
        { keywords: ['portafolio', 'portfolio'], answers: {
            es: 'Portafolio: casos en vivo y proyectos entregados para que veas ejemplos reales.',
            en: 'Portfolio: live cases and delivered projects so you can see real examples.',
            fr: 'Portfolio : cas réels et projets livrés pour voir des exemples concrets.',
            de: 'Portfolio: Live-Cases und gelieferte Projekte als echte Beispiele.',
            pt: 'Portfólio: casos ao vivo e projetos entregues para ver exemplos reais.',
            it: 'Portfolio: casi reali e progetti consegnati per vedere esempi concreti.'
        }},
        { keywords: ['testimonios', 'testimonials', 'reviews'], answers: {
            es: 'Testimonios: opiniones de clientes sobre cómo trabajamos y los resultados obtenidos.',
            en: 'Testimonials: client feedback about how we work and the results delivered.',
            fr: 'Témoignages : avis clients sur notre manière de travailler et les résultats obtenus.',
            de: 'Referenzen: Kundenmeinungen zu unserer Arbeit und den erzielten Ergebnissen.',
            pt: 'Depoimentos: opiniões de clientes sobre nosso trabalho e resultados.',
            it: 'Testimonianze: feedback dei clienti sul nostro lavoro e risultati.'
        }},
        { keywords: ['contacto', 'contact', 'contactar'], answers: {
            es: 'Contacto: sección con formulario, WhatsApp y correo. Tel: +57 311 412 5971 · Email: proyectosmceaa@gmail.com',
            en: 'Contact: form plus WhatsApp and email. Phone: +57 311 412 5971 · Email: proyectosmceaa@gmail.com',
            fr: 'Contact : formulaire, WhatsApp et email. Tél : +57 311 412 5971 · Email : proyectosmceaa@gmail.com',
            de: 'Kontakt: Formular, WhatsApp und E-Mail. Tel: +57 311 412 5971 · E-Mail: proyectosmceaa@gmail.com',
            pt: 'Contato: formulário, WhatsApp e e-mail. Tel: +57 311 412 5971 · Email: proyectosmceaa@gmail.com',
            it: 'Contatti: form, WhatsApp ed email. Tel: +57 311 412 5971 · Email: proyectosmceaa@gmail.com'
        }},
        { keywords: ['whatsapp', 'wa'], answers: {
            es: 'Nuestro WhatsApp: https://wa.me/573114125971',
            en: 'Our WhatsApp: https://wa.me/573114125971',
            fr: 'Notre WhatsApp : https://wa.me/573114125971',
            de: 'Unser WhatsApp: https://wa.me/573114125971',
            pt: 'Nosso WhatsApp: https://wa.me/573114125971',
            it: 'Il nostro WhatsApp: https://wa.me/573114125971'
        }},
        { keywords: ['telegram'], answers: {
            es: 'Telegram: https://t.me/proyectosmce',
            en: 'Telegram: https://t.me/proyectosmce',
            fr: 'Telegram : https://t.me/proyectosmce',
            de: 'Telegram: https://t.me/proyectosmce',
            pt: 'Telegram: https://t.me/proyectosmce',
            it: 'Telegram: https://t.me/proyectosmce'
        }},
        { keywords: ['instagram', 'ig'], answers: {
            es: 'Instagram: https://www.instagram.com/proyectosmce/',
            en: 'Instagram: https://www.instagram.com/proyectosmce/',
            fr: 'Instagram : https://www.instagram.com/proyectosmce/',
            de: 'Instagram: https://www.instagram.com/proyectosmce/',
            pt: 'Instagram: https://www.instagram.com/proyectosmce/',
            it: 'Instagram: https://www.instagram.com/proyectosmce/'
        }},
        { keywords: ['linkedin', 'linkein', 'ln'], answers: {
            es: 'LinkedIn: https://www.linkedin.com/company/proyectosmce/',
            en: 'LinkedIn: https://www.linkedin.com/company/proyectosmce/',
            fr: 'LinkedIn : https://www.linkedin.com/company/proyectosmce/',
            de: 'LinkedIn: https://www.linkedin.com/company/proyectosmce/',
            pt: 'LinkedIn: https://www.linkedin.com/company/proyectosmce/',
            it: 'LinkedIn: https://www.linkedin.com/company/proyectosmce/'
        }},
        { keywords: ['facebook', 'fb'], answers: {
            es: 'Facebook: https://www.facebook.com/proyectosmce',
            en: 'Facebook: https://www.facebook.com/proyectosmce',
            fr: 'Facebook : https://www.facebook.com/proyectosmce',
            de: 'Facebook: https://www.facebook.com/proyectosmce',
            pt: 'Facebook: https://www.facebook.com/proyectosmce',
            it: 'Facebook: https://www.facebook.com/proyectosmce'
        }},
        { keywords: ['tiktok', 'tt'], answers: {
            es: 'TikTok: https://www.tiktok.com/@proyectosmce',
            en: 'TikTok: https://www.tiktok.com/@proyectosmce',
            fr: 'TikTok : https://www.tiktok.com/@proyectosmce',
            de: 'TikTok: https://www.tiktok.com/@proyectosmce',
            pt: 'TikTok: https://www.tiktok.com/@proyectosmce',
            it: 'TikTok: https://www.tiktok.com/@proyectosmce'
        }},
        { keywords: ['mce'], answers: {
            es: 'MCE es nuestra marca: Proyectos MCE. Son las iniciales de Marlon Carabalí, programador y líder del equipo.',
            en: 'MCE is our brand: Proyectos MCE. It’s the initials of Marlon Carabalí, programmer and team lead.',
            fr: 'MCE est notre marque : Proyectos MCE. Ce sont les initiales de Marlon Carabalí, développeur et lead.',
            de: 'MCE ist unsere Marke: Proyectos MCE. Es sind die Initialen von Marlon Carabalí, Entwickler und Lead.',
            pt: 'MCE é nossa marca: Proyectos MCE. São as iniciais de Marlon Carabalí, programador e líder.',
            it: 'MCE è il nostro brand: Proyectos MCE. Sono le iniziali di Marlon Carabalí, programmatore e lead.'
        }},
        { keywords: ['contacto', 'correo', 'email', 'whatsapp'], answers: {
            es: 'Escríbenos a proyectosmceaa@gmail.com o por WhatsApp al +57 311 412 5971.',
            en: 'Write to us at proyectosmceaa@gmail.com or WhatsApp +57 311 412 5971.',
            fr: 'Écrivez-nous à proyectosmceaa@gmail.com ou WhatsApp +57 311 412 5971.',
            de: 'Schreib uns an proyectosmceaa@gmail.com oder per WhatsApp +57 311 412 5971.',
            pt: 'Fale conosco em proyectosmceaa@gmail.com ou WhatsApp +57 311 412 5971.',
            it: 'Scrivici a proyectosmceaa@gmail.com o su WhatsApp +57 311 412 5971.'
        }},
    ];

    const defaultMsg = {
        es: 'Solo puedo responder sobre los contenidos y servicios de esta página: desarrollo web a medida, tiendas online, inventarios, UX/UI, APIs, soporte y sesiones Discovery.',
        en: 'I can answer only about this site’s content and services: custom web dev, online stores, inventory, UX/UI, APIs, support, and Discovery sessions.',
        fr: 'Je peux répondre uniquement sur les contenus et services de ce site : dev web sur mesure, boutiques en ligne, inventaires, UX/UI, APIs, support, et sessions Discovery.',
        de: 'Ich antworte nur zu Inhalten/Services dieser Seite: individuelle Webentwicklung, Shops, Inventar, UX/UI, APIs, Support und Discovery-Sessions.',
        pt: 'Posso responder apenas sobre o conteúdo e serviços deste site: desenvolvimento web sob medida, lojas online, inventários, UX/UI, APIs, suporte e sessões Discovery.',
        it: 'Posso rispondere solo sui contenuti e servizi di questo sito: sviluppo web su misura, e-commerce, inventari, UX/UI, API, supporto e sessioni Discovery.'
    };

    const detectLang = (q) => {
        const lower = q;
        if (/[àâçéèêëîïôûùüÿœ]/.test(lower) || lower.match(/\bbonjour|salut\b/)) return 'fr';
        if (lower.match(/\bciao\b/)) return 'it';
        if (lower.match(/\b(ola|olá)\b/)) return 'pt';
        if (lower.match(/\bhallo\b/)) return 'de';
        if (lower.match(/\bhello\b|\bhi\b|\bwhat\b|\bhow\b/)) return 'en';
        return 'es';
    };

    const linkify = (str) => {
        return (str || '').replace(/https?:\/\/\S+/g, (url) => `<a href="${url}" target="_blank" rel="noopener">${url}</a>`);
    };

    const isRelevant = (q) => faqs.some(f => f.keywords.some(k => q.includes(k)));
    const findAnswer = (q, lang) => {
        const match = faqs.find(f => f.keywords.some(k => q.includes(k)));
        if (!match) return defaultMsg[lang] || defaultMsg.es;
        return match.answers[lang] || match.answers.es || defaultMsg[lang] || defaultMsg.es;
    };

    function handleAsk() {
        const q = (questionInput.value || '').trim().toLowerCase();
        if (!q) return;
        const lang = detectLang(q);
        const raw = isRelevant(q) ? findAnswer(q, lang) : (defaultMsg[lang] || defaultMsg.es);
        answerBox.innerHTML = linkify(raw);
    }

    function openPanel() {
        panel.classList.add('open');
        toggle.classList.add('paused');
        toggle.style.display = 'none';
        setTimeout(() => questionInput.focus(), 50);
    }

    function closePanel() {
        panel.classList.remove('open');
        toggle.classList.remove('paused');
        toggle.style.display = 'grid';
    }

    toggle.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);
    sendBtn.addEventListener('click', handleAsk);
    questionInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleAsk();
        }
    });
})();
</script>

<?php include 'includes/footer.php'; ?>
