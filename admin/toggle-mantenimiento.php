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
    if (file_exists($maintenanceFile) && !isset($_POST['update_time'])) {
        unlink($maintenanceFile);
        admin_log_action($conn, 'desactivar', 'mantenimiento', 0, 'Modo mantenimiento desactivado');
    } else {
        $hours = (int)($_POST['hours'] ?? 0);
        $minutes = (int)($_POST['minutes'] ?? 0);
        $totalMinutes = ($hours * 60) + $minutes;
        $backAt = $totalMinutes > 0 ? (time() + ($totalMinutes * 60)) : 0;
        
        file_put_contents($maintenanceFile, $backAt);
        $msg = $totalMinutes > 0 ? "Activado con estimación de $hours h $minutes min" : "Activado sin tiempo definido";
        admin_log_action($conn, 'activar', 'mantenimiento', 0, $msg);
    }
}

header('Location: dashboard.php');
exit;
