<?php
/**
 * GET /api/productos/listar.php
 * Listar todos los productos activos
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

// Parámetros opcionales
$categoriaId = isset($_GET['categoria_id']) ? (int) $_GET['categoria_id'] : null;
$soloOfertas = isset($_GET['ofertas']) && $_GET['ofertas'] === '1';

if ($categoriaId) {
    $productos = Producto::getByCategoria($categoriaId);
} elseif ($soloOfertas) {
    $productos = Producto::getEnOferta();
} else {
    $productos = Producto::getAll();
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Listado de productos',
    'data' => $productos,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
