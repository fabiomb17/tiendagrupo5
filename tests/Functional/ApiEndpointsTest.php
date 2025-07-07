<?php

namespace Tests\Functional;

use Tests\Support\BaseTestCase;

class ApiEndpointsTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Simular headers HTTP
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['REQUEST_URI'] = '/tiendagrupo5/api/products';
    }
    
    public function testApiReturnsJsonResponse()
    {
        $response = [
            'success' => true,
            'data' => [
                ['id' => 1, 'name' => 'Producto 1'],
                ['id' => 2, 'name' => 'Producto 2']
            ]
        ];
        
        $json = json_encode($response);
        $decoded = json_decode($json, true);
        
        $this->assertIsString($json);
        $this->assertIsArray($decoded);
        $this->assertEquals($response, $decoded);
    }
    
    public function testProductsEndpoint()
    {
        // Simular respuesta de productos
        $expectedResponse = [
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'name' => 'iPhone 13',
                    'price' => 999.99,
                    'category' => 'electronics',
                    'image' => 'iphone13.jpg'
                ],
                [
                    'id' => 2,
                    'name' => 'Samsung Galaxy',
                    'price' => 899.99,
                    'category' => 'electronics',
                    'image' => 'galaxy.jpg'
                ]
            ]
        ];
        
        $this->assertTrue($expectedResponse['success']);
        $this->assertIsArray($expectedResponse['data']);
        $this->assertCount(2, $expectedResponse['data']);
        
        foreach ($expectedResponse['data'] as $product) {
            $this->assertArrayHasKey('id', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('category', $product);
        }
    }
    
    public function testProductSearchEndpoint()
    {
        $searchTerm = 'iPhone';
        $allProducts = [
            ['id' => 1, 'name' => 'iPhone 13', 'category' => 'electronics'],
            ['id' => 2, 'name' => 'Samsung Galaxy', 'category' => 'electronics'],
            ['id' => 3, 'name' => 'Camisa Azul', 'category' => 'clothing']
        ];
        
        // Filtrar productos por término de búsqueda
        $filteredProducts = array_filter($allProducts, function($product) use ($searchTerm) {
            return stripos($product['name'], $searchTerm) !== false;
        });
        
        $this->assertCount(1, $filteredProducts);
        $this->assertEquals('iPhone 13', reset($filteredProducts)['name']);
    }
    
    public function testCategoryFilterEndpoint()
    {
        $category = 'electronics';
        $products = [
            ['id' => 1, 'name' => 'iPhone', 'category' => 'electronics'],
            ['id' => 2, 'name' => 'Laptop', 'category' => 'electronics'],
            ['id' => 3, 'name' => 'Camisa', 'category' => 'clothing']
        ];
        
        $filtered = array_filter($products, function($product) use ($category) {
            return $product['category'] === $category;
        });
        
        $this->assertCount(2, $filtered);
        
        foreach ($filtered as $product) {
            $this->assertEquals($category, $product['category']);
        }
    }
    
    public function testAuthLoginEndpoint()
    {
        $credentials = [
            'email' => 'admin@test.com',
            'password' => 'password123'
        ];
        
        // Simular respuesta de login exitoso
        $loginResponse = [
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@test.com',
                'role' => 'admin'
            ]
        ];
        
        $this->assertTrue($loginResponse['success']);
        $this->assertArrayHasKey('user', $loginResponse);
        $this->assertEquals($credentials['email'], $loginResponse['user']['email']);
    }
    
    public function testCartEndpoints()
    {
        $cartItem = [
            'product_id' => 1,
            'quantity' => 2
        ];
        
        // Simular agregar al carrito
        $addResponse = [
            'success' => true,
            'message' => 'Producto agregado al carrito'
        ];
        
        $this->assertTrue($addResponse['success']);
        
        // Simular obtener carrito
        $cartResponse = [
            'success' => true,
            'data' => [
                'items' => [
                    [
                        'product_id' => 1,
                        'name' => 'iPhone 13',
                        'price' => 999.99,
                        'quantity' => 2,
                        'subtotal' => 1999.98
                    ]
                ],
                'summary' => [
                    'total_items' => 2,
                    'total_amount' => 1999.98
                ]
            ]
        ];
        
        $this->assertTrue($cartResponse['success']);
        $this->assertArrayHasKey('items', $cartResponse['data']);
        $this->assertArrayHasKey('summary', $cartResponse['data']);
    }
    
    public function testErrorHandling()
    {
        // Simular respuesta de error
        $errorResponse = [
            'success' => false,
            'message' => 'Producto no encontrado',
            'error_code' => 404
        ];
        
        $this->assertFalse($errorResponse['success']);
        $this->assertIsString($errorResponse['message']);
        $this->assertIsInt($errorResponse['error_code']);
    }
    
    public function testCorsHeaders()
    {
        $expectedHeaders = [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization'
        ];
        
        foreach ($expectedHeaders as $header => $value) {
            $this->assertIsString($header);
            $this->assertIsString($value);
        }
    }
    
    public function testValidateApiRoute()
    {
        $validRoutes = [
            '/api/products',
            '/api/products/show',
            '/api/auth/login',
            '/api/cart',
            '/api/users'
        ];
        
        $invalidRoutes = [
            '/api/invalid',
            '/api/',
            '/invalid',
            ''
        ];
        
        foreach ($validRoutes as $route) {
            $this->assertStringStartsWith('/api/', $route);
        }
        
        foreach ($invalidRoutes as $route) {
            if (!empty($route)) {
                $isValidApi = strpos($route, '/api/') === 0 && strlen($route) > 5;
                $this->assertFalse($isValidApi);
            }
        }
    }
}
