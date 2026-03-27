<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<main class="bg-slate-100">
  <section class="max-w-5xl mx-auto px-4 py-12">
    <article class="bg-white rounded-2xl shadow-lg border border-slate-200 px-6 md:px-10 py-10 space-y-8">
      <header class="space-y-2">
        <p class="text-sm text-slate-500">Actualizado: <?php echo date('F d, Y'); ?></p>
        <h1 class="text-3xl font-bold" style="color:#1a2c3e;">Aviso Legal · Proyectos MCE</h1>
        <p class="text-lg font-semibold" style="color:#2c5282;">Desarrollo de software a medida en Colombia</p>
      </header>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">1. Identificación del Titular</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Proyectos MCE es la marca comercial de Marlon Carabalí, con domicilio en Colombia. Correo de contacto:
          <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">2. Condiciones de Uso</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          El acceso y uso de este sitio implican la aceptación de este Aviso Legal. El usuario se compromete a hacer un uso
          diligente, lícito y acorde con la buena fe de los contenidos y servicios ofrecidos.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">3. Propiedad Intelectual e Industrial</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Todo el contenido (textos, diseños, código, marcas y logotipos) pertenece a Proyectos MCE o a sus licenciantes.
          Se prohíbe su reproducción, distribución o transformación sin autorización expresa y escrita.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">4. Limitación de Responsabilidad</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Aunque procuramos información precisa y servicios disponibles, no garantizamos ausencia total de errores o
          interrupciones. Proyectos MCE no será responsable por daños derivados del uso del sitio o de decisiones tomadas
          con base en su contenido.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">5. Enlaces Externos</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          El sitio puede contener enlaces a terceros. Proyectos MCE no controla ni responde por su contenido o políticas.
          El usuario accede bajo su propia responsabilidad.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">6. Modificaciones</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Nos reservamos el derecho de modificar este Aviso Legal y el contenido del sitio. Los cambios serán efectivos desde
          su publicación. Recomendamos revisarlo periódicamente.
        </p>
      </section>

      <section class="space-y-3">
        <h2 class="text-xl font-semibold" style="color:#1a2c3e;">7. Legislación Aplicable</h2>
        <p class="text-base leading-relaxed" style="color:#4a5568;">
          Este Aviso Legal se rige por las leyes de la República de Colombia. Cualquier controversia se someterá a los
          jueces y tribunales competentes en Colombia.
        </p>
      </section>

      <footer class="pt-2 flex flex-wrap gap-3">
        <a href="<?php echo app_url(); ?>" class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-200 text-slate-800 font-semibold hover:bg-slate-50 transition">Volver al inicio</a>
        <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Contactar</a>
      </footer>
    </article>
  </section>
</main>

<!-- Página legal sin footer para foco en el contenido -->
</body>
</html>
