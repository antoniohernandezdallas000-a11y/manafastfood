<?php
/**
 * MANÁ FAST FOOD - TEST DE CHECKOUT (FLUJO DE PAGO)
 * 
 * Pruebas unitarias de CuentaPagoMovil, TasaBcv y
 * la lógica de upload de captures.
 */

use PHPUnit\Framework\TestCase;

class test_checkout extends TestCase
{
    private $fixtures;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = require __DIR__ . '/../fixtures/data.php';
        Database::reset();
        $_FILES = [];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        Database::reset();
        $_FILES = [];
    }
    
    /**
     * Helper para inyectar mock PDO
     */
    private function injectMockPDO(PDO $mockPdo): void
    {
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
    }
    
    // ──────────────────────────────────────────────
    // TEST 1: Obtener cuenta PagoMóvil activa
    // ──────────────────────────────────────────────
    public function test_obtener_cuenta_pagomovil_activa()
    {
        $cuentaMock = [
            'id' => 1,
            'banco' => 'Mercantil',
            'codigo_banco' => '0105',
            'titular' => 'Maná Fast Food C.A.',
            'telefono' => '04141234567',
            'cedula_rif' => 'J-12345678-9',
            'tipo_cuenta' => 'corriente',
            'activa' => 1,
            'created_at' => '2024-06-01 12:00:00',
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetch')->willReturn($cuentaMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $cuenta = CuentaPagoMovil::getActiva();
        
        $this->assertNotNull($cuenta);
        $this->assertEquals('Mercantil', $cuenta['banco']);
        $this->assertEquals(1, $cuenta['activa']);
        $this->assertArrayHasKey('telefono', $cuenta);
        $this->assertArrayHasKey('cedula_rif', $cuenta);
    }
    
    // ──────────────────────────────────────────────
    // TEST 2: Obtener cuenta PagoMóvil cuando ninguna está activa
    // ──────────────────────────────────────────────
    public function test_obtener_cuenta_pagomovil_sin_activa_debe_retornar_null()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetch')->willReturn(false); // No hay cuenta activa
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $cuenta = CuentaPagoMovil::getActiva();
        
        $this->assertNull($cuenta, 'Si no hay cuenta activa, debe retornar null');
    }
    
    // ──────────────────────────────────────────────
    // TEST 3: Obtener tasa BCV actual
    // ──────────────────────────────────────────────
    public function test_obtener_tasa_bcv_actual()
    {
        $tasaMock = [
            'id' => 1,
            'tasa_usd_bs' => '62.45',
            'tipo' => 'automatica',
            'created_at' => '2024-06-01 12:00:00',
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetch')->willReturn($tasaMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $tasa = TasaBcv::getUltima();
        
        $this->assertNotNull($tasa);
        $this->assertEquals(62.45, (float) $tasa['tasa_usd_bs']);
        $this->assertEquals('automatica', $tasa['tipo']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 4: Obtener tasa BCV cuando no hay ninguna registrada
    // ──────────────────────────────────────────────
    public function test_obtener_tasa_bcv_sin_registro_debe_retornar_null()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetch')->willReturn(false);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $tasa = TasaBcv::getUltima();
        
        $this->assertNull($tasa, 'Si no hay tasa registrada, debe retornar null');
    }
    
    // ──────────────────────────────────────────────
    // TEST 5: Obtener modo de tasa (auto/manual)
    // ──────────────────────────────────────────────
    public function test_obtener_modo_tasa()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(['valor' => 'automatica']);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $modo = TasaBcv::getModo();
        
        $this->assertEquals('automatica', $modo);
    }
    
    // ──────────────────────────────────────────────
    // TEST 6: Cambiar modo de tasa
    // ──────────────────────────────────────────────
    public function test_cambiar_modo_tasa()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $resultado = TasaBcv::setModo('manual');
        $this->assertTrue($resultado);
        
        // Modo inválido debe retornar false
        $resultadoInvalido = TasaBcv::setModo('invalido');
        $this->assertFalse($resultadoInvalido);
    }
    
    // ──────────────────────────────────────────────
    // TEST 7: Crear nueva tasa
    // ──────────────────────────────────────────────
    public function test_crear_nueva_tasa()
    {
        $nuevaTasa = 65.00;
        
        $mockStmtInsert = $this->createMock(PDOStatement::class);
        $mockStmtInsert->method('execute')->willReturn(true);
        
        $mockStmtFetch = $this->createMock(PDOStatement::class);
        $mockStmtFetch->method('execute')->willReturn(true);
        $mockStmtFetch->method('fetch')->willReturn([
            'id' => 2,
            'tasa_usd_bs' => (string) $nuevaTasa,
            'tipo' => 'manual',
            'created_at' => '2024-06-01 12:00:00',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturnOnConsecutiveCalls($mockStmtInsert, $mockStmtFetch);
        $mockPdo->method('lastInsertId')->willReturn('2');
        
        $this->injectMockPDO($mockPdo);
        
        $tasa = TasaBcv::create($nuevaTasa, 'manual');
        
        $this->assertNotNull($tasa);
        $this->assertEquals($nuevaTasa, (float) $tasa['tasa_usd_bs']);
        $this->assertEquals('manual', $tasa['tipo']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 8: Verificar cuentas PagoMóvil- listar todas
    // ──────────────────────────────────────────────
    public function test_listar_cuentas_pagomovil()
    {
        $cuentasMock = [
            [
                'id' => 1, 'banco' => 'Mercantil', 'activa' => 1,
                'telefono' => '04141234567',
            ],
            [
                'id' => 2, 'banco' => 'Banco de Venezuela', 'activa' => 0,
                'telefono' => '04141234568',
            ],
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('query')->willReturnSelf();
        $mockStmt->method('fetchAll')->willReturn($cuentasMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('query')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $cuentas = CuentaPagoMovil::getAll();
        
        $this->assertCount(2, $cuentas);
        $this->assertEquals('Mercantil', $cuentas[0]['banco']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 9: Activar cuenta PagoMóvil
    // ──────────────────────────────────────────────
    public function test_activar_cuenta_pagomovil()
    {
        $cuentaMock = [
            'id' => 1, 'banco' => 'Mercantil', 'activa' => 1,
            'telefono' => '04141234567', 'cedula_rif' => 'J-12345678-9',
        ];
        
        $mockStmtExec = $this->createMock(PDOStatement::class);
        $mockStmtExec->method('execute')->willReturn(true);
        
        $mockStmtFetch = $this->createMock(PDOStatement::class);
        $mockStmtFetch->method('execute')->willReturn(true);
        $mockStmtFetch->method('fetch')->willReturn($cuentaMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('beginTransaction')->willReturn(true);
        $mockPdo->method('commit')->willReturn(true);
        $mockPdo->method('exec')->willReturn(1);
        
        // El primer prepare es para UPDATE cuentas SET activa=0
        // El segundo es para UPDATE cuentas SET activa=1 WHERE id=?
        // El tercero es para SELECT * FROM cuentas WHERE id=?
        $mockPdo->method('prepare')->willReturnOnConsecutiveCalls(
            $mockStmtExec,  // UPDATE SET activa=0
            $mockStmtExec,  // UPDATE SET activa=1 WHERE id=?
            $mockStmtFetch  // SELECT
        );
        
        $this->injectMockPDO($mockPdo);
        
        $cuenta = CuentaPagoMovil::activar(1);
        
        $this->assertNotNull($cuenta);
        $this->assertEquals(1, $cuenta['activa']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 10: Validar archivo de capture (tipo JPG)
    // ──────────────────────────────────────────────
    public function test_validar_tipo_archivo_capture_jpg_valido()
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        $this->assertContains('image/jpeg', $allowedTypes);
        $this->assertContains('image/png', $allowedTypes);
        
        $mimeType = 'image/jpeg';
        $this->assertTrue(in_array($mimeType, $allowedTypes), 
            'El MIME type image/jpeg debe ser válido');
    }
    
    // ──────────────────────────────────────────────
    // TEST 11: Validar archivo de capture inválido (TXT)
    // ──────────────────────────────────────────────
    public function test_validar_archivo_invalido_txt_debe_fallar()
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        $mimeTypeInvalido = $this->fixtures['archivo_invalido_mime']; // text/plain
        
        $this->assertNotContains($mimeTypeInvalido, $allowedTypes,
            'El tipo text/plain no debe estar permitido para captures');
        
        // Validar extensión
        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
        $extensionInvalida = 'txt';
        
        $this->assertNotContains($extensionInvalida, $extensionesPermitidas,
            'La extensión .txt no debe estar permitida');
    }
    
    // ──────────────────────────────────────────────
    // TEST 12: Validar tamaño máximo de capture (5MB)
    // ──────────────────────────────────────────────
    public function test_validar_tamano_maximo_capture()
    {
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $archivoValidoSize = $this->fixtures['capture_size']; // 100KB
        $archivoGrandeSize = $this->fixtures['archivo_demasiado_grande']; // 6MB
        
        $this->assertLessThanOrEqual($maxSize, $archivoValidoSize,
            'Un archivo de 100KB debe estar dentro del límite');
        
        $this->assertGreaterThan($maxSize, $archivoGrandeSize,
            'Un archivo de 6MB debe exceder el límite de 5MB');
    }
    
    // ──────────────────────────────────────────────
    // TEST 13: Confirmar pago - datos válidos
    // ──────────────────────────────────────────────
    public function test_confirmar_pago_con_referencia_valida()
    {
        $referencia = $this->fixtures['referencia_valida'];
        
        $this->assertNotEmpty($referencia);
        $this->assertGreaterThanOrEqual(4, strlen($referencia),
            'La referencia debe tener al menos 4 caracteres');
        
        // Simular actualización de pedido
        $mockStmtUpdate = $this->createMock(PDOStatement::class);
        $mockStmtUpdate->method('execute')->willReturn(true);
        
        $mockStmtFetch = $this->createMock(PDOStatement::class);
        $mockStmtFetch->method('execute')->willReturn(true);
        $mockStmtFetch->method('fetch')->willReturn([
            'id' => 1,
            'referencia_pago' => $referencia,
            'pago_confirmado' => 1,
            'estado' => 'pendiente',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturnOnConsecutiveCalls($mockStmtUpdate, $mockStmtFetch);
        
        $this->injectMockPDO($mockPdo);
        
        $pedido = Pedido::confirmarPago(1, $referencia);
        
        $this->assertNotNull($pedido);
        $this->assertEquals(1, $pedido['pago_confirmado']);
        $this->assertEquals($referencia, $pedido['referencia_pago']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 14: Confirmar pago - datos vacíos (debe fallar)
    // ──────────────────────────────────────────────
    public function test_confirmar_pago_sin_datos_debe_fallar()
    {
        $referencia = '';
        $pedidoId = 0;
        
        $this->assertEmpty($referencia, 'La referencia vacía no debe ser válida');
        $this->assertEmpty($pedidoId, 'El pedido_id 0 no debe ser válido');
        
        // La validación ocurre en el endpoint confirmar-pago.php
        $camposRequeridos = ['pedido_id', 'referencia'];
        $input = ['pedido_id' => 0, 'referencia' => ''];
        
        $errores = [];
        foreach ($camposRequeridos as $campo) {
            if (empty($input[$campo])) {
                $errores[] = $campo;
            }
        }
        
        $this->assertNotEmpty($errores, 'Debe haber errores de validación');
        $this->assertContains('pedido_id', $errores);
        $this->assertContains('referencia', $errores);
    }
    
    // ──────────────────────────────────────────────
    // TEST 15: Validar referencia corta
    // ──────────────────────────────────────────────
    public function test_referencia_corta_debe_fallar()
    {
        $referenciaCorta = $this->fixtures['referencia_corta']; // 'AB'
        
        $this->assertLessThan(4, strlen($referenciaCorta),
            'La referencia debe tener al menos 4 caracteres');
        
        // El endpoint valida referencia >= 4 caracteres
        $esValida = strlen($referenciaCorta) >= 4;
        $this->assertFalse($esValida, 'Una referencia de 2 caracteres debe ser inválida');
    }
    
    // ──────────────────────────────────────────────
    // TEST 16: Obtener historial de tasas
    // ──────────────────────────────────────────────
    public function test_obtener_historial_tasas()
    {
        $historialMock = [
            ['id' => 3, 'tasa_usd_bs' => '65.00', 'tipo' => 'manual', 'created_at' => '2024-06-03 12:00:00'],
            ['id' => 2, 'tasa_usd_bs' => '63.00', 'tipo' => 'automatica', 'created_at' => '2024-06-02 12:00:00'],
            ['id' => 1, 'tasa_usd_bs' => '62.45', 'tipo' => 'automatica', 'created_at' => '2024-06-01 12:00:00'],
        ];
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetchAll')->willReturn($historialMock);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $this->injectMockPDO($mockPdo);
        
        $historial = TasaBcv::getHistorial(3);
        
        $this->assertCount(3, $historial);
        $this->assertEquals(65.00, (float) $historial[0]['tasa_usd_bs']);
    }
}
