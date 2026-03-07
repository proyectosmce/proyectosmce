<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos MCE Soluciones Digitales</title>
    <?php
    $faviconFile = dirname(__DIR__) . '/favicon.ico';
    $faviconPngFile = dirname(__DIR__) . '/favicon.png';
    $appleTouchIconFile = dirname(__DIR__) . '/apple-touch-icon.png';
    $faviconVersion = is_file($faviconFile) ? filemtime($faviconFile) : time();
    $faviconIcoUrl = app_url('favicon.ico') . '?v=' . $faviconVersion;
    $faviconPngUrl = app_url('favicon.png') . '?v=' . (is_file($faviconPngFile) ? filemtime($faviconPngFile) : $faviconVersion);
    $appleTouchIconUrl = app_url('apple-touch-icon.png') . '?v=' . (is_file($appleTouchIconFile) ? filemtime($appleTouchIconFile) : $faviconVersion);
    ?>

    <?php include __DIR__ . '/metas.php'; ?>
    
    <!-- Tailwind CSS via CDN (rapido y profesional) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Tu CSS personalizado (pequenas modificaciones) -->
    <link rel="stylesheet" href="<?php echo app_url('assets/css/estilo.css'); ?>">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $faviconIcoUrl; ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?php echo $faviconPngUrl; ?>" sizes="64x64">
    <link rel="shortcut icon" href="<?php echo $faviconIcoUrl; ?>">
    <link rel="apple-touch-icon" href="<?php echo $appleTouchIconUrl; ?>">
</head>
<body class="bg-gray-50">
    <!-- Loader -->
    <div id="loader" class="fixed inset-0 bg-white z-[60] flex items-center justify-center transition-opacity duration-500">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-gray-600">Cargando Proyectos MCE...</p>
        </div>
    </div>
    
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
                            <img src="<?php echo app_url('imag/MCE.jpg'); ?>" alt="MCE Proyectos" class="object-cover w-full h-full">
                        </span>
                    </span>
                    <div class="leading-tight">
                        <span class="block text-xl font-semibold text-slate-900 tracking-tight">MCE Proyectos</span>
                        <span class="block text-[11px] text-slate-500 uppercase tracking-[0.22em]">Desarrollo   Web</span>
                    </div>
                </a>
                
                <!-- Menú desktop -->
                <div class="hidden md:flex space-x-8">
                    <a href="<?php echo app_url(); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-home text-blue-500/80"></i><span>Inicio</span></a>
                    <a href="<?php echo app_url('servicios.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-layer-group text-blue-500/80"></i><span>Servicios</span></a>
                    <a href="<?php echo app_url('portafolio.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'portafolio.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-briefcase text-blue-500/80"></i><span>Portafolio</span></a>
                    <a href="<?php echo app_url('testimonios.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'testimonios.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-comments text-blue-500/80"></i><span>Testimonios</span></a>
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 transition <?php echo basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'font-bold text-blue-600' : ''; ?>"><i class="fas fa-envelope-open-text text-blue-500/80"></i><span>Contacto</span></a>
                </div>
                
                <!-- Botón móvil -->
                <div class="md:hidden">
                    <button id="menu-btn" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menú móvil mejorado -->
        <div id="mobile-menu" class="hidden md:hidden fixed inset-0 bg-white z-40 pt-20">
            <div class="flex flex-col items-center space-y-6 p-8">
                <a href="<?php echo app_url(); ?>" class="text-2xl font-semibold hover:text-blue-600 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-blue-600' : ''; ?>"><i class="fas fa-home text-blue-500/80"></i><span>Inicio</span></a>
                <a href="<?php echo app_url('servicios.php'); ?>" class="text-2xl font-semibold hover:text-blue-600 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'text-blue-600' : ''; ?>"><i class="fas fa-layer-group text-blue-500/80"></i><span>Servicios</span></a>
                <a href="<?php echo app_url('portafolio.php'); ?>" class="text-2xl font-semibold hover:text-blue-600 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'portafolio.php' ? 'text-blue-600' : ''; ?>"><i class="fas fa-briefcase text-blue-500/80"></i><span>Portafolio</span></a>
                <a href="<?php echo app_url('testimonios.php'); ?>" class="text-2xl font-semibold hover:text-blue-600 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'testimonios.php' ? 'text-blue-600' : ''; ?>"><i class="fas fa-comments text-blue-500/80"></i><span>Testimonios</span></a>
                <a href="<?php echo app_url('contacto.php'); ?>" class="text-2xl font-semibold hover:text-blue-600 transition inline-flex items-center gap-3 <?php echo basename($_SERVER['PHP_SELF']) == 'contacto.php' ? 'text-blue-600' : ''; ?>"><i class="fas fa-envelope-open-text text-blue-500/80"></i><span>Contacto</span></a>
                
                <!-- Botón de contacto en móvil -->
                <a href="<?php echo app_url('contacto.php'); ?>" 
                   class="mt-6 bg-blue-600 text-white px-8 py-3 rounded-lg text-xl">
                    <i class="fas fa-whatsapp mr-2"></i>Contactar
                </a>
            </div>
        </div>
    </nav>
    
    <main class="min-h-screen">
