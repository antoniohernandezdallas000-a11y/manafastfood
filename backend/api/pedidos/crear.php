<?php
/**
 * POST /api/pedidos/crear.php
 * Crear un nuevo pedido (requiere autenticación)
 */

require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../models/Pedido.php';
require_once __DIR__ . '/../../models/Producto.php';

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

// Validar campos requeridos
$required = ['items', 'tipo_entrega'];
$errors = [];
foreach ($required as $field) {
    if (empty($input[$field])) {
        $errors[] = "El campo '$field' es requerido";
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error de validación', 'data' => null, 'error' => implode('; ', $errors)]);
    exit;
}

// Validar tipo de entrega
$tiposValidos = ['delivery', 'retiro'];
if (!in_array($input['tipo_entrega'], $tiposValidos)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de entrega inválido. Debe ser "delivery" o "retiro"', 'data' => null, 'error' => 'INVALID_DELIVERY_TYPE']);
    exit;
}

// Si es delivery, dirección es requerida
if ($input['tipo_entrega'] === 'delivery' && empty($input['direccion'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La dirección es requerida para entrega a domicilio', 'data' => null, 'error' => 'MISSING_ADDRESS']);
    exit;
}

// Validar items
if (!is_array($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Debe incluir al menos un producto en el pedido', 'data' => null, 'error' => 'EMPTY_CART']);
    exit;
}

// Calcular subtotal y preparar items con datos del producto
$itemsData = [];
$subtotal = 0;

foreach ($input['items'] as $item) {
    if (empty($item['producto_id']) || empty($item['cantidad'])) {
        continue;
    }

    $producto = Producto::getById((int) $item['producto_id']);
    if (!$producto || !$producto['activo']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Producto ID {$item['producto_id']} no encontrado o inactivo", 'data' => null, 'error' => 'PRODUCT_UNAVAILABLE']);
        exit;
    }

    $precio = $producto['en_oferta'] ? (float) $producto['precio_oferta_usd'] : (float) $producto['precio_usd'];
    $cantidad = (int) $item['cantidad'];
    $itemSubtotal = round($precio * $cantidad, 2);
    $subtotal += $itemSubtotal;

    $itemsData[] = [
        'producto_id' => $producto['id'],
        'nombre_producto' => $producto['nombre'],
        'cantidad' => $cantidad,
        'precio_unitario_usd' => $precio,
    ];
}

// Aplicar descuento si existe
$descuento = (float) ($input['descuento_usd'] ?? 0);
$totalUsd = round($subtotal - $descuento, 2);
if ($totalUsd < 0) {
    $totalUsd = 0;
}

// Crear pedido
$pedido = Pedido::create([
    'usuario_id' => $payload['user_id'],
    'tipo_entrega' => $input['tipo_entrega'],
    'direccion' => $input['direccion'] ?? null,
    'ciudad' => $input['ciudad'] ?? null,
    'telefono_contacto' => $input['telefono'] ?? $payload['telefono'] ?? null,
    'notas' => $input['notas'] ?? null,
    'subtotal_usd' => $subtotal,
    'descuento_usd' => $descuento,
    'total_usd' => $totalUsd,
    'referencia_pago' => $input['referencia_pago'] ?? null,
    'capture_path' => $input['capture_path'] ?? null,
    'metodo_pago' => $input['metodo_pago'] ?? 'pagomovil',
    'pago_confirmado' => !empty($input['referencia_pago']),
    'items' => $itemsData,
]);

if (!$pedido) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al crear el pedido', 'data' => null, 'error' => 'ORDER_CREATE_ERROR']);
    exit;
}

// Obtener detalles
$detalles = Pedido::getDetalles($pedido['id']);
$pedido['detalles'] = $detalles;

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Pedido creado exitosamente',
    'data' => $pedido,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
