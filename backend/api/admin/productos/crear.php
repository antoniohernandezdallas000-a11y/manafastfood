<?php
/**
 * POST /api/admin/productos/crear.php
 * Crear un nuevo producto
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Producto.php';

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
$required = ['categoria_id', 'nombre', 'precio_usd'];
$errors = [];
foreach ($required as $field) {
    if (!isset($input[$field]) || (empty($input[$field]) && $input[$field] !== 0)) {
        $errors[] = "El campo '$field' es requerido";
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error de validación', 'data' => null, 'error' => implode('; ', $errors)]);
    exit;
}

// Generar slug
$nombre = trim($input['nombre']);
$slug = $input['slug'] ?? generarSlug($nombre);

// Verificar slug único
$existente = Producto::getBySlug($slug);
if ($existente) {
    $slug = $slug . '-' . time();
}

$producto = Producto::create([
    'categoria_id' => (int) $input['categoria_id'],
    'nombre' => $nombre,
    'slug' => $slug,
    'descripcion' => $input['descripcion'] ?? null,
    'ingredientes' => $input['ingredientes'] ?? null,
    'precio_usd' => (float) $input['precio_usd'],
    'imagen_url' => $input['imagen_url'] ?? null,
    'activo' => isset($input['activo']) ? (int) $input['activo'] : 1,
]);

if (!$producto) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al crear el producto', 'data' => null, 'error' => 'CREATE_ERROR']);
    exit;
}

http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Producto creado exitosamente',
    'data' => $producto,
    'error' => null,
], JSON_UNESCAPED_UNICODE);

/**
 * Generar slug a partir de un string
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
