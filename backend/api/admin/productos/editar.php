<?php
/**
 * PUT /api/admin/productos/editar.php
 * Actualizar un producto existente
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Producto.php';

// Solo PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Leer body
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El campo id es requerido', 'data' => null, 'error' => 'MISSING_ID']);
    exit;
}

$id = (int) $input['id'];

// Verificar que existe
$producto = Producto::getById($id);
if (!$producto) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado', 'data' => null, 'error' => 'PRODUCT_NOT_FOUND']);
    exit;
}

// Actualizar solo los campos enviados
$updateData = [];
$fieldsMap = ['categoria_id', 'nombre', 'slug', 'descripcion', 'ingredientes', 'precio_usd', 'imagen_url', 'en_oferta', 'descuento_porcentaje', 'activo'];

foreach ($fieldsMap as $field) {
    if (array_key_exists($field, $input)) {
        $updateData[$field] = $input[$field];
    }
}

if (empty($updateData)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se enviaron campos para actualizar', 'data' => null, 'error' => 'NO_FIELDS']);
    exit;
}

// Si se actualiza nombre y no slug, generar slug
if (isset($updateData['nombre']) && !isset($updateData['slug'])) {
    $updateData['slug'] = generarSlug($updateData['nombre']);
}

$productoActualizado = Producto::update($id, $updateData);

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Producto actualizado exitosamente',
    'data' => $productoActualizado,
    'error' => null,
], JSON_UNESCAPED_UNICODE);

/**
 * Generar slug
 */
function generarSlug(string $texto): string
{
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
        ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
        $texto
    );
    $texto = preg_replace('/[^a-z0-9-]/', '-', $texto);
    $texto = preg_replace('/-+/', '-', $texto);
    $texto = trim($texto, '-');
    return $texto;
}
