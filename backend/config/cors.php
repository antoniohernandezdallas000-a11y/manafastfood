<?php
/**
 * MANÁ FAST FOOD - Configuración CORS
 * 
 * Se ejecuta en cada petición para permitir peticiones cross-origin.
 */

// Cargar .env antes que nada
require_once __DIR__ . '/env.php';

// ─── Orígenes permitidos ───
$allowedOrigins = [
    'http://localhost:3000',
    'http://localhost:5173',
    'http://localhost:8000',
    'http://localhost:8080',
    'http://localhost',
    'http://127.0.0.1',
    'https://manafastfood.com',
    'https://www.manafastfood.com',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header('Access-Control-Allow-Origin: *');
}

// ─── Headers permitidos ───
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

// ─── Content-Type por defecto ───
header('Content-Type: application/json; charset=utf-8');

// ─── Manejo de preflight OPTIONS ───
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
