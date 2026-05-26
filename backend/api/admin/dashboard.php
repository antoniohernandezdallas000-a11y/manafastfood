<?php
/**
 * GET /api/admin/dashboard.php
 * Dashboard de administración (requiere rol admin)
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../models/Producto.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Obtener datos del dashboard
$pedidosHoy = Pedido::getPedidosHoy();
$ingresosHoy = Pedido::getIngresosHoy();
$productosActivos = Producto::countActivos();
$pedidosPendientes = Pedido::countPendientes();
$ultimosPedidos = Pedido::getUltimos(5);

// Contar pedidos de hoy
$totalPedidosHoy = count($pedidosHoy);

$dashboard = [
    'total_pedidos_hoy' => $totalPedidosHoy,
    'ingresos_hoy_usd' => $ingresosHoy,
    'productos_activos' => $productosActivos,
    'pedidos_pendientes' => $pedidosPendientes,
    'ultimos_pedidos' => $ultimosPedidos,
];

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Dashboard de administración',
    'data' => $dashboard,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
