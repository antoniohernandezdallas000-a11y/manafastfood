<?php
/**
 * MANÁ FAST FOOD - Modelo Cuenta PagoMóvil
 */

require_once __DIR__ . '/../config/database.php';

class CuentaPagoMovil
{
    /**
     * Obtener todas las cuentas
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM cuentas_pagomovil ORDER BY activa DESC, created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener cuenta por ID
     */
    public static function getById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM cuentas_pagomovil WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener cuenta activa
     */
    public static function getActiva(): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM cuentas_pagomovil WHERE activa = 1 LIMIT 1");
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Crear cuenta
     */
    public static function create(array $data): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO cuentas_pagomovil (banco, codigo_banco, titular, telefono, cedula_rif, tipo_cuenta, activa)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['banco'],
            $data['codigo_banco'],
            $data['titular'],
            $data['telefono'],
            $data['cedula_rif'],
            $data['tipo_cuenta'] ?? 'ahorro',
            $data['activa'] ?? 0,
        ]);

        $id = (int) $db->lastInsertId();
        return self::getById($id);
    }

    /**
     * Actualizar cuenta
     */
    public static function update(int $id, array $data): ?array
    {
        $db = Database::getInstance();
        $fields = [];
        $values = [];

        $allowed = ['banco', 'codigo_banco', 'titular', 'telefono', 'cedula_rif', 'tipo_cuenta', 'activa'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return self::getById($id);
        }

        $values[] = $id;
        $sql = "UPDATE cuentas_pagomovil SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        return self::getById($id);
    }

    /**
     * Eliminar cuenta
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cuentas_pagomovil WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Activar una cuenta y desactivar las demás
     */
    public static function activar(int $id): ?array
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Desactivar todas
            $db->exec("UPDATE cuentas_pagomovil SET activa = 0");

            // Activar la solicitada
            $stmt = $db->prepare("UPDATE cuentas_pagomovil SET activa = 1 WHERE id = ?");
            $stmt->execute([$id]);

            $db->commit();
            return self::getById($id);
        } catch (Exception $e) {
            $db->rollBack();
            error_log('[CuentaPagoMovil] Error activando cuenta: ' . $e->getMessage());
            return null;
        }
    }
}
