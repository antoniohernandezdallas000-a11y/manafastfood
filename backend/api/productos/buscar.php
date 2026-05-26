<?php
/**
 * GET /api/productos/buscar.php
 * Buscar productos por nombre, descripción o ingredientes
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

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Debe proporcionar un término de búsqueda (q)', 'data' => null, 'error' => 'MISSING_QUERY']);
    exit;
}

if (strlen($query) < 2) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El término de búsqueda debe tener al menos 2 caracteres', 'data' => null, 'error' => 'QUERY_TOO_SHORT']);
    exit;
}

$productos = Producto::search($query);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => count($productos) . ' producto(s) encontrado(s)',
    'data' => $productos,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
