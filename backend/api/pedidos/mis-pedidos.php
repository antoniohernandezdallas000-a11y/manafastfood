<?php
/**
 * GET /api/pedidos/mis-pedidos.php
 * Obtener pedidos del usuario autenticado
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../models/Pedido.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar token
$payload = AuthMiddleware::verificarToken();

// Obtener pedidos del usuario
$pedidos = Pedido::getByUsuario($payload['user_id']);

// Agregar detalles a cada pedido
foreach ($pedidos as &$pedido) {
    $pedido['detalles'] = Pedido::getDetalles($pedido['id']);
}
unset($pedido);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => count($pedidos) . ' pedido(s) encontrado(s)',
    'data' => $pedidos,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
