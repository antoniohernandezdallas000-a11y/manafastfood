<?php
/**
 * PATCH /api/admin/ofertas/toggle.php
 * Activar/Desactivar una oferta (toggle)
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Oferta.php';

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
$oferta = Oferta::getById($id);
if (!$oferta) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Oferta no encontrada', 'data' => null, 'error' => 'OFFER_NOT_FOUND']);
    exit;
}

$ofertaActualizada = Oferta::toggle($id);
$ofertaActualizada['productos'] = Oferta::getProductos($id);

$estadoTexto = $ofertaActualizada['activa'] ? 'activada' : 'desactivada';

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => "Oferta $estadoTexto exitosamente",
    'data' => $ofertaActualizada,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
