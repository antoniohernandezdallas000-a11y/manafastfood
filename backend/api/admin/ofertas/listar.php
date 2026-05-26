<?php
/**
 * GET /api/admin/ofertas/listar.php
 * Listar todas las ofertas
 */

require_once __DIR__ . '/../../../config/cors.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../models/Oferta.php';

// Solo GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido', 'data' => null, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

// Verificar admin
AuthMiddleware::esAdmin();

// Si se pide una oferta específica
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($id) {
    $oferta = Oferta::getById($id);
    if (!$oferta) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Oferta no encontrada', 'data' => null, 'error' => 'OFFER_NOT_FOUND']);
        exit;
    }
    $oferta['productos'] = Oferta::getProductos($id);
    $data = $oferta;
} else {
    $ofertas = Oferta::getAll();
    foreach ($ofertas as &$oferta) {
        $oferta['productos'] = Oferta::getProductos($oferta['id']);
    }
    unset($oferta);
    $data = $ofertas;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Listado de ofertas',
    'data' => $data,
    'error' => null,
], JSON_UNESCAPED_UNICODE);
