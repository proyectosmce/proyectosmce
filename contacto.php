<?php require_once 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Titulo de pagina -->
<section class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-4xl font-bold mb-2">Contacto</h1>
        <p class="text-xl text-gray-600">Cuentanos tu proyecto y te presupuestamos</p>
    </div>
</section>

<!-- Formulario de contacto -->
<section class="max-w-4xl mx-auto px-4 py-16">
    <?php if (isset($_GET['success'])): ?>
        <div
            data-auto-dismiss="5000"
            data-query-flag="success"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 transition-opacity duration-500"
        >
            Mensaje enviado. Te contactaremos a la brevedad.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php if ($_GET['error'] == 4): ?>
                El formulario se guardo, pero falta configurar el correo del sitio. Intentalo mas tarde.
            <?php elseif ($_GET['error'] == 5): ?>
                El formulario se guardo, pero no se pudo conectar con el servicio de correo. Revisa SMTP_USER, SMTP_PASS y la App Password.
            <?php else: ?>
                Hubo un error. Por favor intenta nuevamente.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="enviar-contacto.php" method="POST" class="bg-white p-8 rounded-xl shadow-lg">
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 mb-2">Nombre *</label>
                <input type="text" name="nombre" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Telefono</label>
                <input type="tel" name="telefono" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Que servicio te interesa?</label>
                <select name="servicio" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
                    <option value="">Seleccionar...</option>
                    <?php
                    $servicios = $conn->query("SELECT titulo FROM servicios ORDER BY orden");
                    while ($s = $servicios->fetch_assoc()) {
                        echo "<option value='{$s['titulo']}'>{$s['titulo']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2">Mensaje *</label>
                <textarea name="mensaje" rows="5" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600"></textarea>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Enviar mensaje
            </button>
        </div>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
