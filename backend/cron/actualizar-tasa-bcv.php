<?php
/**
 * CRON - Actualizar tasa BCV automáticamente
 * 
 * Este script se ejecuta desde Windows Task Scheduler
 * o desde un cron job en Linux.
 * 
 * Ejecuta el scraping del BCV y guarda la tasa si el modo es automático.
 * 
 * Programación sugerida:
 *   Windows: Todos los días a las 10:30 AM (cuando el BCV publica la tasa)
 *   Linux:   30 10 * * * php /ruta/a/backend/cron/actualizar-tasa-bcv.php
 */

// Configurar error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/TasaBcv.php';
require_once __DIR__ . '/../services/BcvScraper.php';

echo "[" . date('Y-m-d H:i:s') . "] Iniciando actualización automática de tasa BCV...\n";

// Verificar modo actual
$modo = TasaBcv::getModo();
echo "Modo actual: {$modo}\n";

if ($modo !== 'automatica') {
    echo "Modo manual activo. No se actualiza automáticamente.\n";
    exit(0);
}

// Obtener tasa desde BCV
echo "Consultando tasa BCV...\n";
$resultado = BcvScraper::obtenerTasa();

if (!$resultado['success'] || $resultado['rate'] === null) {
    echo "ERROR: {$resultado['message']}\n";
    exit(1);
}

$tasa = $resultado['rate'];
echo "Tasa obtenida: Bs. {$tasa} por USD (fuente: {$resultado['source']})\n";

// Guardar en la base de datos
$nuevaTasa = TasaBcv::create($tasa, 'automatica');
if (!$nuevaTasa) {
    echo "ERROR: No se pudo guardar la tasa en la base de datos.\n";
    exit(1);
}

echo "✅ Tasa BCV actualizada exitosamente.\n";
echo "ID: {$nuevaTasa['id']}\n";
echo "Tasa: {$nuevaTasa['tasa_usd_bs']}\n";
echo "Fecha: {$nuevaTasa['created_at']}\n";
exit(0);
