<?php
$pageSlug = basename($_SERVER["PHP_SELF"], ".php");
$titleKey = "meta-title-" . $pageSlug;
?>
<!DOCTYPE html>
<html lang="es" data-page-key="<?php echo htmlspecialchars($titleKey, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="<?php echo htmlspecialchars($titleKey, ENT_QUOTES, 'UTF-8'); ?>">Proyectos MCE | Soluciones Digitales</title>
    <?php
    $faviconFile = dirname(__DIR__) . '/favicon.ico';
    $faviconPngFile = dirname(__DIR__) . '/favicon.png';
    $appleTouchIconFile = dirname(__DIR__) . '/apple-touch-icon.png';
    $styleFile = dirname(__DIR__) . '/assets/css/estilo.css';
    $faviconVersion = is_file($faviconFile) ? filemtime($faviconFile) : time();
    $styleVersion = is_file($styleFile) ? filemtime($styleFile) : $faviconVersion;
    $faviconIcoUrl = app_url('favicon.ico') . '?v=' . $faviconVersion;
    $faviconPngUrl = app_url('favicon.png') . '?v=' . (is_file($faviconPngFile) ? filemtime($faviconPngFile) : $faviconVersion);
    $appleTouchIconUrl = app_url('apple-touch-icon.png') . '?v=' . (is_file($appleTouchIconFile) ? filemtime($appleTouchIconFile) : $faviconVersion);
    $styleUrl = app_url('assets/css/estilo.css') . '?v=' . $styleVersion;
    ?>

    <?php include __DIR__ . '/metas.php'; ?>
    
    <!-- Tailwind CSS via CDN (rapido y profesional) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Tu CSS personalizado (pequenas modificaciones) -->
    <link rel="stylesheet" href="<?php echo $styleUrl; ?>">
    <!-- Estilos asistente flotante -->
    <style>
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
        .float-btn.assistant:hover img.bot-img,
        .float-btn.assistant.paused img.bot-img {
            animation-play-state: paused;
        }
        /* Efecto profesional para badgets de foto MCE */
        .mce-photo-badge {
            position: relative;
            overflow: hidden;
            border-radius: 9999px;
            box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.16);
            animation: mceHalo 6s ease-in-out infinite;
            animation-delay: 1s;
            isolation: isolate;
        }
        .mce-photo-badge::before {
            content: "";
            position: absolute;
            inset: -3px;
            background: conic-gradient(from 45deg, #0ea5e9 0%, #22d3ee 50%, #0ea5e9 100%);
            animation: mceSpin 18s linear infinite;
            animation-delay: 3s; /* empieza tras el primer halo */
            z-index: -1;
            mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 calc(100% - 1px));
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 calc(100% - 1px));
        }
        .mce-photo-badge::after {
            content: "";
            position: absolute;
            top: -60%;
            left: -80%;
            width: 200%;
            height: 200%;
            background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.32) 50%, transparent 100%);
            transform: rotate(15deg) translateX(-50%);
            opacity: 0;
            pointer-events: none;
            animation: mceShineAuto 12s ease-in-out infinite;
            animation-delay: 6s; /* se dispara después del halo y del giro */
        }
        .mce-photo-badge:hover::after {
            opacity: 1;
            animation: mceShine 0.65s ease forwards;
        }
        @keyframes mceSpin {
            to { transform: rotate(360deg); }
        }
        @keyframes mceHalo {
            0%,100% { box-shadow: 0 0 0 0 rgba(14,165,233,0.16), 0 8px 18px rgba(15,23,42,0.18); }
            50%     { box-shadow: 0 0 0 4px rgba(14,165,233,0.10), 0 10px 22px rgba(15,23,42,0.22); }
        }
        @keyframes mceShine {
            from { transform: rotate(15deg) translateX(-60%); opacity: 0.18; }
            to   { transform: rotate(15deg) translateX(80%);  opacity: 0; }
        }
        @keyframes mceShineAuto {
            0%,84% { opacity: 0; transform: rotate(15deg) translateX(-60%); }
            88%    { opacity: 0.14; }
            96%    { opacity: 0; transform: rotate(15deg) translateX(80%); }
            100%   { opacity: 0; transform: rotate(15deg) translateX(80%); }
        }
        @media (prefers-reduced-motion: reduce) {
            .mce-photo-badge { animation: none; }
            .mce-photo-badge::before { animation: none; }
            .mce-photo-badge:hover::after { animation: none; opacity: 0; }
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
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            background: #ffffffd9;
            color: #0f172a;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(15,23,42,0.08);
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
            background: #ffffffee;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(15,23,42,0.16);
            padding: 8px 8px 10px;
            display: none;
            z-index: 5;
            max-height: 200px;
            overflow-y: auto;
            min-width: 180px;
        }
        .lang-list::-webkit-scrollbar { width: 8px; }
        .lang-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
        .lang-list::-webkit-scrollbar-track { background: #f8fafc; border-radius: 6px; }
        .lang-list.open { display: block; }
        .lang-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: inherit;
        }
        .lang-option:hover { background: #f1f5f9; }
        .lang-option img {
            width: 18px;
            height: 14px;
            object-fit: cover;
            border-radius: 2px;
        }
        /* Ocultar select nativo del selector de idiomas del sitio (desktop) */
        #site-lang { display: none; }
    </style>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $faviconIcoUrl; ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?php echo $faviconPngUrl; ?>" sizes="64x64">
    <link rel="shortcut icon" href="<?php echo $faviconIcoUrl; ?>">
    <link rel="apple-touch-icon" href="<?php echo $appleTouchIconUrl; ?>">
</head>
<body class="bg-gray-50">
    <!-- Navbar profesional -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="<?php echo app_url(); ?>" class="flex items-center space-x-3">
                    <span class="mce-logo-frame relative inline-flex items-center justify-center w-12 h-12">
                        <span class="mce-logo-border absolute inset-0 rounded-2xl opacity-90 shadow-lg"></span>
                        <span class="absolute inset-[3px] rounded-2xl bg-white"></span>
                        <span class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl overflow-hidden ring-2 ring-white/60 shadow-sm">
                            <img src="<?php echo app_url('imag/MCE.jpg'); ?>" alt="Proyectos MCE" class="object-cover w-full h-full" data-i18n="brand-name">
                        </span>
                    </span>
                    <div class="leading-tight">
                        <span class="block text-xl font-semibold text-slate-900 tracking-tight" data-i18n="brand-name">Proyectos MCE</span>
                        <span class="block text-[11px] text-slate-500 uppercase tracking-[0.22em]" data-i18n="nav-subtitle">Desarrollo   Web</span>
                    </div>
                </a>
                
                <!-- Menú desktop -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a data-i18n="nav-home" href="<?php echo app_url(); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-home text-blue-500/80"></i><span>Inicio</span></a>
                    <a data-i18n="nav-services" href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-layer-group text-blue-500/80"></i><span>Servicios</span></a>
                    <a data-i18n="nav-portfolio" href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'portafolio.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-briefcase text-blue-500/80"></i><span>Portafolio</span></a>
                    <a data-i18n="nav-testimonials" href="<?php echo app_url('testimonios.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'testimonios.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-comments text-blue-500/80"></i><span>Testimonios</span></a>
                    <a data-i18n="nav-contact" href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-envelope-open-text text-blue-500/80"></i><span>Contacto</span></a>
                    
                    <!-- Selector de idioma en desktop, justo después de Contacto -->
                    <div class="relative">
                        <select id="site-lang" aria-hidden="true">
                            <option value="es" selected>Español</option>
                            <option value="en">English</option>
                            <option value="fr">Français</option>
                            <option value="de">Deutsch</option>
                            <option value="pt">Português</option>
                            <option value="it">Italiano</option>
                        </select>
                        <button id="site-lang-toggle" class="lang-toggle" type="button" style="background:#ffffffd9;color:#0f172a;border-color:#e2e8f0;">
                            <img id="site-lang-flag" src="https://flagcdn.com/w20/es.png" alt="Español">
                            <span id="site-lang-label">Español</span>
                        </button>
                        <div class="lang-list" id="site-lang-list">
                            <div class="lang-option" data-lang="es" data-flag="es" data-label="Español">
                                <img src="https://flagcdn.com/w20/es.png" alt="Español"><span style="color:#c1121f;">Español</span>
                            </div>
                            <div class="lang-option" data-lang="en" data-flag="us" data-label="English">
                                <img src="https://flagcdn.com/w20/us.png" alt="English"><span style="color:#0a3161;">English</span>
                            </div>
                            <div class="lang-option" data-lang="fr" data-flag="fr" data-label="Français">
                                <img src="https://flagcdn.com/w20/fr.png" alt="Français"><span style="color:#002654;">Français</span>
                            </div>
                            <div class="lang-option" data-lang="de" data-flag="de" data-label="Deutsch">
                                <img src="https://flagcdn.com/w20/de.png" alt="Deutsch"><span style="color:#000000;">Deutsch</span>
                            </div>
                            <div class="lang-option" data-lang="pt" data-flag="br" data-label="Português">
                                <img src="https://flagcdn.com/w20/pt.png" alt="Português"><span style="color:#046a38;">Português</span>
                            </div>
                            <div class="lang-option" data-lang="it" data-flag="it" data-label="Italiano">
                                <img src="https://flagcdn.com/w20/it.png" alt="Italiano"><span style="color:#009246;">Italiano</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Controles móviles: idioma + menú -->
                <div class="md:hidden flex items-center gap-3">
                    <div class="relative">
                        <button id="site-lang-toggle-mobile" class="lang-toggle" type="button" style="background:#ffffffd9;color:#0f172a;border-color:#e2e8f0;">
                            <img id="site-lang-flag-mobile" src="https://flagcdn.com/w20/es.png" alt="Español">
                            <span id="site-lang-label-mobile">Español</span>
                        </button>
                        <div class="lang-list" id="site-lang-list-mobile">
                            <div class="lang-option" data-lang="es" data-flag="es" data-label="Español">
                                <img src="https://flagcdn.com/w20/es.png" alt="Español"><span style="color:#c1121f;">Español</span>
                            </div>
                            <div class="lang-option" data-lang="en" data-flag="us" data-label="English">
                                <img src="https://flagcdn.com/w20/us.png" alt="English"><span style="color:#0a3161;">English</span>
                            </div>
                            <div class="lang-option" data-lang="fr" data-flag="fr" data-label="Français">
                                <img src="https://flagcdn.com/w20/fr.png" alt="Français"><span style="color:#002654;">Français</span>
                            </div>
                            <div class="lang-option" data-lang="de" data-flag="de" data-label="Deutsch">
                                <img src="https://flagcdn.com/w20/de.png" alt="Deutsch"><span style="color:#000000;">Deutsch</span>
                            </div>
                            <div class="lang-option" data-lang="pt" data-flag="br" data-label="Português">
                                <img src="https://flagcdn.com/w20/pt.png" alt="Português"><span style="color:#046a38;">Português</span>
                            </div>
                            <div class="lang-option" data-lang="it" data-flag="it" data-label="Italiano">
                                <img src="https://flagcdn.com/w20/it.png" alt="Italiano"><span style="color:#009246;">Italiano</span>
                            </div>
                        </div>
                    </div>
                    <button id="menu-btn" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menú móvil mejorado -->
        <div id="mobile-menu" class="hidden md:hidden fixed inset-0 z-40 pt-20" style="background-image: linear-gradient(rgba(16,23,40,0.78), rgba(16,23,40,0.85)), url('<?php echo app_url('imag/MCE.jpg'); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; background-color: #0f172a;">
            <div class="flex flex-col items-center space-y-6 p-8 text-white drop-shadow">
                <a data-i18n="nav-home" href="<?php echo app_url(); ?>" class="text-2xl font-semibold hover:text-yellow-200 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-yellow-300 font-bold' : ''; ?>"><i class="fas fa-home text-yellow-300/90"></i><span>Inicio</span></a>
                <a data-i18n="nav-services" href="<?php echo app_url('servicios.php'); ?>" class="text-2xl font-semibold hover:text-yellow-200 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'text-yellow-300 font-bold' : ''; ?>"><i class="fas fa-layer-group text-yellow-300/90"></i><span>Servicios</span></a>
                <a data-i18n="nav-portfolio" href="<?php echo app_url('portafolio.php'); ?>" class="text-2xl font-semibold hover:text-yellow-200 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'portafolio.php' ? 'text-yellow-300 font-bold' : ''; ?>"><i class="fas fa-briefcase text-yellow-300/90"></i><span>Portafolio</span></a>
                <a data-i18n="nav-testimonials" href="<?php echo app_url('testimonios.php'); ?>" class="text-2xl font-semibold hover:text-yellow-200 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'testimonios.php' ? 'text-yellow-300 font-bold' : ''; ?>"><i class="fas fa-comments text-yellow-300/90"></i><span>Testimonios</span></a>
                <a data-i18n="nav-contact" href="<?php echo app_url('contacto.php'); ?>" class="text-2xl font-semibold hover:text-yellow-200 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'text-yellow-300 font-bold' : ''; ?>"><i class="fas fa-envelope-open-text text-yellow-300/90"></i><span>Contacto</span></a>
            </div>
        </div>
    </nav>
    
    <main class="min-h-screen">


