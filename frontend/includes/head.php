<?php
// ============================================================
// MANÁ FAST FOOD — HEAD.PHP (INCLUDE REUTILIZABLE)
// Versión: 1.0
// Uso: En cada página .php, al inicio:
//   <?php require_once 'includes/head.php'; ?>
//   $page_name = 'index'; // o 'menu', 'login', 'registro', etc.
//   $page_title_extra = ''; // opcional
// ============================================================

// --- Configuración por defecto ---
if (!isset($page_name)) {
    $page_name = 'index';
}

// --- Cargar meta tags ---
$meta_tags = include __DIR__ . '/../seo-meta-tags.php';
$meta = $meta_tags[$page_name] ?? $meta_tags['index'];

// --- Title final (con fallback) ---
$title = $meta['title'] ?? 'Maná Fast Food | La comida que llega al corazón';

// --- Robots ---
$robots = $meta['index'] ?? 'index, follow';

// --- Canonical ---
$canonical = $meta['canonical'] ?? 'https://manafastfood.com/';

// --- Open Graph ---
$og_title       = $meta['og_title']       ?? $title;
$og_description = $meta['og_description'] ?? ($meta['description'] ?? 'Maná Fast Food: hamburguesas, hot dogs, enrollados y papas en Caracas.');
$og_image       = $meta['og_image']       ?? 'https://manafastfood.com/img/og-mana.jpg';
$og_image_alt   = $meta['og_image_alt']   ?? 'Maná Fast Food';
$og_type        = $meta['og_type']        ?? 'website';
$og_url         = $canonical;

// --- Twitter Card ---
$twitter_card        = $meta['twitter_card']        ?? 'summary_large_image';
$twitter_title       = $meta['twitter_title']       ?? $og_title;
$twitter_description = $meta['twitter_description'] ?? $og_description;
$twitter_image       = $meta['twitter_image']       ?? $og_image;

// --- Theme Color ---
$theme_color = '#111111';

// --- Favicon (SVG inline + emoji fallback) ---
$favicon_svg = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y=".9em" font-size="90">🍔</text></svg>';
$favicon_ico = 'data:image/x-icon;base64,AAABAAEAEBACAAEAAQCwAAAAFgAAACgAAAAQAAAAIAAAAAEAAQAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAJfY6QAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- ============================================================
    MANÁ FAST FOOD — HEAD
    ============================================================ -->

    <!-- CHARSET & VIEWPORT -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- THEME COLOR -->
    <meta name="theme-color" content="<?= $theme_color ?>">
    <meta name="msapplication-TileColor" content="<?= $theme_color ?>">

    <!-- TITLE & DESCRIPTION -->
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta['description'] ?? '') ?>">

    <!-- KEYWORDS -->
    <meta name="keywords" content="maná fast food, hamburguesas caracas, hot dogs caracas, comida rápida venezuela, delivery caracas, pago móvil, pepitos, enrollados, papas fritas">

    <!-- ROBOTS -->
    <meta name="robots" content="<?= $robots ?>">
    <meta name="googlebot" content="<?= $robots ?>">

    <!-- CANONICAL -->
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

    <!-- FAVICON -->
    <link rel="icon" type="image/svg+xml" href="<?= $favicon_svg ?>">
    <link rel="icon" type="image/x-icon" href="<?= $favicon_ico ?>">
    <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍔</text></svg>">

    <!-- ============================================================
    OPEN GRAPH
    ============================================================ -->
    <meta property="og:title"       content="<?= htmlspecialchars($og_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($og_description) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:alt"   content="<?= htmlspecialchars($og_image_alt) ?>">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url"         content="<?= htmlspecialchars($og_url) ?>">
    <meta property="og:type"        content="<?= htmlspecialchars($og_type) ?>">
    <meta property="og:site_name"   content="Maná Fast Food">
    <meta property="og:locale"      content="es_VE">

    <!-- ============================================================
    TWITTER CARDS
    ============================================================ -->
    <meta name="twitter:card"        content="<?= htmlspecialchars($twitter_card) ?>">
    <meta name="twitter:title"       content="<?= htmlspecialchars($twitter_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($twitter_description) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($twitter_image) ?>">
    <meta name="twitter:site"        content="@manafastfood">
    <meta name="twitter:creator"     content="@manafastfood">

    <!-- ============================================================
    GOOGLE FONTS (preconnect + preload crítico)
    ============================================================ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap">
    </noscript>

    <!-- ============================================================
    ESTILOS PRINCIPALES
    ============================================================ -->
    <link rel="stylesheet" href="css/style.css">

    <!-- ============================================================
    SCHEMA.ORG JSON-LD
    ============================================================ -->
    <?php
    require_once __DIR__ . '/../schema-jsonld-mana.php';
    echo generarSchemaOrg($page_name, $producto ?? null);
    ?>

    <!-- ============================================================
    PAGE-SPECIFIC STYLES (abrir bloque <style> en cada página)
    ============================================================ -->

</head>
<body>

    <!-- ============================================================
    HEADER (se inyecta en cada página individual)
    ============================================================ -->

    <!-- ============================================================
    SCRIPTS: se cargan al final del body en cada página
    js/app.js, js/cart.js, js/auth.js, js/checkout.js, etc.
    ============================================================ -->
