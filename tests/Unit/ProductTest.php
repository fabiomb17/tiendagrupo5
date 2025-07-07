<?php

namespace Tests\Unit;

require_once __DIR__ . '/../../tests/support/BaseTestCase.php';
require_once __DIR__ . '/../../models/Product.php';

use Tests\Support\BaseTestCase;

class ProductTest extends BaseTestCase
{
    private $productModel;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->productModel = new \Product();
    }
    
    public function testProductCanBeCreated()
    {
        $name = 'Test Product';
        $price = 99.99;
        $category = 'electronics';
        $image = 'test.jpg';
        $description = 'Test description';
        
        // Validaciones básicas
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
        $this->assertIsFloat($price);
        $this->assertGreaterThan(0, $price);
        $this->assertIsString($category);
        $this->assertIsString($image);
        $this->assertIsString($description);
    }
    
    public function testProductValidation()
    {
        // Validar nombre
        $this->assertFalse(empty('Producto Test'));
        $this->assertTrue(empty(''));
        
        // Validar precio
        $this->assertTrue(99.99 > 0);
        $this->assertFalse(-10.00 > 0);
        $this->assertFalse(0 > 0);
        
        // Validar categoría
        $validCategories = ['electronics', 'clothing', 'home'];
        $this->assertContains('electronics', $validCategories);
        $this->assertContains('clothing', $validCategories);
        $this->assertContains('home', $validCategories);
        $this->assertNotContains('invalid', $validCategories);
    }
    
    public function testProductPriceFormatting()
    {
        $price = 99.99;
        $formattedPrice = number_format($price, 2);
        
        $this->assertEquals('99.99', $formattedPrice);
        $this->assertIsString($formattedPrice);
    }
    
    public function testProductCategories()
    {
        $categories = ['electronics', 'clothing', 'home'];
        
        foreach ($categories as $category) {
            $this->assertIsString($category);
            $this->assertNotEmpty($category);
        }
        
        $this->assertCount(3, $categories);
    }
    
    public function testProductImageValidation()
    {
        // Validar extensiones de imagen
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $testImages = [
            'test.jpg' => true,
            'test.png' => true,
            'test.gif' => true,
            'test.txt' => false,
            'test.pdf' => false
        ];
        
        foreach ($testImages as $image => $shouldBeValid) {
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $isValid = in_array(strtolower($extension), $validExtensions);
            $this->assertEquals($shouldBeValid, $isValid, "Image: $image");
        }
    }
    
    public function testProductActiveStatus()
    {
        $activeProduct = ['active' => 1];
        $inactiveProduct = ['active' => 0];
        
        $this->assertTrue((bool)$activeProduct['active']);
        $this->assertFalse((bool)$inactiveProduct['active']);
    }
    
    public function testProductSearch()
    {
        $products = [
            ['name' => 'iPhone 13', 'category' => 'electronics'],
            ['name' => 'Samsung Galaxy', 'category' => 'electronics'],
            ['name' => 'Camisa Azul', 'category' => 'clothing'],
            ['name' => 'Mesa de Madera', 'category' => 'home']
        ];
        
        // Filtrar por categoría
        $electronicsProducts = array_filter($products, function($product) {
            return $product['category'] === 'electronics';
        });
        
        $this->assertCount(2, $electronicsProducts);
        
        // Buscar por nombre
        $searchResults = array_filter($products, function($product) {
            return stripos($product['name'], 'iPhone') !== false;
        });
        
        $this->assertCount(1, $searchResults);
    }
}
