<?php
/**
 * GET /api/checkout/tasa-bcv.php
 * Obtener la última tasa BCV registrada
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/TasaBcv.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

$tasa = TasaBcv::getUltima();

if (!$tasa) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No hay tasa BCV registrada', 'data' => null, 'error' => 'NO_RATE_FOUND']);
    exit;
}

// Agregar modo actual
$tasa['modo'] = TasaBcv::getModo();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Tasa BCV actual',
    'data' => $tasa,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
