<?php
/**
 * GET /api/admin/tasa/obtener.php
 * Obtener tasa BCV actual y configuración
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/TasaBcv.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

$tasaActual = TasaBcv::getUltima();
$modo = TasaBcv::getModo();
$tasaPersonalizada = TasaBcv::getTasaPersonalizada();
$historial = TasaBcv::getHistorial(10);

$data = [
    'tasa_actual' => $tasaActual,
    'modo' => $modo,
    'tasa_personalizada' => $tasaPersonalizada,
    'historial' => $historial,
];

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Configuración de tasa BCV',
    'data' => $data,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
