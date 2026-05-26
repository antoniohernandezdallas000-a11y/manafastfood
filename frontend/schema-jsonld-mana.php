<?php
// ============================================================
// MANÁ FAST FOOD — SCHEMA.ORG JSON-LD GENERATOR
// Versión: 1.0
// Uso: require_once 'schema-jsonld-mana.php';
//      generarSchemaOrg('index');
//      generarSchemaOrg('producto', $productData);
// ============================================================

function generarSchemaOrg($pagina = 'index', $producto = null) {
    $baseUrl = 'https://manafastfood.com';
    $output = '';

    // ============================================================
    // 1. ORGANIZATION + RESTAURANT (todas las páginas)
    // ============================================================
    $restaurant = [
        '@context'        => 'https://schema.org',
        '@type'           => 'Restaurant',
        '@id'             => $baseUrl . '/#restaurant',
        'name'            => 'Maná Fast Food',
        'description'     => 'Comida rápida urbana artesanal: hamburguesas, hot dogs, enrollados, pepitos y papas en Caracas, Venezuela.',
        'url'             => $baseUrl,
        'telephone'       => '+58-412-1234567',
        'servesCuisine'   => ['Venezuelan', 'Fast Food', 'American', 'Street Food'],
        'foundingDate'    => '2020',
        'logo'            => $baseUrl . '/img/mana-logo.jpg',
        'image'           => $baseUrl . '/img/og-mana.jpg',
        'priceRange'      => '$$',
        'currenciesAccepted' => 'USD',
        'paymentAccepted'    => 'Cash, PagoMóvil, Transferencia',
        'address' => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Av. Principal de Las Mercedes, CC Mercede Plaza, Local 2',
            'addressLocality' => 'Caracas',
            'addressRegion'   => 'Distrito Capital',
            'postalCode'      => '1060',
            'addressCountry'  => 'VE',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => 10.480594,
            'longitude' => -66.869688,
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'opens'     => '11:00',
                'closes'    => '23:00',
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Sunday',
                'opens'     => '12:00',
                'closes'    => '22:00',
            ],
        ],
        'sameAs' => [
            'https://instagram.com/manafastfood',
            'https://facebook.com/manafastfood',
            'https://wa.me/584121234567',
        ],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => $baseUrl . '/menu.php?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $output .= '<script type="application/ld+json">' . "\n";
    $output .= json_encode($restaurant, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    $output .= '</script>' . "\n";

    // ============================================================
    // 2. WEBSITE + SEARCH ACTION (todas las páginas)
    // ============================================================
    $website = [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => $baseUrl . '/#website',
        'name'            => 'Maná Fast Food',
        'url'             => $baseUrl,
        'description'     => 'Maná Fast Food — Hamburguesas, hot dogs, enrollados y papas en Caracas. Pide online.',
        'potentialAction' => [
            [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $baseUrl . '/menu.php?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ],
        'inLanguage' => 'es-VE',
        'publisher'  => [
            '@id' => $baseUrl . '/#restaurant',
        ],
    ];

    $output .= '<script type="application/ld+json">' . "\n";
    $output .= json_encode($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    $output .= '</script>' . "\n";

    // ============================================================
    // 3. BREADCRUMBLIST (según la página actual)
    // ============================================================
    $breadcrumbs = [
        ['name' => 'Inicio', 'url' => $baseUrl . '/'],
    ];

    switch ($pagina) {
        case 'menu':
            $breadcrumbs[] = ['name' => 'Menú', 'url' => $baseUrl . '/menu.php'];
            break;
        case 'login':
            $breadcrumbs[] = ['name' => 'Iniciar Sesión', 'url' => $baseUrl . '/login.php'];
            break;
        case 'registro':
            $breadcrumbs[] = ['name' => 'Registro', 'url' => $baseUrl . '/registro.php'];
            break;
        case 'carrito':
            $breadcrumbs[] = ['name' => 'Carrito', 'url' => $baseUrl . '/carrito.php'];
            break;
        case 'checkout':
            $breadcrumbs[] = ['name' => 'Carrito', 'url' => $baseUrl . '/carrito.php'];
            $breadcrumbs[] = ['name' => 'Checkout', 'url' => $baseUrl . '/checkout.php'];
            break;
        case 'confirmacion':
            $breadcrumbs[] = ['name' => 'Carrito', 'url' => $baseUrl . '/carrito.php'];
            $breadcrumbs[] = ['name' => 'Checkout', 'url' => $baseUrl . '/checkout.php'];
            $breadcrumbs[] = ['name' => 'Confirmación', 'url' => $baseUrl . '/confirmacion.php'];
            break;
        case 'producto':
            if ($producto && isset($producto['nombre'])) {
                $breadcrumbs[] = ['name' => 'Menú', 'url' => $baseUrl . '/menu.php'];
                $breadcrumbs[] = ['name' => $producto['nombre'], 'url' => $baseUrl . '/menu.php#' . ($producto['id'] ?? '')];
            }
            break;
    }

    $breadcrumbList = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        '@id'             => $baseUrl . '/#breadcrumb',
        'itemListElement' => [],
    ];

    foreach ($breadcrumbs as $i => $crumb) {
        $breadcrumbList['itemListElement'][] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb['name'],
            'item'     => $crumb['url'],
        ];
    }

    $output .= '<script type="application/ld+json">' . "\n";
    $output .= json_encode($breadcrumbList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    $output .= '</script>' . "\n";

    // ============================================================
    // 4. MENU + MENUITEMS (en página de menú o producto)
    // ============================================================
    if ($pagina === 'menu' || $pagina === 'producto') {
        $menuItems = [
            ['name' => 'Sencilla',            'description' => 'Carne de res 100%, lechuga, tomate, cebolla, kétchup y mostaza',              'price' => 5.5,  'category' => 'Hamburguesas'],
            ['name' => 'Maná Burger',         'description' => 'Doble carne angus, queso americano, tocineta ahumada, cebolla caramelizada',  'price' => 7.0,  'category' => 'Hamburguesas'],
            ['name' => 'Maná Super Crispy',   'description' => 'Pollito crispy, queso, tocineta, lechuga, tomate y salsa ranch',             'price' => 7.0,  'category' => 'Hamburguesas'],
            ['name' => 'Maná Mixta',          'description' => 'Carne de res + pollo crispy, doble queso, tocineta, huevo',                   'price' => 9.5,  'category' => 'Hamburguesas'],
            ['name' => 'Maná Chuleta-Crispy', 'description' => 'Chuleta de cerdo empanizada + pollo crispy, queso, tocineta',                 'price' => 9.5,  'category' => 'Hamburguesas'],
            ['name' => 'Maná Ahumada',        'description' => 'Carne de res ahumada, queso ahumado, tocineta, cebolla caramelizada',         'price' => 9.5,  'category' => 'Hamburguesas'],
            ['name' => 'Maná Trifásica',      'description' => 'Carne de res + pollo crispy + chuleta ahumada, triple queso, tocineta',       'price' => 12.0, 'category' => 'Especiales'],
            ['name' => 'Maná Dog',            'description' => 'Pan artesanal, salchicha alemana, papa pay, queso, tocineta, cebolla caramelizada', 'price' => 3.5, 'category' => 'Hot Dogs'],
            ['name' => 'Maná Especial',       'description' => 'Pan artesanal, salchicha alemana, papa pay, queso, tocineta, huevo de codorniz',    'price' => 5.5, 'category' => 'Hot Dogs'],
            ['name' => 'Enrollado Mixto',     'description' => 'Carne de res + pollo, queso, verduras salteadas, salsas, tortilla de harina',        'price' => 12.0,'category' => 'Enrollados'],
            ['name' => 'Pepiperro',           'description' => 'Pan artesanal, carne de res desmechada, queso, papa pay, repollo, tomate',            'price' => 8.0, 'category' => 'Pepitos'],
            ['name' => 'Tripapas Maná',       'description' => 'Papas fritas, carne de res + pollo + salchicha, triple queso, tocineta',               'price' => 13.0,'category' => 'Papas'],
            ['name' => 'Salchipapa',          'description' => 'Papas fritas, salchicha alemana, queso amarillo, tocineta, salsas',                    'price' => 9.0, 'category' => 'Papas'],
        ];

        $menu = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Menu',
            '@id'         => $baseUrl . '/menu.php#menu',
            'name'        => 'Menú Maná Fast Food',
            'description' => 'Hamburguesas artesanales, hot dogs, enrollados, pepitos, papas, extras y bebidas.',
            'url'         => $baseUrl . '/menu.php',
            'hasMenuItem' => [],
        ];

        foreach ($menuItems as $item) {
            $menu['hasMenuItem'][] = [
                '@type'       => 'MenuItem',
                'name'        => $item['name'],
                'description' => $item['description'],
                'offers'      => [
                    '@type'         => 'Offer',
                    'price'         => $item['price'],
                    'priceCurrency' => 'USD',
                    'availability'  => 'https://schema.org/InStock',
                    'priceValidUntil' => date('Y-12-31'),
                ],
                'suitableForDiet' => 'https://schema.org/Diet',
            ];
        }

        $output .= '<script type="application/ld+json">' . "\n";
        $output .= json_encode($menu, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        $output .= '</script>' . "\n";
    }

    // ============================================================
    // 5. PRODUCT + AGGREGATEOFFER (página de producto individual)
    // ============================================================
    if ($pagina === 'producto' && $producto && isset($producto['nombre'])) {
        $productSchema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            '@id'      => $baseUrl . '/menu.php#' . ($producto['id'] ?? strtolower(str_replace(' ', '-', $producto['nombre']))),
            'name'     => $producto['nombre'],
            'description' => $producto['descripcion'] ?? $producto['nombre'] . ' de Maná Fast Food',
            'image'       => $producto['imagen'] ?? $baseUrl . '/img/og-mana.jpg',
            'category'    => $producto['categoria'] ?? 'Comida rápida',
            'brand'       => [
                '@type' => 'Brand',
                'name'  => 'Maná Fast Food',
            ],
            'offers' => [
                '@type'         => 'Offer',
                'price'         => $producto['precio'],
                'priceCurrency' => 'USD',
                'availability'  => 'https://schema.org/InStock',
                'url'           => $baseUrl . '/menu.php#' . ($producto['id'] ?? ''),
                'priceValidUntil' => date('Y-12-31', strtotime('+1 year')),
            ],
        ];

        $output .= '<script type="application/ld+json">' . "\n";
        $output .= json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        $output .= '</script>' . "\n";
    }

    // ============================================================
    // 6. FAQPAGE (solo en páginas con preguntas frecuentes)
    // ============================================================
    if ($pagina === 'faq') {
        $faq = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            '@id'        => $baseUrl . '/#faq',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name'  => '¿Cuáles son los horarios de Maná Fast Food?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Lunes a sábado de 11:00 AM a 11:00 PM. Domingos de 12:00 PM a 10:00 PM.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => '¿Hacen delivery?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Sí, hacemos delivery en Caracas. También puedes retirar en nuestro local.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => '¿Cómo puedo pagar?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Aceptamos PagoMóvil, transferencia bancaria y efectivo en el local.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name'  => '¿Tienen opciones sin gluten?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => 'Algunos de nuestros productos pueden prepararse sin gluten. Consulta con nuestro equipo al hacer tu pedido.',
                    ],
                ],
            ],
        ];

        $output .= '<script type="application/ld+json">' . "\n";
        $output .= json_encode($faq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        $output .= '</script>' . "\n";
    }

    // ============================================================
    // LOCAL BUSINESS (todas las páginas - refuerzo)
    // ============================================================
    $localBusiness = [
        '@context' => 'https://schema.org',
        '@type'    => 'FoodEstablishment',
        '@id'      => $baseUrl . '/#foodestablishment',
        'name'     => 'Maná Fast Food',
        'parentOrganization' => [
            '@id' => $baseUrl . '/#restaurant',
        ],
    ];

    $output .= '<script type="application/ld+json">' . "\n";
    $output .= json_encode($localBusiness, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    $output .= '</script>' . "\n";

    return $output;
}
