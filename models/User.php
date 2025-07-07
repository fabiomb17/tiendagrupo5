<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Método para acceder a la base de datos desde el controlador
    public function getDb() {
        return $this->db;
    }

    // Crear usuario
    public function create($username, $email, $password, $role = 'user') {
        // Verificar si el usuario ya existe
        if ($this->existsByEmail($email) || $this->existsByUsername($username)) {
            throw new Exception("El usuario o email ya existe");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (username, email, password, role, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        try {
            $this->db->execute($sql, [$username, $email, $hashedPassword, $role]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }

    // Autenticar usuario
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND active = 1";
        $user = $this->db->fetch($sql, [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último login
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }

    // Obtener usuario por ID
    public function getById($id) {
        $sql = "SELECT id, username, email, role, created_at, last_login FROM {$this->table} 
                WHERE id = ? AND active = 1";
        return $this->db->fetch($sql, [$id]);
    }

    // Obtener usuario por email
    public function getByEmail($email) {
        $sql = "SELECT id, username, email, role, created_at, last_login FROM {$this->table} 
                WHERE email = ? AND active = 1";
        return $this->db->fetch($sql, [$email]);
    }

    // Obtener todos los usuarios
    public function getAll() {
        $sql = "SELECT id, username, email, role, active, created_at, last_login FROM {$this->table} 
                WHERE active = 1 ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    // Verificar si existe usuario por email
    public function existsByEmail($email) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ? AND active = 1";
        $result = $this->db->fetch($sql, [$email]);
        return $result['count'] > 0;
    }

    // Verificar si existe usuario por username
    public function existsByUsername($username) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ? AND active = 1";
        $result = $this->db->fetch($sql, [$username]);
        return $result['count'] > 0;
    }

    // Actualizar perfil de usuario
    public function updateProfile($id, $username, $email) {
        // Verificar que el nuevo email no esté en uso por otro usuario
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE email = ? AND id != ? AND active = 1";
        $result = $this->db->fetch($sql, [$email, $id]);
        
        if ($result['count'] > 0) {
            throw new Exception("El email ya está en uso por otro usuario");
        }

        $sql = "UPDATE {$this->table} SET username = ?, email = ?, updated_at = NOW() 
                WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$username, $email, $id]);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar perfil: " . $e->getMessage());
        }
    }

    // Cambiar contraseña
    public function changePassword($id, $currentPassword, $newPassword) {
        // Verificar contraseña actual
        $sql = "SELECT password FROM {$this->table} WHERE id = ? AND active = 1";
        $user = $this->db->fetch($sql, [$id]);
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            throw new Exception("La contraseña actual es incorrecta");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET password = ?, updated_at = NOW() WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$hashedPassword, $id]);
        } catch (Exception $e) {
            throw new Exception("Error al cambiar contraseña: " . $e->getMessage());
        }
    }

    // Actualizar último login
    private function updateLastLogin($id) {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = ?";
        $this->db->execute($sql, [$id]);
    }

    // Eliminar usuario (soft delete)
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET active = 0, updated_at = NOW() WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$id]);
        } catch (Exception $e) {
            throw new Exception("Error al eliminar usuario: " . $e->getMessage());
        }
    }

    // Cambiar rol de usuario
    public function changeRole($id, $role) {
        $allowedRoles = ['user', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            throw new Exception("Rol no válido");
        }

        $sql = "UPDATE {$this->table} SET role = ?, updated_at = NOW() WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$role, $id]);
        } catch (Exception $e) {
            throw new Exception("Error al cambiar rol: " . $e->getMessage());
        }
    }

    // Verificar si el usuario es admin
    public function isAdmin($id) {
        $sql = "SELECT role FROM {$this->table} WHERE id = ? AND active = 1";
        $user = $this->db->fetch($sql, [$id]);
        return $user && $user['role'] === 'admin';
    }
}
?>
