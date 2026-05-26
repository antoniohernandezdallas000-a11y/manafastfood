<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maná Fast Food — Sabor que Alimenta el Alma</title>
  <meta name="description" content="Maná Fast Food. La mejor comida rápida de la ciudad: hamburguesas, hot dogs, enrollados, papas y más. Pide ya.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — INDEX.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    /* ---- CATEGORIES GRID ---- */
    .categories-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-top: 40px;
    }

    .category-card {
      border-radius: var(--radius-lg);
      padding: 36px 20px 28px;
      text-align: center;
      color: var(--color-blanco);
      transition: var(--transition);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      text-decoration: none;
      display: block;
    }

    .category-card::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, transparent 50%);
      pointer-events: none;
    }

    .category-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 16px 40px rgba(0,0,0,0.4);
    }

    .category-card:active {
      transform: translateY(-2px) scale(0.98);
    }

    .category-icon {
      font-size: 3.2rem;
      margin-bottom: 12px;
      position: relative;
      z-index: 1;
      display: block;
      line-height: 1;
    }

    .category-card h3 {
      font-family: var(--font-heading);
      font-size: 1.05rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      position: relative;
      z-index: 1;
    }

    .category-card .count {
      font-family: var(--font-body);
      font-size: 0.75rem;
      opacity: 0.7;
      margin-top: 4px;
      position: relative;
      z-index: 1;
      display: block;
    }

    /* ---- FEATURED PRODUCTS ---- */
    .featured-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    /* ---- WHY MANÁ ---- */
    .why-mana-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
    }

    .why-mana-card {
      text-align: center;
      padding: 44px 28px;
      background: var(--color-bg-card);
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-gris-oscuro);
      transition: var(--transition);
    }

    .why-mana-card:hover {
      border-color: var(--color-amarillo);
      transform: translateY(-6px);
      box-shadow: 0 12px 32px rgba(0,0,0,0.3);
    }

    .why-mana-icon {
      width: 72px;
      height: 72px;
      margin: 0 auto 20px;
      background: var(--color-bg-alt);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      border: 2px solid var(--color-gris-oscuro);
      transition: var(--transition);
    }

    .why-mana-card:hover .why-mana-icon {
      border-color: var(--color-amarillo);
      background: rgba(255,184,28,0.1);
    }

    .why-mana-card h3 {
      font-family: var(--font-heading);
      font-size: 1.15rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--color-blanco);
      margin-bottom: 10px;
    }

    .why-mana-card p {
      color: var(--color-gris);
      font-size: 0.9rem;
      line-height: 1.7;
    }

    /* ---- TESTIMONIALS ---- */
    .testimonials-section {
      overflow: hidden;
      position: relative;
    }

    .testimonials-track {
      display: flex;
      gap: 24px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      padding: 20px 4px 28px;
      scrollbar-width: none;
    }

    .testimonials-track::-webkit-scrollbar {
      display: none;
    }

    .testimonial-card {
      min-width: 340px;
      flex: 0 0 auto;
      scroll-snap-align: start;
      background: var(--color-bg-card);
      border-radius: var(--radius-lg);
      padding: 36px 28px 28px;
      border: 1px solid var(--color-gris-oscuro);
      position: relative;
      transition: var(--transition);
    }

    .testimonial-card:hover {
      border-color: var(--color-gris);
      transform: translateY(-4px);
    }

    .testimonial-card::before {
      content: '"';
      font-family: var(--font-display);
      font-size: 5rem;
      color: var(--color-rojo);
      opacity: 0.2;
      position: absolute;
      top: -4px;
      left: 16px;
      line-height: 1;
      pointer-events: none;
    }

    .testimonial-stars {
      color: var(--color-amarillo);
      font-size: 1rem;
      letter-spacing: 2px;
      margin-bottom: 12px;
      position: relative;
      z-index: 1;
    }

    .testimonial-card blockquote {
      font-style: italic;
      color: var(--color-gris-claro);
      font-size: 0.95rem;
      line-height: 1.7;
      margin-bottom: 16px;
      position: relative;
      z-index: 1;
    }

    .testimonial-author {
      font-family: var(--font-heading);
      color: var(--color-amarillo);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .testimonial-author span {
      display: block;
      font-family: var(--font-body);
      color: var(--color-gris);
      font-size: 0.75rem;
      text-transform: none;
      letter-spacing: 0;
      margin-top: 2px;
      font-weight: 400;
    }

    .testimonial-dots {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 8px;
    }

    .testimonial-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: var(--color-gris-oscuro);
      border: 2px solid transparent;
      cursor: pointer;
      transition: var(--transition);
      padding: 0;
    }

    .testimonial-dot:hover {
      background: var(--color-gris);
    }

    .testimonial-dot.active {
      background: var(--color-amarillo);
      border-color: var(--color-amarillo);
      box-shadow: 0 0 12px rgba(255,184,28,0.3);
    }

    /* ---- CTA SECTION ---- */
    .cta-section {
      padding: 100px 0;
      text-align: center;
      background: linear-gradient(135deg, #0a0000 0%, #1a0000 40%, #0a0000 100%);
      position: relative;
      overflow: hidden;
    }

    .cta-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse at 30% 50%, rgba(211,18,18,0.2) 0%, transparent 60%),
        radial-gradient(ellipse at 70% 50%, rgba(255,184,28,0.08) 0%, transparent 60%);
      pointer-events: none;
    }

    .cta-section .container {
      position: relative;
      z-index: 1;
    }

    .cta-section h2 {
      font-family: var(--font-display);
      font-size: clamp(2.5rem, 7vw, 5.5rem);
      color: var(--color-blanco);
      text-transform: uppercase;
      line-height: 1;
      margin-bottom: 8px;
    }

    .cta-section .highlight {
      color: var(--color-amarillo);
      display: block;
    }

    .cta-section p {
      color: var(--color-gris);
      font-size: 1.1rem;
      max-width: 500px;
      margin: 16px auto 32px;
    }

    .cta-section .btn {
      font-size: 1.1rem;
      padding: 20px 48px;
      animation: pulse-glow 2s ease-in-out infinite;
    }

    /* ---- SECTION DIVIDER ---- */
    .section-divider {
      width: 80px;
      height: 4px;
      background: var(--color-rojo);
      border-radius: var(--radius-full);
      margin: 0 auto 48px;
    }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 992px) {
      .categories-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .featured-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .why-mana-grid {
        grid-template-columns: 1fr;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
      }
    }

    @media (max-width: 576px) {
      .categories-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
      }

      .category-card {
        padding: 24px 12px 20px;
      }

      .category-icon {
        font-size: 2.4rem;
      }

      .category-card h3 {
        font-size: 0.85rem;
      }

      .featured-grid {
        grid-template-columns: 1fr;
      }

      .testimonial-card {
        min-width: 270px;
        padding: 28px 20px 24px;
      }

      .cta-section {
        padding: 60px 0;
      }

      .cta-section .btn {
        width: 100%;
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
        <a href="index.php" class="active" data-nav>Inicio</a>
        <a href="menu.php" data-nav>Menú</a>
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

  <!-- ===== HERO ===== -->
  <section class="hero" id="hero">
    <div class="hero-pattern"></div>
    <div class="container">
      <span class="hero-badge animate-on-scroll">🔥 Desde 2020</span>
      <h1 class="hero-title animate-on-scroll" style="animation-delay:0.1s;">
        MANÁ
        <span class="highlight">FAST FOOD</span>
      </h1>
      <p class="hero-subtitle animate-on-scroll" style="animation-delay:0.2s;">
        Sabor que alimenta el alma. La mejor comida rápida de la ciudad,<br>
        hecha con ingredientes frescos y mucha pasión.
      </p>
      <div class="hero-actions animate-on-scroll" style="animation-delay:0.3s;">
        <a href="menu.php" class="btn btn-primary btn-lg">VER MENÚ →</a>
        <a href="#categorias" class="btn btn-outline btn-lg">EXPLORAR</a>
      </div>
    </div>
  </section>

  <!-- ===== CATEGORÍAS RÁPIDAS ===== -->
  <section class="section" id="categorias">
    <div class="container">
      <h2 class="section-title text-center animate-on-scroll" style="display:block;text-align:center;">Categorías</h2>
      <p class="section-subtitle text-center animate-on-scroll">Elige tu antojo</p>
      <div class="section-divider"></div>

      <div class="categories-grid animate-on-scroll">
        <a href="menu.php#hamburguesas" class="category-card" style="background:#D31212;">
          <span class="category-icon">🍔</span>
          <h3>Hamburguesas</h3>
          <span class="count">7 variedades</span>
        </a>
        <a href="menu.php#hotdogs" class="category-card" style="background:#FFB81C;color:#111111;">
          <span class="category-icon">🌭</span>
          <h3>Hot Dogs</h3>
          <span class="count">5 estilos</span>
        </a>
        <a href="menu.php#enrollados" class="category-card" style="background:#D31212;">
          <span class="category-icon">🌯</span>
          <h3>Enrollados</h3>
          <span class="count">3 especiales</span>
        </a>
        <a href="menu.php#papas" class="category-card" style="background:#FFB81C;color:#111111;">
          <span class="category-icon">🍟</span>
          <h3>Papas</h3>
          <span class="count">4 opciones</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ===== PRODUCTOS DESTACADOS ===== -->
  <section class="section" style="background:var(--color-bg-alt);">
    <div class="container">
      <h2 class="section-title text-center animate-on-scroll" style="display:block;text-align:center;">Más Pedidos</h2>
      <p class="section-subtitle text-center animate-on-scroll">Los favoritos de nuestros clientes</p>
      <div class="section-divider"></div>

      <div class="featured-grid animate-on-scroll">
        <!-- Producto 1 -->
        <div class="product-card">
          <div class="product-card-image" style="background:linear-gradient(135deg,#2a0000,#1a0505);">
            <span style="font-size:5rem;opacity:0.3;filter:grayscale(1);">🍔</span>
            <span class="product-card-badge">Más vendido</span>
          </div>
          <div class="product-card-body">
            <h3 class="product-card-name">Maná Burger</h3>
            <p class="product-card-desc">Doble carne, queso, tocineta, cebolla caramelizada y salsa especial</p>
            <div class="product-card-footer">
              <span class="product-card-price" data-price="7.0">$7.00</span>
              <button class="btn btn-primary btn-sm add-to-cart" data-id="destacado01" data-nombre="Maná Burger" data-precio="7.0" data-imagen="" data-descripcion="Doble carne, queso, tocineta, cebolla caramelizada y salsa especial" data-categoria="Hamburguesas">Agregar +</button>
            </div>
          </div>
        </div>

        <!-- Producto 2 -->
        <div class="product-card">
          <div class="product-card-image" style="background:linear-gradient(135deg,#2a1a00,#1a0a00);">
            <span style="font-size:5rem;opacity:0.3;filter:grayscale(1);">🌭</span>
            <span class="product-card-badge">🔥 Popular</span>
          </div>
          <div class="product-card-body">
            <h3 class="product-card-name">Maná Dog</h3>
            <p class="product-card-desc">Pan artesanal, salchicha alemana, queso, papa pay y salsas clásicas</p>
            <div class="product-card-footer">
              <span class="product-card-price" data-price="3.5">$3.50</span>
              <button class="btn btn-primary btn-sm add-to-cart" data-id="destacado02" data-nombre="Maná Dog" data-precio="3.5" data-imagen="" data-descripcion="Pan artesanal, salchicha alemana, queso, papa pay y salsas clásicas" data-categoria="Hot Dogs">Agregar +</button>
            </div>
          </div>
        </div>

        <!-- Producto 3 -->
        <div class="product-card">
          <div class="product-card-image" style="background:linear-gradient(135deg,#001a2a,#000a1a);">
            <span style="font-size:5rem;opacity:0.3;filter:grayscale(1);">🌯</span>
          </div>
          <div class="product-card-body">
            <h3 class="product-card-name">Enrollado Mixto</h3>
            <p class="product-card-desc">Carne, pollo, queso, verduras salteadas y salsas envueltos en tortilla</p>
            <div class="product-card-footer">
              <span class="product-card-price" data-price="12.0">$12.00</span>
              <button class="btn btn-primary btn-sm add-to-cart" data-id="destacado03" data-nombre="Enrollado Mixto" data-precio="12.0" data-imagen="" data-descripcion="Carne, pollo, queso, verduras salteadas y salsas envueltos en tortilla" data-categoria="Enrollados">Agregar +</button>
            </div>
          </div>
        </div>

        <!-- Producto 4 -->
        <div class="product-card">
          <div class="product-card-image" style="background:linear-gradient(135deg,#2a2a00,#1a1a00);">
            <span style="font-size:5rem;opacity:0.3;filter:grayscale(1);">🍟</span>
          </div>
          <div class="product-card-body">
            <h3 class="product-card-name">Salchipapa</h3>
            <p class="product-card-desc">Papas fritas, salchicha, queso amarillo, salsas y tocineta</p>
            <div class="product-card-footer">
              <span class="product-card-price" data-price="9.0">$9.00</span>
              <button class="btn btn-primary btn-sm add-to-cart" data-id="destacado04" data-nombre="Salchipapa" data-precio="9.0" data-imagen="" data-descripcion="Papas fritas, salchicha, queso amarillo, salsas y tocineta" data-categoria="Papas">Agregar +</button>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center animate-on-scroll" style="margin-top:40px;">
        <a href="menu.php" class="btn btn-outline-amarillo">Ver Menú Completo →</a>
      </div>
    </div>
  </section>

  <!-- ===== POR QUÉ MANÁ ===== -->
  <section class="section">
    <div class="container">
      <h2 class="section-title text-center animate-on-scroll" style="display:block;text-align:center;">¿Por Qué Maná?</h2>
      <p class="section-subtitle text-center animate-on-scroll">Tres razones para elegirnos</p>
      <div class="section-divider"></div>

      <div class="why-mana-grid">
        <div class="why-mana-card animate-on-scroll">
          <div class="why-mana-icon">🔥</div>
          <h3>Sabor Único</h3>
          <p>Recetas originales creadas por chefs apasionados. Cada bocado es una explosión de sabor que no encontrarás en ningún otro lugar.</p>
        </div>
        <div class="why-mana-card animate-on-scroll" style="animation-delay:0.1s;">
          <div class="why-mana-icon">⚡</div>
          <h3>Rapidez</h3>
          <p>Sabemos que tu tiempo vale. Tu pedido estará listo en menos de 15 minutos o te invitamos. Compromiso Maná.</p>
        </div>
        <div class="why-mana-card animate-on-scroll" style="animation-delay:0.2s;">
          <div class="why-mana-icon">🥩</div>
          <h3>Calidad</h3>
          <p>Ingredientes frescos, carnes de primera y vegetales seleccionados. Cocina limpia, visible y sin atajos.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONIOS ===== -->
  <section class="section testimonials-section" style="background:var(--color-bg-alt);">
    <div class="container">
      <h2 class="section-title text-center animate-on-scroll" style="display:block;text-align:center;">Lo Que Dicen</h2>
      <p class="section-subtitle text-center animate-on-scroll">Nuestros comensales hablan</p>
      <div class="section-divider"></div>

      <div class="testimonials-track animate-on-scroll" id="testimonials-track">
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <blockquote>La Maná Burger es una locura. El punto de la carne, la tocineta crujiente… y ese pan artesanal lo cambia todo. Pido desde hace meses y nunca falla.</blockquote>
          <div class="testimonial-author">Carlos Méndez <span>Cliente frecuente</span></div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <blockquote>Los enrollados son los mejores de la zona. El Mixto lleva una cantidad de relleno que te deja full. Súper recomendados.</blockquote>
          <div class="testimonial-author">María Fernanda R.<span>Pedido semanal</span></div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <blockquote>Pido por delivery y siempre llega caliente, bien empacado y en menos de 30 minutos. La calidad es consistente, eso es lo que más valoro.</blockquote>
          <div class="testimonial-author">José Gregorio L.<span>Delivery frecuente</span></div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★☆</div>
          <blockquote>Las Tripapas Maná son brutales. Con tres tipos de proteína, es perfecta para compartir o para un antojo bien serio. Volveré por más.</blockquote>
          <div class="testimonial-author">Andrea Castillo <span>Grupo de amigos</span></div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-stars">★★★★★</div>
          <blockquote>El Maná Dog Especial es mi perdición. Pan tostado, salchicha alemana, papa pay… Una combinación perfecta. El mejor hot dog de Caracas.</blockquote>
          <div class="testimonial-author">Luisana Parra <span>Fanática de los hot dogs</span></div>
        </div>
      </div>

      <div class="testimonial-dots" id="testimonial-dots">
        <button class="testimonial-dot active" data-index="0" aria-label="Testimonio 1"></button>
        <button class="testimonial-dot" data-index="1" aria-label="Testimonio 2"></button>
        <button class="testimonial-dot" data-index="2" aria-label="Testimonio 3"></button>
        <button class="testimonial-dot" data-index="3" aria-label="Testimonio 4"></button>
        <button class="testimonial-dot" data-index="4" aria-label="Testimonio 5"></button>
      </div>
    </div>
  </section>

  <!-- ===== CTA FINAL ===== -->
  <section class="cta-section">
    <div class="container">
      <h2 class="animate-on-scroll">
        ¿Listo para el<br>
        <span class="highlight">mejor maná?</span>
      </h2>
      <p class="animate-on-scroll">Haz tu pedido ahora y descubre por qué todos hablan de nosotros.</p>
      <a href="menu.php" class="btn btn-primary btn-lg animate-on-scroll">PIDE AHORA →</a>
    </div>
  </section>

  <!-- ===== FOOTER (inyectado por app.js) ===== -->
  <div id="footer-placeholder"></div>

  <!-- ===== TOAST CONTAINER (inyectado por app.js si no existe) ===== -->

  <!-- ===== SCRIPTS ===== -->
  <script src="js/app.js"></script>
  <script src="js/cart.js"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — INDEX.JS
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      /* ---- Testimonials Carousel ---- */
      const track = document.getElementById('testimonials-track');
      const dots = document.querySelectorAll('.testimonial-dot');

      if (track && dots.length) {
        // Click en dots
        dots.forEach(function(dot) {
          dot.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            const cards = track.querySelectorAll('.testimonial-card');
            if (cards[index]) {
              cards[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            }
            dots.forEach(function(d) { d.classList.remove('active'); });
            this.classList.add('active');
          });
        });

        // Actualizar dot activo al hacer scroll
        let scrollTimeout;
        track.addEventListener('scroll', function() {
          clearTimeout(scrollTimeout);
          scrollTimeout = setTimeout(function() {
            const cards = track.querySelectorAll('.testimonial-card');
            const scrollLeft = track.scrollLeft;
            const cardWidth = cards.length > 0 ? cards[0].offsetWidth + 24 : 400;
            const activeIndex = Math.round(scrollLeft / cardWidth);
            dots.forEach(function(d, i) {
              d.classList.toggle('active', i === activeIndex);
            });
          }, 100);
        });
      }

      /* ---- Add to Cart from featured products ---- */
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
          } else {
            console.warn('Cart no disponible');
          }
        });
      });
    });
  </script>

</body>
</html>



