<?php
/**
 * POST /api/checkout/confirmar-pago.php
 * Confirmar pago de un pedido (requiere autenticación)
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../models/Pedido.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar token
$payload = AuthMiddleware::verificarToken();

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

// Validar campos
if (empty($input['pedido_id']) || empty($input['referencia'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'pedido_id y referencia son requeridos', 'data' => null, 'error' => 'MISSING_FIELDS']);
    exit;
}

$pedidoId = (int) $input['pedido_id'];
$referencia = trim($input['referencia']);
$capturePath = $input['capture_path'] ?? null;

// Verificar que el pedido existe
$pedido = Pedido::getById($pedidoId);
if (!$pedido) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado', 'data' => null, 'error' => 'ORDER_NOT_FOUND']);
    exit;
}

// Verificar que el pedido pertenece al usuario (o es admin)
if ($payload['rol'] !== 'admin' && $pedido['usuario_id'] != $payload['user_id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permiso para confirmar este pago', 'data' => null, 'error' => 'FORBIDDEN']);
    exit;
}

// Verificar que el pedido no esté ya pagado
if ($pedido['pago_confirmado']) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Este pedido ya tiene el pago confirmado', 'data' => null, 'error' => 'ALREADY_PAID']);
    exit;
}

// Confirmar pago
$pedidoActualizado = Pedido::confirmarPago($pedidoId, $referencia, $capturePath);

if (!$pedidoActualizado) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al confirmar el pago', 'data' => null, 'error' => 'CONFIRM_ERROR']);
    exit;
}

// Obtener detalles
$pedidoActualizado['detalles'] = Pedido::getDetalles($pedidoActualizado['id']);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Pago confirmado exitosamente',
    'data' => $pedidoActualizado,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
