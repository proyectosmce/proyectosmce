<?php
// MODO MANTENIMIENTO: Activo si existe el archivo .maintenance
// Solo los administradores desde "/admin" podran ver el sitio.
define('MAINTENANCE_PATH', __DIR__ . '/.maintenance');
define('MAINTENANCE_MODE', file_exists(MAINTENANCE_PATH));
$maintenance_back_at = MAINTENANCE_MODE ? (int)file_get_contents(MAINTENANCE_PATH) : 0;

// Carga secretos locales o generados en el deploy si existen.
$secretPath = __DIR__ . '/secrets.php';
if (is_file($secretPath)) {
    require $secretPath;
}

// Configuracion basica de la base de datos.
// Ajusta las variables de entorno DB_HOST, DB_USER, DB_PASS y DB_NAME si necesitas credenciales distintas.
$DB_HOST   = $DB_HOST ?? getenv('DB_HOST') ?: 'localhost';          // Servidor local
$DB_USER   = $DB_USER ?? getenv('DB_USER') ?: 'root';               // Usuario por defecto en XAMPP
$DB_PASS   = $DB_PASS ?? getenv('DB_PASS') ?: '';                   // Clave vacia por defecto
$DB_NAME   = $DB_NAME ?? getenv('DB_NAME') ?: 'proyectosmce';       // Nombre de la BD local
$DB_PORT   = $DB_PORT ?? getenv('DB_PORT') ?: 3306;                 // Puerto MySQL por defecto
$DB_SOCKET = $DB_SOCKET ?? getenv('DB_SOCKET') ?: null;             // Usalo si tu hosting exige conectar por socket

// Si usas localhost, fuerza 127.0.0.1 para evitar sockets inexistentes.
if ($DB_HOST === 'localhost') {
    $DB_HOST = '127.0.0.1';
}

// Evita excepciones de mysqli y controla el error manualmente.
mysqli_report(MYSQLI_REPORT_OFF);

// Crear conexion mysqli.
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int) $DB_PORT, $DB_SOCKET);

// Verificar conexion.
if ($conn->connect_error) {
    $hint = '';
    if ($conn->connect_errno === 2002) {
        $hint = 'Revisa DB_HOST/DB_PORT y que el servidor MySQL acepte conexiones.';
    }
    error_log('Error de conexion a la base de datos: ' . $conn->connect_error . ' ' . $hint);
    http_response_code(500);
    exit('Error de conexion a la base de datos. ' . $hint);
}

// Establecer charset.
if (!$conn->set_charset('utf8mb4')) {
    error_log('No se pudo establecer el charset utf8mb4: ' . $conn->error);
}

// Iniciar sesion (para secciones admin).
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Limpia entradas basicas antes de usarlas.
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Redireccion sencilla.
function redirect($url)
{
    header("Location: $url");
    exit;
}

function app_base_path()
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $envBase = getenv('APP_BASE_PATH');
    if ($envBase !== false && $envBase !== '') {
        $normalizedEnvBase = '/' . trim(str_replace('\\', '/', $envBase), '/');
        $basePath = $normalizedEnvBase === '/' ? '' : $normalizedEnvBase;
        return $basePath;
    }

    $projectRoot = realpath(dirname(__DIR__));
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;

    if ($projectRoot && $documentRoot) {
        $normalizedProjectRoot = str_replace('\\', '/', $projectRoot);
        $normalizedDocumentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');

        if (strpos($normalizedProjectRoot, $normalizedDocumentRoot) === 0) {
            $relativePath = trim(substr($normalizedProjectRoot, strlen($normalizedDocumentRoot)), '/');
            $basePath = $relativePath === '' ? '' : '/' . $relativePath;
            return $basePath;
        }
    }

    $basePath = '';
    return $basePath;
}

function app_url($path = '')
{
    $path = ltrim((string) $path, '/');
    // URLs amigables: quita el .php para páginas del front
    $path = preg_replace('/\.php(\?|#|$)/', '$1', $path);
    
    $basePath = app_base_path();

    if ($path === '') {
        return $basePath !== '' ? $basePath . '/' : '/';
    }

    return ($basePath !== '' ? $basePath : '') . '/' . $path;
}

function app_absolute_url($path = '')
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host . app_url($path);
}

function current_absolute_url()
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

    return $scheme . '://' . $host . $requestUri;
}

