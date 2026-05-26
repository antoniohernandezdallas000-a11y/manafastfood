<?php
/**
 * GET /api/pedidos/listar.php
 * Lista todos los pedidos (para admin)
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar autenticación admin
$user = AuthMiddleware::esAdmin();

// Obtener todos los pedidos (el filtro se hace desde JS)
$pedidos = Pedido::getAll();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Pedidos obtenidos',
    'data' => $pedidos,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
