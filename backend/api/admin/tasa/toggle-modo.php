<?php
/**
 * PATCH /api/admin/tasa/toggle-modo.php
 * Cambiar el modo de la tasa (auto/manual)
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/TasaBcv.php';

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

if (empty($input['modo'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El campo modo es requerido (automatica o manual)', 'data' => null, 'error' => 'MISSING_MODE']);
    exit;
}

$modo = strtolower(trim($input['modo']));

if (!in_array($modo, ['automatica', 'manual'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Modo inválido. Debe ser "automatica" o "manual"', 'data' => null, 'error' => 'INVALID_MODE']);
    exit;
}

$actualizado = TasaBcv::setModo($modo);

if (!$actualizado) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al cambiar el modo', 'data' => null, 'error' => 'UPDATE_ERROR']);
    exit;
}

$tasaActual = TasaBcv::getUltima();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => "Modo cambiado a '$modo' exitosamente",
    'data' => [
        'modo' => $modo,
        'tasa_actual' => $tasaActual,
    ],
    'error' => null,
], JSON_UNESCAPED_UNICODE);
