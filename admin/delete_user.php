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
    header('Location: ../index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if (!$id) {
            throw new Exception("ID de usuario no válido");
        }

        // No permitir que el admin se elimine a sí mismo
        if ($id == $_SESSION['user_id']) {
            throw new Exception("No puedes eliminar tu propia cuenta");
        }

        // Verificar que el usuario existe
        $user = $userModel->getById($id);
        if (!$user) {
            throw new Exception("Usuario no encontrado");
        }

        // Eliminar usuario (soft delete)
        $userModel->delete($id);

        $_SESSION['success'] = "Usuario '{$user['username']}' eliminado exitosamente";
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: users.php');
exit;
?>
