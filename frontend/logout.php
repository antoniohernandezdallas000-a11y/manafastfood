<?php
/**
 * MANÁ FAST FOOD - LOGOUT
 * Limpia la sesión y redirige al login
 */
session_start();
session_destroy();

// Limpiar cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirigir al login
header('Location: login.php');
exit;
