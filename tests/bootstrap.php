<?php
/**
 * MANÁ FAST FOOD - TEST BOOTSTRAP
 * 
 * Configuración global para todos los tests.
 * Carga autoload, variables de entorno y helpers de test.
 */

// ─── Indicar que estamos en entorno de testing ───
define('TESTING', true);

// ─── Error reporting ───
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// ─── Timezone ───
date_default_timezone_set('America/Caracas');

// ─── Cargar autoload de Composer ───
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

$autoloadLoaded = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoloadLoaded = true;
        break;
    }
}

if (!$autoloadLoaded) {
    // Fallback: autoload manual para los tests sin Composer
    spl_autoload_register(function ($class) {
        $paths = [
            __DIR__ . '/../backend/models/' . $class . '.php',
            __DIR__ . '/../backend/helpers/' . $class . '.php',
            __DIR__ . '/../backend/middleware/' . $class . '.php',
            __DIR__ . '/../backend/config/' . $class . '.php',
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
    });
}

// ─── Cargar archivos esenciales del backend ───
$requiredFiles = [
    __DIR__ . '/../backend/config/database.php',
    __DIR__ . '/../backend/config/cors.php',
    __DIR__ . '/../backend/helpers/jwt.php',
    __DIR__ . '/../backend/middleware/AuthMiddleware.php',
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

// ─── Cargar variables de entorno ───
if (class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// ─── Helpers para tests ───

/**
 * Obtiene un mock de PDO para tests unitarios
 * 
 * @param array $methods Métodos a mockear
 * @return PHPUnit\Framework\MockObject\MockObject|PDO
 */
function getMockDB()
{
    $mockPdo = $this->createMock(PDO::class);
    
    $mockStmt = $this->createMock(PDOStatement::class);
    $mockStmt->method('execute')->willReturn(true);
    $mockStmt->method('fetch')->willReturn(null);
    $mockStmt->method('fetchAll')->willReturn([]);
    $mockStmt->method('rowCount')->willReturn(0);
    
    $mockPdo->method('prepare')->willReturn($mockStmt);
    $mockPdo->method('query')->willReturn($mockStmt);
    $mockPdo->method('lastInsertId')->willReturn('1');
    $mockPdo->method('beginTransaction')->willReturn(true);
    $mockPdo->method('commit')->willReturn(true);
    $mockPdo->method('rollBack')->willReturn(true);
    $mockPdo->method('exec')->willReturn(1);
    
    return $mockPdo;
}

/**
 * Genera un token JWT para tests
 * 
 * @param int $userId ID del usuario
 * @param string $rol Rol del usuario
 * @param int $expOffset Segundos para expiración (por defecto 86400 = 24h)
 * @return string Token JWT
 */
function getTestToken(int $userId = 1, string $rol = 'cliente', int $expOffset = 86400): string
{
    $payload = [
        'user_id' => $userId,
        'email' => $rol === 'admin' ? 'admin@mana.com' : 'test@mana.com',
        'rol' => $rol,
        'nombre' => $rol === 'admin' ? 'Administrador' : 'Test User',
    ];
    
    return JWT::encode($payload);
}

/**
 * Obtiene un token de administrador para tests
 */
function getAdminToken(): string
{
    return getTestToken(1, 'admin');
}

/**
 * Simula una petición HTTP para probar endpoints
 * 
 * @param string $method Método HTTP
 * @param string $uri URI del endpoint
 * @param array $data Datos del body
 * @param string|null $token Token JWT opcional
 * @return array Respuesta decodificada
 */
function simulateRequest(string $method, string $uri, array $data = [], ?string $token = null): array
{
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $uri;
    
    if ($token) {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
    } else {
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }
    
    // Simular php://input
    $GLOBALS['_TEST_INPUT'] = json_encode($data);
    
    // Capturar output
    ob_start();
    
    // Aquí se ejecutaría el endpoint real, pero para tests unitarios
    // no ejecutamos el archivo directamente sino que probamos los modelos
    
    $output = ob_get_clean();
    
    // Limpiar
    unset($GLOBALS['_TEST_INPUT']);
    
    if (!empty($output)) {
        return json_decode($output, true) ?? ['success' => false, 'error' => 'INVALID_JSON'];
    }
    
    return ['success' => true, 'data' => null];
}

/**
 * Limpia variables superglobales entre tests
 */
function resetSuperGlobals(): void
{
    $_SERVER = [];
    $_GET = [];
    $_POST = [];
    $_FILES = [];
    $_COOKIE = [];
    unset($GLOBALS['_TEST_INPUT']);
    
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
    $_SERVER['HTTP_HOST'] = 'localhost:8000';
}

/**
 * Crea un archivo temporal de imagen para tests de upload
 * 
 * @param string $extension Extensión del archivo
 * @param int $size Tamaño en bytes
 * @return string Ruta del archivo temporal
 */
function createTestImageFile(string $extension = 'jpg', int $size = 1024): string
{
    $tmpDir = sys_get_temp_dir();
    $filename = 'test_capture_' . uniqid() . '.' . $extension;
    $path = $tmpDir . DIRECTORY_SEPARATOR . $filename;
    
    if ($extension === 'jpg' || $extension === 'jpeg') {
        // Crear una imagen JPEG mínima válida
        $img = imagecreatetruecolor(100, 100);
        imagejpeg($img, $path, 90);
        imagedestroy($img);
    } elseif ($extension === 'png') {
        $img = imagecreatetruecolor(100, 100);
        imagepng($img, $path);
        imagedestroy($img);
    } else {
        // Archivo de texto plano
        file_put_contents($path, str_repeat('x', $size));
    }
    
    return $path;
}
