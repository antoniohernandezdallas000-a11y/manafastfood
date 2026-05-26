<?php
/**
 * MANÁ FAST FOOD - TEST DE PRODUCTOS
 * 
 * Pruebas unitarias del modelo Producto.
 * Mockeamos PDO para no depender de BD real.
 */

use PHPUnit\Framework\TestCase;

class test_productos extends TestCase
{
    private $fixtures;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = require __DIR__ . '/../fixtures/data.php';
        Database::reset();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        Database::reset();
    }
    
    /**
     * Helper para crear mock de PDO con fetchAll configurable
     */
    private function createMockPdoWithFetchAll(array $fetchAllResult, array $fetchResult = [])
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetchAll')->willReturn($fetchAllResult);
        $mockStmt->method('fetch')->willReturn($fetchResult ?: false);
        $mockStmt->method('rowCount')->willReturn(count($fetchAllResult));
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        $mockPdo->method('query')->willReturn($mockStmt);
        $mockPdo->method('lastInsertId')->willReturn('999');
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        return $mockPdo;
    }
    
    // ──────────────────────────────────────────────
    // TEST 1: Listar productos activos
    // ──────────────────────────────────────────────
    public function test_listar_productos_activos()
    {
        $productosMock = [
            [
                'id' => 1, 'categoria_id' => 1, 'nombre' => 'Maná Burger',
                'slug' => 'mana-burger', 'precio_usd' => '7.00',
                'activo' => 1, 'en_oferta' => 0,
                'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
            ],
            [
                'id' => 2, 'categoria_id' => 1, 'nombre' => 'Maná Doble',
                'slug' => 'mana-doble', 'precio_usd' => '11.50',
                'activo' => 1, 'en_oferta' => 0,
                'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
            ],
        ];
        
        $this->createMockPdoWithFetchAll($productosMock);
        
        $resultados = Producto::getAll();
        
        $this->assertCount(2, $resultados);
        $this->assertEquals('Maná Burger', $resultados[0]['nombre']);
        $this->assertEquals(1, $resultados[0]['activo']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 2: Listar productos por categoría
    // ──────────────────────────────────────────────
    public function test_listar_productos_por_categoria()
    {
        $productosMock = [
            [
                'id' => 1, 'categoria_id' => 1, 'nombre' => 'Maná Burger',
                'slug' => 'mana-burger', 'precio_usd' => '7.00',
                'activo' => 1,
                'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
            ],
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetchAll')->willReturn($productosMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $resultados = Producto::getByCategoria(1);
        
        $this->assertCount(1, $resultados);
        $this->assertEquals(1, $resultados[0]['categoria_id']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 3: Buscar productos por nombre
    // ──────────────────────────────────────────────
    public function test_buscar_productos_por_nombre()
    {
        $productosMock = [
            [
                'id' => 1, 'nombre' => 'Maná Burger Clásica',
                'slug' => 'mana-burger-clasica', 'precio_usd' => '8.99',
                'activo' => 1,
                'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
            ],
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetchAll')->willReturn($productosMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $resultados = Producto::search('Burger');
        
        $this->assertCount(1, $resultados);
        $this->assertStringContainsString('Burger', $resultados[0]['nombre']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 4: Obtener detalle de producto existente
    // ──────────────────────────────────────────────
    public function test_obtener_detalle_producto_existente()
    {
        $productoMock = [
            'id' => 1, 'categoria_id' => 1, 'nombre' => 'Maná Burger Clásica',
            'slug' => 'mana-burger-clasica', 'descripcion' => 'La clásica',
            'precio_usd' => '8.99', 'activo' => 1,
            'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn($productoMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $producto = Producto::getById(1);
        
        $this->assertNotNull($producto);
        $this->assertEquals('Maná Burger Clásica', $producto['nombre']);
        $this->assertEquals(8.99, (float) $producto['precio_usd']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 5: Obtener detalle de producto inexistente
    // ──────────────────────────────────────────────
    public function test_obtener_detalle_producto_inexistente_retorna_null()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false); // No encontrado
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $producto = Producto::getById(99999);
        $this->assertNull($producto, 'Producto inexistente debe retornar null');
    }
    
    // ──────────────────────────────────────────────
    // TEST 6: Crear producto (admin)
    // ──────────────────────────────────────────────
    public function test_crear_producto()
    {
        $fixture = $this->fixtures['producto_valido'];
        
        $mockStmtInsert = $this->createMock(PDOStatement::class);
        $mockStmtInsert->method('execute')->willReturn(true);
        
        $mockStmtFetch = $this->createMock(PDOStatement::class);
        $mockStmtFetch->method('execute')->willReturn(true);
        $mockStmtFetch->method('fetch')->willReturn([
            'id' => 999,
            'categoria_id' => $fixture['categoria_id'],
            'nombre' => $fixture['nombre'],
            'slug' => $fixture['slug'],
            'descripcion' => $fixture['descripcion'],
            'precio_usd' => (string) $fixture['precio_usd'],
            'activo' => 1,
            'categoria_nombre' => 'Hamburguesas',
            'categoria_slug' => 'hamburguesas',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturnOnConsecutiveCalls($mockStmtInsert, $mockStmtFetch);
        $mockPdo->method('lastInsertId')->willReturn('999');
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $producto = Producto::create($fixture);
        
        $this->assertNotNull($producto);
        $this->assertEquals($fixture['nombre'], $producto['nombre']);
        $this->assertEquals(999, $producto['id']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 7: Editar producto
    // ──────────────────────────────────────────────
    public function test_editar_producto()
    {
        $updateData = $this->fixtures['producto_update_data'];
        
        $mockStmtUpdate = $this->createMock(PDOStatement::class);
        $mockStmtUpdate->method('execute')->willReturn(true);
        
        $mockStmtFetch = $this->createMock(PDOStatement::class);
        $mockStmtFetch->method('execute')->willReturn(true);
        $mockStmtFetch->method('fetch')->willReturn([
            'id' => 1,
            'nombre' => $updateData['nombre'],
            'precio_usd' => (string) $updateData['precio_usd'],
            'descripcion' => $updateData['descripcion'],
            'activo' => 1,
            'categoria_nombre' => 'Hamburguesas',
            'categoria_slug' => 'hamburguesas',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturnOnConsecutiveCalls($mockStmtUpdate, $mockStmtFetch);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $productoActualizado = Producto::update(1, $updateData);
        
        $this->assertNotNull($productoActualizado);
        $this->assertEquals($updateData['nombre'], $productoActualizado['nombre']);
        $this->assertEquals($updateData['precio_usd'], (float) $productoActualizado['precio_usd']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 8: Eliminar producto
    // ──────────────────────────────────────────────
    public function test_eliminar_producto()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('rowCount')->willReturn(1);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $resultado = Producto::delete(1);
        $this->assertTrue($resultado, 'Eliminar producto debe retornar true');
    }
    
    // ──────────────────────────────────────────────
    // TEST 9: Contar productos activos
    // ──────────────────────────────────────────────
    public function test_contar_productos_activos()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetch')->willReturn(['total' => 15]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $total = Producto::countActivos();
        $this->assertEquals(15, $total);
    }
    
    // ──────────────────────────────────────────────
    // TEST 10: Obtener productos en oferta
    // ──────────────────────────────────────────────
    public function test_obtener_productos_en_oferta()
    {
        $productosMock = [
            [
                'id' => 1, 'nombre' => 'Maná Burger Oferta',
                'slug' => 'mana-burger-oferta', 'precio_usd' => '10.00',
                'en_oferta' => 1, 'descuento_porcentaje' => '20.00',
                'activo' => 1,
                'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
            ],
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetchAll')->willReturn($productosMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $resultados = Producto::getEnOferta();
        
        $this->assertCount(1, $resultados);
        $this->assertEquals(1, $resultados[0]['en_oferta']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 11: Obtener producto por slug
    // ──────────────────────────────────────────────
    public function test_obtener_producto_por_slug()
    {
        $productoMock = [
            'id' => 1, 'nombre' => 'Maná Burger',
            'slug' => 'mana-burger', 'precio_usd' => '7.00',
            'activo' => 1,
            'categoria_nombre' => 'Hamburguesas', 'categoria_slug' => 'hamburguesas',
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn($productoMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $producto = Producto::getBySlug('mana-burger');
        
        $this->assertNotNull($producto);
        $this->assertEquals('mana-burger', $producto['slug']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 12: Listar todos los productos (admin)
    // ──────────────────────────────────────────────
    public function test_listar_todos_los_productos_admin()
    {
        $productosMock = [
            [
                'id' => 1, 'nombre' => 'Producto Activo',
                'activo' => 1,
                'categoria_nombre' => 'Hamburguesas',
            ],
            [
                'id' => 2, 'nombre' => 'Producto Inactivo',
                'activo' => 0,
                'categoria_nombre' => 'Hamburguesas',
            ],
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetchAll')->willReturn($productosMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $resultados = Producto::getAllAdmin();
        
        $this->assertCount(2, $resultados);
        // El admin ve activos e inactivos
        $activos = array_filter($resultados, fn($p) => $p['activo'] == 1);
        $inactivos = array_filter($resultados, fn($p) => $p['activo'] == 0);
        $this->assertCount(1, $activos);
        $this->assertCount(1, $inactivos);
    }
}
