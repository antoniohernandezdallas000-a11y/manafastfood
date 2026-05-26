<?php
/**
 * PUT /api/admin/ofertas/editar.php
 * Actualizar una oferta existente
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Oferta.php';

// Solo PUT/PATCH
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
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

// Actualizar solo los campos enviados
$updateData = [];
$fieldsMap = ['nombre', 'tipo', 'descripcion', 'descuento_porcentaje', 'fecha_inicio', 'fecha_fin', 'activa', 'productos'];

foreach ($fieldsMap as $field) {
    if (array_key_exists($field, $input)) {
        $updateData[$field] = $input[$field];
    }
}

if (empty($updateData)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se enviaron campos para actualizar', 'data' => null, 'error' => 'NO_FIELDS']);
    exit;
}

$ofertaActualizada = Oferta::update($id, $updateData);
$ofertaActualizada['productos'] = Oferta::getProductos($id);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Oferta actualizada exitosamente',
    'data' => $ofertaActualizada,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
