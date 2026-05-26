<?php
/**
 * MANÁ FAST FOOD - FRONT CONTROLLER
 * Redirige al frontend automáticamente
 */

// Detectar protocolo y host
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Construir URL absoluta
$redirectUrl = $protocol . '://' . $host . '/mana-fast-food/frontend/index.php';

header('Location: ' . $redirectUrl, true, 302);
exit;