// Ejecución de MODO MANTENIMIENTO al final (cuando ya existen funciones como app_url)
if (MAINTENANCE_MODE && strpos($_SERVER['SCRIPT_NAME'], '/admin/') === false) {
    http_response_code(503);
    $_SERVER["PHP_SELF"] = '/mantenimiento.php';
    require_once __DIR__ . '/header.php';
    
    // Determinar si el tiempo ya expiró pero sigue bloqueado
    $time_expired = ($maintenance_back_at > 0 && time() >= $maintenance_back_at);

    // Inyectar CSS
    echo '<style>
        html, body { 
            overflow: hidden !important; 
            height: 100%; 
            margin: 0; 
            padding: 0; 
        }
        
        nav a[data-i18n^="nav-"], 
        #menu-btn, 
        #mobile-menu, 
        .floating-buttons, 
        .mce-call-float,
        footer { display: none !important; }
        
        .maint-mosaic-bg {
            background-image: url(\'' . app_url('imag/MCE.jpg') . '\');
            background-size: 20% 25%;
            background-repeat: repeat;
            height: calc(100dvh - 64px);
            min-height: calc(100vh - 64px);
            position: relative;
            display: flex;
            align-items: center;
            justify-content:center;
            overflow: hidden;
            width: 100%;
        }

        @media (max-width: 640px) {
            .maint-mosaic-bg {
                background-size: 33.33% 25%;
                height: calc(100dvh - 64px);
            }
        }

        .spin-gear {
            display: inline-block;
            animation: gearSpin 4s linear infinite;
        }
        .spin-gear-rev {
            display: inline-block;
            animation: gearSpinRev 4s linear infinite;
            margin-left: -8px;
        }
        @keyframes gearSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes gearSpinRev { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }

        .social-link { color: rgba(255,255,255,0.6); font-size: 1.2rem; transition: all 0.3s ease; }
        .social-link:hover { color: #fff; transform: translateY(-3px); }
        
        #countdown-wrap {
            background: rgba(124, 58, 237, 0.1);
            border: 1px solid rgba(124, 58, 237, 0.2);
            padding: 0.8rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            display: inline-block;
            min-width: 200px;
        }
    </style>';
    
    // Inyectar tarjeta central
    echo '<div class="maint-mosaic-bg">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.73);z-index:1;"></div>
    <div style="position:relative;z-index:2;background:rgba(255,255,255,0.03);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.08);padding:3rem 2rem;border-radius:24px;text-align:center;color:#fff;max-width:90%;width:420px;margin:1rem;box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        
        <div style="font-size: 2.8rem; margin-bottom: 1.2rem; color: #a78bfa;">
            <i class="fas fa-cog spin-gear"></i>
            <i class="fas fa-cog spin-gear-rev" style="font-size: 1.8rem; vertical-align: bottom;"></i>
        </div>

        <h1 data-i18n="maint-title" style="margin:0 0 0.5rem;font-size:1.8rem;line-height:1.2;font-weight:900;letter-spacing:1px;">EN MANTENIMIENTO</h1>
        <p data-i18n="maint-desc" style="font-size:1rem;opacity:0.7;line-height:1.5;margin:0;">Estamos mejorando nuestra plataforma para brindarte una mejor experiencia.</p>
        
        <div id="countdown-wrap"' . (($maintenance_back_at == 0) ? ' style="display:none;"' : '') . '>
            <div id="timer" style="font-family:monospace; font-size: 1.6rem; font-weight: bold; color: #fff;">00:00:00</div>
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.5; margin-top: 4px;">Tiempo Estimado</div>
        </div>

        <div style="height: 10px;"></div>

        <!-- Redes Sociales -->
        <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 1rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="https://www.instagram.com/proyectosmce/" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="https://www.linkedin.com/company/proyectosmce/" target="_blank" class="social-link"><i class="fab fa-linkedin"></i></a>
            <a href="https://www.facebook.com/proyectosmce" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.tiktok.com/@proyectosmce" target="_blank" class="social-link"><i class="fab fa-tiktok"></i></a>
            <a href="https://t.me/proyectosmce" target="_blank" class="social-link"><i class="fab fa-telegram-plane"></i></a>
        </div>
    </div>
</div>';

    // Script para la cuenta regresiva y WhatsApp
    $backAtJs = $maintenance_back_at * 1000;
    echo '<script>
        let backAt = ' . $backAtJs . ';
        const timerEl = document.getElementById("timer");
        const wrapEl = document.getElementById("countdown-wrap");

        function updateTimer() {
            if (backAt === 0) return;
            const now = Date.now();
            let diff = backAt - now;
            
            if (diff <= 0) {
                // Si el tiempo caduca, reiniciar 30 seg repetidamente hasta que el admin desbloquee
                backAt = Date.now() + 30000;
                diff = 30000;
            }
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((diff % (1000 * 60)) / 1000);
            
            if (timerEl) {
                timerEl.innerHTML = 
                    String(hours).padStart(2, "0") + ":" + 
                    String(mins).padStart(2, "0") + ":" + 
                    String(secs).padStart(2, "0");
            }
        }

        if (backAt > 0) {
            setInterval(updateTimer, 1000);
            updateTimer();
        }

        const checkWa = setInterval(() => {
            const waBtn = document.querySelector(\'a[href*="wa.me"]\');
            if (waBtn) {
                const url = new URL(waBtn.href);
                url.searchParams.set(\'text\', \'Hola! Quisiera averiguar sobre un proyecto, pero veo que la página está en mantenimiento.\');
                waBtn.href = url.toString();
                clearInterval(checkWa);
            }
        }, 100);
    </script>';

    // Incluir footer.php para cargar los scripts de traduccion, pero el CSS arriba oculta la caja visual del footer.
    require_once __DIR__ . '/footer.php';
    exit;
}
?>
