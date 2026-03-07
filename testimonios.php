<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/project-helpers.php'; ?>
<?php
// Asegurar tabla de testimonios
$conn->query("CREATE TABLE IF NOT EXISTS testimonios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    testimonio TEXT NOT NULL,
    valoracion INT DEFAULT 5,
    proyecto_id INT,
    destacado BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Manejo de envío de testimonios (solo alta)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'nuevo_testimonio') {
    $nombre  = trim($_POST['nombre'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $proyId  = (int) ($_POST['proyecto_id'] ?? 0);
    $valor   = (int) ($_POST['valoracion'] ?? 5);
    $valor   = max(1, min(5, $valor));

    // Obtener nombre del proyecto para guardarlo en 'empresa'
    $empresa = '';
    if ($proyId > 0) {
        if ($stProj = $conn->prepare('SELECT titulo FROM proyectos WHERE id = ? LIMIT 1')) {
            $stProj->bind_param('i', $proyId);
            $stProj->execute();
            $stProj->bind_result($projTitulo);
            if ($stProj->fetch()) {
                $empresa = $projTitulo;
            }
            $stProj->close();
        }
    }

    if ($nombre !== '' && $mensaje !== '' && $proyId > 0) {
        if ($stmt = $conn->prepare('INSERT INTO testimonios (nombre, testimonio, proyecto_id, empresa, valoracion, destacado) VALUES (?, ?, ?, ?, ?, 0)')) {
            $stmt->bind_param('ssisi', $nombre, $mensaje, $proyId, $empresa, $valor);
            $stmt->execute();
            $stmt->close();
            header('Location: testimonios.php?testimonio=ok#testimonios');
            exit;
        }
    }
}

$testimonios     = $conn->query("SELECT t.nombre, t.testimonio, t.valoracion, p.titulo AS proyecto FROM testimonios t LEFT JOIN proyectos p ON t.proyecto_id = p.id ORDER BY t.destacado DESC, t.created_at DESC LIMIT 9");
$projectOptions  = fetchProjectDropdownOptions($conn);
$testimonioOk    = isset($_GET['testimonio']) && $_GET['testimonio'] === 'ok';
$testError       = !$testimonios instanceof mysqli_result;
$hasTestimonios  = !$testError && $testimonios->num_rows > 0;
$hasProjectOptions = !empty($projectOptions);
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Testimonios -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-800 text-white">
    <div class="absolute inset-0 bg-grid-white/10"></div>
    <div class="absolute -top-24 -left-16 w-72 h-72 bg-blue-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -bottom-24 -right-10 w-80 h-80 bg-purple-500/25 blur-3xl rounded-full"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold bg-white/10 border border-white/20 rounded-full backdrop-blur">
                    <i class="fas fa-comment-dots mr-2 text-yellow-300"></i> Testimonios · Proyectos MCE
                </span>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight">Lo que dicen quienes operan con nuestro software</h1>
                <p class="text-lg text-blue-50 max-w-3xl">
                    Comentarios de equipos que hoy usan las soluciones en producción: releases frecuentes, soporte real y métricas visibles.
                </p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Acompañamiento end-to-end en cada release.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Equipos de negocio y tecnología siempre informados.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-yellow-300 mt-1"><i class="fas fa-check-circle"></i></span>
                        <p class="text-blue-50">Feedback directo que usamos para mejorar el producto.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                    <a href="#testimonios" class="inline-flex items-center justify-center bg-yellow-300 text-slate-900 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-yellow-900/20 hover:bg-yellow-200 transition">
                        <i class="fas fa-comments mr-2"></i> Leer testimonios
                    </a>
                    <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-slate-900 transition">
                        <i class="fas fa-handshake mr-2"></i> Compartir mi experiencia
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-8 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Lo que valoran</p>
                            <p class="text-2xl font-semibold text-white">Claridad, tiempos y soporte cercano</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-300 text-slate-900 font-bold shadow-lg">MCE</span>
                    </div>
                    <ul class="space-y-3 text-blue-50">
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-clock"></i></span>
                            <div>
                                <p class="font-semibold">Visibilidad constante</p>
                                <p class="text-sm text-blue-100">Demos en cada sprint, tableros y comunicación directa.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-shield-alt"></i></span>
                            <div>
                                <p class="font-semibold">Cuidado de la operación</p>
                                <p class="text-sm text-blue-100">Backups, roles, permisos y monitoreo activo.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 border border-white/15 text-yellow-300"><i class="fas fa-face-smile"></i></span>
                            <div>
                                <p class="font-semibold">Experiencias claras</p>
                                <p class="text-sm text-blue-100">UX/UI pensado para equipos operativos y clientes finales.</p>
                            </div>
                        </li>
                    </ul>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-100">Participa</p>
                            <p class="font-semibold">Deja tu testimonio y ayuda a otros equipos</p>
                        </div>
                        <i class="fas fa-arrow-right text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="testimonios" class="max-w-7xl mx-auto px-4 py-12">
    <?php if ($testimonioOk): ?>
    <div id="alert-testimonio" class="mb-8 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3">
        ¡Gracias! Tu testimonio se guardó y ya es visible.
    </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-8">
        <div>
            <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Experiencias reales</p>
            <h2 class="text-3xl font-bold text-slate-900">Historias de equipos que confiaron en Proyectos MCE</h2>
            <p class="text-gray-700 mt-2 max-w-3xl">Lee cómo usamos entregas iterativas, acompañamiento y soporte para llevar sus proyectos a producción.</p>
        </div>
        <a href="#form-testimonio" class="inline-flex items-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:bg-slate-800 transition">
            <i class="fas fa-comments mr-2"></i> Quiero contar mi experiencia
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php if ($testError): ?>
            <div class="md:col-span-3 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
                No pudimos cargar los testimonios en este momento.
            </div>
        <?php elseif (!$hasTestimonios): ?>
            <div class="md:col-span-3 bg-white border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-600">
                Aún no hay testimonios. ¡Sé el primero en dejar el tuyo!
            </div>
        <?php else: ?>
            <?php while ($t = $testimonios->fetch_assoc()): ?>
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($t['nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-sm text-blue-600"><?php echo htmlspecialchars($t['proyecto'] ?? 'Proyecto MCE', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="text-right">
                    <div class="flex text-yellow-400 justify-end">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <i class="fas fa-star<?php echo $i < (int) $t['valoracion'] ? '' : '-o'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <?php
                        $v = (int) $t['valoracion'];
                        if ($v <= 1) { $lbl = 'No recomiendo'; $cls = 'text-red-600'; }
                        else if ($v == 2) { $lbl = 'Poco recomendable'; $cls = 'text-red-600'; }
                        else if ($v == 3) { $lbl = 'Neutral'; $cls = 'text-gray-600'; }
                        else if ($v == 4) { $lbl = 'Recomiendo'; $cls = 'text-green-600'; }
                        else { $lbl = 'Sí recomiendo'; $cls = 'text-green-600'; }
                    ?>
                    <p class="text-xs font-semibold <?php echo $cls; ?> mt-1"><?php echo $v; ?> / 5 · <?php echo $lbl; ?></p>
                </div>
            </div>
                <?php
                    $projName = $t['proyecto'] ?? 'su proyecto';
                    $textoFinal = "Yo, {$t['nombre']} dueño de {$projName}, {$t['testimonio']}";
                ?>
                <p class="text-gray-700 leading-relaxed text-sm">"<?php echo nl2br(htmlspecialchars($textoFinal, ENT_QUOTES, 'UTF-8')); ?>"</p>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-blue-100 p-8" id="form-testimonio">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Deja tu testimonio</h3>
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm flex items-start gap-3">
            <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-500 text-white rounded-full text-xs mt-0.5">
                <i class="fas fa-exclamation"></i>
            </span>
            <span>Advertencia: si el nombre no coincide con el propietario del proyecto, el testimonio podrá ser eliminado.</span>
        </div>
        <form id="testimonio-form" method="POST" action="#testimonios" class="grid md:grid-cols-2 gap-6">
            <input type="hidden" name="action" value="nuevo_testimonio">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Selecciona tu proyecto</label>
                    <input id="t-proyecto-search" type="text" <?php echo $hasProjectOptions ? '' : 'disabled'; ?> class="w-full border border-gray-200 rounded-lg px-4 py-2 mb-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-100 disabled:text-gray-400" placeholder="Escribe para buscar un proyecto">
                    <select id="t-proyecto" name="proyecto_id" required <?php echo $hasProjectOptions ? '' : 'disabled'; ?> class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="">Elige un proyecto</option>
                        <?php foreach ($projectOptions as $projectOption): ?>
                            <option value="<?php echo $projectOption['id']; ?>"><?php echo htmlspecialchars($projectOption['titulo'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!$hasProjectOptions): ?>
                        <p class="mt-2 text-sm text-amber-700">No hay proyectos publicados en el portafolio para seleccionar.</p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre completo</label>
                    <input id="t-nombre" name="nombre" required type="text" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. Ana Martínez">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Escribe tu experiencia</label>
                    <textarea id="t-mensaje" name="mensaje" required rows="5" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cuenta cómo te fue con el proyecto"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Calificación (1 = no recomiendo, 5 = sí recomiendo)</label>
                    <div class="flex items-center gap-2" id="rating-group">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="cursor-pointer text-2xl transition text-gray-300" data-star="<?php echo $i; ?>">
                            <input type="radio" class="sr-only" name="valoracion" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>>
                            <i class="fas fa-star"></i>
                        </label>
                        <?php endfor; ?>
                    </div>
                    <p id="rating-text" class="text-sm text-green-600 mt-2">5 / 5 (sí recomiendo)</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="t-prev-btn" class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition">Ver vista previa</button>
                    <button type="submit" <?php echo $hasProjectOptions ? '' : 'disabled'; ?> class="border border-blue-600 text-blue-600 px-5 py-3 rounded-lg hover:bg-blue-50 transition disabled:border-gray-300 disabled:text-gray-400 disabled:bg-gray-100">
                        Enviar testimonio
                    </button>
                </div>
            </div>
            <div class="space-y-3">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Lo que estás escribiendo</p>
                    <p id="t-live-raw" class="text-gray-600 whitespace-pre-line min-h-[120px]">Aquí verás tu texto a medida que escribes…</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                    <p class="text-sm font-semibold text-blue-700 mb-2">Así se verá publicado</p>
                    <p id="t-live-final" class="text-gray-800 leading-relaxed min-h-[120px]">
                        Yo, [tu nombre] dueño de [tu proyecto], aquí aparecerá tu testimonio final.
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- CTA final -->
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-800 text-white rounded-2xl p-10 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-sm font-semibold text-blue-100 uppercase tracking-wide">Hablemos</p>
            <h3 class="text-2xl font-bold">¿Quieres aparecer en esta sección?</h3>
            <p class="text-blue-100 mt-2">Cuéntanos cómo te fue con tu proyecto y te contactamos para publicarlo.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="#form-testimonio" class="inline-flex items-center px-5 py-3 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:bg-blue-50 transition">
                <i class="fas fa-paper-plane mr-2"></i> Dejar testimonio
            </a>
            <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-5 py-3 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">
                <i class="fas fa-comments mr-2"></i> Hablar con el equipo
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
(() => {
    const nombre = document.getElementById('t-nombre');
    const proyecto = document.getElementById('t-proyecto');
    const proyectoSearch = document.getElementById('t-proyecto-search');
    const mensaje = document.getElementById('t-mensaje');
    const liveRaw = document.getElementById('t-live-raw');
    const liveFinal = document.getElementById('t-live-final');
    const form = document.getElementById('testimonio-form');
    const prevBtn = document.getElementById('t-prev-btn');
    let submitting = false;
    const ratingInputs = document.querySelectorAll('input[name=\"valoracion\"]');
    const starLabels = document.querySelectorAll('[data-star]');
    const ratingText = document.getElementById('rating-text');

    function updatePreview() {
        const n = (nombre.value || 'tu nombre').trim();
        const p = (proyecto.selectedOptions[0]?.text || 'tu proyecto').trim();
        const msg = mensaje.value.trim() || 'escribe aquí tu experiencia con el proyecto';
        liveRaw.textContent = mensaje.value || 'Aquí verás tu texto a medida que escribes…';
        liveFinal.textContent = `Yo, ${n} dueño de ${p}, ${msg}`;
    }

    nombre.addEventListener('input', updatePreview);
    proyecto.addEventListener('change', updatePreview);
    mensaje.addEventListener('input', updatePreview);
    prevBtn.addEventListener('click', updatePreview);

    const updateStars = () => {
        const selected = document.querySelector('input[name=\"valoracion\"]:checked');
        const value = selected ? parseInt(selected.value, 10) : 0;
        starLabels.forEach(label => {
            const v = parseInt(label.dataset.star, 10);
            label.classList.toggle('text-yellow-400', v <= value);
            label.classList.toggle('text-gray-300', v > value);
        });
        if (ratingText) {
            ratingText.classList.remove('text-red-600','text-green-600','text-gray-600');
            if (value === 1) {
                ratingText.textContent = '1 / 5 (no recomiendo)';
                ratingText.classList.add('text-red-600');
            } else if (value === 2) {
                ratingText.textContent = '2 / 5 (poco recomendable)';
                ratingText.classList.add('text-red-600');
            } else if (value === 3) {
                ratingText.textContent = '3 / 5 (neutral)';
                ratingText.classList.add('text-gray-600');
            } else if (value === 4) {
                ratingText.textContent = '4 / 5 (recomiendo)';
                ratingText.classList.add('text-green-600');
            } else if (value === 5) {
                ratingText.textContent = '5 / 5 (sí recomiendo)';
                ratingText.classList.add('text-green-600');
            } else {
                ratingText.textContent = `${value} / 5`;
                ratingText.classList.add('text-gray-600');
            }
        }
    };
    ratingInputs.forEach(r => {
        r.addEventListener('change', () => {
            updateStars();
            updatePreview();
        });
    });
    updateStars();

    // Búsqueda rápida en el select de proyectos
    if (proyectoSearch) {
        proyectoSearch.addEventListener('input', () => {
            const term = proyectoSearch.value.toLowerCase();
            let matched = null;
            Array.from(proyecto.options).forEach(opt => {
                if (!opt.value) return;
                const show = opt.text.toLowerCase().includes(term);
                opt.hidden = term.length ? !show : false;
                if (!matched && show) matched = opt;
            });
            if (matched) {
                proyecto.value = matched.value;
            } else if (term.length) {
                proyecto.value = '';
            }
            updatePreview();
        });
    }

    // Ocultar aviso y limpiar querystring
    const alertTestimonio = document.getElementById('alert-testimonio');
    if (alertTestimonio) {
        setTimeout(() => alertTestimonio.classList.add('hidden'), 4000);
        const url = new URL(window.location);
        url.searchParams.delete('testimonio');
        window.history.replaceState({}, '', url);
    }

    form.addEventListener('submit', (e) => {
        if (submitting) return;
        e.preventDefault();
        updatePreview();
        const ok = confirm(`Enviar este testimonio?\n\n${liveFinal.textContent}`);
        if (ok) {
            submitting = true;
            form.submit();
        }
    });

    updatePreview();
})();
</script>
