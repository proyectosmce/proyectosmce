<?php
require_once '../includes/config.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_validate_csrf($_POST['csrf_token'] ?? null)) {
        header('Location: dashboard.php?msg=csrf');
        exit;
    }

    $maintenanceFile = __DIR__ . '/../includes/.maintenance';
    
    // Toggle state
    if (file_exists($maintenanceFile)) {
        unlink($maintenanceFile);
        admin_log_action($conn, 'desactivar', 'mantenimiento', 0, 'Modo mantenimiento desactivado');
    } else {
        file_put_contents($maintenanceFile, 'En mantenimiento');
        admin_log_action($conn, 'activar', 'mantenimiento', 0, 'Modo mantenimiento activado');
    }
}

header('Location: dashboard.php');
exit;
