<?php
/**
 * DELETE /api/admin/productos/eliminar.php
 * Eliminar un producto
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Producto.php';

// Solo DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Leer body o query params
$id = null;

// Para DELETE, puede venir en el body o en query string
$input = json_decode(file_get_contents('php://input'), true);
if ($input && !empty($input['id'])) {
    $id = (int) $input['id'];
} elseif (!empty($_GET['id'])) {
    $id = (int) $_GET['id'];
}

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El campo id es requerido', 'data' => null, 'error' => 'MISSING_ID']);
    exit;
}

// Verificar que existe
$producto = Producto::getById($id);
if (!$producto) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado', 'data' => null, 'error' => 'PRODUCT_NOT_FOUND']);
    exit;
}

if (Producto::delete($id)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Producto eliminado exitosamente',
        'data' => ['id' => $id],
        'error' => null,
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar el producto',
        'data' => null,
        'error' => 'DELETE_ERROR',
    ]);
}
