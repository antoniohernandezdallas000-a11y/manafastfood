<?php
/**
 * GET /api/auth/me.php
 * Obtener datos del usuario autenticado
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../models/Usuario.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar token
$payload = AuthMiddleware::verificarToken();

// Obtener datos frescos del usuario
$usuario = Usuario::getById($payload['user_id']);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado', 'data' => null, 'error' => 'USER_NOT_FOUND']);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Datos del usuario',
    'data' => [
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'],
        'rol' => $usuario['rol'],
        'activo' => (bool) $usuario['activo'],
        'created_at' => $usuario['created_at'],
    ],
    'error' => null,
], JSON_UNESCAPED_UNICODE);
