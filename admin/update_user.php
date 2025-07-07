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
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        // Validaciones
        if (!$id) {
            throw new Exception("ID de usuario no válido");
        }

        if (empty($username) || empty($email) || empty($role)) {
            throw new Exception("Todos los campos obligatorios deben ser completados");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email no válido");
        }

        if (!in_array($role, ['user', 'admin'])) {
            throw new Exception("Rol no válido");
        }

        // Verificar que el usuario existe
        $existingUser = $userModel->getById($id);
        if (!$existingUser) {
            throw new Exception("Usuario no encontrado");
        }

        // Actualizar perfil básico
        $userModel->updateProfile($id, $username, $email);

        // Cambiar rol
        $userModel->changeRole($id, $role);

        // Cambiar contraseña si se proporciona
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                throw new Exception("La nueva contraseña debe tener al menos 6 caracteres");
            }
            
            // Actualizar contraseña directamente (privilegio de admin)
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
            $userModel->getDb()->execute($sql, [$hashedPassword, $id]);
        }

        $_SESSION['success'] = "Usuario '{$username}' actualizado exitosamente";
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: users.php');
exit;
?>
