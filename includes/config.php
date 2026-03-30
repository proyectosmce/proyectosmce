<?php
// MODO MANTENIMIENTO: Activo si existe el archivo .maintenance
// Solo los administradores desde "/admin" podran ver el sitio.
define('MAINTENANCE_MODE', file_exists(__DIR__ . '/.maintenance'));

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
    // Faker vars para header
    $_SERVER["PHP_SELF"] = '/mantenimiento.php';
    require_once __DIR__ . '/header.php';
    
    // Inyectar CSS para ocultar TODO el menú (links y móvil) excepto logo e idioma
    echo '<style>
        nav a[data-i18n^="nav-"], 
        #menu-btn, 
        #mobile-menu { display: none !important; }
    </style>';
    
    // Inyectar tarjeta central con fondo
    echo '<div style="background:url(\'' . app_url('imag/MCE.jpg') . '\') center/cover no-repeat;min-height:calc(100vh - 64px);position:relative;display:flex;align-items:center;justify-content:center;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.7);z-index:1;"></div>
    <div style="position:relative;z-index:2;background:rgba(255,255,255,0.05);backdrop-filter:blur(15px);border:1px solid rgba(255,255,255,0.1);padding:3rem 2rem;border-radius:20px;text-align:center;color:#fff;max-width:420px;margin:2rem;">
        <h1 style="margin:0 0 1rem;font-size:2rem;font-weight:bold;">🛠️ En Mantenimiento</h1>
        <p style="font-size:1.1rem;opacity:0.8;line-height:1.5;margin:0;">Estamos trabajando en mejoras y nuevas funciones. Regresamos en breve.</p>
    </div>
</div>';

    // Cerrar tags de header
    echo '</main></body></html>';
    exit;
}
?>
