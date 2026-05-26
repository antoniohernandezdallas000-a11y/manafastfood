-- ============================================
-- MANÁ FAST FOOD - ESQUEMA DE BASE DE DATOS
-- MySQL 8.0+
-- ============================================

CREATE DATABASE IF NOT EXISTS mana_fast_food CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mana_fast_food;

-- ============================================
-- 1. USUARIOS (clientes + admin)
-- ============================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. CATEGORÍAS
-- ============================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. PRODUCTOS
-- precio_oferta_usd se calcula automáticamente
-- ============================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    descripcion TEXT,
    ingredientes TEXT,
    precio_usd DECIMAL(10,2) NOT NULL,
    imagen_url VARCHAR(500),
    en_oferta TINYINT(1) DEFAULT 0,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    precio_oferta_usd DECIMAL(10,2) GENERATED ALWAYS AS (precio_usd * (1 - descuento_porcentaje/100)) STORED,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,
    INDEX idx_categoria (categoria_id),
    INDEX idx_activo (activo),
    INDEX idx_oferta (en_oferta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. OFERTAS (promociones generales)
-- ============================================
CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    tipo ENUM('porcentaje', 'precio_fijo', 'combo') NOT NULL,
    descripcion TEXT,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    activa TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. PRODUCTOS_EN_OFERTA (relación many-to-many)
-- ============================================
CREATE TABLE productos_en_oferta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    producto_id INT NOT NULL,
    FOREIGN KEY (oferta_id) REFERENCES ofertas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_oferta_producto (oferta_id, producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. PEDIDOS
-- ============================================
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    numero_pedido VARCHAR(20) UNIQUE NOT NULL,
    tipo_entrega ENUM('delivery', 'retiro') NOT NULL,
    direccion TEXT,
    ciudad VARCHAR(100),
    estado VARCHAR(50) DEFAULT 'pendiente',
    telefono_contacto VARCHAR(20),
    notas TEXT,
    subtotal_usd DECIMAL(10,2) NOT NULL,
    descuento_usd DECIMAL(10,2) DEFAULT 0,
    total_usd DECIMAL(10,2) NOT NULL,
    tasa_bcv DECIMAL(10,2) NOT NULL,
    total_bs DECIMAL(10,2) NOT NULL,
    referencia_pago VARCHAR(100),
    capture_path VARCHAR(500),
    metodo_pago VARCHAR(50) DEFAULT 'pagomovil',
    pago_confirmado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_estado (estado),
    INDEX idx_fecha (created_at),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. DETALLES DEL PEDIDO
-- ============================================
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    nombre_producto VARCHAR(200),
    cantidad INT NOT NULL,
    precio_unitario_usd DECIMAL(10,2) NOT NULL,
    subtotal_usd DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. CUENTAS PAGOMÓVIL
-- ============================================
CREATE TABLE cuentas_pagomovil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    banco VARCHAR(100) NOT NULL,
    codigo_banco VARCHAR(10) NOT NULL,
    titular VARCHAR(200) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    cedula_rif VARCHAR(20) NOT NULL,
    tipo_cuenta ENUM('ahorro', 'corriente') DEFAULT 'ahorro',
    activa TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. TASA BCV (historial)
-- ============================================
CREATE TABLE tasa_bcv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tasa_usd_bs DECIMAL(10,2) NOT NULL,
    tipo ENUM('automatica', 'manual') DEFAULT 'automatica',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. CONFIGURACIÓN
-- ============================================
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. TRANSACCIONES
-- ============================================
CREATE TABLE transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    monto_usd DECIMAL(10,2) NOT NULL,
    monto_bs DECIMAL(10,2) NOT NULL,
    moneda VARCHAR(3) DEFAULT 'USD',
    metodo_pago ENUM('c2p', 'stripe') NOT NULL,
    referencia_banco VARCHAR(100),
    stripe_payment_intent VARCHAR(100),
    estado ENUM('pendiente', 'completado', 'fallido') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    INDEX idx_referencia (referencia_banco),
    INDEX idx_estado_pago (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- ADMIN POR DEFECTO (password: admin123)
-- Hash generado de 'admin123' con bcrypt
INSERT INTO usuarios (nombre, telefono, email, password_hash, rol) VALUES
('Administrador', '04140000000', 'admin@mana.com', '$2y$12$ulcx465obpJXDl1i3m9C0On0V75c3ZQu98QHZcysJaRHL607ioEJ.', 'admin');

-- CATEGORÍAS
INSERT INTO categorias (nombre, slug, orden) VALUES
('Hamburguesas', 'hamburguesas', 1),
('Hamburguesas Especiales', 'especiales', 2),
('Hot Dogs', 'hotdogs', 3),
('Enrollados', 'enrollados', 4),
('Pepitos', 'pepitos', 5),
('Especiales Fritos', 'papas', 6),
('Extras', 'extras', 7),
('Bebidas', 'bebidas', 8);

-- ============================================
-- PRODUCTOS - HAMBURGUESAS (categoria_id = 1)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(1, 'Maná Burger Clásica', 'mana-burger-clasica',
 'La hamburguesa insignia de Maná. Carne de res 100% angus, lechuga, tomate, cebolla caramelizada y nuestra salsa especial Maná.',
 'Carne de res angus 150g, pan artesanal, lechuga, tomate, cebolla caramelizada, salsa Maná, queso amarillo, pepinillos',
 8.99, 1),
(1, 'Maná Doble Carne', 'mana-doble-carne',
 'Dos carnes de res angus con queso doble, para los que tienen hambre de verdad.',
 'Doble carne angus 300g, pan artesanal, doble queso amarillo, lechuga, tomate, cebolla, salsa Maná, bacon',
 11.50, 1),
(1, 'Maná Pollo Crispy', 'mana-pollo-crispy',
 'Pollo empanizado crujiente con lechuga, tomate y mayonesa especial de la casa.',
 'Pechuga de pollo empanizada 180g, pan artesanal, lechuga, tomate, mayonesa especial, queso blanco',
 10.50, 1),
(1, 'Maná Veggie', 'mana-veggie',
 'Opción vegetariana con torta de garbanzo, quinoa, espinaca y vegetales frescos.',
 'Torta garbanzo-quinoa, pan integral, espinaca, tomate, cebolla morada, aguacate, salsa yogur',
 9.99, 1);

-- ============================================
-- PRODUCTOS - HAMBURGUESAS ESPECIALES (categoria_id = 2)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(2, 'Maná BBQ Bacon', 'mana-bbq-bacon',
 'Hamburguesa bañada en salsa BBQ ahumada con bacon crujiente y aros de cebolla.',
 'Carne angus 150g, bacon crujiente, salsa BBQ ahumada, aros de cebolla, queso cheddar, pan brioche, lechuga, tomate',
 13.99, 1),
(2, 'Maná Chipotle', 'mana-chipotle',
 'Intensa y picante. Carne angus con salsa chipotle ahumada, jalapeños y guacamole.',
 'Carne angus 150g, salsa chipotle, jalapeños frescos, guacamole, queso pepper jack, pan artesanal',
 14.50, 1),
(2, 'Maná Costeña', 'mana-costena',
 'Inspirada en los sabores del mar. Carne angus con queso costeño, chorizo criollo y plátano maduro.',
 'Carne angus 150g, queso costeño, chorizo criollo, plátano maduro frito, salsa de la casa, pan artesanal',
 15.00, 1),
(2, 'Maná Trufada', 'mana-trufada',
 'Hamburguesa premium con salsa de trufa negra, champiñones salteados y queso brie.',
 'Carne angus 150g, salsa de trufa negra, champiñones salteados, queso brie, rúcula, pan brioche artesanal',
 16.99, 1),
(2, 'Maná Hawaiana', 'mana-hawaiana',
 'La polémica favorita. Carne angus con piña asada, jamón glaseado y salsa teriyaki.',
 'Carne angus 150g, piña asada, jamón glaseado, salsa teriyaki, queso mozzarella, pan artesanal',
 12.99, 1);

-- ============================================
-- PRODUCTOS - HOT DOGS (categoria_id = 3)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(3, 'Hot Dog Maná Supreme', 'hot-dog-mana-supreme',
 'Salchicha alemana con todos los toppings: papas hilo, queso amarillo, cebolla, salsa de tomate, mostaza, mayonesa y salsa Maná.',
 'Salchicha alemana 180g, pan artesanal, papas hilo, queso amarillo, cebolla rallada, salsa de tomate, mostaza, mayonesa, salsa Maná',
 7.50, 1),
(3, 'Hot Dog Catia', 'hot-dog-catia',
 'Estilo Catia con queso rayado, jamón picado, cebolla, repollo y salsas.',
 'Salchicha alemana, pan artesanal, queso rayado, jamón picado, cebolla, repollo, salsas rosada y de tomate',
 8.00, 1),
(3, 'Hot Dog BBQ', 'hot-dog-bbq',
 'Hot dog con salsa BBQ, bacon crujiente y queso cheddar fundido.',
 'Salchicha alemana 180g, pan artesanal, salsa BBQ, bacon crujiente, queso cheddar, aros de cebolla fritos',
 8.99, 1),
(3, 'Hot Dog Hawaiano', 'hot-dog-hawaiano',
 'Salchicha con piña asada, jamón y queso mozzarella fundido.',
 'Salchicha alemana, pan artesanal, piña asada, jamón, queso mozzarella, salsa dulce',
 8.50, 1);

-- ============================================
-- PRODUCTOS - ENROLLADOS (categoria_id = 4)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(4, 'Enrollado de Pepperoni', 'enrollado-de-pepperoni',
 'Masa artesanal rellena de pepperoni y queso mozzarella, horneado a la perfección.',
 'Masa artesanal, pepperoni, queso mozzarella, orégano, salsa de tomate',
 6.99, 1),
(4, 'Enrollado de Jamón y Queso', 'enrollado-de-jamon-y-queso',
 'Clásico enrollado con jamón y queso fundido. Simple y perfecto.',
 'Masa artesanal, jamón, queso mozzarella y amarillo, orégano',
 5.99, 1),
(4, 'Enrollado Cheddar Bacon', 'enrollado-cheddar-bacon',
 'Enrollado con bacon crujiente, queso cheddar y cebolla caramelizada.',
 'Masa artesanal, bacon, queso cheddar, cebolla caramelizada, salsa BBQ',
 7.99, 1),
(4, 'Enrollado Maná Especial', 'enrollado-mana-especial',
 'Enrollado supremo: pepperoni, jamón, bacon, tres quesos y salsa especial.',
 'Masa artesanal, pepperoni, jamón, bacon, queso mozzarella, cheddar, parmesano, salsa especial Maná',
 8.99, 1);

-- ============================================
-- PRODUCTOS - PEPITOS (categoria_id = 5)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(5, 'Pepito de Pollo', 'pepito-de-pollo',
 'Pechuga de pollo salteada con vegetales, queso amarillo y salsa de la casa en pan artesanal.',
 'Pechuga de pollo, pan artesanal, queso amarillo, pimentón, cebolla, tomate, lechuga, salsa Maná, papas',
 9.99, 1),
(5, 'Pepito de Res', 'pepito-de-res',
 'Carne de res salteada con vegetales, queso amarillo, aguacate y salsas. ¡Espectacular!',
 'Carne de res, pan artesanal, queso amarillo, pimentón, cebolla, tomate, aguacate, salsa Maná, papas',
 10.99, 1),
(5, 'Pepito Mixto', 'pepito-mixto',
 'La combinación perfecta: pollo y res salteados con todos los vegetales y doble queso.',
 'Pollo y res, pan artesanal, doble queso, pimentón, cebolla, tomate, bacon, aguacate, salsa Maná, papas',
 11.99, 1),
(5, 'Pepito Vegano', 'pepito-vegano',
 'Proteína vegetal salteada con vegetales frescos, aguacate y salsa vegana de la casa.',
 'Proteína vegetal (soya texturizada), pan integral, pimentón, cebolla, tomate, aguacate, lechuga, salsa vegana',
 9.50, 1);

-- ============================================
-- PRODUCTOS - ESPECIALES FRITOS (categoria_id = 6)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(6, 'Papas Maná Cheddar', 'papas-mana-cheddar',
 'Papas fritas bañadas en queso cheddar fundido y bacon crujiente.',
 'Papas fritas, queso cheddar fundido, bacon crujiente, cebollín',
 6.50, 1),
(6, 'Papas Maná Supreme', 'papas-mana-supreme',
 'Papas con carne mechada, queso cheddar, guacamole, crema agria y pico de gallo.',
 'Papas fritas, carne mechada, queso cheddar, guacamole, crema agria, pico de gallo',
 8.99, 1),
(6, 'Tequeños (6 unid.)', 'tequenos-6-unidades',
 'Tequeños de queso blanco fritos, crujientes por fuera y suaves por dentro.',
 'Queso blanco, masa de tequeño, aceite vegetal',
 5.50, 1),
(6, 'Aros de Cebolla', 'aros-de-cebolla',
 'Aros de cebolla empanizados, fritos y servidos con salsa ranch.',
 'Cebolla, empanizado artesanal, salsa ranch',
 4.99, 1),
(6, 'Alitas BBQ (8 unid.)', 'alitas-bbq-8-unidades',
 'Alitas de pollo bañadas en salsa BBQ ahumada, servidas con vegetales frescos.',
 'Alitas de pollo, salsa BBQ ahumada, vegetales frescos (apio, zanahoria)',
 9.99, 1);

-- ============================================
-- PRODUCTOS - EXTRAS (categoria_id = 7)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(7, 'Extra Queso', 'extra-queso',
 'Porción adicional de queso amarillo fundido para tu pedido.',
 'Queso amarillo',
 1.50, 1),
(7, 'Extra Tocineta', 'extra-tocineta',
 'Porción adicional de tocineta (bacon) crujiente.',
 'Bacon ahumado',
 2.00, 1),
(7, 'Extra Aguacate', 'extra-aguacate',
 'Mitad de aguacate fresco en rebanadas.',
 'Aguacate fresco',
 1.75, 1),
(7, 'Extra Carne', 'extra-carne',
 'Porción adicional de carne angus de 150g.',
 'Carne angus 150g',
 3.50, 1),
(7, 'Salsa Maná 100ml', 'salsa-mana-100ml',
 'La salsa especial de la casa. Para llevar.',
 'Salsa especial Maná',
 1.00, 1),
(7, 'Papas Fritas (Porción)', 'papas-fritas-porcion',
 'Porción individual de papas fritas crocantes.',
 'Papas, aceite vegetal, sal',
 3.00, 1);

-- ============================================
-- PRODUCTOS - BEBIDAS (categoria_id = 8)
-- ============================================
INSERT INTO productos (categoria_id, nombre, slug, descripcion, ingredientes, precio_usd, activo) VALUES
(8, 'Coca-Cola 500ml', 'coca-cola-500ml',
 'Coca-Cola original 500ml bien fría.',
 'Agua carbonatada, azúcar, color caramelo, ácido fosfórico, cafeína',
 2.00, 1),
(8, 'Coca-Cola Zero 500ml', 'coca-cola-zero-500ml',
 'Coca-Cola sin azúcar 500ml.',
 'Agua carbonatada, edulcorantes, color caramelo, ácido fosfórico, cafeína',
 2.00, 1),
(8, 'Pepsi 500ml', 'pepsi-500ml',
 'Pepsi original 500ml.',
 'Agua carbonatada, azúcar, color caramelo, ácido fosfórico, cafeína',
 1.80, 1),
(8, 'Agua Mineral 500ml', 'agua-mineral-500ml',
 'Agua mineral pura sin gas.',
 'Agua mineral',
 1.50, 1),
(8, 'Jugo Natural de Naranja', 'jugo-natural-de-naranja',
 'Jugo de naranja recién exprimido 400ml.',
 'Naranjas 100% naturales',
 3.50, 1),
(8, 'Malta Polar 355ml', 'malta-polar-355ml',
 'Malta Polar bien fría.',
 'Agua, malta, azúcar, lúpulo',
 2.50, 1),
(8, 'Papelón con Limón', 'papelon-con-limon',
 'Refrescante papelón con limón natural, preparado al momento.',
 'Papelón, limón natural, agua, hielo',
 2.50, 1),
(8, 'Milkshake de Chocolate', 'milkshake-de-chocolate',
 'Batido cremoso de chocolate con leche y helado.',
 'Leche, helado de chocolate, crema de cacao',
 4.99, 1),
(8, 'Milkshake de Vainilla', 'milkshake-de-vainilla',
 'Batido cremoso de vainilla con leche y helado.',
 'Leche, helado de vainilla, esencia de vainilla',
 4.99, 1),
(8, 'Milkshake de Fresa', 'milkshake-de-fresa',
 'Batido cremoso de fresa con leche y helado.',
 'Leche, helado de fresa, fresas naturales',
 4.99, 1);

-- ============================================
-- TASA BCV INICIAL
-- ============================================
INSERT INTO tasa_bcv (tasa_usd_bs, tipo) VALUES (62.45, 'automatica');

-- ============================================
-- CONFIGURACIÓN INICIAL
-- ============================================
INSERT INTO configuracion (clave, valor) VALUES
('tasa_tipo', 'automatica'),
('tasa_personalizada', ''),
('cuenta_whatsapp', '584140000000'),
('direccion_local', 'Av. Principal, Local 5, Caracas'),
('nombre_negocio', 'Maná Fast Food'),
('horario_lunes_viernes', '11:00 AM - 10:00 PM'),
('horario_sabado', '11:00 AM - 11:00 PM'),
('horario_domingo', '12:00 PM - 8:00 PM'),
('instagram', '@manafastfood'),
('facebook', 'ManáFastFood'),
('telefono_contacto', '0212-5551234'),
('max_delivery_distance_km', '10'),
('delivery_cost_usd', '2.50'),
('minimum_order_usd', '5.00'),
('whatsapp_template', '¡Hola! Quiero confirmar mi pedido #%pedido% de Maná Fast Food.');

-- ============================================
-- CUENTA PAGOMÓVIL INICIAL
-- ============================================
INSERT INTO cuentas_pagomovil (banco, codigo_banco, titular, telefono, cedula_rif, tipo_cuenta, activa) VALUES
('Mercantil Banco', '0105', 'Maná Fast Food C.A.', '04141234567', 'J-12345678-9', 'corriente', 1);

-- ============================================
-- OFERTAS INICIALES
-- ============================================
INSERT INTO ofertas (nombre, tipo, descripcion, descuento_porcentaje, fecha_inicio, fecha_fin, activa) VALUES
('Happy Hour Maná', 'porcentaje', '15% de descuento en hamburguesas clásicas y hot dogs de lunes a viernes de 2pm a 5pm.', 15.00, '2026-05-20 00:00:00', '2026-12-31 23:59:59', 1),
('Combo Familiar', 'combo', '2 Maná Burgers + 2 Papas + 2 Bebidas con 20% de descuento.', 20.00, '2026-05-01 00:00:00', '2026-06-30 23:59:59', 1);

-- Productos en ofertas
INSERT INTO productos_en_oferta (oferta_id, producto_id) VALUES
(1, 1), (1, 9), (1, 10), (1, 11),  -- Happy Hour: productos 1(Maná Burger), 9(Hot Dog Supreme), 10(Hot Dog Catia), 11(Hot Dog BBQ)
(2, 1), (2, 2), (2, 33), (2, 34), (2, 35);  -- Combo Familiar

-- ============================================
-- PEDIDOS DE EJEMPLO
-- ============================================
-- Se requiere un cliente de ejemplo
INSERT INTO usuarios (nombre, telefono, email, password_hash, rol) VALUES
('Carlos Mendoza', '04121234567', 'carlos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('María Rodríguez', '04149876543', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'),
('José García', '04265554321', 'jose@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente');

-- Pedidos de ejemplo
INSERT INTO pedidos (usuario_id, numero_pedido, tipo_entrega, direccion, estado, telefono_contacto, notas, subtotal_usd, descuento_usd, total_usd, tasa_bcv, total_bs, referencia_pago, pago_confirmado) VALUES
(2, 'MANA-1042', 'delivery', 'Av. Las Palmas, Edif. Aurora, Piso 3, Apto 3A', 'entregado', '04121234567', 'Sin cebolla', 18.50, 0.00, 18.50, 62.45, 1155.33, 'BCV-20260523-001', 1),
(3, 'MANA-1041', 'retiro', '', 'listo', '04149876543', '', 24.00, 0.00, 24.00, 62.45, 1498.80, 'MP-20260523-042', 1),
(4, 'MANA-1040', 'delivery', 'Calle Los Mangos, Qta. María, Urb. La Floresta', 'preparando', '04265554321', 'Tocar el timbre 2 veces', 12.75, 0.00, 12.75, 62.45, 796.24, 'BCV-20260523-039', 1);

-- Detalles de pedidos
INSERT INTO detalle_pedido (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario_usd, subtotal_usd) VALUES
(1, 1, 'Maná Burger Clásica', 2, 8.99, 17.98),
(1, 33, 'Papas Fritas (Porción)', 1, 3.00, 3.00),
(2, 3, 'Maná BBQ Bacon', 1, 13.99, 13.99),
(2, 35, 'Coca-Cola 500ml', 1, 2.00, 2.00),
(3, 16, 'Pepito de Pollo', 1, 9.99, 9.99),
(3, 18, 'Enrollado de Jamón y Queso', 1, 5.99, 5.99);
