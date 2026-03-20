<?php
// admin/cita-estado.php
require_once '../includes/config.php';
require_once '../includes/admin-helpers.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$redirect = isset($_POST['redirect']) ? trim((string) $_POST['redirect']) : 'dashboard.php#agenda-llamadas';
$csrf = $_POST['csrf'] ?? '';

if (!admin_validate_csrf($csrf)) {
    http_response_code(400);
    exit('CSRF token invalido.');
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$estado = strtolower(trim((string) ($_POST['estado'] ?? '')));
$allowed = ['pendiente', 'confirmada', 'cancelada'];

if ($id <= 0 || !in_array($estado, $allowed, true)) {
    header('Location: ' . $redirect);
    exit;
}

ensureCitasSchema($conn);

if ($stmt = $conn->prepare('UPDATE citas SET estado = ? WHERE id = ?')) {
    $stmt->bind_param('si', $estado, $id);
    $stmt->execute();
    $stmt->close();
}

admin_log_action($conn, 'Actualizar cita', 'cita', $id, 'Estado: ' . $estado);

header('Location: ' . $redirect);
exit;
