<?php
/**
 * POST /api/admin/ofertas/crear.php
 * Crear una nueva oferta
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Oferta.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

// Validar campos requeridos
$required = ['nombre', 'tipo'];
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

// Validar tipo
$tiposValidos = ['porcentaje', 'precio_fijo', 'combo'];
if (!in_array($input['tipo'], $tiposValidos)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Tipo inválido. Valores válidos: ' . implode(', ', $tiposValidos),
        'data' => null,
        'error' => 'INVALID_TYPE'
    ]);
    exit;
}

// Validar descuento
$descuento = (float) ($input['descuento_porcentaje'] ?? 0);
if ($descuento < 0 || $descuento > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El descuento debe estar entre 0 y 100', 'data' => null, 'error' => 'INVALID_DISCOUNT']);
    exit;
}

$oferta = Oferta::create([
    'nombre' => trim($input['nombre']),
    'tipo' => $input['tipo'],
    'descripcion' => $input['descripcion'] ?? null,
    'descuento_porcentaje' => $descuento,
    'fecha_inicio' => $input['fecha_inicio'] ?? null,
    'fecha_fin' => $input['fecha_fin'] ?? null,
    'activa' => $input['activa'] ?? 1,
    'productos' => $input['productos'] ?? [],
]);

if (!$oferta) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al crear la oferta', 'data' => null, 'error' => 'CREATE_ERROR']);
    exit;
}

$oferta['productos'] = Oferta::getProductos($oferta['id']);

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Oferta creada exitosamente',
    'data' => $oferta,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
