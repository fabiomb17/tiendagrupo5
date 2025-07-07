<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Obtener todos los usuarios (solo admin)
    public function index() {
        AuthController::requireAdmin();
        
        try {
            $users = $this->userModel->getAll();
            return $this->jsonResponse(['success' => true, 'data' => $users]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener usuario por ID (solo admin)
    public function show() {
        AuthController::requireAdmin();
        
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de usuario requerido'], 400);
        }

        try {
            $user = $this->userModel->getById($id);
            
            if (!$user) {
                return $this->jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }
            
            return $this->jsonResponse(['success' => true, 'data' => $user]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Crear usuario (solo admin)
    public function create() {
        AuthController::requireAdmin();

        $data = $this->getPostData();
        
        // Validar datos requeridos
        $required = ['username', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->jsonResponse(['success' => false, 'message' => "Campo {$field} requerido"], 400);
            }
        }

        // Validaciones adicionales
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
        }

        if (strlen($data['password']) < 6) {
            return $this->jsonResponse(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
        }

        $role = $data['role'] ?? 'user';
        if (!in_array($role, ['user', 'admin'])) {
            return $this->jsonResponse(['success' => false, 'message' => 'Rol no válido'], 400);
        }

        try {
            $userId = $this->userModel->create(
                $data['username'],
                $data['email'],
                $data['password'],
                $role
            );
            
            return $this->jsonResponse([
                'success' => true, 
                'message' => 'Usuario creado exitosamente', 
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Actualizar usuario (solo admin)
    public function update() {
        AuthController::requireAdmin();

        $data = $this->getPostData();
        $id = $data['id'] ?? 0;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de usuario requerido'], 400);
        }

        // Verificar que el usuario existe
        $existingUser = $this->userModel->getById($id);
        if (!$existingUser) {
            return $this->jsonResponse(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Validaciones
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email inválido'], 400);
        }

        try {
            // Actualizar perfil básico
            if (!empty($data['username']) && !empty($data['email'])) {
                $this->userModel->updateProfile($id, $data['username'], $data['email']);
            }

            // Cambiar rol si se especifica
            if (!empty($data['role']) && in_array($data['role'], ['user', 'admin'])) {
                $this->userModel->changeRole($id, $data['role']);
            }

            // Cambiar contraseña si se proporciona
            if (!empty($data['new_password'])) {
                if (strlen($data['new_password']) < 6) {
                    return $this->jsonResponse(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'], 400);
                }
                
                // Para admin, permitir cambio directo sin contraseña actual
                // Esto es una funcionalidad especial de admin
                $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
                $this->userModel->getDb()->execute($sql, [$hashedPassword, $id]);
            }
            
            return $this->jsonResponse(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Eliminar usuario (solo admin)
    public function delete() {
        AuthController::requireAdmin();

        $data = $this->getPostData();
        $id = $data['id'] ?? 0;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de usuario requerido'], 400);
        }

        // No permitir que el admin se elimine a sí mismo
        if ($id == $_SESSION['user_id']) {
            return $this->jsonResponse(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta'], 400);
        }

        try {
            $this->userModel->delete($id);
            return $this->jsonResponse(['success' => true, 'message' => 'Usuario eliminado exitosamente']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Cambiar estado activo/inactivo
    public function toggleStatus() {
        AuthController::requireAdmin();

        $data = $this->getPostData();
        $id = $data['id'] ?? 0;
        $active = $data['active'] ?? true;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de usuario requerido'], 400);
        }

        // No permitir desactivar la propia cuenta
        if ($id == $_SESSION['user_id'] && !$active) {
            return $this->jsonResponse(['success' => false, 'message' => 'No puedes desactivar tu propia cuenta'], 400);
        }

        try {
            $sql = "UPDATE users SET active = ?, updated_at = NOW() WHERE id = ?";
            $this->userModel->getDb()->execute($sql, [$active ? 1 : 0, $id]);
            
            $status = $active ? 'activado' : 'desactivado';
            return $this->jsonResponse(['success' => true, 'message' => "Usuario {$status} exitosamente"]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener estadísticas de usuarios
    public function getStats() {
        AuthController::requireAdmin();

        try {
            $sql = "SELECT 
                        COUNT(*) as total_users,
                        COUNT(CASE WHEN role = 'admin' THEN 1 END) as admin_users,
                        COUNT(CASE WHEN role = 'user' THEN 1 END) as regular_users,
                        COUNT(CASE WHEN active = 1 THEN 1 END) as active_users,
                        COUNT(CASE WHEN active = 0 THEN 1 END) as inactive_users,
                        COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_today
                    FROM users";
            
            $stats = $this->userModel->getDb()->fetch($sql);
            
            return $this->jsonResponse(['success' => true, 'data' => $stats]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Métodos auxiliares
    private function getPostData() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }
        
        // Para APIs REST, obtener datos JSON
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
