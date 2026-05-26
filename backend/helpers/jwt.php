<?php
/**
 * MANÁ FAST FOOD - JWT Helper
 * 
 * Codificación y decodificación de JSON Web Tokens usando HMAC-SHA256.
 */

class JWT
{
    private static string $secret = '';
    private static int $expiry = 86400; // 24 hours default

    /**
     * Inicializa configuración JWT
     */
    public static function init(): void
    {
        self::$secret = getenv('JWT_SECRET') ?: 'mana_fast_food_secret_change_me';
        $expiry = getenv('JWT_EXPIRY') ?: '24h';
        self::$expiry = self::parseExpiry($expiry);
    }

    /**
     * Codifica un payload en un JWT
     */
    public static function encode(array $payload): string
    {
        self::init();

        $header = self::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload['iat'] = time();
        $payload['exp'] = time() + self::$expiry;

        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payloadEncoded", self::$secret, true)
        );

        return "$header.$payloadEncoded.$signature";
    }

    /**
     * Decodifica y valida un JWT
     */
    public static function decode(string $token): ?array
    {
        self::init();

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        // Verify signature
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::$secret, true)
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        // Decode payload
        $data = json_decode(self::base64UrlDecode($payload), true);
        if ($data === null) {
            return null;
        }

        // Check expiration
        if (isset($data['exp']) && $data['exp'] < time()) {
            return null;
        }

        return $data;
    }

    /**
     * Decodifica el payload sin verificar firma (solo lectura)
     */
    public static function decodePayload(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $data = json_decode(self::base64UrlDecode($parts[1]), true);
        return $data ?: null;
    }

    /**
     * Convierte string de expiración a segundos
     */
    private static function parseExpiry(string $expiry): int
    {
        $unit = strtolower(substr($expiry, -1));
        $value = (int) substr($expiry, 0, -1);

        return match ($unit) {
            'h' => $value * 3600,
            'd' => $value * 86400,
            'm' => $value * 60,
            's' => $value,
            default => 86400, // 24h
        };
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
