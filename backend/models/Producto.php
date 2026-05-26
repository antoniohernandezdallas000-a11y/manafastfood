<?php
/**
 * MANÁ FAST FOOD - Modelo Producto
 */

require_once __DIR__ . '/../config/database.php';

class Producto
{
    /**
     * Obtener todos los productos activos
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = 1
            ORDER BY c.orden, p.nombre
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener producto por ID
     */
    public static function getById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener producto por slug
     */
    public static function getBySlug(string $slug): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.slug = ?
        ");
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Buscar productos por nombre o descripción
     */
    public static function search(string $query): array
    {
        $db = Database::getInstance();
        $like = '%' . $query . '%';
        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = 1
              AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR p.ingredientes LIKE ?)
            ORDER BY p.nombre
            LIMIT 20
        ");
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener productos por categoría
     */
    public static function getByCategoria(int $categoriaId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = 1 AND p.categoria_id = ?
            ORDER BY p.nombre
        ");
        $stmt->execute([$categoriaId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener productos en oferta
     */
    public static function getEnOferta(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = 1 AND p.en_oferta = 1
            ORDER BY p.descuento_porcentaje DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los productos (incluyendo inactivos) - para admin
     */
    public static function getAllAdmin(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.activo DESC, c.orden, p.nombre
        ");
        return $stmt->fetchAll();
    }

    /**
     * Crear producto
     */
    public static function create(array $data): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, imagen_url, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['categoria_id'],
            $data['nombre'],
            $data['slug'],
            $data['descripcion'] ?? null,
            $data['ingredientes'] ?? null,
            $data['precio_usd'],
            $data['imagen_url'] ?? null,
            $data['activo'] ?? 1,
        ]);

        $id = (int) $db->lastInsertId();
        return self::getById($id);
    }

    /**
     * Actualizar producto
     */
    public static function update(int $id, array $data): ?array
    {
        $db = Database::getInstance();
        $fields = [];
        $values = [];

        $allowed = ['categoria_id', 'nombre', 'slug', 'descripcion', 'ingredientes', 'precio_usd', 'imagen_url', 'en_oferta', 'descuento_porcentaje', 'activo'];
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
        $sql = "UPDATE productos SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        return self::getById($id);
    }

    /**
     * Eliminar producto
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Contar productos activos
     */
    public static function countActivos(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM productos WHERE activo = 1");
        return (int) $stmt->fetch()['total'];
    }
}
