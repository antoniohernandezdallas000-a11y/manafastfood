<?php
/**
 * MANÁ FAST FOOD - FIXTURES DE DATOS DE PRUEBA
 * 
 * Datos de prueba reutilizables en todos los tests.
 */

return [
    // ─── USUARIOS ───
    'usuario_valido' => [
        'nombre' => 'Test User',
        'telefono' => '04141234567',
        'email' => 'test@mana.com',
        'password' => 'test123456',
    ],
    
    'admin_valido' => [
        'nombre' => 'Admin Test',
        'telefono' => '04140000000',
        'email' => 'admin@mana.com',
        'password' => 'admin123',
        'rol' => 'admin',
    ],
    
    'usuario_actualizar' => [
        'nombre' => 'Test Updated',
        'telefono' => '04241234567',
    ],
    
    // ─── CATEGORÍAS ───
    'categoria_valida' => [
        'nombre' => 'Hamburguesas Test',
        'slug' => 'hamburguesas-test',
        'orden' => 1,
    ],
    
    // ─── PRODUCTOS ───
    'producto_valido' => [
        'categoria_id' => 1,
        'nombre' => 'Maná Burger Test',
        'slug' => 'mana-burger-test',
        'descripcion' => 'Hamburguesa de prueba para tests unitarios',
        'ingredientes' => 'Carne, queso, lechuga, tomate',
        'precio_usd' => 7.00,
        'activo' => 1,
    ],
    
    'producto_inactivo' => [
        'categoria_id' => 1,
        'nombre' => 'Maná Burger Inactiva',
        'slug' => 'mana-burger-inactiva',
        'descripcion' => 'Producto inactivo para tests',
        'precio_usd' => 5.00,
        'activo' => 0,
    ],
    
    'producto_oferta' => [
        'categoria_id' => 1,
        'nombre' => 'Maná Burger Oferta',
        'slug' => 'mana-burger-oferta',
        'descripcion' => 'Producto en oferta para tests',
        'precio_usd' => 10.00,
        'en_oferta' => 1,
        'descuento_porcentaje' => 20.00,
        'activo' => 1,
    ],
    
    'producto_update_data' => [
        'nombre' => 'Maná Burger Editada',
        'precio_usd' => 8.50,
        'descripcion' => 'Descripción actualizada',
    ],
    
    // ─── CUENTAS PAGOMÓVIL ───
    'cuenta_pagomovil_valida' => [
        'banco' => 'Mercantil',
        'codigo_banco' => '0105',
        'titular' => 'Maná Fast Food Test',
        'telefono' => '04141234567',
        'cedula_rif' => 'J-12345678-9',
        'tipo_cuenta' => 'ahorro',
        'activa' => 1,
    ],
    
    'cuenta_pagomovil_inactiva' => [
        'banco' => 'Banco de Venezuela',
        'codigo_banco' => '0102',
        'titular' => 'Maná Fast Food Test',
        'telefono' => '04141234568',
        'cedula_rif' => 'J-12345678-9',
        'tipo_cuenta' => 'corriente',
        'activa' => 0,
    ],
    
    // ─── PEDIDOS ───
    'pedido_valido' => [
        'tipo_entrega' => 'delivery',
        'direccion' => 'Calle Test, #123, Caracas',
        'ciudad' => 'Caracas',
        'telefono_contacto' => '04141234567',
        'notas' => 'Sin cebolla por favor',
        'subtotal_usd' => 21.00,
        'descuento_usd' => 0,
        'total_usd' => 21.00,
        'tasa_bcv' => 60.00,
        'metodo_pago' => 'pagomovil',
        'pago_confirmado' => 0,
        'estado' => 'pendiente',
        'items' => [
            ['producto_id' => 1, 'nombre_producto' => 'Maná Burger Clásica', 'cantidad' => 2, 'precio_unitario_usd' => 7.00],
            ['producto_id' => 2, 'nombre_producto' => 'Maná Doble Carne', 'cantidad' => 1, 'precio_unitario_usd' => 7.00],
        ],
    ],
    
    'pedido_retiro' => [
        'tipo_entrega' => 'retiro',
        'notas' => '',
        'subtotal_usd' => 15.00,
        'total_usd' => 15.00,
        'tasa_bcv' => 60.00,
        'metodo_pago' => 'pagomovil',
        'pago_confirmado' => 0,
        'estado' => 'pendiente',
        'items' => [
            ['producto_id' => 1, 'nombre_producto' => 'Maná Burger Clásica', 'cantidad' => 1, 'precio_unitario_usd' => 15.00],
        ],
    ],
    
    'estados_validos' => ['pendiente', 'preparando', 'listo', 'entregado', 'cancelado'],
    
    'estado_invalido' => 'estado_inexistente',
    
    // ─── TASA BCV ───
    'tasa_bcv_valida' => 62.45,
    'tasa_bcv_actualizada' => 65.00,
    
    // ─── OFERTAS ───
    'oferta_valida' => [
        'nombre' => 'Oferta Test',
        'tipo' => 'porcentaje',
        'descripcion' => 'Oferta de prueba',
        'descuento_porcentaje' => 15.00,
        'activa' => 1,
    ],
    
    // ─── ARCHIVOS ───
    'capture_nombre' => 'capture_test.jpg',
    'capture_mime' => 'image/jpeg',
    'capture_size' => 102400, // 100KB
    
    'archivo_invalido_nombre' => 'documento.txt',
    'archivo_invalido_mime' => 'text/plain',
    
    'archivo_demasiado_grande' => 6 * 1024 * 1024, // 6MB (excede 5MB)
    
    // ─── REFERENCIAS DE PAGO ───
    'referencia_valida' => 'BCV-20260523-ABC123',
    'referencia_corta' => 'AB',
];
