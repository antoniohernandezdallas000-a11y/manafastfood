<?php
/**
 * GET /api/productos/detalle.php
 * Obtener detalle de un producto por ID o slug
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Producto.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

if (!$id && !$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Debe proporcionar un id o slug del producto', 'data' => null, 'error' => 'MISSING_PARAM']);
    exit;
}

if ($id) {
    $producto = Producto::getById($id);
} else {
    $producto = Producto::getBySlug($slug);
}

if (!$producto) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado', 'data' => null, 'error' => 'PRODUCT_NOT_FOUND']);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Detalle del producto',
    'data' => $producto,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
