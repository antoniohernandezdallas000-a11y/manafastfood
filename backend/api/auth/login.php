<?php
/**
 * POST /api/auth/login.php
 * Inicio de sesión de usuario
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../helpers/jwt.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

// Validar campos
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email y contraseña son requeridos', 'data' => null, 'error' => 'MISSING_FIELDS']);
    exit;
}

$email = strtolower(trim($input['email']));
$password = $input['password'];

// Buscar usuario
$usuario = Usuario::getByEmail($email);

if (!$usuario) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Credenciales inválidas', 'data' => null, 'error' => 'INVALID_CREDENTIALS']);
    exit;
}

// Verificar si está activo
if (!$usuario['activo']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Cuenta desactivada. Contacta al administrador.', 'data' => null, 'error' => 'ACCOUNT_DISABLED']);
    exit;
}

// Verificar password
if (!Usuario::verificarPassword($password, $usuario['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Credenciales inválidas', 'data' => null, 'error' => 'INVALID_CREDENTIALS']);
    exit;
}

// Generar JWT
$token = JWT::encode([
    'user_id' => $usuario['id'],
    'email' => $usuario['email'],
    'rol' => $usuario['rol'],
    'nombre' => $usuario['nombre'],
]);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Inicio de sesión exitoso',
    'data' => [
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'rol' => $usuario['rol'],
        ],
        'token' => $token,
    ],
    'error' => null,
], JSON_UNESCAPED_UNICODE);
