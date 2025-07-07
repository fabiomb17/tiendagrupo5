<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Iniciar sesión
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email y contraseña son requeridos'], 400);
        }

        try {
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                return $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Inicio de sesión exitoso',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                return $this->jsonResponse(['success' => false, 'message' => 'Credenciales inválidas'], 401);
            }
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Cerrar sesión
    public function logout() {
        session_start();
        session_destroy();
        
        return $this->jsonResponse(['success' => true, 'message' => 'Sesión cerrada exitosamente']);
    }

    // Registrar usuario
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validaciones básicas
        if (empty($username) || empty($email) || empty($password)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Todos los campos son requeridos'], 400);
        }

        if ($password !== $confirmPassword) {
            return $this->jsonResponse(['success' => false, 'message' => 'Las contraseñas no coinciden'], 400);
        }

        if (strlen($password) < 6) {
            return $this->jsonResponse(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
        }

        try {
            $userId = $this->userModel->create($username, $email, $password);
            
            return $this->jsonResponse([
                'success' => true, 
                'message' => 'Usuario registrado exitosamente',
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Verificar sesión actual
    public function checkSession() {
        session_start();
        
        if (isset($_SESSION['user_id'])) {
            try {
                $user = $this->userModel->getById($_SESSION['user_id']);
                
                if ($user) {
                    return $this->jsonResponse([
                        'success' => true,
                        'authenticated' => true,
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'email' => $user['email'],
                            'role' => $user['role']
                        ]
                    ]);
                }
            } catch (Exception $e) {
                // En caso de error, limpiar sesión
                session_destroy();
            }
        }
        
        return $this->jsonResponse([
            'success' => true,
            'authenticated' => false
        ]);
    }

    // Cambiar contraseña
    public function changePassword() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'No autorizado'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Todos los campos son requeridos'], 400);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->jsonResponse(['success' => false, 'message' => 'Las nuevas contraseñas no coinciden'], 400);
        }

        if (strlen($newPassword) < 6) {
            return $this->jsonResponse(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres'], 400);
        }

        try {
            $this->userModel->changePassword($_SESSION['user_id'], $currentPassword, $newPassword);
            
            return $this->jsonResponse(['success' => true, 'message' => 'Contraseña actualizada exitosamente']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Actualizar perfil
    public function updateProfile() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'No autorizado'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($username) || empty($email)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Username y email son requeridos'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
        }

        try {
            $this->userModel->updateProfile($_SESSION['user_id'], $username, $email);
            
            // Actualizar datos de sesión
            $_SESSION['user'] = $username;
            $_SESSION['user_email'] = $email;
            
            return $this->jsonResponse(['success' => true, 'message' => 'Perfil actualizado exitosamente']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Middleware para verificar autenticación
    public static function requireAuth() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        
        return $_SESSION['user_id'];
    }

    // Middleware para verificar que sea admin
    public static function requireAdmin() {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
            exit;
        }
        
        return $_SESSION['user_id'];
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
