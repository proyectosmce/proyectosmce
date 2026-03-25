<?php
// includes/metas.php
$meta_titulo = $meta_titulo ?? 'Proyectos MCE - Desarrollo Web Profesional';
$meta_descripcion = $meta_descripcion ?? 'Desarrollo de sistemas web a medida y aplicaciones. Transformamos tus ideas en código.';
$meta_imagen = $meta_imagen ?? app_absolute_url('imag/MCE.jpg');
?>
<!-- Meta tags basicos -->
<meta name="description" content="<?php echo $meta_descripcion; ?>">
<meta name="keywords" content="desarrollo web, sistemas a medida, php, mysql, inventario">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo current_absolute_url(); ?>">
<meta property="og:title" content="<?php echo $meta_titulo; ?>">
<meta property="og:description" content="<?php echo $meta_descripcion; ?>">
<meta property="og:image" content="<?php echo $meta_imagen; ?>">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo current_absolute_url(); ?>">
<meta property="twitter:title" content="<?php echo $meta_titulo; ?>">
<meta property="twitter:description" content="<?php echo $meta_descripcion; ?>">
<meta property="twitter:image" content="<?php echo $meta_imagen; ?>">

<!-- Canonical URL -->
<link rel="canonical" href="<?php echo current_absolute_url(); ?>">
