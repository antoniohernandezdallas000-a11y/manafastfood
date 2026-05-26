<?php
/**
 * GET /api/admin/pedidos/listar.php
 * Listar todos los pedidos (admin)
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Pedido.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Filtros opcionales
$pedidos = Pedido::getAll();

// Filtrar por estado si se solicita
if (!empty($_GET['estado'])) {
    $estado = $_GET['estado'];
    $pedidos = array_filter($pedidos, function ($p) use ($estado) {
        return $p['estado'] === $estado;
    });
}

// Re-indexar array después de filter
$pedidos = array_values($pedidos);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => count($pedidos) . ' pedido(s) encontrado(s)',
    'data' => $pedidos,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
