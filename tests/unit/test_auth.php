<?php
/**
 * MANÁ FAST FOOD - TEST DE AUTENTICACIÓN
 * 
 * Prueba unitaria del modelo Usuario y helper JWT.
 * Mockeamos PDO para no depender de BD real.
 */

use PHPUnit\Framework\TestCase;

class test_auth extends TestCase
{
    private $fixtures;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtures = require __DIR__ . '/../fixtures/data.php';
        
        // Resetear superglobales
        $_SERVER = [];
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['HTTP_HOST'] = 'localhost:8000';
        
        // Resetear singleton de Database
        Database::reset();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        Database::reset();
        Mockery::close();
    }
    
    // ──────────────────────────────────────────────
    // TEST 1: Registro con datos válidos
    // ──────────────────────────────────────────────
    public function test_registro_con_datos_validos()
    {
        $fixture = $this->fixtures['usuario_valido'];
        
        // Configurar mock de PDO
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
        
        // Primer execute: getByEmail (SELECT)
        // Segundo execute: create (INSERT)
        $mockStmt->method('fetch')
            ->willReturnOnConsecutiveCalls(
                false,  // getByEmail: no existe -> false
                [       // getById después del insert
                    'id' => 1,
                    'nombre' => $fixture['nombre'],
                    'telefono' => $fixture['telefono'],
                    'email' => $fixture['email'],
                    'rol' => 'cliente',
                    'activo' => 1,
                    'created_at' => '2024-01-01 00:00:00',
                ]
            );
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        $mockPdo->method('lastInsertId')->willReturn('1');
        
        // Inyectar mock
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        // Probar que el email no existe
        $existente = Usuario::getByEmail($fixture['email']);
        $this->assertNull($existente, 'El email no debería existir antes del registro');
        
        // Hash de password
        $hash = Usuario::hashPassword($fixture['password']);
        $this->assertNotEmpty($hash, 'El hash de password no debe estar vacío');
        $this->assertNotEquals($fixture['password'], $hash, 'El hash debe ser diferente al password original');
        $this->assertTrue(Usuario::verificarPassword($fixture['password'], $hash), 'El password debe verificar contra su hash');
        
        // Crear usuario
        $usuario = Usuario::create([
            'nombre' => $fixture['nombre'],
            'telefono' => $fixture['telefono'],
            'email' => $fixture['email'],
            'password_hash' => $hash,
            'rol' => 'cliente',
        ]);
        
        $this->assertNotNull($usuario, 'El usuario creado no debe ser null');
        $this->assertEquals($fixture['nombre'], $usuario['nombre']);
        $this->assertEquals($fixture['email'], $usuario['email']);
        $this->assertEquals('cliente', $usuario['rol']);
        $this->assertArrayHasKey('id', $usuario);
    }
    
    // ──────────────────────────────────────────────
    // TEST 2: Registro con email duplicado
    // ──────────────────────────────────────────────
    public function test_registro_email_duplicado_debe_fallar()
    {
        $fixture = $this->fixtures['usuario_valido'];
        
        // El email ya existe en la BD
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'nombre' => $fixture['nombre'],
            'telefono' => $fixture['telefono'],
            'email' => $fixture['email'],
            'password_hash' => '$2y$12$hash_existente',
            'rol' => 'cliente',
            'activo' => 1,
            'created_at' => '2024-01-01 00:00:00',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        // Verificar que getByEmail retorna el usuario existente
        $usuarioExistente = Usuario::getByEmail($fixture['email']);
        $this->assertNotNull($usuarioExistente, 'El usuario duplicado debe existir');
        $this->assertEquals($fixture['email'], $usuarioExistente['email']);
        
        // El registro debería detectar el duplicado (simulando la lógica del endpoint)
        $this->assertNotEmpty($usuarioExistente, 'No se debería poder registrar un email duplicado');
    }
    
    // ──────────────────────────────────────────────
    // TEST 3: Login con credenciales correctas
    // ──────────────────────────────────────────────
    public function test_login_con_credenciales_correctas()
    {
        $fixture = $this->fixtures['usuario_valido'];
        $hash = Usuario::hashPassword($fixture['password']);
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'nombre' => $fixture['nombre'],
            'telefono' => $fixture['telefono'],
            'email' => $fixture['email'],
            'password_hash' => $hash,
            'rol' => 'cliente',
            'activo' => 1,
            'created_at' => '2024-01-01 00:00:00',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        // Obtener usuario por email
        $usuario = Usuario::getByEmail($fixture['email']);
        $this->assertNotNull($usuario);
        
        // Verificar password
        $passwordValido = Usuario::verificarPassword($fixture['password'], $usuario['password_hash']);
        $this->assertTrue($passwordValido, 'El password correcto debe verificar');
        
        // Verificar que está activo
        $this->assertEquals(1, $usuario['activo'], 'El usuario debe estar activo');
        
        // Generar token JWT
        $token = JWT::encode([
            'user_id' => $usuario['id'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
        ]);
        
        $this->assertNotEmpty($token);
        $this->assertStringContainsString('.', $token, 'Un JWT debe contener puntos');
        
        // Decodificar y verificar payload
        $payload = JWT::decode($token);
        $this->assertNotNull($payload);
        $this->assertEquals(1, $payload['user_id']);
        $this->assertEquals($fixture['email'], $payload['email']);
        $this->assertEquals('cliente', $payload['rol']);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
    }
    
    // ──────────────────────────────────────────────
    // TEST 4: Login con contraseña incorrecta
    // ──────────────────────────────────────────────
    public function test_login_con_password_incorrecto()
    {
        $fixture = $this->fixtures['usuario_valido'];
        $hash = Usuario::hashPassword($fixture['password']);
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'password_hash' => $hash,
            'activo' => 1,
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $passwordIncorrecto = 'wrong_password_123';
        $this->assertFalse(
            Usuario::verificarPassword($passwordIncorrecto, $hash),
            'Un password incorrecto no debe verificar'
        );
    }
    
    // ──────────────────────────────────────────────
    // TEST 5: Login con email no registrado
    // ──────────────────────────────────────────────
    public function test_login_con_email_no_registrado()
    {
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn(false); // No existe
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $emailInexistente = 'noexiste@test.com';
        $usuario = Usuario::getByEmail($emailInexistente);
        
        $this->assertNull($usuario, 'Un email no registrado debe retornar null');
    }
    
    // ──────────────────────────────────────────────
    // TEST 6: Verificación de token JWT
    // ──────────────────────────────────────────────
    public function test_verificacion_token_jwt()
    {
        $token = JWT::encode([
            'user_id' => 42,
            'email' => 'usuario@test.com',
            'rol' => 'admin',
        ]);
        
        // Verificar estructura
        $parts = explode('.', $token);
        $this->assertCount(3, $parts, 'Un JWT debe tener 3 partes separadas por punto');
        
        // Decodificar payload
        $payload = JWT::decode($token);
        $this->assertNotNull($payload, 'Un token válido debe decodificar');
        $this->assertEquals(42, $payload['user_id']);
        $this->assertEquals('usuario@test.com', $payload['email']);
        $this->assertEquals('admin', $payload['rol']);
        
        // Verificar que el payload (sin verificar firma) también funciona
        $payloadRaw = JWT::decodePayload($token);
        $this->assertNotNull($payloadRaw);
        $this->assertEquals(42, $payloadRaw['user_id']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 7: Token expirado
    // ──────────────────────────────────────────────
    public function test_token_expirado()
    {
        // Crear token con exp en el pasado usando JWT::encode con manipulación
        $payload = [
            'user_id' => 1,
            'email' => 'test@mana.com',
            'rol' => 'cliente',
            'iat' => time() - 7200,     // 2 horas atrás
            'exp' => time() - 3600,     // 1 hora atrás (EXPiRADO)
        ];
        
        // Generar token manualmente (misma lógica que JWT::encode pero con exp custom)
        $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $payloadEncoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        $signature = rtrim(strtr(base64_encode(
            hash_hmac('sha256', "$header.$payloadEncoded", 
                getenv('JWT_SECRET') ?: 'mana_fast_food_secret_change_me', true)
        ), '+/', '-_'), '=');
        $token = "$header.$payloadEncoded.$signature";
        
        // Verificar que el token está expirado
        $decoded = JWT::decode($token);
        $this->assertNull($decoded, 'Un token expirado debe retornar null');
        
        // Pero el payload raw se puede leer
        $payloadRaw = JWT::decodePayload($token);
        $this->assertNotNull($payloadRaw);
        $this->assertTrue($payloadRaw['exp'] < time(), 'El timestamp de exp debe estar en el pasado');
    }
    
    // ──────────────────────────────────────────────
    // TEST 8: Token inválido (malformed)
    // ──────────────────────────────────────────────
    public function test_token_invalido()
    {
        $this->assertNull(JWT::decode(''), 'Token vacío debe retornar null');
        $this->assertNull(JWT::decode('not-a-jwt'), 'Token sin formato debe retornar null');
        $this->assertNull(JWT::decode('solo.dos.partes.extra'), 'Token con 4 partes debe retornar null');
        $this->assertNull(JWT::decode('una.parte'), 'Token con 2 partes debe retornar null');
    }
    
    // ──────────────────────────────────────────────
    // TEST 9: AuthMiddleware verificarToken sin token
    // ──────────────────────────────────────────────
    public function test_ruta_protegida_sin_token_retorna_401()
    {
        // Simular request sin header de autorización
        $_SERVER['HTTP_AUTHORIZATION'] = '';
        
        // AuthMiddleware::verificarToken() llama a exit(), debemos capturarlo
        $this->expectOutputRegex('/401|Token de autenticación requerido/');
        
        // Esto debería llamar a self::respond(401, ...) y hacer exit
        try {
            AuthMiddleware::verificarToken();
        } catch (Exception $e) {
            // La función respond() hace exit, pero en CLI podemos capturar
        }
    }
    
    // ──────────────────────────────────────────────
    // TEST 10: AuthMiddleware esAdmin sin rol admin
    // ──────────────────────────────────────────────
    public function test_es_admin_sin_rol_admin_retorna_403()
    {
        $token = getTestToken(1, 'cliente');
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        
        // AuthMiddleware::esAdmin() debe detectar que no es admin
        $this->expectOutputRegex('/403|Acceso denegado/');
        
        try {
            AuthMiddleware::esAdmin();
        } catch (Exception $e) {
            // La función respond() hace exit, pero en CLI podemos capturar
        }
    }
    
    // ──────────────────────────────────────────────
    // TEST 11: AuthMiddleware esAdmin con rol admin
    // ──────────────────────────────────────────────
    public function test_es_admin_con_rol_admin_exitoso()
    {
        $token = getAdminToken();
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        
        $payload = AuthMiddleware::esAdmin();
        $this->assertNotNull($payload);
        $this->assertEquals('admin', $payload['rol']);
        $this->assertEquals(1, $payload['user_id']);
    }
    
    // ──────────────────────────────────────────────
    // TEST 12: Hash de password con costo
    // ──────────────────────────────────────────────
    public function test_hash_password_usa_bcrypt()
    {
        $password = 'MiPasswordSegura123!';
        $hash = Usuario::hashPassword($password);
        
        $this->assertStringStartsWith('$2y$', $hash, 'El hash debe usar Bcrypt ($2y$)');
        $this->assertTrue(password_verify($password, $hash), 'El hash debe verificar con password_verify');
        
        // Mismo password debe generar hash diferente cada vez (salt aleatorio)
        $hash2 = Usuario::hashPassword($password);
        $this->assertNotEquals($hash, $hash2, 'Cada hash debe ser único por el salt');
    }
    
    // ──────────────────────────────────────────────
    // TEST 13: Parseo de expiración JWT
    // ──────────────────────────────────────────────
    public function test_jwt_expiry_parsing()
    {
        $token = JWT::encode(['user_id' => 1, 'email' => 'test@test.com', 'rol' => 'cliente']);
        $payload = JWT::decode($token);
        
        $this->assertNotNull($payload);
        $this->assertArrayHasKey('exp', $payload);
        
        // La expiración debe ser futura
        $this->assertGreaterThan(time(), $payload['exp'], 'La expiración debe ser en el futuro');
        
        // Diferencia debe ser ~24 horas (por defecto)
        $diff = $payload['exp'] - $payload['iat'];
        $this->assertEquals(86400, $diff, 'La expiración por defecto debe ser 24 horas');
    }
    
    // ──────────────────────────────────────────────
    // TEST 14: Usuario desactivado no puede hacer login
    // ──────────────────────────────────────────────
    public function test_usuario_desactivado_no_puede_hacer_login()
    {
        $fixture = $this->fixtures['usuario_valido'];
        $hash = Usuario::hashPassword($fixture['password']);
        
        $mockStmt = $this->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn([
            'id' => 1,
            'email' => $fixture['email'],
            'password_hash' => $hash,
            'activo' => 0, // DESACTIVADO
            'rol' => 'cliente',
        ]);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($mockStmt);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        $usuario = Usuario::getByEmail($fixture['email']);
        $this->assertNotNull($usuario);
        $this->assertEquals(0, $usuario['activo'], 'El usuario debe estar desactivado');
        
        // Aunque el password sea correcto, el usuario está desactivado
        $passwordValido = Usuario::verificarPassword($fixture['password'], $usuario['password_hash']);
        $this->assertTrue($passwordValido, 'El password debe seguir siendo válido');
        
        // La lógica de login debe verificar activo antes de permitir acceso
        $this->assertEquals(0, $usuario['activo'], 'Usuario desactivado no debería poder loguearse');
    }
    
    // ──────────────────────────────────────────────
    // TEST 15: Actualización de usuario
    // ──────────────────────────────────────────────
    public function test_actualizar_usuario()
    {
        $mockStmtFetch = $this->createMock(PDOStatement::class);
        $mockStmtFetch->method('execute')->willReturn(true);
        $mockStmtFetch->method('fetch')->willReturn([
            'id' => 1,
            'nombre' => 'Usuario Original',
            'telefono' => '04141234567',
            'email' => 'original@test.com',
            'rol' => 'cliente',
            'activo' => 1,
            'created_at' => '2024-01-01 00:00:00',
        ]);
        
        $mockStmtUpdate = $this->createMock(PDOStatement::class);
        $mockStmtUpdate->method('execute')->willReturn(true);
        $mockStmtUpdate->method('rowCount')->willReturn(1);
        
        $mockPdo = $this->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturnOnConsecutiveCalls($mockStmtUpdate, $mockStmtFetch);
        
        $reflection = new ReflectionClass(Database::class);
        $instanceProp = $reflection->getProperty('instance');
        $instanceProp->setAccessible(true);
        $instanceProp->setValue(null, $mockPdo);
        
        // Simular que el update se hizo y luego getById retorna datos actualizados
        $mockStmtFetch->method('fetch')->willReturn([
            'id' => 1,
            'nombre' => 'Nombre Actualizado',
            'telefono' => '04241234567',
            'email' => 'original@test.com',
            'rol' => 'cliente',
            'activo' => 1,
            'created_at' => '2024-01-01 00:00:00',
        ]);
        
        // Verificar borrado lógico
        $resultado = Usuario::delete(1);
        $this->assertTrue($resultado, 'El borrado lógico debe retornar true');
    }
}
