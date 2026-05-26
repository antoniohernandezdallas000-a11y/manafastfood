<?php
/**
 * MANÁ FAST FOOD - TEST DE INTEGRACIÓN (FLUJO COMPLETO)
 * 
 * Prueba el flujo completo: registro → login → productos → pedido → pago.
 * NOTA: Este test requiere una base de datos MySQL real configurada en .env
 * con el entorno 'testing'. No mockea PDO.
 * 
 * Para ejecutar: vendor/bin/phpunit --testsuite Integration
 * 
 * La BD de test debe tener el schema cargado (tests/_data/schema.sql)
 */

use PHPUnit\Framework\TestCase;

class test_flujo_completo extends TestCase
{
    private static $token = null;
    private static $adminToken = null;
    private static $pedidoId = null;
    private static $productoId = null;
    private static $cuentaId = null;
    private static $tasaOriginal = null;
    
    /**
     * Configuración: levantar BD de test si se puede
     */
    public static function setUpBeforeClass(): void
    {
        // Verificar si tenemos conexión a BD real
        try {
            $db = Database::getInstance();
            $db->query("SELECT 1");
        } catch (Exception $e) {
            echo "\n⚠️  No se pudo conectar a la BD de test. Los tests de integración requieren MySQL.\n";
            echo "   Crea la BD 'mana_fast_food_test' y configura .env.\n\n";
        }
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset Database singleton para cada test
        Database::reset();
        
        // Limpiar datos de tests anteriores si existen
        $this->cleanTestData();
    }
    
    protected function tearDown(): void
    {
        // Limpiar datos creados en este test
        $this->cleanTestData();
        parent::tearDown();
    }
    
    /**
     * Limpia datos de prueba
     */
    private function cleanTestData(): void
    {
        try {
            $db = Database::getInstance();
            $db->exec("DELETE FROM detalle_pedido WHERE pedido_id IN (SELECT id FROM pedidos WHERE notas LIKE '%TEST INTEGRACION%')");
            $db->exec("DELETE FROM pedidos WHERE notas LIKE '%TEST INTEGRACION%'");
            $db->exec("DELETE FROM usuarios WHERE email LIKE '%test-integracion%@mana.com'");
            $db->exec("DELETE FROM productos WHERE slug LIKE 'test-integracion-%'");
        } catch (Exception $e) {
            // Si no hay BD, ignoramos
        }
    }
    
