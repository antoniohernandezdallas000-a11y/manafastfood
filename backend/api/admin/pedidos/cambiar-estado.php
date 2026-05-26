<?php
/**
 * PATCH /api/admin/pedidos/cambiar-estado.php
 * Cambiar el estado de un pedido
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Pedido.php';

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

if (empty($input['id']) || empty($input['estado'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Los campos id y estado son requeridos', 'data' => null, 'error' => 'MISSING_FIELDS']);
    exit;
}

$id = (int) $input['id'];
$estado = trim($input['estado']);

// Validar estado
$estadosValidos = ['pendiente', 'preparando', 'listo', 'entregado', 'cancelado'];
if (!in_array($estado, $estadosValidos)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Estado inválido. Valores válidos: ' . implode(', ', $estadosValidos),
        'data' => null,
        'error' => 'INVALID_STATUS'
    ]);
    exit;
}

// Verificar que existe
$pedido = Pedido::getById($id);
if (!$pedido) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado', 'data' => null, 'error' => 'ORDER_NOT_FOUND']);
    exit;
}

$pedidoActualizado = Pedido::cambiarEstado($id, $estado);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Estado del pedido actualizado',
    'data' => $pedidoActualizado,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
