<?php
/**
 * MANÁ FAST FOOD - Modelo Oferta
 */

require_once __DIR__ . '/../config/database.php';

class Oferta
{
    /**
     * Obtener todas las ofertas
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT o.*,
                   (SELECT COUNT(*) FROM productos_en_oferta po WHERE po.oferta_id = o.id) AS total_productos
            FROM ofertas o
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener ofertas activas
     */
    public static function getActivas(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT * FROM ofertas
            WHERE activa = 1
              AND (fecha_inicio IS NULL OR fecha_inicio <= NOW())
              AND (fecha_fin IS NULL OR fecha_fin >= NOW())
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener oferta por ID
     */
    public static function getById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT o.*,
                   (SELECT COUNT(*) FROM productos_en_oferta po WHERE po.oferta_id = o.id) AS total_productos
            FROM ofertas o
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener productos de una oferta
     */
    public static function getProductos(int $ofertaId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.id, p.nombre, p.slug, p.precio_usd, p.precio_oferta_usd, p.imagen_url
            FROM productos_en_oferta po
            JOIN productos p ON po.producto_id = p.id
            WHERE po.oferta_id = ?
        ");
        $stmt->execute([$ofertaId]);
        return $stmt->fetchAll();
    }

    /**
     * Crear oferta
     */
    public static function create(array $data): ?array
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO ofertas (nombre, tipo, descripcion, descuento_porcentaje, fecha_inicio, fecha_fin, activa)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['nombre'],
                $data['tipo'],
                $data['descripcion'] ?? null,
                $data['descuento_porcentaje'] ?? 0,
                $data['fecha_inicio'] ?? null,
                $data['fecha_fin'] ?? null,
                $data['activa'] ?? 1,
            ]);

            $ofertaId = (int) $db->lastInsertId();

            // Vincular productos si se proporcionan
            if (!empty($data['productos']) && is_array($data['productos'])) {
                $stmtProducto = $db->prepare("INSERT INTO productos_en_oferta (oferta_id, producto_id) VALUES (?, ?)");
                foreach ($data['productos'] as $productoId) {
                    $stmtProducto->execute([$ofertaId, (int) $productoId]);
                }
            }

            $db->commit();
            return self::getById($ofertaId);
        } catch (Exception $e) {
            $db->rollBack();
            error_log('[Oferta] Error creating offer: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar oferta
     */
    public static function update(int $id, array $data): ?array
    {
        $db = Database::getInstance();
        $fields = [];
        $values = [];

        $allowed = ['nombre', 'tipo', 'descripcion', 'descuento_porcentaje', 'fecha_inicio', 'fecha_fin', 'activa'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (!empty($fields)) {
            $values[] = $id;
            $sql = "UPDATE ofertas SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
        }

        // Actualizar productos vinculados si se proporcionan
        if (array_key_exists('productos', $data) && is_array($data['productos'])) {
            $db->prepare("DELETE FROM productos_en_oferta WHERE oferta_id = ?")->execute([$id]);
            $stmtProducto = $db->prepare("INSERT INTO productos_en_oferta (oferta_id, producto_id) VALUES (?, ?)");
            foreach ($data['productos'] as $productoId) {
                $stmtProducto->execute([$id, (int) $productoId]);
            }
        }

        return self::getById($id);
    }

    /**
     * Eliminar oferta
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM ofertas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Activar/Desactivar oferta (toggle)
     */
    public static function toggle(int $id): ?array
    {
        $oferta = self::getById($id);
        if (!$oferta) {
            return null;
        }

        $db = Database::getInstance();
        $nuevoEstado = $oferta['activa'] ? 0 : 1;
        $stmt = $db->prepare("UPDATE ofertas SET activa = ? WHERE id = ?");
        $stmt->execute([$nuevoEstado, $id]);

        return self::getById($id);
    }
}
