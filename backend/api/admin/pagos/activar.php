<?php
/**
 * PATCH /api/admin/pagos/activar.php
 * Activar una cuenta PagoMóvil (desactiva las demás)
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/CuentaPagoMovil.php';

// Solo PATCH
if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El campo id es requerido', 'data' => null, 'error' => 'MISSING_ID']);
    exit;
}

$id = (int) $input['id'];

// Verificar que existe
$cuenta = CuentaPagoMovil::getById($id);
if (!$cuenta) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Cuenta no encontrada', 'data' => null, 'error' => 'ACCOUNT_NOT_FOUND']);
    exit;
}

$cuentaActualizada = CuentaPagoMovil::activar($id);

if (!$cuentaActualizada) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al activar la cuenta', 'data' => null, 'error' => 'ACTIVATE_ERROR']);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Cuenta activada exitosamente',
    'data' => $cuentaActualizada,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
