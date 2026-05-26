<?php
/**
 * MANÁ FAST FOOD - Auth Middleware
 * 
 * Verificación de JWT y roles.
 */

require_once __DIR__ . '/../helpers/jwt.php';

class AuthMiddleware
{
    /**
     * Verifica que el token JWT sea válido y retorna los datos del usuario
     *
     * @return array Datos del usuario del token
     */
    public static function verificarToken(): array
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader)) {
            // Algunos servidores (Apache) no pasan Authorization a $_SERVER
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }

        if (empty($authHeader)) {
            // Fallback: leer de getallheaders()
            $allHeaders = getallheaders();
            $authHeader = $allHeaders['Authorization'] ?? $allHeaders['authorization'] ?? '';
        }

        if (empty($authHeader)) {
            self::respond(401, 'Token de autenticación requerido');
        }

        // Extraer token del header "Bearer <token>"
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
        } else {
            $token = $authHeader;
        }

        $payload = JWT::decode($token);

        if ($payload === null) {
            self::respond(401, 'Token inválido o expirado');
        }

        return $payload;
    }

    /**
     * Verifica que el token sea válido y que el rol sea admin
     *
     * @return array Datos del usuario del token
     */
    public static function esAdmin(): array
    {
        $payload = self::verificarToken();

        if (!isset($payload['rol']) || $payload['rol'] !== 'admin') {
            self::respond(403, 'Acceso denegado. Se requiere rol de administrador');
        }

        return $payload;
    }

    /**
     * Helper: respuesta JSON estandarizada
     */
    public static function respond(int $httpCode, string $message, $data = null, ?string $error = null): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $httpCode >= 200 && $httpCode < 300,
            'data'    => $data,
            'message' => $message,
            'error'   => $error,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
