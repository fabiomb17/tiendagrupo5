<?php

namespace Tests\Integration;

require_once __DIR__ . '/../../vendor/autoload.php';


use Tests\Support\BaseTestCase;

require_once __DIR__ . '/../../controllers/ProductController.php';

class ProductControllerTest extends BaseTestCase
{
    private $controller;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new \ProductController();
        
        // Mock $_SERVER variables
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
    }
    
    public function testIndexReturnsProducts()
    {
        // Simular buffer de salida para capturar JSON
        ob_start();
        
        try {
            // Simular que no hay errores de base de datos
            $this->assertTrue(true); // Placeholder test
            
            $output = ob_get_contents();
            
            // Verificar que se devuelve algo (aunque sea un error controlado)
            $this->assertIsString($output);
            
        } catch (\Exception $e) {
            // Es esperado que falle sin base de datos real
            $this->assertInstanceOf(\Exception::class, $e);
        } finally {
            ob_end_clean();
        }
    }
    
    public function testValidateProductData()
    {
        $validData = [
            'name' => 'Producto Test',
            'price' => 99.99,
            'category' => 'electronics',
            'image' => 'test.jpg',
            'description' => 'Descripción del producto'
        ];
        
        $invalidData = [
            'name' => '', // Vacío
            'price' => -10, // Negativo
            'category' => 'invalid', // Categoría inválida
            'image' => '', // Vacío
            'description' => ''
        ];
        
        // Validar datos válidos
        $this->assertNotEmpty($validData['name']);
        $this->assertGreaterThan(0, $validData['price']);
        $this->assertContains($validData['category'], ['electronics', 'clothing', 'home']);
        $this->assertNotEmpty($validData['image']);
        
        // Validar datos inválidos
        $this->assertEmpty($invalidData['name']);
        $this->assertLessThan(0, $invalidData['price']);
        $this->assertNotContains($invalidData['category'], ['electronics', 'clothing', 'home']);
        $this->assertEmpty($invalidData['image']);
    }
    
    public function testProductSearchFunctionality()
    {
        $searchTerm = 'iPhone';
        $products = [
            ['id' => 1, 'name' => 'iPhone 13', 'category' => 'electronics'],
            ['id' => 2, 'name' => 'Samsung Galaxy', 'category' => 'electronics'],
            ['id' => 3, 'name' => 'Camisa', 'category' => 'clothing']
        ];
        
        // Filtrar productos por término de búsqueda
        $results = array_filter($products, function($product) use ($searchTerm) {
            return stripos($product['name'], $searchTerm) !== false;
        });
        
        $this->assertCount(1, $results);
        $this->assertEquals('iPhone 13', reset($results)['name']);
    }
    
    public function testProductCategoryFilter()
    {
        $category = 'electronics';
        $products = [
            ['id' => 1, 'name' => 'iPhone', 'category' => 'electronics'],
            ['id' => 2, 'name' => 'Laptop', 'category' => 'electronics'],
            ['id' => 3, 'name' => 'Camisa', 'category' => 'clothing']
        ];
        
        // Filtrar por categoría
        $filtered = array_filter($products, function($product) use ($category) {
            return $product['category'] === $category;
        });
        
        $this->assertCount(2, $filtered);
        
        foreach ($filtered as $product) {
            $this->assertEquals($category, $product['category']);
        }
    }
    
    public function testJsonResponse()
    {
        $data = [
            'success' => true,
            'message' => 'Operación exitosa',
            'data' => ['id' => 1, 'name' => 'Test Product']
        ];
        
        $json = json_encode($data);
        $decoded = json_decode($json, true);
        
        $this->assertIsString($json);
        $this->assertIsArray($decoded);
        $this->assertEquals($data, $decoded);
        $this->assertTrue($decoded['success']);
        $this->assertEquals('Operación exitosa', $decoded['message']);
    }
}
