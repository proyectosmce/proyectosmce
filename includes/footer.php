    </main>
    
    <!-- Footer profesional -->
    <footer class="bg-gradient-to-t from-slate-950 via-slate-900 to-slate-900 text-white mt-16 mce-rounded-footer">
        <!-- CTA superior -->
        <div class="border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-sm uppercase tracking-[0.2em] text-blue-200 font-semibold">Proyectos MCE</p>
                    <p class="text-lg md:text-xl font-semibold">Transformamos tus ideas en software listo para usar.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada" class="inline-flex items-center px-4 py-3 rounded-lg bg-white text-slate-900 font-semibold shadow hover:bg-blue-50 transition mce-call-ringing">
                        <span class="call-ico-wrap mr-2 text-slate-900">
                            <i class="fas fa-phone-alt"></i>
                            <span class="call-ring call-ring--1"></span>
                            <span class="call-ring call-ring--2"></span>
                            <span class="call-ring call-ring--3"></span>
                        </span>
                        Agenda una llamada
                    </a>
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-3 rounded-lg border border-white/30 text-white font-semibold hover:bg-white/10 transition">
                        <i class="fab fa-whatsapp mr-2"></i> WhatsApp inmediato
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-12 space-y-10">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <!-- Col 1: Logo y descripción -->
                <div class="space-y-3">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold text-blue-100 uppercase tracking-[0.18em]">MCE</div>
                    <h3 class="text-xl font-bold">Proyectos MCE</h3>
                    <p class="text-gray-300 leading-relaxed">Software a medida, sitios web y automatizaciones hechas con foco en tu operación diaria.</p>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center text-blue-200 hover:text-white font-semibold">
                        <span>Ver casos en vivo</span>
                        <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
                
                <!-- Col 2: Enlaces rápidos -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="<?php echo app_url(); ?>" class="hover:text-white transition inline-flex items-center gap-2"><i class="fas fa-home text-blue-300"></i><span>Inicio</span></a></li>
                        <li><a href="<?php echo app_url('servicios.php'); ?>" class="hover:text-white transition inline-flex items-center gap-2"><i class="fas fa-layer-group text-blue-300"></i><span>Servicios</span></a></li>
                        <li><a href="<?php echo app_url('portafolio.php'); ?>" class="hover:text-white transition inline-flex items-center gap-2"><i class="fas fa-briefcase text-blue-300"></i><span>Portafolio</span></a></li>
                        <li><a href="<?php echo app_url('testimonios.php'); ?>" class="hover:text-white transition inline-flex items-center gap-2"><i class="fas fa-comments text-blue-300"></i><span>Testimonios</span></a></li>
                        <li><a href="<?php echo app_url('contacto.php'); ?>" class="hover:text-white transition inline-flex items-center gap-2"><i class="fas fa-envelope-open-text text-blue-300"></i><span>Contacto</span></a></li>
                    </ul>
                </div>
                
                <!-- Col 3: Servicios -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Servicios</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li>Desarrollo a Medida</li>
                        <li>Tiendas Online</li>
                        <li>Sistemas de Inventario</li>
                        <li>Mantenimiento Web</li>
                    </ul>
                </div>
                
                <!-- Col 4: Contacto y redes -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold">Contacto</h4>
                    <ul class="space-y-3 text-gray-300">
                        <li>
                            <a href="mailto:proyectosmceaa@gmail.com" class="inline-flex items-center hover:text-white transition">
                                <i class="fas fa-envelope mr-2"></i><span>proyectosmceaa@gmail.com</span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:+573114125971" class="inline-flex items-center hover:text-white transition">
                                <i class="fas fa-phone mr-2"></i><span>+57 311 412 59 71</span>
                            </a>
                        </li>
                        <li class="inline-flex items-center text-gray-300">
                            <i class="fas fa-clock mr-2"></i><span>Lunes a sábado · 8:00 - 18:00 (GMT-5)</span>
                        </li>
                        <li class="flex flex-wrap items-center gap-4 pt-2">
                            <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener" class="inline-flex text-gray-300 hover:text-white text-xl" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <a href="https://t.me/proyectosmce" target="_blank" rel="noopener" class="inline-flex text-gray-300 hover:text-white text-xl" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
                            <a href="https://www.instagram.com/proyectosmce/" target="_blank" rel="noopener" class="inline-flex text-gray-300 hover:text-white text-xl" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.linkedin.com/company/proyectosmce/" target="_blank" rel="noopener" class="inline-flex text-gray-300 hover:text-white text-xl" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            <a href="https://www.facebook.com/proyectosmce" target="_blank" rel="noopener" class="inline-flex text-gray-300 hover:text-white text-xl" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.tiktok.com/@proyectosmce" target="_blank" rel="noopener" class="inline-flex text-gray-300 hover:text-white text-xl" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-white/10 mt-10 pt-6 text-center text-gray-400 text-sm">
                <p>&copy; <?php echo date('Y'); ?> Proyectos MCE. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto"
       target="_blank"
       class="mce-whatsapp-float fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-colors duration-300 z-50 group">
        <span class="mce-whatsapp-float__icon">
            <i class="fab fa-whatsapp text-3xl"></i>
        </span>
        <span class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
            ¡Chatea con nosotros!
        </span>
    </a>

    <!-- Botón flotante extra para llamada (desktop) -->
    <a href="<?php echo app_url('contacto.php'); ?>#agenda-llamada"
       class="mce-call-float mce-call-ringing">
        <span class="call-ico-wrap mr-2 text-slate-900">
            <i class="fas fa-phone-alt"></i>
            <span class="call-ring call-ring--1"></span>
            <span class="call-ring call-ring--2"></span>
            <span class="call-ring call-ring--3"></span>
        </span>
        Agenda una llamada
    </a>

    <!-- Panel de asistente global -->
    <div class="assistant-panel" id="assistant-panel">
        <div class="assistant-header">
            <div class="left">
                <img src="<?php echo app_url('asstv.webp'); ?>" alt="Asistente MCE" class="assistant-avatar">
                <span>Asistente MCE</span>
            </div>
            <button id="assistant-close" style="background:none;border:none;color:#ffd700;font-weight:800;font-size:1rem;cursor:pointer;">×</button>
        </div>
        <div class="assistant-body">
            <div class="assistant-lang">
                <select id="assistant-lang" aria-hidden="true">
                    <option value="auto" selected>Auto</option>
                    <option value="es">ES</option>
                    <option value="en">EN</option>
                    <option value="fr">FR</option>
                    <option value="de">DE</option>
                    <option value="pt">PT</option>
                    <option value="it">IT</option>
                </select>
                <button id="assistant-lang-toggle" class="lang-toggle" type="button">
                    <img id="assistant-lang-flag" src="https://flagcdn.com/w20/un.png" alt="Auto">
                    <span id="assistant-lang-label">Auto</span>
                </button>
                <div class="lang-list" id="assistant-lang-list">
                    <div class="lang-option" data-lang="auto" data-flag="un" data-label="Auto">
                        <img src="https://flagcdn.com/w20/un.png" alt="Auto"><span>Auto</span>
                    </div>
                    <div class="lang-option" data-lang="es" data-flag="es" data-label="Español">
                        <img src="https://flagcdn.com/w20/es.png" alt="Español"><span>Español</span>
                    </div>
                    <div class="lang-option" data-lang="en" data-flag="us" data-label="English">
                        <img src="https://flagcdn.com/w20/us.png" alt="English"><span>English</span>
                    </div>
                    <div class="lang-option" data-lang="fr" data-flag="fr" data-label="Français">
                        <img src="https://flagcdn.com/w20/fr.png" alt="Français"><span>Français</span>
                    </div>
                    <div class="lang-option" data-lang="de" data-flag="de" data-label="Deutsch">
                        <img src="https://flagcdn.com/w20/de.png" alt="Deutsch"><span>Deutsch</span>
                    </div>
                    <div class="lang-option" data-lang="pt" data-flag="br" data-label="Português">
                        <img src="https://flagcdn.com/w20/br.png" alt="Português"><span>Português</span>
                    </div>
                    <div class="lang-option" data-lang="it" data-flag="it" data-label="Italiano">
                        <img src="https://flagcdn.com/w20/it.png" alt="Italiano"><span>Italiano</span>
                    </div>
                </div>
            </div>
            <div class="assistant-answer" id="assistant-answer"></div>
            <div class="assistant-input">
                <input id="assistant-question" type="text" placeholder="Escribe tu pregunta..." />
                <button id="assistant-send">Enviar</button>
            </div>
        </div>
    </div>

    <!-- Botón flotante del asistente -->
    <div class="floating-buttons" id="floating-buttons">
        <button class="float-btn assistant" id="assistant-toggle" aria-label="Asistente virtual">
            <img src="<?php echo app_url('asstv.webp'); ?>" alt="Abrir asistente MCE" class="bot-img">
            <span style="position:absolute;opacity:0;">Asistente</span>
        </button>
    </div>
    
    <!-- Script para menú móvil -->
    <script>
        document.getElementById('menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    
    <!-- Tu script personalizado -->
    <?php
        $scriptFile = dirname(__DIR__) . '/assets/js/script.js';
        $scriptVersion = is_file($scriptFile) ? filemtime($scriptFile) : time();
        $scriptUrl = app_url('assets/js/script.js') . '?v=' . $scriptVersion;
    ?>
    <script src="<?php echo $scriptUrl; ?>"></script>
    <!-- Lógica asistente global -->
    <script>
    (() => {
        const panel = document.getElementById("assistant-panel");
        const toggle = document.getElementById("assistant-toggle");
        const closeBtn = document.getElementById("assistant-close");
        const sendBtn = document.getElementById("assistant-send");
        const questionInput = document.getElementById("assistant-question");
        const answerBox = document.getElementById("assistant-answer");
        const langSelect = document.getElementById("assistant-lang");
        const langToggle = document.getElementById("assistant-lang-toggle");
        const langFlag = document.getElementById("assistant-lang-flag");
        const langLabel = document.getElementById("assistant-lang-label");
        const langList = document.getElementById("assistant-lang-list");

        if (!panel || !toggle || !answerBox || !questionInput) return;

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
            es: 'Proyectos MCE es nuestra marca. MCE Son las iniciales de Marlon Carabalí, programador y líder del equipo.',
            en: 'Proyectos MCE is our brand. MCE It’s the initials of Marlon Carabalí, programmer and team lead.',
            fr: 'Proyectos MCE est notre marque.  MCE Ce sont les initiales de Marlon Carabalí, développeur et lead.',
            de: 'Proyectos MCE ist unsere Marke. MCE Es sind die Initialen von Marlon Carabalí, Entwickler und Lead.',
            pt: 'Proyectos MCE é nossa marca. MCE São as iniciais de Marlon Carabalí, programador e líder.',
            it: 'Proyectos MCE è il nostro brand. MCE Sono le iniziali di Marlon Carabalí, programmatore e lead.'
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
            es: "Solo puedo responder sobre los contenidos y servicios de esta página: desarrollo web a medida, tiendas online, inventarios, UX/UI, APIs, soporte y sesiones Discovery.",
            en: "I can answer only about this site’s content and services: custom web dev, online stores, inventory, UX/UI, APIs, support, and Discovery sessions.",
            fr: "Je peux répondre uniquement sur les contenus et services de ce site : dev web sur mesure, boutiques en ligne, inventaires, UX/UI, APIs, support, et sessions Discovery.",
            de: "Ich antworte nur zu Inhalten/Services dieser Seite: individuelle Webentwicklung, Shops, Inventar, UX/UI, APIs, Support und Discovery-Sessions.",
            pt: "Posso responder apenas sobre o conteúdo e serviços deste site: desenvolvimento web sob medida, lojas online, inventários, UX/UI, APIs, suporte e sessões Discovery.",
            it: "Posso rispondere solo sui contenuti e servizi di questo sito: sviluppo web su misura, e-commerce, inventari, UX/UI, API, supporto e sessioni Discovery."
        };

        const greeting = {
            es: "Hola, ¿cómo estás? ¿En qué puedo ayudarte hoy?",
            en: "Hi! How are you? How can I help you today?",
            fr: "Salut ! Comment ça va ? Comment puis-je t’aider aujourd’hui ?",
            de: "Hallo! Wie geht’s? Wobei kann ich dir heute helfen?",
            pt: "Oi! Tudo bem? Como posso ajudar você hoje?",
            it: "Ciao! Come stai? Come posso aiutarti oggi?"
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

        const linkify = (str) => (str || '').replace(/https?:\/\/\S+/g, (url) => `<a href="${url}" target="_blank" rel="noopener">${url}</a>`);

        const isRelevant = (q) => faqs.some(f => f.keywords.some(k => q.includes(k)));
        const findAnswer = (q, lang) => {
            const match = faqs.find(f => f.keywords.some(k => q.includes(k)));
            if (!match) return defaultMsg[lang] || defaultMsg.es;
            return match.answers[lang] || match.answers.es || defaultMsg[lang] || defaultMsg.es;
        };

        const setLangUI = (lang, flag, label) => {
            if (langSelect) langSelect.value = lang;
            if (langFlag) { langFlag.src = `https://flagcdn.com/w20/${flag}.png`; langFlag.alt = label; }
            if (langLabel) langLabel.textContent = label;
        };
        setLangUI('auto', 'un', 'Auto');

        const lockScroll = () => { document.body.dataset.scrollLock = '1'; document.body.style.overflow = 'hidden'; };
        const unlockScroll = () => { delete document.body.dataset.scrollLock; document.body.style.overflow = ''; };

        function handleAsk() {
            const q = (questionInput.value || '').trim().toLowerCase();
            if (!q) return;
            const choice = langSelect ? langSelect.value : 'auto';
            const lang = choice === 'auto' ? detectLang(q) : choice;
            const raw = isRelevant(q) ? findAnswer(q, lang) : (defaultMsg[lang] || defaultMsg.es);
            answerBox.innerHTML = linkify(raw);
        }

        function openPanel() {
            panel.classList.add('open');
            toggle.classList.add('paused');
            toggle.style.display = 'none';
            setTimeout(() => questionInput.focus(), 50);
            lockScroll();
        }

        function closePanel() {
            panel.classList.remove('open');
            toggle.classList.remove('paused');
            toggle.style.display = 'grid';
            langList?.classList.remove('open');
            unlockScroll();
        }

        toggle.addEventListener('click', openPanel);
        closeBtn.addEventListener('click', closePanel);
        sendBtn.addEventListener('click', handleAsk);
        questionInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); handleAsk(); }
        });
        langList?.querySelectorAll('.lang-option').forEach((opt) => {
            opt.addEventListener('click', () => {
                const lang = opt.dataset.lang || 'auto';
                const flag = opt.dataset.flag || 'un';
                const label = opt.dataset.label || lang.toUpperCase();
                setLangUI(lang, flag, label);
                langList.classList.remove('open');
                const gLang = lang === 'auto' ? 'es' : lang;
                answerBox.innerHTML = linkify(greeting[gLang] || greeting.es);
            });
        });
        langToggle?.addEventListener('click', () => { langList?.classList.toggle('open'); lockScroll(); });
        document.addEventListener('click', (e) => {
            const clickInsidePanel = panel.contains(e.target);
            const clickToggle = toggle.contains(e.target);
            const clickLangToggle = langToggle?.contains(e.target);

            if (langList && langToggle && !langList.contains(e.target) && !clickLangToggle) {
                langList.classList.remove('open');
                if (!panel.classList.contains('open')) unlockScroll();
            }
            if (panel.classList.contains('open') && !clickInsidePanel && !clickToggle) {
                closePanel();
            }
        });
    })();
    </script>

    <!-- Traducción del sitio -->
    <script>
    (() => {
        const t = {
            'es': {
                'nav-home': 'Inicio',
                'nav-services': 'Servicios',
                'nav-portfolio': 'Portafolio',
                'nav-testimonials': 'Testimonios',
                'nav-contact': 'Contacto',
                'nav-subtitle': 'Desarrollo   Web',
                'hero-title': 'Software a medida para que tu operación no se detenga',
                'hero-sub': 'Planificamos, diseñamos y desarrollamos plataformas web que soportan ventas, inventarios y atención al cliente con control total y visibilidad.',
                'chip1': 'Entendemos tu proceso primero',
                'chip2': 'Diseño gráfico (UX/UI) con maquetas claras',
                'chip3': 'Entregas cortas con pruebas y control',
                'chip4': 'Conexiones a servidores (APIs) y datos en orden',
                'btn-plan': 'Armar mi plan',
                'btn-portfolio': 'Ver casos en vivo',
                'btn-cases': 'Casos de éxito',
                'link-services': 'Servicios',
                'card1-label': 'Gobernanza',
                'card1-text': 'Backlog claro, prioridades y criterios de entrega',
                'card2-label': 'Entrega iterativa',
                'card2-text': 'Demos quincenales, pruebas automáticas y regresiones',
                'card3-label': 'Operabilidad',
                'card3-text': 'Monitoreo, alertas y soporte continuo',
                'card4-label': 'Hoja de ruta MCE',
                'card4-title': 'Tu proyecto, con control total',
                'card4-step1-title': 'Arranque y plan',
                'card4-step1-text': 'Conversamos de tus procesos y definimos lo más importante primero.',
                'card4-step2-title': 'Diseño claro',
                'card4-step2-text': 'Pantallas fáciles de usar para tu equipo y tus clientes.',
                'card4-step3-title': 'Construcción por etapas',
                'card4-step3-text': 'Avanzamos en ciclos cortos con avances y pruebas visibles.',
                'pres-label': 'Presentación',
                'pres-title': 'Proyectos MCE',
                'pres-desc': 'Equipo con experiencia que entiende tu negocio, diseña la experiencia y entrega software confiable listo para producción.',
                'pres-b1': 'Mapeamos objetivos, usuarios y métricas para no programar a ciegas.',
                'pres-b2': 'Diseño gráfico (UX/UI) con flujos claros y pantallas que cualquier persona entiende.',
                'pres-b3': 'Conexiones a servidores (APIs) y bases de datos para que todo hable entre sí.',
                'pres-b4': 'Soporte, monitoreo y mejoras continuas después de lanzar.',
                'tl1-title': 'Kickoff y plan',
                'tl1-text': 'Revisamos tus objetivos y qué debe hacer la solución.',
                'tl2-title': 'Diseño y prototipos',
                'tl2-text': 'Te mostramos maquetas para confirmar que el flujo es correcto.',
                'tl3-title': 'Desarrollo incremental',
                'tl3-text': 'Entregamos en ciclos cortos con pruebas y notas claras.',
                'tl4-title': 'Lanzamiento y soporte',
                'tl4-text': 'Publicamos, monitoreamos y mejoramos de forma continua.',
                'availability-label': 'Disponibilidad',
                'availability-text': 'Equipo listo para comenzar en menos de 7 días',
                'lang-title': 'Idioma del sitio',
                'lang-desc': 'Elige el idioma para navegar esta página',
                'srv-hero-title': 'Software a medida para operaciones, ventas y servicio',
                'srv-hero-sub': 'Te acompañamos de punta a punta: entendemos el proceso, diseñamos la experiencia, desarrollamos, probamos, desplegamos y dejamos todo documentado.',
                'srv-b1': 'Sistemas de gestión alineados a procesos y métricas del negocio.',
                'srv-b2': 'Sitios, tiendas y landing pages pensadas para vender y posicionar tu marca.',
                'srv-b3': 'Automatización de procesos (bots) e integraciones con servidores (APIs) para reducir tareas manuales.',
                'srv-b4': 'Software personalizado con monitoreo, seguridad y control de accesos por rol.',
                'srv-btn-call': 'Agenda una reunión',
                'srv-btn-portfolio': 'Ver proyectos',
                'srv-btn-cases': 'Casos de éxito',
                'pf-hero-title': 'Productos digitales y sistemas en producción',
                'pf-hero-sub': 'Proyectos reales con usuarios y datos en vivo: paneles internos, portales, automatizaciones y sitios de marca conectados a pasarelas y APIs.',
                'pf-chip1': 'Retail · Logística · Servicios',
                'pf-chip2': 'APIs, colas y webhooks',
                'pf-chip3': 'Seguridad y pruebas completas',
                'pf-btn-demo': 'Solicitar demo',
                'pf-btn-see': 'Ver proyectos',
                'pf-btn-cases': 'Casos de éxito',
                'pf-what': 'Qué verás',
                'pf-what-title': 'Casos listos para inspirar tu proyecto',
                'pf-b1-title': 'Aplicaciones internas',
                'pf-b1-text': 'Paneles internos con roles y flujos simples.',
                'pf-b2-title': 'Sitios y tiendas online',
                'pf-b2-text': 'Catálogos, carrito de compra, pagos y medición de ventas.',
                'pf-b3-title': 'Automatizaciones',
                'pf-b3-text': 'Integraciones que conectan sistemas y reducen tareas manuales.',
                'pf-cta-small': 'Entrega con visibilidad',
                'pf-cta-big': 'Demos frecuentes y documentación',
                'ts-badge': 'Testimonios · Proyectos MCE',
                'ts-hero-title': 'Lo que dicen quienes operan con nuestro software',
                'ts-hero-sub': 'Comentarios de equipos que hoy usan las soluciones en producción: releases frecuentes, soporte real y métricas visibles.',
                'ts-b1': 'Acompañamiento end-to-end en cada release.',
                'ts-b2': 'Equipos de negocio y tecnología siempre informados.',
                'ts-b3': 'Feedback directo que usamos para mejorar el producto.',
                'ts-btn-read': 'Leer testimonios',
                'ts-btn-share': 'Compartir mi experiencia'
            },
            'en': {
                'nav-home': 'Home',
                'nav-services': 'Services',
                'nav-portfolio': 'Portfolio',
                'nav-testimonials': 'Testimonials',
                'nav-contact': 'Contact',
                'nav-subtitle': 'Web Development',
                'hero-title': 'Custom software so your operation never stops',
                'hero-sub': 'We plan, design and build web platforms that support sales, inventory and customer care with full control and visibility.',
                'chip1': 'We understand your process first',
                'chip2': 'UX/UI design with clear mockups',
                'chip3': 'Short iterations with tests and control',
                'chip4': 'Server (API) connections and data in order',
                'btn-plan': 'Build my plan',
                'btn-portfolio': 'See live cases',
                'btn-cases': 'Success stories',
                'link-services': 'Services',
                'card1-label': 'Governance',
                'card1-text': 'Clear backlog, priorities and acceptance criteria',
                'card2-label': 'Iterative delivery',
                'card2-text': 'Bi-weekly demos, automated tests and regressions',
                'card3-label': 'Operability',
                'card3-text': 'Monitoring, alerts and continuous support',
                'card4-label': 'MCE roadmap',
                'card4-title': 'Your project, full control',
                'card4-step1-title': 'Kickoff & plan',
                'card4-step1-text': 'We review your processes and define what matters most first.',
                'card4-step2-title': 'Clear design',
                'card4-step2-text': 'Screens your team and customers can use easily.',
                'card4-step3-title': 'Build in stages',
                'card4-step3-text': 'Short cycles with visible advances and tests.',
                'pres-label': 'Overview',
                'pres-title': 'Proyectos MCE',
                'pres-desc': 'A seasoned team that understands your business, designs the experience and ships reliable software to production.',
                'pres-b1': 'We map goals, users and metrics so we don’t code blindly.',
                'pres-b2': 'UX/UI design with clear flows and screens anyone can follow.',
                'pres-b3': 'API and database connections so everything talks to each other.',
                'pres-b4': 'Support, monitoring and continuous improvements after launch.',
                'tl1-title': 'Kickoff & plan',
                'tl1-text': 'We review your goals and what the solution must do.',
                'tl2-title': 'Design & prototypes',
                'tl2-text': 'We show mockups to confirm the flow is right.',
                'tl3-title': 'Incremental delivery',
                'tl3-text': 'Short cycles with tests and clear notes.',
                'tl4-title': 'Launch & support',
                'tl4-text': 'We deploy, monitor and improve continuously.',
                'availability-label': 'Availability',
                'availability-text': 'Team ready to start in under 7 days',
                'lang-title': 'Site language',
                'lang-desc': 'Pick the language to browse this page',
                'srv-hero-title': 'Custom software for operations, sales and service',
                'srv-hero-sub': 'We handle end to end: understand the process, design the experience, build, test, deploy and document everything.',
                'srv-b1': 'Management systems aligned to your processes and business metrics.',
                'srv-b2': 'Websites, shops and landing pages designed to sell and boost your brand.',
                'srv-b3': 'Process automation (bots) and API integrations to cut manual work.',
                'srv-b4': 'Custom software with monitoring, security and role-based access.',
                'srv-btn-call': 'Book a meeting',
                'srv-btn-portfolio': 'See projects',
                'srv-btn-cases': 'Case studies',
                'pf-hero-title': 'Digital products and systems in production',
                'pf-hero-sub': 'Real projects with live users and data: internal dashboards, portals, automations, and branded sites linked to gateways and APIs.',
                'pf-chip1': 'Retail · Logistics · Services',
                'pf-chip2': 'APIs, queues and webhooks',
                'pf-chip3': 'Security and full testing',
                'pf-btn-demo': 'Request demo',
                'pf-btn-see': 'View projects',
                'pf-btn-cases': 'Success stories',
                'pf-what': 'What you’ll see',
                'pf-what-title': 'Cases ready to inspire your project',
                'pf-b1-title': 'Internal applications',
                'pf-b1-text': 'Internal dashboards with roles and simple flows.',
                'pf-b2-title': 'Websites and online stores',
                'pf-b2-text': 'Catalogs, cart, payments and sales tracking.',
                'pf-b3-title': 'Automations',
                'pf-b3-text': 'Integrations that connect systems and cut manual work.',
                'pf-cta-small': 'Delivery with visibility',
                'pf-cta-big': 'Frequent demos and documentation',
                'ts-badge': 'Testimonials · MCE Projects',
                'ts-hero-title': 'What users say about our software',
                'ts-hero-sub': 'Feedback from teams running the solutions in production: frequent releases, real support and visible metrics.',
                'ts-b1': 'End-to-end support on every release.',
                'ts-b2': 'Business and tech teams always informed.',
                'ts-b3': 'Direct feedback we use to improve the product.',
                'ts-btn-read': 'Read testimonials',
                'ts-btn-share': 'Share my experience'
            },
            'fr': {
                'nav-home': 'Accueil',
                'nav-services': 'Services',
                'nav-portfolio': 'Portfolio',
                'nav-testimonials': 'Témoignages',
                'nav-contact': 'Contact',
                'nav-subtitle': 'Développement Web',
                'hero-title': 'Logiciel sur mesure pour que votre opération ne s’arrête pas',
                'hero-sub': 'Nous planifions, concevons et développons des plateformes web pour ventes, inventaires et service client avec contrôle total et visibilité.',
                'chip1': 'Nous comprenons d’abord vos processus',
                'chip2': 'Design UX/UI avec maquettes claires',
                'chip3': 'Itérations courtes avec tests et contrôle',
                'chip4': 'Connexions serveurs (APIs) et données en ordre',
                'btn-plan': 'Créer mon plan',
                'btn-portfolio': 'Voir des cas en ligne',
                'btn-cases': 'Succès clients',
                'link-services': 'Services',
                'card1-label': 'Gouvernance',
                'card1-text': 'Backlog clair, priorités et critères d’acceptation',
                'card2-label': 'Livraison itérative',
                'card2-text': 'Démos bi-hebdo, tests automatiques et régressions',
                'card3-label': 'Opérabilité',
                'card3-text': 'Monitoring, alertes et support continu',
                'card4-label': 'Feuille de route MCE',
                'card4-title': 'Votre projet, avec contrôle total',
                'card4-step1-title': 'Kickoff & plan',
                'card4-step1-text': 'On revoit vos processus et on priorise l’essentiel.',
                'card4-step2-title': 'Design clair',
                'card4-step2-text': 'Écrans faciles pour votre équipe et vos clients.',
                'card4-step3-title': 'Construction par étapes',
                'card4-step3-text': 'Cycles courts avec avancées et tests visibles.',
                'pres-label': 'Présentation',
                'pres-title': 'Projets MCE',
                'pres-desc': 'Équipe expérimentée qui comprend votre métier, conçoit l’expérience et livre du logiciel fiable prêt pour la prod.',
                'pres-b1': 'Nous cartographions objectifs, utilisateurs et métriques pour ne pas coder à l’aveugle.',
                'pres-b2': 'Design UX/UI avec parcours clairs et écrans compréhensibles.',
                'pres-b3': 'Connexions API et bases de données pour que tout communique.',
                'pres-b4': 'Support, monitoring et améliorations continues après lancement.',
                'tl1-title': 'Kickoff & plan',
                'tl1-text': 'Nous revoyons vos objectifs et ce que doit faire la solution.',
                'tl2-title': 'Design & prototypes',
                'tl2-text': 'Nous montrons des maquettes pour confirmer le flux.',
                'tl3-title': 'Développement incrémental',
                'tl3-text': 'Cycles courts avec tests et notes claires.',
                'tl4-title': 'Lancement & support',
                'tl4-text': 'Nous publions, monitorons et améliorons en continu.',
                'availability-label': 'Disponibilité',
                'availability-text': 'Équipe prête à démarrer en moins de 7 jours',
                'lang-title': 'Langue du site',
                'lang-desc': 'Choisissez la langue pour naviguer cette page',
                'srv-hero-title': 'Logiciel sur mesure pour opérations, ventes et service',
                'srv-hero-sub': 'On s’occupe de bout en bout : comprendre le process, concevoir l’expérience, développer, tester, déployer et documenter.',
                'srv-b1': 'Systèmes de gestion alignés sur vos processus et métriques métier.',
                'srv-b2': 'Sites, boutiques et landing pages conçus pour vendre et valoriser votre marque.',
                'srv-b3': 'Automatisation (bots) et intégrations API pour réduire le travail manuel.',
                'srv-b4': 'Logiciel personnalisé avec monitoring, sécurité et accès par rôles.',
                'srv-btn-call': 'Planifier un rendez-vous',
                'srv-btn-portfolio': 'Voir les projets',
                'srv-btn-cases': 'Études de cas',
                'pf-hero-title': 'Produits digitaux et systèmes en production',
                'pf-hero-sub': 'Projets réels avec utilisateurs et données en direct : tableaux internes, portails, automatisations et sites de marque connectés à passerelles et APIs.',
                'pf-chip1': 'Retail · Logistique · Services',
                'pf-chip2': 'APIs, queues et webhooks',
                'pf-chip3': 'Sécurité et tests complets',
                'pf-btn-demo': 'Demander une démo',
                'pf-btn-see': 'Voir les projets',
                'pf-btn-cases': 'Succès clients',
                'pf-what': 'Ce que vous verrez',
                'pf-what-title': 'Cas prêts à inspirer votre projet',
                'pf-b1-title': 'Applications internes',
                'pf-b1-text': 'Tableaux internes avec rôles et flux simples.',
                'pf-b2-title': 'Sites et boutiques en ligne',
                'pf-b2-text': 'Catalogues, panier, paiements et suivi des ventes.',
                'pf-b3-title': 'Automatisations',
                'pf-b3-text': 'Intégrations qui connectent les systèmes et réduisent le manuel.',
                'pf-cta-small': 'Livraison avec visibilité',
                'pf-cta-big': 'Démos fréquentes et documentation',
                'ts-badge': 'Témoignages · Projets MCE',
                'ts-hero-title': 'Ce que disent ceux qui utilisent notre logiciel',
                'ts-hero-sub': 'Retours d’équipes en production : releases fréquentes, support réel et métriques visibles.',
                'ts-b1': 'Accompagnement bout en bout à chaque release.',
                'ts-b2': 'Équipes métiers et tech toujours informées.',
                'ts-b3': 'Feedback direct que nous utilisons pour améliorer le produit.',
                'ts-btn-read': 'Lire les témoignages',
                'ts-btn-share': 'Partager mon expérience'
            },
            'de': {
                'nav-home': 'Start',
                'nav-services': 'Leistungen',
                'nav-portfolio': 'Portfolio',
                'nav-testimonials': 'Referenzen',
                'nav-contact': 'Kontakt',
                'nav-subtitle': 'Webentwicklung',
                'hero-title': 'Individuelle Software, damit dein Betrieb nicht stoppt',
                'hero-sub': 'Wir planen, designen und bauen Webplattformen für Vertrieb, Lager und Kundenservice mit vollem Überblick.',
                'chip1': 'Wir verstehen zuerst deinen Prozess',
                'chip2': 'UX/UI-Design mit klaren Mockups',
                'chip3': 'Kurze Iterationen mit Tests und Kontrolle',
                'chip4': 'Server- (API) Anbindungen und saubere Daten',
                'btn-plan': 'Meinen Plan starten',
                'btn-portfolio': 'Live-Cases ansehen',
                'btn-cases': 'Erfolgsgeschichten',
                'link-services': 'Leistungen',
                'card1-label': 'Governance',
                'card1-text': 'Klares Backlog, Prioritäten und Abnahmekriterien',
                'card2-label': 'Iterative Lieferung',
                'card2-text': 'Zweiwöchentliche Demos, automatisierte Tests und Regressionen',
                'card3-label': 'Betriebsfähigkeit',
                'card3-text': 'Monitoring, Alerts und kontinuierlicher Support',
                'card4-label': 'MCE Roadmap',
                'card4-title': 'Dein Projekt, volle Kontrolle',
                'card4-step1-title': 'Kickoff & Plan',
                'card4-step1-text': 'Wir prüfen Prozesse und priorisieren das Wichtigste zuerst.',
                'card4-step2-title': 'Klares Design',
                'card4-step2-text': 'Einfache Screens für Team und Kunden.',
                'card4-step3-title': 'Schrittweiser Aufbau',
                'card4-step3-text': 'Kurze Zyklen mit sichtbaren Fortschritten und Tests.',
                'pres-label': 'Vorstellung',
                'pres-title': 'Proyectos MCE',
                'pres-desc': 'Ein erfahrenes Team versteht dein Business, designt die Experience und liefert verlässliche Software in Produktion.',
                'pres-b1': 'Wir mappen Ziele, Nutzer und Metriken, damit wir nicht blind entwickeln.',
                'pres-b2': 'UX/UI mit klaren Flows und verständlichen Screens.',
                'pres-b3': 'API- und Datenbank-Anbindungen, damit alles spricht.',
                'pres-b4': 'Support, Monitoring und kontinuierliche Verbesserungen nach dem Launch.',
                'tl1-title': 'Kickoff & Plan',
                'tl1-text': 'Wir prüfen deine Ziele und was die Lösung leisten muss.',
                'tl2-title': 'Design & Prototypen',
                'tl2-text': 'Wir zeigen Mockups, um den Flow zu bestätigen.',
                'tl3-title': 'Inkrementelle Entwicklung',
                'tl3-text': 'Kurze Zyklen mit Tests und klaren Notizen.',
                'tl4-title': 'Launch & Support',
                'tl4-text': 'Wir deployen, monitoren und verbessern kontinuierlich.',
                'availability-label': 'Verfügbarkeit',
                'availability-text': 'Team startklar in unter 7 Tagen',
                'lang-title': 'Seitensprache',
                'lang-desc': 'Wähle die Sprache für diese Seite',
                'srv-hero-title': 'Individuelle Software für Betrieb, Vertrieb und Service',
                'srv-hero-sub': 'Wir kümmern uns end-to-end: Prozess verstehen, Experience designen, entwickeln, testen, deployen und dokumentieren.',
                'srv-b1': 'Management-Systeme abgestimmt auf deine Prozesse und Business-KPIs.',
                'srv-b2': 'Websites, Shops und Landing Pages, die verkaufen und deine Marke stärken.',
                'srv-b3': 'Prozessautomatisierung (Bots) und API-Integrationen, um manuelle Arbeit zu senken.',
                'srv-b4': 'Individuelle Software mit Monitoring, Sicherheit und Rollen-Rechten.',
                'srv-btn-call': 'Termin vereinbaren',
                'srv-btn-portfolio': 'Projekte ansehen',
                'srv-btn-cases': 'Erfolgscases',
                'pf-hero-title': 'Digitale Produkte und Systeme in Produktion',
                'pf-hero-sub': 'Echte Projekte mit Live-Usern und Daten: interne Dashboards, Portale, Automatisierungen und Marken-Sites mit Payment/API-Anbindung.',
                'pf-chip1': 'Retail · Logistik · Services',
                'pf-chip2': 'APIs, Queues und Webhooks',
                'pf-chip3': 'Sicherheit und umfassende Tests',
                'pf-btn-demo': 'Demo anfragen',
                'pf-btn-see': 'Projekte ansehen',
                'pf-btn-cases': 'Erfolgsgeschichten',
                'pf-what': 'Was du sehen wirst',
                'pf-what-title': 'Cases, die dein Projekt inspirieren',
                'pf-b1-title': 'Interne Anwendungen',
                'pf-b1-text': 'Interne Dashboards mit Rollen und einfachen Flows.',
                'pf-b2-title': 'Websites & Online-Shops',
                'pf-b2-text': 'Kataloge, Warenkorb, Payments und Sales-Tracking.',
                'pf-b3-title': 'Automatisierungen',
                'pf-b3-text': 'Integrationen, die Systeme verbinden und Handarbeit senken.',
                'pf-cta-small': 'Lieferung mit Transparenz',
                'pf-cta-big': 'Häufige Demos und Dokumentation',
                'ts-badge': 'Referenzen · Proyectos MCE',
                'ts-hero-title': 'Was Nutzer über unsere Software sagen',
                'ts-hero-sub': 'Feedback von Teams in Produktion: häufige Releases, echter Support und sichtbare Metriken.',
                'ts-b1': 'End-to-end Begleitung bei jedem Release.',
                'ts-b2': 'Business- und Tech-Teams stets informiert.',
                'ts-b3': 'Direktes Feedback, das wir zur Verbesserung nutzen.',
                'ts-btn-read': 'Referenzen lesen',
                'ts-btn-share': 'Meine Erfahrung teilen'
            },
            'pt': {
                'nav-home': 'Início',
                'nav-services': 'Serviços',
                'nav-portfolio': 'Portfólio',
                'nav-testimonials': 'Depoimentos',
                'nav-contact': 'Contato',
                'nav-subtitle': 'Desenvolvimento Web',
                'hero-title': 'Software sob medida para sua operação não parar',
                'hero-sub': 'Planejamos, desenhamos e desenvolvemos plataformas web que suportam vendas, estoques e atendimento com controle total e visibilidade.',
                'chip1': 'Entendemos seu processo primeiro',
                'chip2': 'Design UX/UI com maquetes claras',
                'chip3': 'Entregas curtas com testes e controle',
                'chip4': 'Conexões com servidores (APIs) e dados em ordem',
                'btn-plan': 'Montar meu plano',
                'btn-portfolio': 'Ver casos ao vivo',
                'btn-cases': 'Casos de sucesso',
                'link-services': 'Serviços',
                'card1-label': 'Governança',
                'card1-text': 'Backlog claro, prioridades e critérios de entrega',
                'card2-label': 'Entrega iterativa',
                'card2-text': 'Demos quinzenais, testes automáticos e regressões',
                'card3-label': 'Operabilidade',
                'card3-text': 'Monitoramento, alertas e suporte contínuo',
                'card4-label': 'Roteiro MCE',
                'card4-title': 'Seu projeto, com controle total',
                'card4-step1-title': 'Kickoff e plano',
                'card4-step1-text': 'Revisamos processos e priorizamos o mais importante primeiro.',
                'card4-step2-title': 'Design claro',
                'card4-step2-text': 'Telass fáceis para seu time e clientes.',
                'card4-step3-title': 'Construção por etapas',
                'card4-step3-text': 'Ciclos curtos com avanços e testes visíveis.',
                'pres-label': 'Apresentação',
                'pres-title': 'Proyectos MCE',
                'pres-desc': 'Equipe experiente que entende seu negócio, desenha a experiência e entrega software confiável pronto para produção.',
                'pres-b1': 'Mapeamos objetivos, usuários e métricas para não programar no escuro.',
                'pres-b2': 'UX/UI com fluxos claros e telas fáceis de entender.',
                'pres-b3': 'Conexões API e bases de dados para tudo conversar.',
                'pres-b4': 'Suporte, monitoramento e melhorias contínuas após o lançamento.',
                'tl1-title': 'Kickoff e plano',
                'tl1-text': 'Revisamos seus objetivos e o que a solução deve fazer.',
                'tl2-title': 'Design e protótipos',
                'tl2-text': 'Mostramos maquetes para confirmar o fluxo.',
                'tl3-title': 'Desenvolvimento incremental',
                'tl3-text': 'Ciclos curtos com testes e notas claras.',
                'tl4-title': 'Lançamento e suporte',
                'tl4-text': 'Publicamos, monitoramos e melhoramos continuamente.',
                'availability-label': 'Disponibilidade',
                'availability-text': 'Equipe pronta para começar em menos de 7 dias',
                'lang-title': 'Idioma do site',
                'lang-desc': 'Escolha o idioma para navegar na página',
                'srv-hero-title': 'Software sob medida para operações, vendas e atendimento',
                'srv-hero-sub': 'Cuidamos ponta a ponta: entendemos o processo, desenhamos a experiência, desenvolvemos, testamos, publicamos e documentamos tudo.',
                'srv-b1': 'Sistemas de gestão alinhados a processos e métricas do negócio.',
                'srv-b2': 'Sites, lojas e landing pages pensadas para vender e fortalecer a marca.',
                'srv-b3': 'Automação de processos (bots) e integrações API para reduzir trabalho manual.',
                'srv-b4': 'Software personalizado com monitoramento, segurança e acesso por papéis.',
                'srv-btn-call': 'Agendar uma reunião',
                'srv-btn-portfolio': 'Ver projetos',
                'srv-btn-cases': 'Cases de sucesso',
                'pf-hero-title': 'Produtos digitais e sistemas em produção',
                'pf-hero-sub': 'Projetos reais com usuários e dados ao vivo: painéis internos, portais, automações e sites de marca conectados a gateways e APIs.',
                'pf-chip1': 'Varejo · Logística · Serviços',
                'pf-chip2': 'APIs, filas e webhooks',
                'pf-chip3': 'Segurança e testes completos',
                'pf-btn-demo': 'Pedir demo',
                'pf-btn-see': 'Ver projetos',
                'pf-btn-cases': 'Casos de sucesso',
                'pf-what': 'O que você verá',
                'pf-what-title': 'Cases para inspirar seu projeto',
                'pf-b1-title': 'Aplicações internas',
                'pf-b1-text': 'Painéis internos com papéis e fluxos simples.',
                'pf-b2-title': 'Sites e lojas online',
                'pf-b2-text': 'Catálogos, carrinho, pagamentos e acompanhamento de vendas.',
                'pf-b3-title': 'Automatizações',
                'pf-b3-text': 'Integrações que conectam sistemas e reduzem trabalho manual.',
                'pf-cta-small': 'Entrega com visibilidade',
                'pf-cta-big': 'Demos frequentes e documentação',
                'ts-badge': 'Depoimentos · Proyectos MCE',
                'ts-hero-title': 'O que dizem quem opera com nosso software',
                'ts-hero-sub': 'Feedback de equipes em produção: releases frequentes, suporte real e métricas visíveis.',
                'ts-b1': 'Acompanhamento ponta a ponta em cada release.',
                'ts-b2': 'Times de negócio e tech sempre informados.',
                'ts-b3': 'Feedback direto que usamos para melhorar o produto.',
                'ts-btn-read': 'Ler depoimentos',
                'ts-btn-share': 'Compartilhar minha experiência'
            },
            'it': {
                'nav-home': 'Home',
                'nav-services': 'Servizi',
                'nav-portfolio': 'Portfolio',
                'nav-testimonials': 'Testimonianze',
                'nav-contact': 'Contatti',
                'nav-subtitle': 'Sviluppo Web',
                'hero-title': 'Software su misura perché la tua operazione non si fermi',
                'hero-sub': 'Pianifichiamo, progettiamo e sviluppiamo piattaforme web per vendite, inventari e customer care con controllo totale e visibilità.',
                'chip1': 'Capire prima il tuo processo',
                'chip2': 'Design UX/UI con mockup chiari',
                'chip3': 'Rilasci brevi con test e controllo',
                'chip4': 'Connessioni server (API) e dati in ordine',
                'btn-plan': 'Crea il mio piano',
                'btn-portfolio': 'Vedi casi live',
                'btn-cases': 'Casi di successo',
                'link-services': 'Servizi',
                'card1-label': 'Governance',
                'card1-text': 'Backlog chiaro, priorità e criteri di consegna',
                'card2-label': 'Consegna iterativa',
                'card2-text': 'Demo quindicinali, test automatici e regression',
                'card3-label': 'Operatività',
                'card3-text': 'Monitoraggio, alert e supporto continuo',
                'card4-label': 'Roadmap MCE',
                'card4-title': 'Il tuo progetto, controllo totale',
                'card4-step1-title': 'Kickoff e piano',
                'card4-step1-text': 'Rivediamo i processi e priorizziamo ciò che conta.',
                'card4-step2-title': 'Design chiaro',
                'card4-step2-text': 'Schermate semplici per team e clienti.',
                'card4-step3-title': 'Costruzione a fasi',
                'card4-step3-text': 'Cicli brevi con progressi e test visibili.',
                'pres-label': 'Presentazione',
                'pres-title': 'Proyectos MCE',
                'pres-desc': 'Team esperto che capisce il tuo business, progetta l’esperienza e consegna software affidabile pronto per la produzione.',
                'pres-b1': 'Mappiamo obiettivi, utenti e metriche per non programmare al buio.',
                'pres-b2': 'UX/UI con flussi chiari e schermate comprensibili.',
                'pres-b3': 'Connessioni API e database per far dialogare tutto.',
                'pres-b4': 'Supporto, monitoraggio e miglioramenti continui dopo il lancio.',
                'tl1-title': 'Kickoff e piano',
                'tl1-text': 'Rivediamo gli obiettivi e cosa deve fare la soluzione.',
                'tl2-title': 'Design e prototipi',
                'tl2-text': 'Mostriamo mockup per confermare il flusso.',
                'tl3-title': 'Sviluppo incrementale',
                'tl3-text': 'Cicli brevi con test e note chiare.',
                'tl4-title': 'Lancio e supporto',
                'tl4-text': 'Pubblicazione, monitoraggio e miglioramenti continui.',
                'availability-label': 'Disponibilità',
                'availability-text': 'Team pronto a partire in meno di 7 giorni',
                'lang-title': 'Lingua del sito',
                'lang-desc': 'Scegli la lingua per navigare questa pagina',
                'srv-hero-title': 'Software su misura per operazioni, vendite e assistenza',
                'srv-hero-sub': 'Gestiamo tutto end-to-end: capire il processo, progettare l’esperienza, sviluppare, testare, pubblicare e documentare.',
                'srv-b1': 'Sistemi gestionali allineati ai tuoi processi e KPI di business.',
                'srv-b2': 'Siti, shop e landing page pensati per vendere e rafforzare il brand.',
                'srv-b3': 'Automazione (bot) e integrazioni API per ridurre il lavoro manuale.',
                'srv-b4': 'Software personalizzato con monitoraggio, sicurezza e accessi per ruolo.',
                'srv-btn-call': 'Fissa un incontro',
                'srv-btn-portfolio': 'Vedi progetti',
                'srv-btn-cases': 'Casi di successo',
                'pf-hero-title': 'Prodotti digitali e sistemi in produzione',
                'pf-hero-sub': 'Progetti reali con utenti e dati live: dashboard interni, portali, automazioni e siti brand con gateway e API.',
                'pf-chip1': 'Retail · Logistica · Servizi',
                'pf-chip2': 'API, queue e webhook',
                'pf-chip3': 'Sicurezza e test completi',
                'pf-btn-demo': 'Richiedi demo',
                'pf-btn-see': 'Vedi progetti',
                'pf-btn-cases': 'Storie di successo',
                'pf-what': 'Cosa vedrai',
                'pf-what-title': 'Casi pronti a ispirare il tuo progetto',
                'pf-b1-title': 'Applicazioni interne',
                'pf-b1-text': 'Dashboard interni con ruoli e flussi semplici.',
                'pf-b2-title': 'Siti e shop online',
                'pf-b2-text': 'Cataloghi, carrello, pagamenti e tracking vendite.',
                'pf-b3-title': 'Automazioni',
                'pf-b3-text': 'Integrazioni che collegano sistemi e riducono il lavoro manuale.',
                'pf-cta-small': 'Consegna con visibilità',
                'pf-cta-big': 'Demo frequenti e documentazione',
                'ts-badge': 'Testimonianze · Proyectos MCE',
                'ts-hero-title': 'Cosa dicono di chi usa il nostro software',
                'ts-hero-sub': 'Feedback di team in produzione: release frequenti, supporto reale e metriche visibili.',
                'ts-b1': 'Supporto end-to-end a ogni release.',
                'ts-b2': 'Team business e tech sempre informati.',
                'ts-b3': 'Feedback diretto che usiamo per migliorare il prodotto.',
                'ts-btn-read': 'Leggi le testimonianze',
                'ts-btn-share': 'Condividi la mia esperienza'
            }
        };

        const elems = (cls, key) => document.querySelectorAll('.i18n-' + key) || [];

        // Map keys to elements
        const mapClass = {
            'hero-title': '.i18n-hero-title',
            'hero-sub': '.i18n-hero-sub',
            'chip1': '.i18n-chip1',
            'chip2': '.i18n-chip2',
            'chip3': '.i18n-chip3',
            'chip4': '.i18n-chip4',
            'btn-plan': '.i18n-btn-plan',
            'btn-portfolio': '.i18n-btn-portfolio',
            'btn-cases': '.i18n-btn-cases',
            'link-services': '.i18n-link-services',
            'card1-label': '.i18n-card1-label',
            'card1-text': '.i18n-card1-text',
            'card2-label': '.i18n-card2-label',
            'card2-text': '.i18n-card2-text',
            'card3-label': '.i18n-card3-label',
            'card3-text': '.i18n-card3-text',
            'card4-label': '.i18n-card4-label',
            'card4-title': '.i18n-card4-title',
            'card4-step1-title': '.i18n-card4-step1-title',
            'card4-step1-text': '.i18n-card4-step1-text',
            'card4-step2-title': '.i18n-card4-step2-title',
            'card4-step2-text': '.i18n-card4-step2-text',
            'card4-step3-title': '.i18n-card4-step3-title',
            'card4-step3-text': '.i18n-card4-step3-text',
            'pres-label': '.i18n-pres-label',
            'pres-title': '.i18n-pres-title',
            'pres-desc': '.i18n-pres-desc',
            'pres-b1': '.i18n-pres-b1',
            'pres-b2': '.i18n-pres-b2',
            'pres-b3': '.i18n-pres-b3',
            'pres-b4': '.i18n-pres-b4',
            'tl1-title': '.i18n-tl1-title',
            'tl1-text': '.i18n-tl1-text',
            'tl2-title': '.i18n-tl2-title',
            'tl2-text': '.i18n-tl2-text',
            'tl3-title': '.i18n-tl3-title',
            'tl3-text': '.i18n-tl3-text',
            'tl4-title': '.i18n-tl4-title',
            'tl4-text': '.i18n-tl4-text',
            'availability-label': '.i18n-availability-label',
            'availability-text': '.i18n-availability-text',
            'lang-title': '.i18n-lang-title',
            'lang-desc': '.i18n-lang-desc'
        };

        function setSiteLangUI(lang) {
            const flagMap = { es:'es', en:'us', fr:'fr', de:'de', pt:'br', it:'it' };
            const labelMap = { es:'Español', en:'English', fr:'Français', de:'Deutsch', pt:'Português', it:'Italiano' };
            const flag = flagMap[lang] || 'es';
            const label = labelMap[lang] || 'Español';
            const flagEl = document.getElementById('site-lang-flag');
            const labelEl = document.getElementById('site-lang-label');
            const flagElM = document.getElementById('site-lang-flag-mobile');
            const labelElM = document.getElementById('site-lang-label-mobile');
            if (flagEl) flagEl.src = `https://flagcdn.com/w20/${flag}.png`;
            if (labelEl) labelEl.textContent = label;
            if (flagElM) flagElM.src = `https://flagcdn.com/w20/${flag}.png`;
            if (labelElM) labelElM.textContent = label;
            const select = document.getElementById('site-lang');
            if (select) select.value = lang;
        }

        function initSiteLang() {
            const stored = localStorage.getItem('siteLang') || 'es';
            applyLang(stored);
            setSiteLangUI(stored);
        }

        document.getElementById('site-lang-toggle')?.addEventListener('click', () => {
            document.getElementById('site-lang-list')?.classList.toggle('open');
        });
        document.querySelectorAll('#site-lang-list .lang-option').forEach(opt => {
            opt.addEventListener('click', () => {
                const lang = opt.dataset.lang;
                const flag = opt.dataset.flag;
                const label = opt.dataset.label;
                document.getElementById('site-lang-list')?.classList.remove('open');
                setSiteLangUI(lang);
                applyLang(lang);
            });
        });
        document.getElementById('site-lang-toggle-mobile')?.addEventListener('click', () => {
            document.getElementById('site-lang-list-mobile')?.classList.toggle('open');
        });
        document.querySelectorAll('#site-lang-list-mobile .lang-option').forEach(opt => {
            opt.addEventListener('click', () => {
                const lang = opt.dataset.lang;
                document.getElementById('site-lang-list-mobile')?.classList.remove('open');
                setSiteLangUI(lang);
                applyLang(lang);
            });
        });
        document.addEventListener('click', (e) => {
            const list = document.getElementById('site-lang-list');
            const toggle = document.getElementById('site-lang-toggle');
            const listM = document.getElementById('site-lang-list-mobile');
            const toggleM = document.getElementById('site-lang-toggle-mobile');
            if (list && toggle && !list.contains(e.target) && !toggle.contains(e.target)) list.classList.remove('open');
            if (listM && toggleM && !listM.contains(e.target) && !toggleM.contains(e.target)) listM.classList.remove('open');
        });

        // apply classes mapping
        for (const key in mapClass) {
            document.querySelectorAll(mapClass[key]).forEach(el => el.dataset.i18nKey = key);
        }
        // apply nav data-i18n spans
        document.querySelectorAll('[data-i18n]').forEach(el => {
            if (!el.querySelector('span')) {
                const span = document.createElement('span');
                span.textContent = el.textContent.trim();
                el.innerHTML = '';
                el.appendChild(span);
            }
        });

        // Reemplazo robusto: traduce data-i18n, data-i18n-key y clases i18n-*
        function applyLang(lang) {
            const dict = t[lang] || t.es;
            // Atributo data-i18n
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.dataset.i18n;
                if (dict[key]) {
                    const span = el.querySelector('span');
                    if (span) span.textContent = dict[key];
                    else el.textContent = dict[key];
                }
            });
            // Atributo data-i18n-key (marcado desde mapClass)
            document.querySelectorAll('[data-i18n-key]').forEach(el => {
                const key = el.dataset.i18nKey;
                if (dict[key]) el.innerHTML = dict[key];
            });
            // Clases i18n-*
            document.querySelectorAll('[class*=\"i18n-\"]').forEach(el => {
                if (el.dataset.i18n) return; // si ya tiene data-i18n no sobrescribimos (para no borrar íconos)
                const key = Array.from(el.classList).find(c => c.startsWith('i18n-'))?.replace('i18n-','');
                if (key && dict[key]) el.innerHTML = dict[key];
            });
            localStorage.setItem('siteLang', lang);
        }

        initSiteLang();
    })();
    </script>
</body>
</html>


