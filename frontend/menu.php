<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menú — Maná Fast Food</title>
  <meta name="description" content="Menú completo de Maná Fast Food: Hamburguesas, Hot Dogs, Enrollados, Pepitos, Papas, Extras y Bebidas.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — MENU.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    .menu-page {
      padding-top: calc(var(--header-height) + 40px);
      min-height: 100vh;
    }

    .menu-hero {
      text-align: center;
      padding: 60px 0 40px;
      position: relative;
    }

    .menu-hero h1 {
      font-family: var(--font-display);
      font-size: clamp(3rem, 10vw, 6rem);
      text-transform: uppercase;
      color: var(--color-blanco);
      line-height: 1;
      letter-spacing: 4px;
    }

    .menu-hero h1 .highlight {
      color: var(--color-amarillo);
    }

    .menu-hero p {
      color: var(--color-gris);
      font-size: 1.05rem;
      margin-top: 12px;
    }

    /* ---- CATEGORY FILTER TABS ---- */
    .menu-filters {
      position: sticky;
      top: var(--header-height);
      z-index: 50;
      background: rgba(17,17,17,0.95);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 16px 0;
      border-bottom: 1px solid var(--color-gris-oscuro);
    }

    .menu-filters .container {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      scrollbar-width: none;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 4px;
    }

    .menu-filters .container::-webkit-scrollbar {
      display: none;
    }

    .filter-btn {
      flex: 0 0 auto;
      padding: 10px 22px;
      border-radius: var(--radius-full);
      background: var(--color-bg-card);
      color: var(--color-gris);
      font-family: var(--font-heading);
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      border: 2px solid transparent;
      transition: var(--transition);
      white-space: nowrap;
      cursor: pointer;
    }

    .filter-btn:hover {
      color: var(--color-blanco);
      border-color: var(--color-gris-oscuro);
    }

    .filter-btn.active {
      background: var(--color-rojo);
      color: var(--color-blanco);
      border-color: var(--color-rojo);
      box-shadow: var(--shadow-btn);
    }

    /* ---- CATEGORY SECTIONS ---- */
    .category-section {
      padding: 48px 0;
      scroll-margin-top: calc(var(--header-height) + 80px);
    }

    .category-section:nth-child(odd) {
      background: var(--color-bg);
    }

    .category-section:nth-child(even) {
      background: var(--color-bg-alt);
    }

    .category-section h2 {
      font-family: var(--font-display);
      font-size: clamp(1.8rem, 4vw, 2.8rem);
      text-transform: uppercase;
      color: var(--color-amarillo);
      margin-bottom: 8px;
      letter-spacing: 2px;
    }

    .category-section h2 .cat-count {
      font-family: var(--font-body);
      font-size: 0.9rem;
      color: var(--color-gris);
      text-transform: none;
      letter-spacing: 0;
      font-weight: 400;
    }

    .category-divider {
      width: 60px;
      height: 4px;
      background: var(--color-rojo);
      border-radius: var(--radius-full);
      margin-bottom: 28px;
    }

    /* ---- Product Grid dentro de categorías ---- */
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

    /* ---- Extras/Bebidas style compact ---- */
    .menu-grid.compact {
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }

    .menu-grid .product-card-desc {
      font-size: 0.8rem;
    }

    /* ---- Responsive ---- */
    @media (max-width: 768px) {
      .menu-hero {
        padding: 40px 0 24px;
      }

      .category-section {
        padding: 32px 0;
      }

      .menu-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 16px;
      }

      .menu-grid.compact {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
      }
    }

    @media (max-width: 480px) {
      .menu-grid {
        grid-template-columns: 1fr;
      }

      .menu-grid.compact {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="header" id="main-header">
    <div class="container">
      <a href="index.php" class="header-logo" aria-label="Maná Fast Food - Inicio">
        <img src="../images/logo-mana.jpeg" alt="Maná Fast Food" height="42" style="object-fit:contain;">
        <span>Maná <span class="rojo">Fast Food</span></span>
      </a>

      <nav class="header-nav" id="main-nav">
        <a href="index.php" data-nav>Inicio</a>
        <a href="menu.php" class="active" data-nav>Menú</a>
        <a href="login.php" class="btn btn-sm btn-outline-rojo" style="padding:6px 16px;font-size:0.7rem;" data-nav>Entrar</a>
        <a href="registro.php" class="btn btn-sm btn-primary" style="padding:6px 16px;font-size:0.7rem;" data-nav>Registro</a>
        <a href="carrito.php" class="cart-btn" aria-label="Carrito de compras">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
          <span>Carrito</span>
          <span class="cart-badge" id="cart-badge">0</span>
        </a>
      </nav>

      <button class="hamburger" id="hamburger-btn" aria-label="Abrir menú de navegación">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>

  <!-- ===== MENU PAGE ===== -->
  <main class="menu-page">

    <!-- Hero Menú -->
    <section class="menu-hero">
      <div class="container">
        <h1>NUESTRO <span class="highlight">MENÚ</span></h1>
        <p>Elige, combina, disfruta. Todo hecho al momento.</p>
      </div>
    </section>

    <!-- Filtros -->
    <div class="menu-filters" id="menu-filters">
      <div class="container">
        <button class="filter-btn active" data-filter="todos">Todos</button>
        <button class="filter-btn" data-filter="hamburguesas">Hamburguesas</button>
        <button class="filter-btn" data-filter="especiales">Especiales</button>
        <button class="filter-btn" data-filter="hotdogs">Hot Dogs</button>
        <button class="filter-btn" data-filter="enrollados">Enrollados</button>
        <button class="filter-btn" data-filter="pepitos">Pepitos</button>
        <button class="filter-btn" data-filter="papas">Papas</button>
        <button class="filter-btn" data-filter="extras">Extras</button>
        <button class="filter-btn" data-filter="bebidas">Bebidas</button>
      </div>
    </div>

    <!-- ===== HAMBURGUESAS ===== -->
    <section class="category-section" data-categoria="hamburguesas" id="hamburguesas">
      <div class="container">
        <h2>Hamburguesas <span class="cat-count">(7)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid" id="grid-hamburguesas"></div>
      </div>
    </section>

    <!-- ===== ESPECIALES ===== -->
    <section class="category-section" data-categoria="especiales" id="especiales">
      <div class="container">
        <h2>Especiales <span class="cat-count">(4)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid" id="grid-especiales"></div>
      </div>
    </section>

    <!-- ===== HOT DOGS ===== -->
    <section class="category-section" data-categoria="hotdogs" id="hotdogs">
      <div class="container">
        <h2>Hot Dogs <span class="cat-count">(5)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid" id="grid-hotdogs"></div>
      </div>
    </section>

    <!-- ===== ENROLLADOS ===== -->
    <section class="category-section" data-categoria="enrollados" id="enrollados">
      <div class="container">
        <h2>Enrollados <span class="cat-count">(3)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid" id="grid-enrollados"></div>
      </div>
    </section>

    <!-- ===== PEPITOS ===== -->
    <section class="category-section" data-categoria="pepitos" id="pepitos">
      <div class="container">
        <h2>Pepitos <span class="cat-count">(2)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid" id="grid-pepitos"></div>
      </div>
    </section>

    <!-- ===== PAPAS ===== -->
    <section class="category-section" data-categoria="papas" id="papas">
      <div class="container">
        <h2>Papas <span class="cat-count">(4)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid" id="grid-papas"></div>
      </div>
    </section>

    <!-- ===== EXTRAS ===== -->
    <section class="category-section" data-categoria="extras" id="extras">
      <div class="container">
        <h2>Extras <span class="cat-count">(8)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid compact" id="grid-extras"></div>
      </div>
    </section>

    <!-- ===== BEBIDAS ===== -->
    <section class="category-section" data-categoria="bebidas" id="bebidas">
      <div class="container">
        <h2>Bebidas <span class="cat-count">(3)</span></h2>
        <div class="category-divider"></div>
        <div class="menu-grid compact" id="grid-bebidas"></div>
      </div>
    </section>

  </main>

  <!-- ===== FOOTER ===== -->
  <div id="footer-placeholder"></div>

  <!-- ===== PRODUCTOS DATA (JSON embebido para cart.js) ===== -->
  <script id="productos-data" type="application/json">
  {
    "hamburguesas": [
      { "id": "ham01", "nombre": "Sencilla", "precio": 5.5, "descripcion": "Carne de res 100%, lechuga, tomate, cebolla, kétchup y mostaza", "categoria": "Hamburguesas" },
      { "id": "ham02", "nombre": "Maná Burger", "precio": 7.0, "descripcion": "Doble carne angus, queso americano, tocineta ahumada, cebolla caramelizada, salsa Maná", "categoria": "Hamburguesas" },
      { "id": "ham03", "nombre": "Maná Super Crispy", "precio": 7.0, "descripcion": "Pollito crispy, queso, tocineta, lechuga, tomate y salsa ranch", "categoria": "Hamburguesas" },
      { "id": "ham04", "nombre": "Maná Mixta", "precio": 9.5, "descripcion": "Carne de res + pollo crispy, doble queso, tocineta, huevo, cebolla, lechuga, tomate y salsas", "categoria": "Hamburguesas" },
      { "id": "ham05", "nombre": "Maná Chuleta-Crispy", "precio": 9.5, "descripcion": "Chuleta de cerdo empanizada + pollo crispy, queso, tocineta, lechuga, tomate, cebolla", "categoria": "Hamburguesas" },
      { "id": "ham06", "nombre": "Maná Ahumada", "precio": 9.5, "descripcion": "Carne de res ahumada, queso ahumado, tocineta, cebolla caramelizada, salsa BBQ ahumada", "categoria": "Hamburguesas" },
      { "id": "ham07", "nombre": "Maná Chuleta Ahumada", "precio": 7.5, "descripcion": "Chuleta de cerdo ahumada, queso, tocineta, cebolla caramelizada, salsa de la casa", "categoria": "Hamburguesas" }
    ],
    "especiales": [
      { "id": "esp01", "nombre": "Maná Chicken Grill", "precio": 7.0, "descripcion": "Pechuga de pollo grillada, queso, lechuga, tomate, cebolla, aderezo de la casa", "categoria": "Especiales" },
      { "id": "esp02", "nombre": "Maná Cheese", "precio": 8.5, "descripcion": "Carne de res, triple queso (amarillo, mozzarella, parmesano), tocineta, cebolla caramelizada", "categoria": "Especiales" },
      { "id": "esp03", "nombre": "Maná Cheese Crispy", "precio": 8.0, "descripcion": "Pollo crispy, triple queso, tocineta, lechuga, tomate, salsa especial", "categoria": "Especiales" },
      { "id": "esp04", "nombre": "Maná Trifásica", "precio": 12.0, "descripcion": "Carne de res + pollo crispy + chuleta ahumada, triple queso, tocineta, huevo, cebolla, lechuga, tomate", "categoria": "Especiales" }
    ],
    "hotdogs": [
      { "id": "hd01", "nombre": "Maná Sencillo", "precio": 1.8, "descripcion": "Pan suave, salchicha, papa pay, queso amarillo, salsas clásicas", "categoria": "Hot Dogs" },
      { "id": "hd02", "nombre": "Maná Dog", "precio": 3.5, "descripcion": "Pan artesanal, salchicha alemana, papa pay, queso, tocineta, cebolla caramelizada, salsas", "categoria": "Hot Dogs" },
      { "id": "hd03", "nombre": "Maná Clásico", "precio": 3.5, "descripcion": "Pan artesanal, salchicha especial, papa pay, queso amarillo, repollo, tomate, cebolla, salsas", "categoria": "Hot Dogs" },
      { "id": "hd04", "nombre": "Maná Especial", "precio": 5.5, "descripcion": "Pan artesanal, salchicha alemana, papa pay, queso, tocineta, cebolla caramelizada, lechuga, tomate, salsas, huevo de codorniz", "categoria": "Hot Dogs" },
      { "id": "hd05", "nombre": "Especial Maná Clásico", "precio": 6.0, "descripcion": "Pan artesanal, salchicha especial, papa pay, queso, tocineta, repollo, tomate, cebolla caramelizada, huevo de codorniz, salsas", "categoria": "Hot Dogs" }
    ],
    "enrollados": [
      { "id": "enr01", "nombre": "Mixto", "precio": 12.0, "descripcion": "Carne de res + pollo, queso, verduras salteadas, salsas envueltos en tortilla de harina", "categoria": "Enrollados" },
      { "id": "enr02", "nombre": "Doble Proteína", "precio": 22.0, "descripcion": "Carne de res + pollo crispy, doble queso, tocineta, verduras salteadas, salsas, tortilla grande", "categoria": "Enrollados" },
      { "id": "enr03", "nombre": "Especial Chuleta Ahumada", "precio": 15.0, "descripcion": "Chuleta de cerdo ahumada, queso, tocineta, verduras salteadas, cebolla caramelizada, salsas", "categoria": "Enrollados" }
    ],
    "pepitos": [
      { "id": "pep01", "nombre": "Pepiperro", "precio": 8.0, "descripcion": "Pan artesanal, carne de res desmechada, queso, papa pay, repollo, tomate, cebolla, salsas", "categoria": "Pepitos" },
      { "id": "pep02", "nombre": "Mixto Pepimaná", "precio": 12.0, "descripcion": "Carne de res desmechada + pollo, queso, tocineta, papa pay, repollo, tomate, cebolla caramelizada, salsas", "categoria": "Pepitos" }
    ],
    "papas": [
      { "id": "pap01", "nombre": "Salchipapa", "precio": 9.0, "descripcion": "Papas fritas, salchicha alemana, queso amarillo, tocineta, salsas", "categoria": "Papas" },
      { "id": "pap02", "nombre": "Tender de Pollo", "precio": 11.0, "descripcion": "Papas fritas, tiras de pollo empanizado, queso amarillo, tocineta, salsas", "categoria": "Papas" },
      { "id": "pap03", "nombre": "Papas Mixtas", "precio": 11.5, "descripcion": "Papas fritas, carne de res desmechada + salchicha, queso amarillo, tocineta, salsas", "categoria": "Papas" },
      { "id": "pap04", "nombre": "Tripapas Maná", "precio": 13.0, "descripcion": "Papas fritas, carne de res + pollo + salchicha, triple queso, tocineta, cebolla caramelizada, salsas", "categoria": "Papas" }
    ],
    "extras": [
      { "id": "ext01", "nombre": "Porción de Papa", "precio": 3.0, "descripcion": "Porción extra de papas fritas", "categoria": "Extras" },
      { "id": "ext02", "nombre": "Tocineta", "precio": 2.0, "descripcion": "Porción extra de tocineta ahumada", "categoria": "Extras" },
      { "id": "ext03", "nombre": "Queso", "precio": 1.5, "descripcion": "Porción extra de queso amarillo", "categoria": "Extras" },
      { "id": "ext04", "nombre": "Salchicha", "precio": 2.0, "descripcion": "Salchicha alemana extra", "categoria": "Extras" },
      { "id": "ext05", "nombre": "Pollo", "precio": 3.0, "descripcion": "Porción extra de pollo", "categoria": "Extras" },
      { "id": "ext06", "nombre": "Queso Parmesano", "precio": 2.5, "descripcion": "Queso parmesano rallado", "categoria": "Extras" },
      { "id": "ext07", "nombre": "Extra Queso HD", "precio": 0.5, "descripcion": "Queso amarillo extra para Hot Dogs", "categoria": "Extras" },
      { "id": "ext08", "nombre": "Extra Tocineta HD", "precio": 0.5, "descripcion": "Tocineta extra para Hot Dogs", "categoria": "Extras" }
    ],
    "bebidas": [
      { "id": "beb01", "nombre": "Refresco", "precio": 1.5, "descripcion": "Lata 355ml: Coca-Cola, Sprite, Malta o Frescolita", "categoria": "Bebidas" },
      { "id": "beb02", "nombre": "Nestea", "precio": 1.5, "descripcion": "Té helado sabor limón 500ml", "categoria": "Bebidas" },
      { "id": "beb03", "nombre": "Jugo de Parchita", "precio": 2.0, "descripcion": "Jugo natural de parchita 500ml", "categoria": "Bebidas" }
    ]
  }
  </script>

  <!-- ===== SCRIPTS ===== -->
  <script src="js/app.js"></script>
  <script src="js/cart.js"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — MENU.JS
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      var productosData = {};
      try {
        var scriptEl = document.getElementById('productos-data');
        if (scriptEl) {
          productosData = JSON.parse(scriptEl.textContent);
        }
      } catch (e) {
        console.error('Error al parsear productos data:', e);
      }

      /* ---- Renderizar productos ---- */
      var iconMap = {
        hamburguesas: '🍔',
        especiales: '🍔',
        hotdogs: '🌭',
        enrollados: '🌯',
        pepitos: '🥖',
        papas: '🍟',
        extras: '🧀',
        bebidas: '🥤'
      };

      var bgColors = {
        hamburguesas: 'linear-gradient(135deg,#2a0000,#1a0505)',
        especiales: 'linear-gradient(135deg,#2a0a00,#1a0500)',
        hotdogs: 'linear-gradient(135deg,#2a1a00,#1a0a00)',
        enrollados: 'linear-gradient(135deg,#001a2a,#000a1a)',
        pepitos: 'linear-gradient(135deg,#2a001a,#1a000a)',
        papas: 'linear-gradient(135deg,#2a2a00,#1a1a00)',
        extras: 'linear-gradient(135deg,#1a1a2a,#0a0a1a)',
        bebidas: 'linear-gradient(135deg,#001a1a,#000a0a)'
      };

      function renderCategoria(catKey, gridId) {
        var grid = document.getElementById(gridId);
        if (!grid) return;

        var productos = productosData[catKey] || [];
        if (productos.length === 0) {
          grid.innerHTML = '<p style="color:var(--color-gris);grid-column:1/-1;text-align:center;">Próximamente</p>';
          return;
        }

        var html = '';
        productos.forEach(function(p) {
          var icon = iconMap[catKey] || '🍽️';
          var bg = bgColors[catKey] || 'var(--color-bg-alt)';
          html +=
            '<div class="product-card">' +
              '<div class="product-card-image" style="background:' + bg + ';">' +
                '<span style="font-size:4rem;opacity:0.25;filter:grayscale(1);">' + icon + '</span>' +
              '</div>' +
              '<div class="product-card-body">' +
                '<h3 class="product-card-name">' + p.nombre + '</h3>' +
                '<p class="product-card-desc">' + p.descripcion + '</p>' +
                '<div class="product-card-footer">' +
                  '<span class="product-card-price" data-price="' + p.precio + '">$' + p.precio.toFixed(2) + '</span>' +
                  '<button class="btn btn-primary btn-sm add-to-cart" ' +
                    'data-id="' + p.id + '" ' +
                    'data-nombre="' + p.nombre.replace(/"/g,'&quot;') + '" ' +
                    'data-precio="' + p.precio + '" ' +
                    'data-descripcion="' + p.descripcion.replace(/"/g,'&quot;') + '" ' +
                    'data-categoria="' + p.categoria + '">' +
                    'Agregar +' +
                  '</button>' +
                '</div>' +
              '</div>' +
            '</div>';
        });

        grid.innerHTML = html;
      }

      // Renderizar todas las categorías
      renderCategoria('hamburguesas', 'grid-hamburguesas');
      renderCategoria('especiales', 'grid-especiales');
      renderCategoria('hotdogs', 'grid-hotdogs');
      renderCategoria('enrollados', 'grid-enrollados');
      renderCategoria('pepitos', 'grid-pepitos');
      renderCategoria('papas', 'grid-papas');
      renderCategoria('extras', 'grid-extras');
      renderCategoria('bebidas', 'grid-bebidas');

      /* ---- Add to Cart ---- */
      document.querySelectorAll('.add-to-cart').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          if (typeof Cart !== 'undefined' && Cart.addToCart) {
            Cart.addToCart({
              id: this.dataset.id,
              nombre: this.dataset.nombre,
              precio: parseFloat(this.dataset.precio),
              imagen: this.dataset.imagen || '',
              descripcion: this.dataset.descripcion || '',
              categoria: this.dataset.categoria || '',
              cantidad: 1
            });
          }
        });
      });

      /* ---- Filtros por categoría ---- */
      var filterBtns = document.querySelectorAll('.filter-btn');
      var categorySections = document.querySelectorAll('.category-section');

      filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
          var filter = this.dataset.filter;

          // Actualizar botón activo
          filterBtns.forEach(function(b) { b.classList.remove('active'); });
          this.classList.add('active');

          if (filter === 'todos') {
            categorySections.forEach(function(s) {
              s.style.display = 'block';
            });
            // Scroll al inicio del menú
            document.querySelector('.menu-hero').scrollIntoView({ behavior: 'smooth' });
          } else {
            categorySections.forEach(function(s) {
              var cat = s.dataset.categoria;
              s.style.display = cat === filter ? 'block' : 'none';
            });
            // Scroll a la sección
            var target = document.querySelector('.category-section[data-categoria="' + filter + '"]');
            if (target) {
              target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }
        });
      });

      /* ---- Smooth scroll ajustado por header desde filtros ---- */
      // Cuando se hace clic en category-card en index, viene con hash
      if (window.location.hash) {
        var targetSection = document.querySelector(window.location.hash);
        if (targetSection) {
          setTimeout(function() {
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 300);

          // Activar el filtro correspondiente
          var catId = window.location.hash.replace('#', '');
          var filterBtn = document.querySelector('.filter-btn[data-filter="' + catId + '"]');
          if (filterBtn) {
            filterBtn.click();
          }
        }
      }

      /* ---- Sticky filter scroll offset ---- */
      // Ajustar scroll-margin-top dinámicamente según header height
      var headerHeight = document.getElementById('main-header')?.offsetHeight || 70;
      categorySections.forEach(function(s) {
        s.style.scrollMarginTop = (headerHeight + 80) + 'px';
      });
    });
  </script>

</body>
</html>



