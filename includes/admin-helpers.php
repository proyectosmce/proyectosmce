<?php

function admin_get_csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['admin_csrf_token']) || !is_string($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['admin_csrf_token'];
}

function admin_validate_csrf(?string $token): bool
{
    if (!is_string($token) || $token === '') {
        return false;
    }

    return hash_equals(admin_get_csrf_token(), $token);
}

function admin_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function admin_build_query(array $params): string
{
    $clean = [];

    foreach ($params as $key => $value) {
        if ($value === null || $value === '' || $value === false) {
            continue;
        }

        if ($key === 'page' && (int) $value <= 1) {
            continue;
        }

        $clean[$key] = $value;
    }

    return http_build_query($clean);
}

function admin_build_url(string $path, array $params = []): string
{
    $query = admin_build_query($params);

    return $query !== '' ? $path . '?' . $query : $path;
}

function admin_paginate(int $totalItems, int $perPage, int $currentPage): array
{
    $perPage = max(1, $perPage);
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'per_page' => $perPage,
        'page' => $currentPage,
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
    ];
}

function admin_send_csv(string $filename, array $headers, array $rows): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    if ($output === false) {
        exit;
    }

    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, $headers);

    foreach ($rows as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

function admin_normalize_phone(?string $phone): string
{
    $phone = trim((string) $phone);
    return preg_replace('/[^0-9]/', '', $phone) ?? '';
}

function admin_whatsapp_url(?string $phone, string $contactName = ''): ?string
{
    $normalizedPhone = admin_normalize_phone($phone);

    if ($normalizedPhone === '') {
        return null;
    }

    $message = 'Hola';
    if ($contactName !== '') {
        $message .= ' ' . trim($contactName);
    }
    $message .= ', te escribo desde el panel de Proyectos MCE.';

    return 'https://wa.me/' . $normalizedPhone . '?text=' . rawurlencode($message);
}

function admin_mailto_url(string $email, string $subject, string $body): string
{
    return 'mailto:' . rawurlencode($email) . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($body);
}

function admin_get_message_reply_templates(?string $contactName = null): array
{
    $contactName = trim((string) $contactName);
    $greeting = $contactName !== '' ? 'Hola ' . $contactName . ',' : 'Hola,';

    return [
        [
            'id' => 'received',
            'label' => 'Confirmar recepcion',
            'description' => 'Confirma que ya viste el mensaje y que responderas con detalle.',
            'subject' => 'Recibimos tu mensaje - Proyectos MCE',
            'body' => $greeting . "\n\nGracias por escribirnos a Proyectos MCE. Ya recibimos tu mensaje y voy a revisar lo que necesitas para responderte con mas detalle en breve.\n\nSi quieres agilizar la propuesta, puedes compartirnos tambien tu presupuesto estimado y la fecha en la que te gustaria iniciar.\n\nQuedo atento.\n\nProyectos MCE",
        ],
        [
            'id' => 'call',
            'label' => 'Agendar llamada',
            'description' => 'Propone una llamada corta para aclarar alcance y tiempos.',
            'subject' => 'Podemos agendar una llamada corta',
            'body' => $greeting . "\n\nGracias por tu mensaje. Para entender mejor lo que necesitas, podemos agendar una llamada corta de 15 minutos y asi revisar alcance, tiempos y presupuesto.\n\nSi te parece bien, enviame dos horarios que te funcionen y coordinamos.\n\nQuedo atento.\n\nProyectos MCE",
        ],
        [
            'id' => 'quote',
            'label' => 'Preparar cotizacion',
            'description' => 'Pide la informacion minima para enviar una propuesta mas precisa.',
            'subject' => 'Informacion para enviarte una cotizacion',
            'body' => $greeting . "\n\nGracias por contactarnos. Para enviarte una cotizacion mas precisa necesito estos datos:\n- Objetivo del proyecto\n- Funciones principales que necesitas\n- Fecha ideal de entrega\n- Presupuesto aproximado\n\nCon esa informacion te respondo con una propuesta mas clara.\n\nProyectos MCE",
        ],
    ];
}

function admin_build_toast(?string $code, array $map): ?array
{
    if (!is_string($code) || $code === '' || !isset($map[$code]) || !is_array($map[$code])) {
        return null;
    }

    $toast = $map[$code];
    $type = $toast['type'] ?? 'success';
    if (!in_array($type, ['success', 'error', 'warning', 'info'], true)) {
        $type = 'success';
    }

    $title = trim((string) ($toast['title'] ?? ''));
    if ($title === '') {
        $defaults = [
            'success' => 'Listo',
            'error' => 'Atencion',
            'warning' => 'Revisa esto',
            'info' => 'Informacion',
        ];
        $title = $defaults[$type];
    }

    $message = trim((string) ($toast['message'] ?? ''));
    if ($message === '') {
        return null;
    }

    return [
        'type' => $type,
        'title' => $title,
        'message' => $message,
    ];
}

function admin_render_toast(?array $toast): void
{
    if (!$toast) {
        return;
    }

    $type = $toast['type'] ?? 'success';

    switch ($type) {
        case 'error':
            $panelClasses = 'border-red-200 bg-white text-slate-900';
            $iconClasses = 'bg-red-100 text-red-600';
            $progressClasses = 'bg-red-500';
            $icon = 'fa-circle-exclamation';
            break;
        case 'warning':
            $panelClasses = 'border-amber-200 bg-white text-slate-900';
            $iconClasses = 'bg-amber-100 text-amber-600';
            $progressClasses = 'bg-amber-500';
            $icon = 'fa-triangle-exclamation';
            break;
        case 'info':
            $panelClasses = 'border-sky-200 bg-white text-slate-900';
            $iconClasses = 'bg-sky-100 text-sky-600';
            $progressClasses = 'bg-sky-500';
            $icon = 'fa-circle-info';
            break;
        default:
            $panelClasses = 'border-emerald-200 bg-white text-slate-900';
            $iconClasses = 'bg-emerald-100 text-emerald-600';
            $progressClasses = 'bg-emerald-500';
            $icon = 'fa-circle-check';
            break;
    }

    ?>
    <div id="admin-toast-region" class="pointer-events-none fixed right-4 top-4 z-[90] w-full max-w-sm">
        <div
            id="admin-toast"
            class="pointer-events-auto overflow-hidden rounded-2xl border shadow-2xl ring-1 ring-black/5 transition duration-300 <?php echo $panelClasses; ?>"
            role="status"
            aria-live="polite"
        >
            <div class="flex items-start gap-4 p-4">
                <div class="mt-0.5 inline-flex h-10 w-10 items-center justify-center rounded-2xl <?php echo $iconClasses; ?>">
                    <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-900"><?php echo admin_escape((string) ($toast['title'] ?? '')); ?></p>
                    <p class="mt-1 text-sm leading-6 text-slate-600"><?php echo admin_escape((string) ($toast['message'] ?? '')); ?></p>
                </div>
                <button type="button" id="admin-toast-close" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Cerrar notificacion">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="h-1 w-full bg-slate-100">
                <div id="admin-toast-progress" class="h-full w-full origin-left <?php echo $progressClasses; ?>"></div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var toast = document.getElementById('admin-toast');
            var closeButton = document.getElementById('admin-toast-close');
            var progress = document.getElementById('admin-toast-progress');
            var duration = 4200;
            var closing = false;

            if (!toast || !progress) {
                return;
            }

            progress.style.transition = 'transform ' + duration + 'ms linear';
            requestAnimationFrame(function () {
                progress.style.transform = 'scaleX(0)';
            });

            function closeToast() {
                if (closing) {
                    return;
                }

                closing = true;
                toast.classList.add('translate-y-2', 'opacity-0');
                window.setTimeout(function () {
                    var region = document.getElementById('admin-toast-region');
                    if (region) {
                        region.remove();
                    }
                }, 280);
            }

            window.setTimeout(closeToast, duration);

            if (closeButton) {
                closeButton.addEventListener('click', closeToast);
            }
        }());
    </script>
    <?php
}

