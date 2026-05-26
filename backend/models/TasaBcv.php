<?php
/**
 * MANÁ FAST FOOD - Modelo Tasa BCV
 */

require_once __DIR__ . '/../config/database.php';

class TasaBcv
{
    /**
     * Obtener la última tasa registrada
     */
    public static function getUltima(): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT * FROM tasa_bcv
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener historial de tasas
     */
    public static function getHistorial(int $limit = 30): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM tasa_bcv
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener tasa por ID
     */
    public static function getById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM tasa_bcv WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Insertar nueva tasa
     */
    public static function create(float $tasa, string $tipo = 'manual'): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO tasa_bcv (tasa_usd_bs, tipo) VALUES (?, ?)");
        $stmt->execute([$tasa, $tipo]);

        $id = (int) $db->lastInsertId();
        return self::getById($id);
    }

    /**
     * Obtener modo de tasa desde configuracion (auto/manual)
     */
    public static function getModo(): string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = 'tasa_tipo'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['valor'] : 'automatica';
    }

    /**
     * Cambiar modo de tasa
     */
    public static function setModo(string $modo): bool
    {
        if (!in_array($modo, ['automatica', 'manual'])) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO configuracion (clave, valor)
            VALUES ('tasa_tipo', ?)
            ON DUPLICATE KEY UPDATE valor = VALUES(valor)
        ");
        $stmt->execute([$modo]);
        return true;
    }

    /**
     * Obtener tasa personalizada (manual)
     */
    public static function getTasaPersonalizada(): ?string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = 'tasa_personalizada'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['valor'] : null;
    }

    /**
     * Establecer tasa personalizada
     */
    public static function setTasaPersonalizada(string $tasa): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO configuracion (clave, valor)
            VALUES ('tasa_personalizada', ?)
            ON DUPLICATE KEY UPDATE valor = VALUES(valor)
        ");
        $stmt->execute([$tasa]);
        return true;
    }
}
