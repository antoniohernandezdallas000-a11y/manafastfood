<?php
/**
 * GET /api/checkout/cuentas-pagomovil.php
 * Obtener la cuenta PagoMóvil activa
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/CuentaPagoMovil.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

$cuenta = CuentaPagoMovil::getActiva();

if (!$cuenta) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No hay cuenta PagoMóvil activa', 'data' => null, 'error' => 'NO_ACTIVE_ACCOUNT']);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Cuenta PagoMóvil activa',
    'data' => $cuenta,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
