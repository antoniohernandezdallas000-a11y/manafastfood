<?php
/**
 * POST /api/auth/register.php
 * Registro de nuevo usuario
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

// Validar campos requeridos
$required = ['nombre', 'telefono', 'email', 'password'];
$errors = [];
foreach ($required as $field) {
    if (empty($input[$field])) {
        $errors[] = "El campo '$field' es requerido";
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error de validación', 'data' => null, 'error' => implode('; ', $errors)]);
    exit;
}

// Sanitizar
$nombre = trim($input['nombre']);
$telefono = trim($input['telefono']);
$email = strtolower(trim($input['email']));
$password = $input['password'];

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido', 'data' => null, 'error' => 'INVALID_EMAIL']);
    exit;
}

// Validar password
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres', 'data' => null, 'error' => 'WEAK_PASSWORD']);
    exit;
}

// Verificar email único
$usuarioExistente = Usuario::getByEmail($email);
if ($usuarioExistente) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'El email ya está registrado', 'data' => null, 'error' => 'EMAIL_EXISTS']);
    exit;
}

// Crear usuario
$usuario = Usuario::create([
    'nombre' => $nombre,
    'telefono' => $telefono,
    'email' => $email,
    'password_hash' => Usuario::hashPassword($password),
    'rol' => 'cliente',
]);

if (!$usuario) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al registrar usuario', 'data' => null, 'error' => 'REGISTER_ERROR']);
    exit;
}

// Generar JWT
$token = JWT::encode([
    'user_id' => $usuario['id'],
    'email' => $usuario['email'],
    'rol' => $usuario['rol'],
]);

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Usuario registrado exitosamente',
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
