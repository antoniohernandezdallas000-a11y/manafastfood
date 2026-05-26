<?php
/**
 * GET /api/admin/tasa/diagnosticar.php
 * Diagnóstico de conectividad con fuentes BCV
 */
require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../services/BcvScraper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

AuthMiddleware::esAdmin();

$diag = BcvScraper::diagnosticar();

// También probar obtener tasa
$tasa = null;
$tasaAttempt = BcvScraper::obtenerTasa();
if ($tasaAttempt['success']) {
    $tasa = $tasaAttempt;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Diagnóstico de conectividad BCV',
    'data' => [
        'diagnostico' => $diag,
        'tasa' => $tasa,
        'consejo' => $tasa ? '✅ Conexión exitosa' : '⚠ No se pudo obtener la tasa. Prueba con modo manual.',
    ],
    'error' => null,
], JSON_UNESCAPED_UNICODE);