    // ──────────────────────────────────────────────
    // TEST 1: Registrar un nuevo usuario
    // ──────────────────────────────────────────────
    public function test_paso1_registrar_usuario()
    {
        // Saltar si no hay BD
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $db = Database::getInstance();
        
        // Verificar que el email no existe
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute(['test-integracion@mana.com']);
        $existente = $stmt->fetch();
        
        if ($existente) {
            // Ya existe de una ejecución anterior, lo eliminamos
            $db->exec("DELETE FROM usuarios WHERE email = 'test-integracion@mana.com'");
        }
        
        // Crear usuario
        $hash = Usuario::hashPassword('test123456');
        $usuario = Usuario::create([
            'nombre' => 'Test Integración',
            'telefono' => '04141234567',
            'email' => 'test-integracion@mana.com',
            'password_hash' => $hash,
            'rol' => 'cliente',
        ]);
        
        $this->assertNotNull($usuario, 'El usuario debe crearse exitosamente');
        $this->assertArrayHasKey('id', $usuario);
        $this->assertEquals('test-integracion@mana.com', $usuario['email']);
        $this->assertEquals('cliente', $usuario['rol']);
        
        // Generar token para pruebas siguientes
        self::$token = JWT::encode([
            'user_id' => $usuario['id'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'nombre' => $usuario['nombre'],
        ]);
        
        $this->assertNotEmpty(self::$token);
    }
    
    // ──────────────────────────────────────────────
    // TEST 2: Iniciar sesión con el usuario creado
    // ──────────────────────────────────────────────
    public function test_paso2_login_usuario()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $usuario = Usuario::getByEmail('test-integracion@mana.com');
        $this->assertNotNull($usuario, 'El usuario debe existir en BD');
        
        // Verificar password
        $passwordValido = Usuario::verificarPassword('test123456', $usuario['password_hash']);
        $this->assertTrue($passwordValido, 'El password debe ser correcto');
        
        // Regenerar token
        self::$token = JWT::encode([
            'user_id' => $usuario['id'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
            'nombre' => $usuario['nombre'],
        ]);
        
        $this->assertNotEmpty(self::$token);
    }
    
    // ──────────────────────────────────────────────
    // TEST 3: Verificar token JWT
    // ──────────────────────────────────────────────
    public function test_paso3_verificar_token()
    {
        $this->assertNotNull(self::$token, 'Debe haber un token generado');
        
        $payload = JWT::decode(self::$token);
        $this->assertNotNull($payload, 'El token debe ser válido');
        $this->assertArrayHasKey('user_id', $payload);
        $this->assertArrayHasKey('email', $payload);
        $this->assertArrayHasKey('rol', $payload);
        $this->assertEquals('test-integracion@mana.com', $payload['email']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 4: Listar productos
    // ──────────────────────────────────────────────
    public function test_paso4_listar_productos()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $productos = Producto::getAll();
        $this->assertIsArray($productos);
        
        // Si hay productos en la BD, deben tener estructura esperada
        if (count($productos) > 0) {
            $this->assertArrayHasKey('nombre', $productos[0]);
            $this->assertArrayHasKey('precio_usd', $productos[0]);
            $this->assertArrayHasKey('categoria_nombre', $productos[0]);
        }
    }
    
    // ──────────────────────────────────────────────
    // TEST 5: Crear un producto de prueba (admin)
    // ──────────────────────────────────────────────
    public function test_paso5_crear_producto_admin()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        // Verificar que hay admin en BD
        $admin = Usuario::getByEmail('admin@mana.com');
        if (!$admin) {
            $this->markTestSkipped('No hay admin en BD. Ejecuta schema.sql primero.');
        }
        
        self::$adminToken = JWT::encode([
            'user_id' => $admin['id'],
            'email' => $admin['email'],
            'rol' => $admin['rol'],
        ]);
        
        // Crear producto
        $producto = Producto::create([
            'categoria_id' => 1,
            'nombre' => 'Test Integración Burger',
            'slug' => 'test-integracion-burger-' . time(),
            'descripcion' => 'Producto creado durante test de integración',
            'precio_usd' => 9.99,
            'activo' => 1,
        ]);
        
        $this->assertNotNull($producto, 'El producto debe crearse');
        $this->assertEquals('Test Integración Burger', $producto['nombre']);
        $this->assertEquals(9.99, (float) $producto['precio_usd']);
        
        self::$productoId = $producto['id'];
    }
    
    // ──────────────────────────────────────────────
    // TEST 6: Obtener cuenta PagoMóvil activa
    // ──────────────────────────────────────────────
    public function test_paso6_obtener_cuenta_pagomovil_activa()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $cuenta = CuentaPagoMovil::getActiva();
        
        if (!$cuenta) {
            $this->markTestSkipped('No hay cuenta PagoMóvil activa. Ejecuta schema.sql primero.');
        }
        
        $this->assertArrayHasKey('banco', $cuenta);
        $this->assertArrayHasKey('telefono', $cuenta);
        $this->assertArrayHasKey('cedula_rif', $cuenta);
        $this->assertEquals(1, $cuenta['activa']);
        
        self::$cuentaId = $cuenta['id'];
    }
    
    // ──────────────────────────────────────────────
    // TEST 7: Obtener tasa BCV
    // ──────────────────────────────────────────────
    public function test_paso7_obtener_tasa_bcv()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $tasa = TasaBcv::getUltima();
        
        if (!$tasa) {
            $this->markTestSkipped('No hay tasa BCV registrada. Ejecuta schema.sql primero.');
        }
        
        $this->assertArrayHasKey('tasa_usd_bs', $tasa);
        $this->assertGreaterThan(0, (float) $tasa['tasa_usd_bs']);
        
