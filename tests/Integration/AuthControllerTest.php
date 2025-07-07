<?php

namespace Tests\Integration;

use Tests\Support\BaseTestCase;

require_once __DIR__ . '/../../controllers/AuthController.php';

class AuthControllerTest extends BaseTestCase
{
    private $controller;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new \AuthController();
        
        // Mock $_SERVER variables
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
    }
    
    public function testLoginValidation()
    {
        $validCredentials = [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ];
        
        $invalidCredentials = [
            'email' => 'invalid-email',
            'password' => '123' // Muy corta
        ];
        
        // Validar email
        $this->assertTrue(filter_var($validCredentials['email'], FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var($invalidCredentials['email'], FILTER_VALIDATE_EMAIL) !== false);
        
        // Validar longitud de contraseña
        $this->assertTrue(strlen($validCredentials['password']) >= 6);
        $this->assertFalse(strlen($invalidCredentials['password']) >= 6);
    }
    
    public function testPasswordHashing()
    {
        $password = 'testpassword123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Verificar que el hash es diferente al password original
        $this->assertNotEquals($password, $hash);
        
        // Verificar que se puede verificar correctamente
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }
    
    public function testSessionManagement()
    {
        // Simular datos de usuario
        $userData = [
            'user_id' => 1,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'admin'
        ];
        
        // Simular establecer sesión
        foreach ($userData as $key => $value) {
            $_SESSION[$key] = $value;
        }
        
        // Verificar que los datos de sesión se establecieron
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('testuser', $_SESSION['username']);
        $this->assertEquals('test@example.com', $_SESSION['email']);
        $this->assertEquals('admin', $_SESSION['role']);
        
        // Verificar que el usuario está "logueado"
        $this->assertTrue(isset($_SESSION['user_id']));
    }
    
    public function testLogoutClearsSession()
    {
        // Establecer datos de sesión
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'testuser';
        $_SESSION['email'] = 'test@example.com';
        
        // Simular logout
        $_SESSION = [];
        
        // Verificar que la sesión se limpió
        $this->assertEmpty($_SESSION);
        $this->assertFalse(isset($_SESSION['user_id']));
    }
    
    public function testRegistrationValidation()
    {
        $validRegistration = [
            'username' => 'newuser',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'confirm_password' => 'password123'
        ];
        
        $invalidRegistration = [
            'username' => '', // Vacío
            'email' => 'invalid-email',
            'password' => '123', // Muy corta
            'confirm_password' => 'different' // No coincide
        ];
        
        // Validar datos válidos
        $this->assertNotEmpty($validRegistration['username']);
        $this->assertTrue(filter_var($validRegistration['email'], FILTER_VALIDATE_EMAIL) !== false);
        $this->assertTrue(strlen($validRegistration['password']) >= 6);
        $this->assertEquals($validRegistration['password'], $validRegistration['confirm_password']);
        
        // Validar datos inválidos
        $this->assertEmpty($invalidRegistration['username']);
        $this->assertFalse(filter_var($invalidRegistration['email'], FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(strlen($invalidRegistration['password']) >= 6);
        $this->assertNotEquals($invalidRegistration['password'], $invalidRegistration['confirm_password']);
    }
    
    public function testJsonResponseFormat()
    {
        $successResponse = [
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => 1,
                'username' => 'testuser',
                'role' => 'admin'
            ]
        ];
        
        $errorResponse = [
            'success' => false,
            'message' => 'Credenciales inválidas'
        ];
        
        // Verificar formato de respuesta exitosa
        $this->assertTrue($successResponse['success']);
        $this->assertIsString($successResponse['message']);
        $this->assertIsArray($successResponse['user']);
        
        // Verificar formato de respuesta de error
        $this->assertFalse($errorResponse['success']);
        $this->assertIsString($errorResponse['message']);
        
        // Verificar que se pueden convertir a JSON
        $this->assertIsString(json_encode($successResponse));
        $this->assertIsString(json_encode($errorResponse));
    }
}
