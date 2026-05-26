<?php
/**
 * PUT /api/pedidos/actualizar-estado.php
 * Actualiza el estado de un pedido
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

// Solo PUT o POST
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar autenticación admin
$user = AuthMiddleware::esAdmin();

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

// Validar campos
if (empty($input['id']) || empty($input['estado'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'id y estado son requeridos', 'data' => null, 'error' => 'MISSING_FIELDS']);
    exit;
}

$id = intval($input['id']);
$estado = $input['estado'];

// Validar estado permitido
$estadosValidos = ['pendiente', 'confirmado', 'preparando', 'listo', 'entregado', 'cancelado'];
if (!in_array($estado, $estadosValidos)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Estado inválido', 'data' => null, 'error' => 'INVALID_STATUS']);
    exit;
}

// Actualizar estado
$pedido = Pedido::cambiarEstado($id, $estado);

if (!$pedido) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado', 'data' => null, 'error' => 'NOT_FOUND']);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Estado actualizado a ' . $estado,
    'data' => $pedido,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
