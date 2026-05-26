<?php
/**
 * MANÁ FAST FOOD - Front Controller / Router
 * 
 * Redirige todas las peticiones al archivo PHP correspondiente en /api/
 */

// ─── Error reporting ───
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// ─── CORS ───
require_once __DIR__ . '/config/cors.php';

// ─── Parse request ───
// Obtener la ruta desde diferentes fuentes posibles
$requestUri = '';

// Opción 1: URL amigable con PATH_INFO
if (!empty($_SERVER['PATH_INFO'])) {
    $requestUri = $_SERVER['PATH_INFO'];
}
// Opción 2: URL con query string ?url=
elseif (!empty($_GET['url'])) {
    $requestUri = '/' . ltrim($_GET['url'], '/');
}
// Opción 3: REQUEST_URI completo (para /backend/index.php/api/...)
else {
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    
    // Remover query string
    if (($pos = strpos($requestUri, '?')) !== false) {
        $requestUri = substr($requestUri, 0, $pos);
    }
    
    // Si el SCRIPT_NAME está en la URI (ej: /mana-fast-food/backend/index.php/api/...)
    if (strpos($requestUri, $scriptName) === 0) {
        // Tomar todo lo que sigue después de index.php
        $requestUri = substr($requestUri, strlen($scriptName));
    } else {
        // Opción 4: URL como /backend/index.php/api/... desde raíz
        // Remover /backend/index.php si está presente
        $backendPos = strpos($requestUri, '/backend/index.php');
        if ($backendPos !== false) {
            $requestUri = substr($requestUri, $backendPos + strlen('/backend/index.php'));
        }
        // O como /backend/api/...
        $backendApiPos = strpos($requestUri, '/backend/api');
        if ($backendApiPos !== false) {
            $requestUri = substr($requestUri, $backendApiPos + strlen('/backend/api'));
            $requestUri = '/api' . $requestUri;
        }
    }
}

// Normalizar: asegurar que empiece con /
if ($requestUri === '' || $requestUri === false) {
    $requestUri = '/';
} elseif ($requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// ─── Default route ───
if ($requestUri === '/' || $requestUri === '') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Maná Fast Food API v1.0',
        'data' => [
            'status' => 'running',
            'endpoints' => '/api/...'
        ]
    ]);
    exit;
}

// ─── Map URI to API file ───
$routePath = $requestUri;

// Remove /api prefix if present (to avoid double /api)
if (strpos($routePath, '/api/') === 0) {
    $routePath = substr($routePath, 4); // remove '/api'
} elseif ($routePath === '/api') {
    $routePath = '';
}

// Construir la ruta al archivo PHP
$apiFile = __DIR__ . '/api' . $routePath . '.php';

// Si no existe, intentar con la ruta completa sin modificar
if (!file_exists($apiFile) && $routePath !== '') {
    $apiFile = __DIR__ . $requestUri . '.php';
}

// Check if the exact file exists
if ($routePath !== '' && file_exists($apiFile)) {
    require $apiFile;
    exit;
}

// ─── 404 ───
http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Endpoint no encontrado',
    'data' => null,
    'error' => 'Ruta no válida: ' . $requestUri . ' (buscó: ' . $apiFile . ')'
]);
    exit;
}

// ─── Map URI to API file ───
// Remove /api prefix if present (to avoid double /api)
$routePath = $requestUri;
if (strpos($routePath, '/api/') === 0) {
    $routePath = substr($routePath, 4); // remove '/api'
} elseif ($routePath === '/api') {
    $routePath = '';
}

$apiFile = __DIR__ . '/api' . $routePath . '.php';

// Check if the exact file exists
if ($routePath !== '' && file_exists($apiFile)) {
    require $apiFile;
    exit;
}

// ─── Try clean path without /api prefix ───
// e.g. /auth/register (without /api) -> api/auth/register.php
if ($requestUri !== $routePath) {
    $apiFile = __DIR__ . '/api' . $requestUri . '.php';
    if (file_exists($apiFile)) {
        require $apiFile;
        exit;
    }
}

// ─── 404 ───
http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Endpoint no encontrado',
    'data' => null,
    'error' => 'Ruta no válida: ' . $requestUri
]);
exit;
