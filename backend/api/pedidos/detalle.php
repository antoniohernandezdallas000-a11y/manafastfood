<?php
/**
 * GET /api/pedidos/detalle.php
 * Obtener detalle de un pedido (el usuario solo ve los suyos, admin ve todos)
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

// Verificar token (puede ver cualquiera autenticado)
$payload = AuthMiddleware::verificarToken();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Debe proporcionar un id de pedido', 'data' => null, 'error' => 'MISSING_PARAM']);
    exit;
}

$pedido = Pedido::getById($id);

if (!$pedido) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado', 'data' => null, 'error' => 'ORDER_NOT_FOUND']);
    exit;
}

// Si no es admin, verificar que el pedido le pertenezca
if ($payload['rol'] !== 'admin' && $pedido['usuario_id'] != $payload['user_id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para ver este pedido', 'data' => null, 'error' => 'FORBIDDEN']);
    exit;
}

// Obtener detalles
$pedido['detalles'] = Pedido::getDetalles($pedido['id']);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Detalle del pedido',
    'data' => $pedido,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
