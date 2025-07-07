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
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        // Validaciones
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("Todos los campos obligatorios deben ser completados");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email no válido");
        }

        if (strlen($password) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres");
        }

        if (!in_array($role, ['user', 'admin'])) {
            throw new Exception("Rol no válido");
        }

        // Crear usuario
        $userId = $userModel->create($username, $email, $password, $role);

        $_SESSION['success'] = "Usuario '{$username}' creado exitosamente con ID: {$userId}";
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: users.php');
exit;
?>
