<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tu Pedido — Maná Fast Food</title>
  <meta name="description" content="Revisa tu pedido de Maná Fast Food antes de pagar.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — CARRITO.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    .cart-page {
      padding-top: calc(var(--header-height) + 40px);
      min-height: 100vh;
    }

    .cart-hero {
      text-align: center;
      padding: 40px 0 32px;
    }

    .cart-hero h1 {
      font-family: var(--font-display);
      font-size: clamp(3rem, 10vw, 5.5rem);
      text-transform: uppercase;
      color: var(--color-blanco);
      line-height: 1;
      letter-spacing: 4px;
      text-shadow: 0 4px 20px rgba(211,18,18,0.3);
    }

    .cart-hero h1 .highlight {
      color: var(--color-amarillo);
    }

    .cart-hero p {
      color: var(--color-gris);
      font-size: 1.05rem;
      margin-top: 8px;
    }

    /* ---- CART LAYOUT ---- */
    .cart-layout {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 32px;
      align-items: start;
    }

    /* ---- CART TABLE ---- */
    .cart-table-wrapper {
      background: var(--color-bg-card);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-lg);
      padding: 24px;
      overflow-x: auto;
    }

    .cart-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .cart-table thead th {
      font-family: var(--font-heading);
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--color-gris);
      padding: 12px 16px;
      border-bottom: 2px solid var(--color-gris-oscuro);
      text-align: left;
      white-space: nowrap;
    }

    .cart-table thead th:last-child {
      text-align: center;
    }

    .cart-table tbody tr {
      transition: var(--transition);
    }

    .cart-table tbody tr:hover {
      background: rgba(255,255,255,0.02);
    }

    .cart-table tbody tr:last-child td {
      border-bottom: none;
    }

    .cart-table tbody td {
      padding: 16px;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      vertical-align: middle;
      font-size: 0.95rem;
    }

    .cart-table .product-cell {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .cart-table .product-thumb {
      width: 56px;
      height: 56px;
      border-radius: var(--radius-sm);
      background: var(--color-bg-alt);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      flex-shrink: 0;
      border: 1px solid var(--color-gris-oscuro);
    }

    .cart-table .product-info h3 {
      font-family: var(--font-heading);
      font-size: 0.9rem;
      text-transform: uppercase;
      color: var(--color-blanco);
      margin-bottom: 2px;
      letter-spacing: 0.5px;
    }

    .cart-table .product-info span {
      font-size: 0.75rem;
      color: var(--color-gris);
    }

    .cart-table .price-cell {
      font-family: var(--font-display);
      font-size: 1.15rem;
      color: var(--color-amarillo);
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    .cart-table .qty-cell {
      min-width: 120px;
    }

    .qty-controls {
      display: inline-flex;
      align-items: center;
      gap: 0;
      background: var(--color-bg-alt);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-full);
      overflow: hidden;
    }

    .qty-controls button {
      width: 36px;
      height: 36px;
      background: transparent;
      color: var(--color-blanco);
      font-size: 1.1rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      border: none;
    }

    .qty-controls button:hover {
      background: var(--color-rojo);
      color: var(--color-blanco);
    }

    .qty-controls button:active {
      transform: scale(0.9);
    }

    .qty-controls .qty-value {
      min-width: 36px;
      text-align: center;
      font-family: var(--font-heading);
      font-size: 0.95rem;
      color: var(--color-blanco);
      padding: 0 4px;
    }

    .cart-table .subtotal-cell {
      font-family: var(--font-display);
      font-size: 1.15rem;
      color: var(--color-blanco);
      white-space: nowrap;
    }

    .cart-table .remove-cell {
      text-align: center;
    }

    .btn-remove-item {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: transparent;
      color: var(--color-gris-oscuro);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      border: 1px solid transparent;
    }

    .btn-remove-item:hover {
      background: rgba(211,18,18,0.15);
      color: var(--color-rojo);
      border-color: var(--color-rojo);
      transform: rotate(90deg);
    }

    /* ---- EMPTY STATE ---- */
    .cart-empty-state {
      text-align: center;
      padding: 80px 20px;
    }

    .cart-empty-state .empty-icon {
      font-size: 4rem;
      margin-bottom: 16px;
      opacity: 0.3;
    }

    .cart-empty-state h2 {
      font-family: var(--font-display);
      font-size: 2rem;
      color: var(--color-gris);
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 8px;
    }

    .cart-empty-state p {
      color: var(--color-gris-oscuro);
      font-size: 1rem;
      margin-bottom: 24px;
    }

    .cart-empty-state .btn {
      font-size: 0.9rem;
      padding: 16px 40px;
    }

    /* ---- DELIVERY TOGGLE ---- */
    .delivery-toggle {
      display: flex;
      gap: 0;
      background: var(--color-bg-alt);
      border-radius: var(--radius-md);
      overflow: hidden;
      margin-bottom: 24px;
      border: 2px solid var(--color-gris-oscuro);
    }

    .delivery-toggle-btn {
      flex: 1;
      padding: 14px 20px;
      text-align: center;
      font-family: var(--font-heading);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      background: transparent;
      color: var(--color-gris);
      transition: var(--transition);
      cursor: pointer;
    }

    .delivery-toggle-btn.active {
      background: var(--color-rojo);
      color: var(--color-blanco);
    }

    .delivery-toggle-btn:hover:not(.active) {
      color: var(--color-blanco);
    }

    /* ---- CART SIDEBAR ---- */
    .cart-sidebar {
      background: var(--color-bg-card);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-lg);
      padding: 28px;
      position: sticky;
      top: calc(var(--header-height) + 20px);
    }

    .cart-sidebar h3 {
      font-family: var(--font-heading);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--color-amarillo);
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--color-gris-oscuro);
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      font-size: 0.9rem;
    }

    .summary-row .label {
      color: var(--color-gris);
    }

    .summary-row .value {
      font-weight: 700;
      color: var(--color-blanco);
    }

    .summary-row .value.amarillo {
      color: var(--color-amarillo);
      font-family: var(--font-display);
      font-size: 1.1rem;
    }

    .summary-row .value.verde {
      color: var(--color-success);
    }

    .summary-divider {
      height: 1px;
      background: var(--color-gris-oscuro);
      margin: 8px 0;
    }

    .summary-row.total-usd .label {
      font-family: var(--font-heading);
      font-size: 0.85rem;
      color: var(--color-blanco);
      text-transform: uppercase;
    }

    .summary-row.total-usd .value {
      font-family: var(--font-display);
      font-size: 1.6rem;
      color: var(--color-amarillo);
      letter-spacing: 1px;
    }

    .summary-row.total-bs .label {
      font-size: 0.8rem;
      color: var(--color-gris-oscuro);
    }

    .summary-row.total-bs .value {
      font-size: 0.95rem;
      color: var(--color-gris-claro);
    }

    /* ---- NOTAS ---- */
    .cart-notes {
      margin-top: 24px;
      padding-top: 20px;
      border-top: 1px solid var(--color-gris-oscuro);
    }

    .cart-notes label {
      display: block;
      font-family: var(--font-heading);
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--color-gris-claro);
      margin-bottom: 8px;
    }

    .cart-notes textarea {
      width: 100%;
      padding: 12px 16px;
      background: var(--color-bg-alt);
      border: 2px solid var(--color-gris-oscuro);
      border-radius: var(--radius-md);
      color: var(--color-blanco);
      font-family: var(--font-body);
      font-size: 0.9rem;
      resize: vertical;
      min-height: 80px;
      transition: var(--transition);
    }

    .cart-notes textarea:focus {
      border-color: var(--color-amarillo);
      box-shadow: 0 0 0 3px rgba(255,184,28,0.15);
    }

    /* ---- CART ACTIONS ---- */
    .cart-actions {
      margin-top: 28px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .cart-actions .btn-checkout {
      width: 100%;
      padding: 18px 32px;
      font-size: 1rem;
      letter-spacing: 2px;
    }

    .cart-actions .btn-back-menu {
      width: 100%;
      padding: 14px 32px;
    }

    /* ---- TASA INFO ---- */
    .tasa-info {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      background: rgba(255,184,28,0.08);
      border: 1px solid rgba(255,184,28,0.15);
      border-radius: var(--radius-sm);
      font-size: 0.75rem;
      color: var(--color-gris);
      margin-top: 12px;
    }

    .tasa-info strong {
      color: var(--color-amarillo);
    }

    /* ---- DISCOUNT BADGE ---- */
    .discount-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: rgba(40,167,69,0.15);
      color: var(--color-success);
      font-family: var(--font-heading);
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 3px 10px;
      border-radius: var(--radius-full);
    }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 968px) {
      .cart-layout {
        grid-template-columns: 1fr;
        gap: 24px;
      }

      .cart-sidebar {
        position: static;
      }
    }

    @media (max-width: 600px) {
      .cart-page {
        padding-top: calc(var(--header-height) + 20px);
      }

      .cart-hero {
        padding: 24px 0 20px;
      }

      .cart-table-wrapper {
        padding: 12px;
        border-radius: var(--radius-md);
      }

      .cart-table tbody td {
        padding: 12px 8px;
        font-size: 0.85rem;
      }

      .cart-table .product-thumb {
        width: 40px;
        height: 40px;
        font-size: 1.3rem;
      }

      .cart-table .product-info h3 {
        font-size: 0.75rem;
      }

      .cart-table .price-cell,
      .cart-table .subtotal-cell {
        font-size: 0.95rem;
      }

      .qty-controls button {
        width: 30px;
        height: 30px;
        font-size: 0.9rem;
      }

      .qty-controls .qty-value {
        min-width: 28px;
        font-size: 0.85rem;
      }

      .cart-sidebar {
        padding: 20px;
      }

      .summary-row.total-usd .value {
        font-size: 1.3rem;
      }
    }

    @media (max-width: 480px) {
      .cart-table thead th {
        font-size: 0.6rem;
        padding: 8px 6px;
      }

      .cart-table tbody td {
        padding: 8px 6px;
      }

      .cart-table .product-cell {
        gap: 8px;
      }

      .cart-table .product-thumb {
        width: 32px;
        height: 32px;
        font-size: 1rem;
      }

      .delivery-toggle-btn {
        font-size: 0.7rem;
        padding: 10px 12px;
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
        <a href="menu.php" data-nav>Menú</a>
        <a href="login.php" class="btn btn-sm btn-outline-rojo" style="padding:6px 16px;font-size:0.7rem;" data-nav>Entrar</a>
        <a href="registro.php" class="btn btn-sm btn-primary" style="padding:6px 16px;font-size:0.7rem;" data-nav>Registro</a>
        <a href="carrito.php" class="cart-btn active" aria-label="Carrito de compras">
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

  <!-- ===== CART PAGE ===== -->
  <main class="cart-page">

    <section class="cart-hero">
      <div class="container">
        <h1>TU <span class="highlight">PEDIDO</span></h1>
        <p id="cart-item-count-text">Revisa y confirma tu pedido antes de pagar</p>
      </div>
    </section>

    <section style="padding-bottom:80px;">
      <div class="container">

        <!-- Delivery / Retiro Toggle -->
        <div class="delivery-toggle" id="delivery-toggle">
          <button class="delivery-toggle-btn active" data-tipo="delivery">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><path d="M12 22s-8-4-8-10V5l8-3 8 3v7c0 6-8 10-8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            Delivery
          </button>
          <button class="delivery-toggle-btn" data-tipo="retiro">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            Retiro
          </button>
        </div>

        <!-- Cart Layout -->
        <div class="cart-layout">

          <!-- LEFT: Cart Table -->
          <div class="cart-table-wrapper" id="cart-table-container">
            <!-- Empty State (shown when cart is empty) -->
            <div class="cart-empty-state" id="cart-empty-state" style="display:none;">
              <div class="empty-icon">🍔</div>
              <h2>Nada aquí aún</h2>
              <p>Agrega productos del menú para empezar tu pedido.</p>
              <a href="menu.php" class="btn btn-primary btn-lg">VER MENÚ</a>
            </div>

            <!-- Cart Table (shown when cart has items) -->
            <div id="cart-content">
              <table class="cart-table" id="cart-table">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th style="text-align:right;">Precio Unit.</th>
                    <th style="text-align:center;">Cantidad</th>
                    <th style="text-align:right;">Subtotal</th>
                    <th style="text-align:center;width:50px;"></th>
                  </tr>
                </thead>
                <tbody id="cart-items-body">
                  <!-- Rendered by JavaScript -->
                </tbody>
              </table>
            </div>
          </div>

          <!-- RIGHT: Summary Sidebar -->
          <div class="cart-sidebar" id="cart-sidebar">
            <h3>Resumen del Pedido</h3>

            <div id="sidebar-totals">
              <div class="summary-row">
                <span class="label">Subtotal</span>
                <span class="value" id="subtotal-usd">$0.00</span>
              </div>
              <div class="summary-row" id="discount-row" style="display:none;">
                <span class="label">Descuento</span>
                <span class="value verde" id="discount-value">-$0.00</span>
              </div>
              <div class="summary-divider"></div>
              <div class="summary-row total-usd">
                <span class="label">Total USD</span>
                <span class="value amarillo" id="total-usd">$0.00</span>
              </div>
              <div class="summary-row total-bs" id="total-bs-row">
                <span class="label">Total Bs.</span>
                <span class="value" id="total-bs">Bs. 0,00</span>
              </div>
            </div>

            <div class="tasa-info" id="tasa-info" style="display:none;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
              <span>Tasa BCV: <strong id="tasa-value">Bs. 0,00</strong> por USD</span>
            </div>

            <!-- Notas -->
            <div class="cart-notes">
              <label for="cart-notas">Notas adicionales</label>
              <textarea id="cart-notas" placeholder="Ej: Sin cebolla, extra salsa, punto de la carne…" maxlength="500"></textarea>
            </div>

            <!-- Actions -->
            <div class="cart-actions">
              <a href="checkout.php" class="btn btn-primary btn-checkout" id="btn-continuar">
                CONTINUAR AL PAGO →
              </a>
              <a href="menu.php" class="btn btn-outline btn-back-menu">
                SEGUIR PIDIENDO
              </a>
            </div>

            <!-- Vaciar (solo visible con items) -->
            <button class="btn btn-sm btn-outline-rojo" id="btn-vaciar" style="width:100%;margin-top:8px;font-size:0.7rem;padding:10px;">
              Vaciar Carrito
            </button>
          </div>

        </div><!-- /cart-layout -->
      </div>
    </section>

  </main>

  <!-- ===== FOOTER ===== -->
  <div id="footer-placeholder"></div>

  <!-- ===== SCRIPTS ===== -->
  <script src="js/app.js"></script>
  <script src="js/cart.js"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — CARRITO.JS
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      var cartTableBody = document.getElementById('cart-items-body');
      var cartContent = document.getElementById('cart-content');
      var cartEmpty = document.getElementById('cart-empty-state');
      var cartSidebar = document.getElementById('cart-sidebar');
      var btnContinuar = document.getElementById('btn-continuar');
      var btnVaciar = document.getElementById('btn-vaciar');
      var notasInput = document.getElementById('cart-notas');

      /* ---- Renderizar carrito ---- */
      function renderCart() {
        var cart = Cart.getCart();
        var items = cart.items || [];
        var totals = Cart.calcularTotales();

        // Mostrar/ocultar empty state
        if (items.length === 0) {
          cartContent.style.display = 'none';
          cartEmpty.style.display = 'block';
          btnContinuar.style.opacity = '0.4';
          btnContinuar.style.pointerEvents = 'none';
          btnVaciar.style.display = 'none';
          document.getElementById('cart-item-count-text').textContent = 'Tu carrito está vacío';
          renderSummary(totals);
          return;
        }

        cartContent.style.display = 'block';
        cartEmpty.style.display = 'none';
        btnContinuar.style.opacity = '1';
        btnContinuar.style.pointerEvents = 'auto';
        btnVaciar.style.display = 'block';

        // Actualizar contador
        var totalItems = items.reduce(function(sum, item) { return sum + item.cantidad; }, 0);
        document.getElementById('cart-item-count-text').textContent = totalItems + ' producto' + (totalItems !== 1 ? 's' : '') + ' en tu pedido';

        // Renderizar filas de la tabla
        var html = '';
        var iconMap = {
          'Hamburguesas': '🍔',
          'Especiales': '🍔',
          'Hot Dogs': '🌭',
          'Enrollados': '🌯',
          'Pepitos': '🥖',
          'Papas': '🍟',
          'Extras': '🧀',
          'Bebidas': '🥤',
        };

        items.forEach(function(item) {
          var icon = iconMap[item.categoria] || '🍽️';
          var subtotal = (item.precio * item.cantidad);

          html += '<tr data-id="' + item.id + '">' +
            '<td>' +
              '<div class="product-cell">' +
                '<div class="product-thumb">' + icon + '</div>' +
                '<div class="product-info">' +
                  '<h3>' + item.nombre + '</h3>' +
                  (item.categoria ? '<span>' + item.categoria + '</span>' : '') +
                '</div>' +
              '</div>' +
            '</td>' +
            '<td class="price-cell" style="text-align:right;">' + App.formatUSD(item.precio) + '</td>' +
            '<td class="qty-cell" style="text-align:center;">' +
              '<div class="qty-controls">' +
                '<button class="qty-minus" data-id="' + item.id + '" aria-label="Disminuir cantidad">−</button>' +
                '<span class="qty-value">' + item.cantidad + '</span>' +
                '<button class="qty-plus" data-id="' + item.id + '" aria-label="Aumentar cantidad">+</button>' +
              '</div>' +
            '</td>' +
            '<td class="subtotal-cell" style="text-align:right;">' + App.formatUSD(subtotal) + '</td>' +
            '<td class="remove-cell">' +
              '<button class="btn-remove-item" data-id="' + item.id + '" aria-label="Eliminar producto">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
              '</button>' +
            '</td>' +
          '</tr>';
        });

        cartTableBody.innerHTML = html;

        // Renderizar resumen
        renderSummary(totals);

        // Sincronizar notas
        notasInput.value = cart.notas || '';

        // Sincronizar tipo (delivery/retiro)
        syncDeliveryToggle(cart.tipo);

        // Attach event listeners
        attachTableListeners();
      }

      /* ---- Renderizar resumen ---- */
      function renderSummary(totals) {
        document.getElementById('subtotal-usd').textContent = App.formatUSD(totals.subtotalUSD);

        var discountRow = document.getElementById('discount-row');
        if (totals.descuentoPorcentaje > 0) {
          discountRow.style.display = 'flex';
          document.getElementById('discount-value').textContent = '-' + App.formatUSD(totals.descuentoUSD);
        } else {
          discountRow.style.display = 'none';
        }

        document.getElementById('total-usd').textContent = App.formatUSD(totals.totalUSD);

        var bsRow = document.getElementById('total-bs-row');
        var tasaInfo = document.getElementById('tasa-info');
        if (totals.tasaBCV > 0) {
          bsRow.style.display = 'flex';
          document.getElementById('total-bs').textContent = App.formatBS(totals.totalBS);
          tasaInfo.style.display = 'flex';
          document.getElementById('tasa-value').textContent = App.formatBS(totals.tasaBCV);
        } else {
          bsRow.style.display = 'none';
          tasaInfo.style.display = 'none';
        }
      }

      /* ---- Sincronizar delivery toggle ---- */
      function syncDeliveryToggle(tipo) {
        var btns = document.querySelectorAll('.delivery-toggle-btn');
        btns.forEach(function(btn) {
          btn.classList.toggle('active', btn.dataset.tipo === (tipo || 'delivery'));
        });
      }

      /* ---- Attach listeners a la tabla ---- */
      function attachTableListeners() {
        // Botones de cantidad
        document.querySelectorAll('.qty-minus').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var item = Cart.getCart().items.find(function(i) { return i.id === id; });
            if (item) {
              Cart.updateQuantity(id, item.cantidad - 1);
            }
          });
        });

        document.querySelectorAll('.qty-plus').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var item = Cart.getCart().items.find(function(i) { return i.id === id; });
            if (item) {
              Cart.updateQuantity(id, item.cantidad + 1);
            }
          });
        });

        // Botones de eliminar
        document.querySelectorAll('.btn-remove-item').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var id = this.dataset.id;
            Cart.removeFromCart(id);
          });
        });
      }

      /* ---- Delivery/Retiro Toggle ---- */
      document.querySelectorAll('.delivery-toggle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.delivery-toggle-btn').forEach(function(b) { b.classList.remove('active'); });
          this.classList.add('active');
          Cart.setTipo(this.dataset.tipo);
          App.showToast('Pedido tipo: ' + (this.dataset.tipo === 'delivery' ? 'Delivery' : 'Retiro'), 'info');
        });
      });

      /* ---- Notas ---- */
      notasInput.addEventListener('change', function() {
        Cart.setNotas(this.value);
      });

      // Guardar notas también al perder el foco
      notasInput.addEventListener('blur', function() {
        Cart.setNotas(this.value);
      });

      /* ---- Vaciar carrito ---- */
      btnVaciar.addEventListener('click', function() {
        if (confirm('¿Estás seguro de vaciar el carrito?')) {
          Cart.clearCart();
        }
      });

      /* ---- Escuchar eventos de carrito actualizado ---- */
      document.addEventListener('cartUpdated', function() {
        renderCart();
      });

      /* ---- Renderizar al cargar ---- */
      renderCart();

      /* ---- Si venimos de checkout, mostrar toast ---- */
      var params = new URLSearchParams(window.location.search);
      if (params.get('return') === 'checkout') {
        App.showToast('Vuelve a revisar tu pedido antes de continuar', 'info');
      }
    });
  </script>

</body>
</html>