function admin_count_citas_hoy(mysqli $conn): int
{
    $count = 0;
    $sql = "SELECT COUNT(*) AS total FROM citas WHERE fecha = CURDATE()";
    if ($res = $conn->query($sql)) {
        $count = (int) ($res->fetch_assoc()['total'] ?? 0);
        $res->free();
    }
    return $count;
}

function ensureCitasSchema(mysqli $conn): void
{
    static $ready = false;

    if ($ready) {
        return;
    }

    $conn->query("
        CREATE TABLE IF NOT EXISTS citas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(120) NOT NULL,
            telefono VARCHAR(50),
            servicio VARCHAR(120),
            notas TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_fecha_hora (fecha, hora)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $ready = true;
}

function admin_count_pagos_alerta(mysqli $conn): int
{
    $count = 0;
    $sql = "SELECT COUNT(*) AS total 
            FROM proyecto_pagos 
            WHERE (estado IN ('pendiente','parcial')) 
               OR (proxima_cuota IS NOT NULL AND proxima_cuota <= CURDATE())";
    if ($res = $conn->query($sql)) {
        $count = (int) ($res->fetch_assoc()['total'] ?? 0);
        $res->free();
    }
    return $count;
}

function admin_log_column_exists(mysqli $conn, string $columnName): bool
{
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM admin_activity_log LIKE '{$safeColumn}'");

    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function ensureAdminActivityLogSchema(mysqli $conn): void
{
    static $ready = false;

    if ($ready) {
        return;
    }

    $conn->query("
        CREATE TABLE IF NOT EXISTS admin_activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NULL,
            admin_username VARCHAR(100) NOT NULL,
            action VARCHAR(100) NOT NULL,
            entity_type VARCHAR(100) NOT NULL,
            entity_id INT NULL,
            description TEXT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $missingColumns = [
        'description' => 'ALTER TABLE admin_activity_log ADD COLUMN description TEXT NULL AFTER entity_id',
        'ip_address' => 'ALTER TABLE admin_activity_log ADD COLUMN ip_address VARCHAR(45) NULL AFTER description',
        'user_agent' => 'ALTER TABLE admin_activity_log ADD COLUMN user_agent VARCHAR(255) NULL AFTER ip_address',
    ];

    foreach ($missingColumns as $column => $sql) {
        if (!admin_log_column_exists($conn, $column)) {
            $conn->query($sql);
        }
    }

    $ready = true;
}

function admin_log_action(mysqli $conn, string $action, string $entityType, ?int $entityId = null, ?string $description = null): void
{
    ensureAdminActivityLogSchema($conn);

    $adminId = isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
    $adminUsername = $_SESSION['admin_username'] ?? 'admin';
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    if ($stmt = $conn->prepare('INSERT INTO admin_activity_log (admin_id, admin_username, action, entity_type, entity_id, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {
        $stmt->bind_param(
            'isssisss',
            $adminId,
            $adminUsername,
            $action,
            $entityType,
            $entityId,
            $description,
            $ipAddress,
            $userAgent
        );
        $stmt->execute();
        $stmt->close();
    }
}
