<?php
/**
 * GET /api/admin/productos/listar.php
 * Listar todos los productos (incluyendo inactivos)
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Producto.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

$productos = Producto::getAllAdmin();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Listado completo de productos',
    'data' => $productos,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
