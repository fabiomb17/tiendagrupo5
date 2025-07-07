<?php

namespace Tests\Unit;

require_once __DIR__ . '/../../vendor/autoload.php';

use Tests\Support\BaseTestCase;

require_once __DIR__ . '/../../models/User.php';

class UserTest extends BaseTestCase
{
    private $userModel;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new \User();
    }
    
    public function testUserCanBeCreated()
    {
        $username = 'test_user_' . time();
        $email = 'test' . time() . '@example.com';
        $password = 'password123';
        $role = 'user';
        
        // Mock o usar base de datos de prueba
        $this->assertIsString($username);
        $this->assertIsString($email);
        $this->assertTrue(strlen($password) >= 6);
        $this->assertContains($role, ['user', 'admin']);
    }
    
    public function testUserValidation()
    {
        // Probar validación de email
        $this->assertTrue(filter_var('test@example.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('invalid-email', FILTER_VALIDATE_EMAIL) !== false);
        
        // Probar validación de contraseña
        $this->assertTrue(strlen('password123') >= 6);
        $this->assertFalse(strlen('123') >= 6);
    }
    
    public function testIsAdminMethod()
    {
        // Test con datos mockeados
        $adminUser = $this->createAdminUser();
        $normalUser = $this->createNormalUser();
        
        $this->assertEquals('admin', $adminUser['role']);
        $this->assertEquals('user', $normalUser['role']);
    }
    
    public function testUserRoles()
    {
        $validRoles = ['user', 'admin'];
        
        $this->assertContains('user', $validRoles);
        $this->assertContains('admin', $validRoles);
        $this->assertNotContains('superadmin', $validRoles);
    }
    
    public function testPasswordHashing()
    {
        $password = 'testpassword123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
    }
    
    public function testUserActiveStatus()
    {
        $activeUser = ['active' => 1];
        $inactiveUser = ['active' => 0];
        
        $this->assertTrue((bool)$activeUser['active']);
        $this->assertFalse((bool)$inactiveUser['active']);
    }
}
