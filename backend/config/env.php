<?php
/**
 * MANA FAST FOOD - Cargador de variables de entorno
 * 
 * Reemplaza vlucas/phpdotenv para no depender de Composer.
 * Lee el archivo .env de la raiz del proyecto y expone
 * las variables via getenv() / $_ENV / $_SERVER.
 */

function loadEnv(string $path = null): void
{
    // Buscar .env desde la raiz del proyecto
    if ($path === null) {
        // Partimos desde backend/config/ subiendo 2 niveles
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
    }

    if (!file_exists($path)) {
        error_log('[env.php] Archivo .env no encontrado en: ' . $path);
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        error_log('[env.php] Error al leer .env');
        return;
    }

    foreach ($lines as $line) {
        // Ignorar comentarios
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Ignorar lineas sin signo =
        if (!str_contains($line, '=')) {
            continue;
        }

        // Separar clave=valor (solo la primera ocurrencia de =)
        $parts = explode('=', $line, 2);
        $key = trim($parts[0]);
        $value = trim($parts[1] ?? '');

        if (empty($key)) {
            continue;
        }

        // Remover comillas simples/dobles del valor si existen
        if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        // Asignar a los superglobales
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Cargar automaticamente al incluir este archivo
loadEnv();
