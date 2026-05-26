-- ============================================
-- MANA FAST FOOD - ESQUEMA DE BASE DE DATOS
-- SOLO ESTRUCTURA DEL MENU DE PRODUCTOS
-- MySQL 8.0+
-- ============================================
-- Tablas: categorias, productos, ofertas, productos_en_oferta
-- Sin datos de ejemplo
-- ============================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE productos_en_oferta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    producto_id INT NOT NULL,
    FOREIGN KEY (oferta_id) REFERENCES ofertas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_oferta_producto (oferta_id, producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

