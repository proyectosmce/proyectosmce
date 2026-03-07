<?php require_once 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Contacto -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-headset mr-2 text-yellow-300"></i> Contacto · Proyectos MCE
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Agendemos un diagnóstico y plan de acción</h1>
                <p class="text-lg text-blue-50 max-w-3xl">
                    Cuéntanos qué quieres automatizar, construir o escalar. Te respondemos con tiempos, enfoque técnico y próximos pasos para que tomes decisión rápido.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-bolt mr-2 text-yellow-300"></i>Respuesta en menos de 24h</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-diagram-project mr-2 text-yellow-300"></i>Discovery + propuesta</span>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm"><i class="fas fa-lock mr-2 text-yellow-300"></i>Confidencialidad garantizada</span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Contacta directo</p>
                            <p class="text-2xl font-semibold text-white">Estamos listos para ayudarte</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-phone"></i></span>
                            <div>
                                <p class="font-semibold">Teléfono</p>
                                <p class="text-sm text-blue-100">+57 311 412 59 71</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-envelope"></i></span>
                            <div>
                                <p class="font-semibold">Correo</p>
                                <p class="text-sm text-blue-100">proyectosmceaa@gmail.com</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fab fa-whatsapp"></i></span>
                            <div>
                                <p class="font-semibold">WhatsApp</p>
                                <p class="text-sm text-blue-100">wa.me/573114125971</p>
                            </div>
                        </li>
                    </ul>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Horario</p>
                            <p class="font-semibold">Lunes a sábado · 8:00 - 18:00 (GMT-5)</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulario de contacto -->
<section class="max-w-7xl mx-auto px-4 -mt-10 lg:-mt-14 pb-16">
    <div class="grid lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 order-2 lg:order-1">
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

            <form action="enviar-contacto.php" method="POST" class="bg-white p-8 rounded-2xl shadow-2xl border border-slate-100 space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Nombre *</label>
                        <input type="text" name="nombre" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">Teléfono</label>
                        <input type="tel" name="telefono" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-gray-800 mb-2 font-semibold">¿Qué servicio te interesa?</label>
                        <select name="servicio" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
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
                        <label class="block text-gray-800 mb-2 font-semibold">Mensaje *</label>
                        <textarea name="mensaje" rows="5" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-sm text-gray-600">Al enviar aceptas ser contactado por nuestro equipo.</p>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition w-full sm:w-auto">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar mensaje
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-5 order-1 lg:order-2">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-8 space-y-4">
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Información clave</p>
                <h3 class="text-2xl font-bold text-slate-900">¿Qué recibes al escribirnos?</h3>
                <ul class="space-y-3 text-gray-800 mt-3">
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span>Respuesta personalizada con una ruta inicial y esfuerzos aproximados.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span>Reunión virtual de discovery para entender procesos y objetivos.</span></li>
                    <li class="flex items-start gap-3"><span class="text-blue-600 mt-1"><i class="fas fa-check-circle"></i></span><span>Documento de alcance con los próximos pasos para aprobar o iterar.</span></li>
                </ul>
                <div class="mt-6 p-4 rounded-xl bg-blue-50 text-blue-800 flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <p class="text-sm">Si necesitas NDA antes de compartir detalles, indícalo en el mensaje y lo enviamos.</p>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <a href="https://wa.me/573114125971?text=Hola%21%20Quiero%20consultar%20por%20un%20proyecto" target="_blank" rel="noopener" class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-green-500 text-white font-semibold shadow hover:bg-green-600 transition">
                        <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                    </a>
                    <a href="mailto:proyectosmceaa@gmail.com" class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-slate-200 text-slate-900 font-semibold hover:bg-slate-50 transition">
                        <i class="fas fa-envelope mr-2"></i> Correo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
