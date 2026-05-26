<?php
/**
 * MANÁ FAST FOOD - Modelo Pedido
 */

require_once __DIR__ . '/../config/database.php';

class Pedido
{
    /**
     * Obtener todos los pedidos
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email, u.telefono AS usuario_telefono
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener pedido por ID
     */
    public static function getById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email, u.telefono AS usuario_telefono
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener pedido por número de pedido
     */
    public static function getByNumero(string $numero): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre AS usuario_nombre, u.email AS usuario_email, u.telefono AS usuario_telefono
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.numero_pedido = ?
        ");
        $stmt->execute([$numero]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Obtener pedidos por usuario
     */
    public static function getByUsuario(int $usuarioId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM pedidos
            WHERE usuario_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener detalles de un pedido
     */
    public static function getDetalles(int $pedidoId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT dp.*, p.nombre AS producto_nombre, p.imagen_url
            FROM detalle_pedido dp
            JOIN productos p ON dp.producto_id = p.id
            WHERE dp.pedido_id = ?
        ");
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll();
    }

    /**
     * Crear pedido con sus detalles (transacción)
     */
    public static function create(array $data): ?array
    {
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // Generar número de pedido único
            $numeroPedido = self::generarNumeroPedido($db);

            // Obtener tasa BCV actual
            $tasaBcv = self::obtenerTasaActual($db);

            // Insertar pedido
            $stmt = $db->prepare("
                INSERT INTO pedidos (
                    usuario_id, numero_pedido, tipo_entrega, direccion, ciudad,
                    telefono_contacto, notas, subtotal_usd, descuento_usd,
                    total_usd, tasa_bcv, total_bs, referencia_pago, capture_path,
                    metodo_pago, pago_confirmado, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $subtotal = $data['subtotal_usd'] ?? 0;
            $descuento = $data['descuento_usd'] ?? 0;
            $totalUsd = $data['total_usd'] ?? $subtotal;
            $totalBs = round($totalUsd * $tasaBcv, 2);
            $metodoPago = $data['metodo_pago'] ?? 'pagomovil';
            $pagoConfirmado = $data['pago_confirmado'] ?? 0;
            $estado = $data['estado'] ?? 'pendiente';

            $stmt->execute([
                $data['usuario_id'],
                $numeroPedido,
                $data['tipo_entrega'],
                $data['direccion'] ?? null,
                $data['ciudad'] ?? null,
                $data['telefono_contacto'] ?? null,
                $data['notas'] ?? null,
                $subtotal,
                $descuento,
                $totalUsd,
                $tasaBcv,
                $totalBs,
                $data['referencia_pago'] ?? null,
                $data['capture_path'] ?? null,
                $metodoPago,
                $pagoConfirmado ? 1 : 0,
                $estado,
            ]);

            $pedidoId = (int) $db->lastInsertId();

            // Insertar detalles del pedido
            if (!empty($data['items'])) {
                $stmtDetalle = $db->prepare("
                    INSERT INTO detalle_pedido (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario_usd, subtotal_usd)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                foreach ($data['items'] as $item) {
                    $precioUnitario = $item['precio_unitario_usd'] ?? 0;
                    $cantidad = $item['cantidad'] ?? 1;
                    $itemSubtotal = round($precioUnitario * $cantidad, 2);

                    $stmtDetalle->execute([
                        $pedidoId,
                        $item['producto_id'],
                        $item['nombre_producto'] ?? null,
                        $cantidad,
                        $precioUnitario,
                        $itemSubtotal,
                    ]);
                }
            }

            $db->commit();

            return self::getById($pedidoId);

        } catch (Exception $e) {
            $db->rollBack();
            error_log('[Pedido] Error creating order: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualizar estado del pedido
     */
    public static function cambiarEstado(int $id, string $estado): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);

        return self::getById($id);
    }

    /**
     * Confirmar pago de pedido
     */
    public static function confirmarPago(int $id, string $referencia, ?string $capturePath = null): ?array
    {
        $db = Database::getInstance();

        if ($capturePath) {
            $stmt = $db->prepare("UPDATE pedidos SET pago_confirmado = 1, referencia_pago = ?, capture_path = ? WHERE id = ?");
            $stmt->execute([$referencia, $capturePath, $id]);
        } else {
            $stmt = $db->prepare("UPDATE pedidos SET pago_confirmado = 1, referencia_pago = ? WHERE id = ?");
            $stmt->execute([$referencia, $id]);
        }

        return self::getById($id);
    }

    /**
     * Obtener pedidos del día de hoy
     */
    public static function getPedidosHoy(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.*, u.nombre AS usuario_nombre
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE DATE(p.created_at) = CURDATE()
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Obtener total de ingresos del día
     */
    public static function getIngresosHoy(): float
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT COALESCE(SUM(total_usd), 0) AS total
            FROM pedidos
            WHERE DATE(created_at) = CURDATE()
              AND pago_confirmado = 1
        ");
        return (float) $stmt->fetch()['total'];
    }

    /**
     * Contar pedidos pendientes
     */
    public static function countPendientes(): int
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT COUNT(*) AS total FROM pedidos
            WHERE estado IN ('pendiente', 'preparando')
        ");
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Obtener últimos N pedidos
     */
    public static function getUltimos(int $limit = 5): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre AS usuario_nombre
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Generar número de pedido único: MANA-XXXXX
     */
    private static function generarNumeroPedido(PDO $db): string
    {
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $numero = 'MANA-' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $stmt = $db->prepare("SELECT COUNT(*) AS total FROM pedidos WHERE numero_pedido = ?");
            $stmt->execute([$numero]);
            if ((int) $stmt->fetch()['total'] === 0) {
                return $numero;
            }
        }
        // Fallback con timestamp
        return 'MANA-' . date('Ymd') . '-' . random_int(100, 999);
    }

    /**
     * Obtener tasa BCV actual desde la BD
     */
    private static function obtenerTasaActual(PDO $db): float
    {
        $stmt = $db->query("SELECT tasa_usd_bs FROM tasa_bcv ORDER BY created_at DESC LIMIT 1");
        $result = $stmt->fetch();
        return $result ? (float) $result['tasa_usd_bs'] : 1.0;
    }
}
