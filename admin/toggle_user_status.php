<?php
session_start();
require_once __DIR__ . '/../models/User.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = intval($_POST['id'] ?? 0);
        $active = intval($_POST['active'] ?? 1);
        
        if (!$id) {
            throw new Exception("ID de usuario no válido");
        }

        // No permitir desactivar la propia cuenta
        if ($id == $_SESSION['user_id'] && !$active) {
            throw new Exception("No puedes desactivar tu propia cuenta");
        }

        // Verificar que el usuario existe
        $user = $userModel->getById($id);
        if (!$user) {
            throw new Exception("Usuario no encontrado");
        }

        // Cambiar estado
        $sql = "UPDATE users SET active = ?, updated_at = NOW() WHERE id = ?";
        $userModel->getDb()->execute($sql, [$active, $id]);

        $status = $active ? 'activado' : 'desactivado';
        $_SESSION['success'] = "Usuario '{$user['username']}' {$status} exitosamente";
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: users.php');
exit;
?>
