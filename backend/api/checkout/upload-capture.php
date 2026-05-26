<?php
/**
 * POST /api/checkout/upload-capture.php
 * Subir imagen de capture de pago (FormData)
 */

require_once __DIR__ . '/../../config/cors.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar que se haya enviado un archivo
if (!isset($_FILES['capture']) || $_FILES['capture']['error'] !== UPLOAD_ERR_OK) {
    $errorCode = $_FILES['capture']['error'] ?? -1;
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor',
        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
        UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
        UPLOAD_ERR_NO_FILE => 'No se envió ningún archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco',
    ];

    $message = $errorMessages[$errorCode] ?? 'Error al subir el archivo';
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $message, 'data' => null, 'error' => 'UPLOAD_ERROR']);
    exit;
}

$file = $_FILES['capture'];

// Validar tipo de archivo
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solo se permiten imágenes JPG y PNG', 'data' => null, 'error' => 'INVALID_FILE_TYPE']);
    exit;
}

// Validar extensión
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Extensión de archivo no permitida', 'data' => null, 'error' => 'INVALID_EXTENSION']);
    exit;
}

// Validar tamaño (max 5MB)
$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo de 5MB', 'data' => null, 'error' => 'FILE_TOO_LARGE']);
    exit;
}

// Generar nombre único
$uploadDir = __DIR__ . '/../../uploads/captures/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = 'capture_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
$destPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo', 'data' => null, 'error' => 'SAVE_ERROR']);
    exit;
}

// URL pública del archivo
$publicUrl = 'uploads/captures/' . $filename;

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Capture subido exitosamente',
    'data' => [
        'filename' => $filename,
        'path' => $publicUrl,
        'size' => $file['size'],
        'mime' => $mimeType,
    ],
    'error' => null,
], JSON_UNESCAPED_UNICODE);
