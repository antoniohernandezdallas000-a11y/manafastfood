<?php
/**
 * POST /api/admin/pagos/crear.php
 * Crear una nueva cuenta PagoMóvil
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/CuentaPagoMovil.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

// Validar campos requeridos
$required = ['banco', 'codigo_banco', 'titular', 'telefono', 'cedula_rif'];
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

$cuenta = CuentaPagoMovil::create([
    'banco' => trim($input['banco']),
    'codigo_banco' => trim($input['codigo_banco']),
    'titular' => trim($input['titular']),
    'telefono' => trim($input['telefono']),
    'cedula_rif' => trim($input['cedula_rif']),
    'tipo_cuenta' => $input['tipo_cuenta'] ?? 'ahorro',
    'activa' => $input['activa'] ?? 0,
]);

if (!$cuenta) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al crear la cuenta', 'data' => null, 'error' => 'CREATE_ERROR']);
    exit;
}

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Cuenta PagoMóvil creada exitosamente',
    'data' => $cuenta,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
