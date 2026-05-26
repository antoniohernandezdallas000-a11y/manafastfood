<?php
/**
 * PUT /api/admin/tasa/actualizar.php
 * Actualizar la tasa BCV
 * - Si modo=automatica: ejecuta scraping del BCV
 * - Si modo=manual: guarda la tasa enviada por el admin
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/TasaBcv.php';
require_once __DIR__ . '/../../../services/BcvScraper.php';

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
$modo = $input['modo'] ?? TasaBcv::getModo();

if ($modo === 'automatica') {
    // ===== MODO AUTOMÁTICO: Scraping BCV =====
    $resultado = BcvScraper::obtenerTasa();

    if (!$resultado['success'] || $resultado['rate'] === null) {
        http_response_code(502);
        $msg = $resultado['message'];
        $userMsg = '⚠ No se pudo obtener la tasa del BCV automáticamente.';
        $userMsg .= "\n\nMotivo: {$msg}";
        $userMsg .= "\n\nPosibles causas:\n• Este servidor no tiene acceso a internet\n• El sitio del BCV (www.bcv.org.ve) está caído o bloqueado\n• Firewall corporativo bloqueando la conexión";
        $userMsg .= "\n\n✅ Solución: Activa el modo Manual y escribe la tasa tú mismo.";
        echo json_encode([
            'success' => false,
            'message' => $userMsg,
            'data' => null,
            'error' => 'BCV_SCRAPE_FAILED'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $tasa = $resultado['rate'];
    $tipo = 'automatica';

    // Insertar nueva tasa en el historial
    $nuevaTasa = TasaBcv::create($tasa, $tipo);

    if (!$nuevaTasa) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar la tasa en la base de datos', 'data' => null, 'error' => 'DB_INSERT_ERROR']);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => '✅ Tasa BCV actualizada automáticamente: Bs. ' . number_format($tasa, 2) . ' por USD',
        'data' => [
            'tasa' => $nuevaTasa,
            'fuente' => $resultado['source'],
            'modo' => 'automatica',
        ],
        'error' => null,
    ], JSON_UNESCAPED_UNICODE);

} else {
    // ===== MODO MANUAL: Admin envía la tasa =====
    if (empty($input['tasa'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El campo tasa es requerido', 'data' => null, 'error' => 'MISSING_RATE']);
        exit;
    }

    $tasa = (float) $input['tasa'];
    if ($tasa <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'La tasa debe ser un valor positivo', 'data' => null, 'error' => 'INVALID_RATE']);
        exit;
    }

    $tipo = 'manual';

    // Insertar nueva tasa en el historial
    $nuevaTasa = TasaBcv::create($tasa, $tipo);

    if (!$nuevaTasa) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar la tasa', 'data' => null, 'error' => 'DB_INSERT_ERROR']);
        exit;
    }

    // Guardar también la tasa personalizada
    TasaBcv::setTasaPersonalizada((string) $tasa);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => '✅ Tasa manual guardada: Bs. ' . number_format($tasa, 2) . ' por USD',
        'data' => [
            'tasa' => $nuevaTasa,
            'fuente' => 'manual',
            'modo' => 'manual',
        ],
        'error' => null,
    ], JSON_UNESCAPED_UNICODE);
}
