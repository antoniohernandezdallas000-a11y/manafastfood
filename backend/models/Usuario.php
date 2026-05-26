<?php
/**
 * MANÁ FAST FOOD - Modelo Usuario
 */

require_once __DIR__ . '/../config/database.php';

class Usuario
{
    /**
     * Obtener todos los usuarios
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id, nombre, telefono, email, rol, activo, created_at, updated_at FROM usuarios ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Obtener usuario por ID
     */
    public static function getById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, nombre, telefono, email, rol, activo, created_at, updated_at FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener usuario por email
     */
    public static function getByEmail(string $email): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Crear usuario
     */
    public static function create(array $data): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO usuarios (nombre, telefono, email, password_hash, rol)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['nombre'],
            $data['telefono'],
            $data['email'],
            $data['password_hash'],
            $data['rol'] ?? 'cliente'
        ]);

        $id = (int) $db->lastInsertId();
        return self::getById($id);
    }

    /**
     * Actualizar usuario
     */
    public static function update(int $id, array $data): ?array
    {
        $db = Database::getInstance();
        $fields = [];
        $values = [];

        $allowed = ['nombre', 'telefono', 'email', 'password_hash', 'rol', 'activo'];
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
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        return self::getById($id);
    }

    /**
     * Eliminar usuario (borrado lógico: desactivar)
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Verificar password contra hash
     */
    public static function verificarPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generar hash de password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
