<?php require_once 'includes/config.php'; ?>
<?php
$lang = $_GET['lang'] ?? '';
$allowed = ['es','en','fr','de','pt','it'];
if (!in_array($lang, $allowed, true)) { $lang = 'es'; }
?>
<?php require_once 'includes/header.php'; ?>
<script>
  window.mceCurrentLang = '<?php echo $lang; ?>';
  localStorage.setItem('siteLang','<?php echo $lang; ?>');
</script>

<style>
/* Ocultar navegación principal y menú móvil en páginas legales */
.hidden.md\:flex { display: none !important; }
.md\:hidden.flex.items-center.gap-3 { display: none !important; }
#mobile-menu { display: none !important; }
</style>

<main class="bg-slate-100">
  <section class="max-w-5xl mx-auto px-4 py-12">
    <article class="bg-white rounded-2xl shadow-lg border border-slate-200 px-6 md:px-10 py-10 space-y-8">
      <header class="space-y-2">
        <p class="text-sm text-slate-500">Actualizado: <?php echo date('F d, Y'); ?></p>
        <h1 class="text-3xl font-bold" style="color:#1a2c3e;">Política de Privacidad · Proyectos MCE</h1>
        <p class="text-lg font-semibold" style="color:#2c5282;">Tratamiento de datos para desarrollo de software a medida (Colombia)</p>
      </header>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">1. Responsable del Tratamiento</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Proyectos MCE (Marlon Carabalí) es responsable del tratamiento de los datos personales recopilados en este sitio.
          Correo de contacto: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">2. Datos que Recopilamos</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Nombre, email, teléfono, empresa/proyecto, mensajes enviados, metadatos técnicos (IP, navegador) y, si aplica,
          archivos o enlaces que compartas en los formularios de contacto o testimonio.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">3. Finalidad y Base Legal</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Usamos los datos para responder tus consultas, enviar propuestas, gestionar proyectos y mejorar nuestros servicios.
          La base legal es tu consentimiento y, cuando proceda, la ejecución de un contrato o medidas precontractuales.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">4. Conservación</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Conservamos los datos mientras dure la relación comercial o sea necesario para las finalidades descritas, y el
          tiempo adicional requerido por obligaciones legales o defensa de reclamaciones.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">5. Derechos de los Titulares (ARCO)</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Puedes ejercer acceso, rectificación, cancelación, oposición, revocación del consentimiento y portabilidad cuando
          aplique. Escríbenos a <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>
          indicando tu solicitud y adjuntando identificación.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">6. Seguridad</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Implementamos medidas técnicas y organizativas razonables para proteger los datos (acceso restringido, cifrado en
          tránsito, copias de seguridad). No obstante, ninguna transmisión o almacenamiento es 100% seguro.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">7. Menores de Edad</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Nuestros servicios se dirigen a empresas y adultos. No recopilamos intencionalmente datos de menores. Si crees que
          un menor nos ha enviado información, contáctanos para eliminarla.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">8. Transferencias y Encargados</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Podremos compartir datos con proveedores que actúan como encargados (hosting, email, analítica) bajo contratos que
          protegen la confidencialidad. No vendemos datos personales.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">9. Modificaciones</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Podemos actualizar esta Política. La versión vigente será la publicada en esta página con su fecha de actualización.
        </p>
      </section>

      <footer class="pt-2 flex flex-wrap gap-3">
        <a href="<?php echo app_url(); ?>" class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-200 text-slate-800 font-semibold hover:bg-slate-50 transition">Volver al inicio</a>
        <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Contactar</a>
      </footer>
    </article>
  </section>
</main>

<!-- Página de privacidad sin footer para foco en el contenido -->
</body>
</html>
