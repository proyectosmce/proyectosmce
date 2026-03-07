    </main>
    
    <!-- Footer profesional -->
    <footer class="bg-gradient-to-t from-slate-950 via-slate-900 to-slate-900 text-white mt-16">
        <!-- CTA superior -->
        <div class="border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-sm uppercase tracking-[0.2em] text-blue-200 font-semibold">Proyectos MCE</p>
                    <p class="text-lg md:text-xl font-semibold">Transformamos tus ideas en software listo para usar.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-4 py-3 rounded-lg bg-white text-slate-900 font-semibold shadow hover:bg-blue-50 transition">
                        <i class="fas fa-calendar-check mr-2"></i> Agenda una llamada
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
       class="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 hover:scale-110 z-50 group">
        <i class="fab fa-whatsapp text-3xl"></i>
        <span class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
            ¡Chatea con nosotros!
        </span>
    </a>

    <!-- Botón flotante extra para llamada (desktop) -->
    <a href="<?php echo app_url('contacto.php'); ?>"
       class="hidden md:inline-flex fixed bottom-6 left-6 bg-white text-slate-900 px-4 py-3 rounded-full shadow-lg hover:bg-blue-50 transition-all duration-300 hover:scale-105 z-50 border border-slate-200">
        <i class="fas fa-phone-alt mr-2"></i> Agenda una llamada
    </a>
    
    <!-- Script para menú móvil -->
    <script>
        document.getElementById('menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    
    <!-- Tu script personalizado -->
    <script src="<?php echo app_url('assets/js/script.js'); ?>"></script>
</body>
</html>