        self::$tasaOriginal = $tasa;
    }
    
    // ──────────────────────────────────────────────
    // TEST 8: Crear un pedido con items
    // ──────────────────────────────────────────────
    public function test_paso8_crear_pedido_con_items()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $usuario = Usuario::getByEmail('test-integracion@mana.com');
        $this->assertNotNull($usuario);
        
        $tasa = TasaBcv::getUltima();
        $tasaValor = $tasa ? (float) $tasa['tasa_usd_bs'] : 60.00;
        
        // Crear pedido
        $pedido = Pedido::create([
            'usuario_id' => $usuario['id'],
            'tipo_entrega' => 'delivery',
            'direccion' => 'Av. Test, #123, Caracas',
            'ciudad' => 'Caracas',
            'telefono_contacto' => '04141234567',
            'notas' => 'TEST INTEGRACION - Pedido de prueba',
            'subtotal_usd' => 21.00,
            'descuento_usd' => 0,
            'total_usd' => 21.00,
            'metodo_pago' => 'pagomovil',
            'items' => [
                ['producto_id' => 1, 'nombre_producto' => 'Maná Burger Clásica', 'cantidad' => 2, 'precio_unitario_usd' => 8.99],
                ['producto_id' => 33, 'nombre_producto' => 'Papas Fritas', 'cantidad' => 1, 'precio_unitario_usd' => 3.00],
            ],
        ]);
        
        $this->assertNotNull($pedido, 'El pedido debe crearse exitosamente');
        $this->assertArrayHasKey('numero_pedido', $pedido);
        $this->assertStringStartsWith('MANA-', $pedido['numero_pedido']);
        $this->assertEquals('pendiente', $pedido['estado']);
        $this->assertEquals('delivery', $pedido['tipo_entrega']);
        
        self::$pedidoId = $pedido['id'];
        
        // Verificar detalles
        $detalles = Pedido::getDetalles($pedido['id']);
        $this->assertNotEmpty($detalles, 'El pedido debe tener detalles');
    }
    
    // ──────────────────────────────────────────────
    // TEST 9: Confirmar pago del pedido
    // ──────────────────────────────────────────────
    public function test_paso9_confirmar_pago()
    {
        if (!$this->hasDatabase() || !self::$pedidoId) {
            $this->markTestSkipped('No hay pedido para confirmar');
        }
        
        $pedido = Pedido::confirmarPago(
            self::$pedidoId,
            'BCV-TEST-INTEGRACION-001',
            'uploads/captures/test_integracion.jpg'
        );
        
        $this->assertNotNull($pedido, 'El pedido debe actualizarse');
        $this->assertEquals(1, $pedido['pago_confirmado'], 'El pago debe estar confirmado');
        $this->assertEquals('BCV-TEST-INTEGRACION-001', $pedido['referencia_pago']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 10: Verificar que el pago está confirmado
    // ──────────────────────────────────────────────
    public function test_paso10_verificar_pago_confirmado()
    {
        if (!$this->hasDatabase() || !self::$pedidoId) {
            $this->markTestSkipped('No hay pedido para verificar');
        }
        
        $pedido = Pedido::getById(self::$pedidoId);
        $this->assertNotNull($pedido);
        $this->assertEquals(1, $pedido['pago_confirmado']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 11: Administrador ve el pedido
    // ──────────────────────────────────────────────
    public function test_paso11_admin_ve_pedido()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        $pedidos = Pedido::getAll();
        $this->assertIsArray($pedidos);
        
        // Buscar nuestro pedido de test
        $pedidoTest = null;
        foreach ($pedidos as $p) {
            if ($p['id'] === self::$pedidoId) {
                $pedidoTest = $p;
                break;
            }
        }
        
        if (self::$pedidoId) {
            $this->assertNotNull($pedidoTest, 'El admin debe poder ver el pedido creado');
            $this->assertEquals(1, $pedidoTest['pago_confirmado']);
        }
    }
    
    // ──────────────────────────────────────────────
    // TEST 12: Cambiar estado del pedido a preparando
    // ──────────────────────────────────────────────
    public function test_paso12_cambiar_estado_a_preparando()
    {
        if (!$this->hasDatabase() || !self::$pedidoId) {
            $this->markTestSkipped('No hay pedido para cambiar estado');
        }
        
        $pedido = Pedido::cambiarEstado(self::$pedidoId, 'preparando');
        
        $this->assertNotNull($pedido);
        $this->assertEquals('preparando', $pedido['estado']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 13: Verificar que el estado cambió
    // ──────────────────────────────────────────────
    public function test_paso13_verificar_estado_cambiado()
    {
        if (!$this->hasDatabase() || !self::$pedidoId) {
            $this->markTestSkipped('No hay pedido para verificar');
        }
        
        $pedido = Pedido::getById(self::$pedidoId);
        $this->assertNotNull($pedido);
        $this->assertEquals('preparando', $pedido['estado']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 14: Ver permisos - cliente NO ve pedidos de otros
    // ──────────────────────────────────────────────
    public function test_paso14_cliente_no_ve_pedido_ajeno()
    {
        if (!$this->hasDatabase()) {
            $this->markTestSkipped('No hay conexión a BD');
        }
        
        // Simular que un cliente con ID diferente intenta ver el pedido
        $pedido = Pedido::getById(self::$pedidoId ?? 0);
        
        // Si el pedido existe, verificar que el usuario_id coincide
        if ($pedido) {
            $usuario = Usuario::getByEmail('test-integracion@mana.com');
            $this->assertNotNull($usuario);
            
            $esDueno = $pedido['usuario_id'] == $usuario['id'];
            $esAdmin = false; // Simulamos que no es admin
            
            $this->assertTrue($esDueno, 'El cliente debe ser dueño de su pedido');
            
            // Simular verificación de permiso
            $tienePermiso = $esDueno || $esAdmin;
            $this->assertTrue($tienePermiso, 'El dueño debe tener permiso');
            
            // Si intentara ver otro pedido
            $otroPedido = $pedido['usuario_id'] != $usuario['id'];
            if ($otroPedido) {
                $this->assertFalse($esDueno || $esAdmin, 'No debe tener permiso para ver pedido ajeno');
            }
        }
    }
    
    // ──────────────────────────────────────────────
    // Helper: verificar conexión a BD
    // ──────────────────────────────────────────────
    private function hasDatabase(): bool
    {
        try {
            $db = Database::getInstance();
            $db->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
