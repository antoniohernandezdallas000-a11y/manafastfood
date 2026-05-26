<?php
// ============================================================
// MANÁ FAST FOOD — META TAGS POR PÁGINA
// Versión: 1.0
// Incluir con: $meta = include 'seo-meta-tags.php';
// Usar en <head>: <title><?= $meta[$page_name]['title'] ?></title>
// ============================================================

return [
    // ============================================================
    // PÁGINAS INDEXABLES (con index: follow o sin clave 'index')
    // ============================================================

    'index' => [
        'title'       => 'Maná Fast Food | Hamburguesas, Hot Dogs y más en Caracas',
        'description' => 'Maná Fast Food en Caracas: hamburguesas artesanales, hot dogs, enrollados, pepitos y papas. Pide delivery o retiro. Paga con PagoMóvil.',
        'canonical'   => 'https://manafastfood.com/',
        'og_title'       => 'Maná Fast Food | Sabor urbano que llega al corazón',
        'og_description' => 'Maná Fast Food: hamburguesas, hot dogs, enrollados, papas. Pide online en Caracas. Delivery y retiro.',
        'og_type'        => 'website',
        'og_image'       => 'https://manafastfood.com/img/og-mana.jpg',
        'og_image_alt'   => 'Maná Fast Food - La comida que llega al corazón',
        'twitter_card'        => 'summary_large_image',
        'twitter_title'       => 'Maná Fast Food | Hamburguesas, Hot Dogs y más',
        'twitter_description' => 'Sabor urbano que no engorda, llega al corazón. Hamburguesas, hot dogs, enrollados y papas en Caracas.',
        'twitter_image'       => 'https://manafastfood.com/img/og-mana.jpg',
    ],

    'menu' => [
        'title'       => 'Menú Completo | Maná Fast Food — Hamburguesas, Hot Dogs, Enrollados',
        'description' => 'Menú completo de Maná Fast Food: 7 hamburguesas, 5 hot dogs, 3 enrollados, pepitos, papas, extras y bebidas. Precios en Caracas. Pide online.',
        'canonical'   => 'https://manafastfood.com/menu.php',
        'og_title'       => 'Menú Maná Fast Food — Hamburguesas, Hot Dogs, Enrollados',
        'og_description' => 'Explora nuestro menú: hamburguesas clásicas y especiales, hot dogs artesanales, enrollados, pepitos, papas fritas y más.',
        'og_type'        => 'website',
        'og_image'       => 'https://manafastfood.com/img/og-menu.jpg',
        'twitter_card'        => 'summary_large_image',
        'twitter_title'       => 'Menú Maná Fast Food',
        'twitter_description' => 'Hamburguesas, hot dogs, enrollados, pepitos y papas. Todo el sabor Maná.',
    ],

    // ============================================================
    // PÁGINAS NO INDEXABLES (funcionales / de usuario)
    // ============================================================

    'login' => [
        'title'       => 'Iniciar Sesión | Maná Fast Food',
        'description' => 'Accede a tu cuenta Maná Fast Food para gestionar tus pedidos y dirección de entrega.',
        'canonical'   => 'https://manafastfood.com/login.php',
        'og_title'       => 'Iniciar Sesión | Maná Fast Food',
        'og_description' => 'Entra a tu cuenta y pide más rápido.',
        'og_type'        => 'website',
        'index' => 'noindex, nofollow',
    ],

    'registro' => [
        'title'       => 'Registro Rápido | Maná Fast Food',
        'description' => 'Regístrate en segundos en Maná Fast Food. Solo nombre, teléfono, email y contraseña. Empieza a pedir al instante.',
        'canonical'   => 'https://manafastfood.com/registro.php',
        'og_title'       => 'Regístrate | Maná Fast Food',
        'og_description' => 'Crea tu cuenta en segundos y empieza a pedir.',
        'og_type'        => 'website',
        'index' => 'noindex, nofollow',
    ],

    'carrito' => [
        'title'       => 'Tu Pedido | Maná Fast Food',
        'description' => 'Revisa tu pedido de Maná Fast Food. Elige delivery o retiro y continúa al pago.',
        'canonical'   => 'https://manafastfood.com/carrito.php',
        'og_title'       => 'Tu Pedido | Maná Fast Food',
        'og_description' => 'Revisa tu pedido antes de pagar.',
        'og_type'        => 'website',
        'index' => 'noindex, nofollow',
    ],

    'checkout' => [
        'title'       => 'Finalizar Pedido | Maná Fast Food',
        'description' => 'Completa tu pedido en Maná Fast Food. Ingresa tus datos de entrega y paga con PagoMóvil.',
        'canonical'   => 'https://manafastfood.com/checkout.php',
        'og_title'       => 'Finalizar Pedido | Maná Fast Food',
        'og_description' => 'Completa tus datos y paga con PagoMóvil.',
        'og_type'        => 'website',
        'index' => 'noindex, nofollow',
    ],

    'confirmacion' => [
        'title'       => 'Pedido Confirmado | Maná Fast Food',
        'description' => 'Tu pedido de Maná Fast Food está confirmado. Gracias por tu compra.',
        'canonical'   => 'https://manafastfood.com/confirmacion.php',
        'og_title'       => '¡Pedido Confirmado! | Maná Fast Food',
        'og_description' => 'Tu pedido fue recibido. Gracias por elegir Maná Fast Food.',
        'og_type'        => 'website',
        'index' => 'noindex, nofollow',
    ],
];
