<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido Confirmado — Maná Fast Food</title>
  <meta name="description" content="Tu pedido en Maná Fast Food ha sido confirmado. Gracias por tu compra.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — CONFIRMACION.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    .confirmacion-page {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding-top: calc(var(--header-height) + 20px);
    }

    .confirmacion-page main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px 60px;
    }

    .confirmacion-container {
      width: 100%;
      max-width: 680px;
      text-align: center;
    }

    /* ---- ANIMATED CHECKMARK ---- */
    .checkmark-container {
      width: 100px;
      height: 100px;
      margin: 0 auto 28px;
      position: relative;
    }

    .checkmark-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: rgba(40, 167, 69, 0.1);
      border: 4px solid var(--color-success);
      display: flex;
      align-items: center;
      justify-content: center;
      animation: checkmarkPop 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
      transform: scale(0);
    }

    .checkmark-circle svg {
      width: 50px;
      height: 50px;
      stroke: var(--color-success);
      stroke-width: 3;
      fill: none;
      stroke-dasharray: 80;
      stroke-dashoffset: 80;
      animation: checkmarkDraw 0.6s ease 0.4s forwards;
    }

    @keyframes checkmarkPop {
      0% { transform: scale(0); opacity: 0; }
      60% { transform: scale(1.15); }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes checkmarkDraw {
      to { stroke-dashoffset: 0; }
    }

    /* ---- Rings around checkmark ---- */
    .checkmark-ring {
      position: absolute;
      top: -8px;
      left: -8px;
      right: -8px;
      bottom: -8px;
      border-radius: 50%;
      border: 2px solid rgba(40, 167, 69, 0.2);
      animation: ringPulse 2s ease-in-out infinite;
    }

    .checkmark-ring:nth-child(2) {
      top: -16px;
      left: -16px;
      right: -16px;
      bottom: -16px;
      border-width: 1px;
      animation-delay: 0.5s;
      border-color: rgba(255, 184, 28, 0.15);
    }

    @keyframes ringPulse {
      0%, 100% { transform: scale(1); opacity: 0.5; }
      50% { transform: scale(1.05); opacity: 0.2; }
    }

    /* ---- TITLE ---- */
    .confirmacion-title {
      font-family: var(--font-display);
      font-size: clamp(2.2rem, 6vw, 4rem);
      text-transform: uppercase;
      line-height: 1;
      color: var(--color-blanco);
      margin-bottom: 8px;
      letter-spacing: 3px;
      animation: fadeInUp 0.6s ease 0.2s both;
    }

    .confirmacion-title .highlight {
      color: var(--color-amarillo);
      display: block;
    }

    .confirmacion-subtitle {
      color: var(--color-gris);
      font-size: 1.05rem;
      margin-bottom: 32px;
      animation: fadeInUp 0.6s ease 0.3s both;
    }

    /* ---- ORDER NUMBER ---- */
    .order-number-display {
      font-family: var(--font-heading);
      font-size: 1.6rem;
      letter-spacing: 3px;
      color: var(--color-blanco);
      margin-bottom: 8px;
      animation: fadeInUp 0.6s ease 0.35s both;
    }

    .order-number-display span {
      color: var(--color-amarillo);
    }

    .order-number-label {
      font-size: 0.8rem;
      color: var(--color-gris-oscuro);
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 28px;
      animation: fadeInUp 0.6s ease 0.4s both;
    }

    /* ---- ORDER SUMMARY CARD ---- */
    .order-summary-card {
      background: var(--color-bg-card);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-lg);
      padding: 28px;
      text-align: left;
      margin-bottom: 28px;
      animation: fadeInUp 0.6s ease 0.45s both;
    }

    .order-summary-card h3 {
      font-family: var(--font-heading);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--color-amarillo);
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--color-gris-oscuro);
    }

    .order-summary-card table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }

    .order-summary-card table th {
      font-family: var(--font-heading);
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--color-gris);
      padding: 8px 4px;
      border-bottom: 1px solid var(--color-gris-oscuro);
      text-align: left;
    }

    .order-summary-card table th:last-child,
    .order-summary-card table td:last-child {
      text-align: right;
    }

    .order-summary-card table td {
      padding: 10px 4px;
      font-size: 0.85rem;
      color: var(--color-gris-claro);
      border-bottom: 1px solid rgba(255,255,255,0.04);
    }

    .order-summary-card table .item-name {
      font-family: var(--font-heading);
      color: var(--color-blanco);
      font-size: 0.8rem;
    }

    .order-summary-card table .item-price {
      color: var(--color-amarillo);
      font-weight: 700;
    }

    .table-totals {
      border-top: 2px solid var(--color-gris-oscuro);
      padding-top: 12px;
      margin-top: 4px;
    }

    .table-total-row {
      display: flex;
      justify-content: space-between;
      padding: 4px 0;
      font-size: 0.85rem;
    }

    .table-total-row .label {
      color: var(--color-gris);
    }

    .table-total-row .value {
      font-weight: 700;
      color: var(--color-blanco);
    }

    .table-total-row.grand-total {
      font-family: var(--font-heading);
      font-size: 1.1rem;
      padding-top: 8px;
      margin-top: 4px;
      border-top: 1px solid var(--color-gris-oscuro);
    }

    .table-total-row.grand-total .value {
      color: var(--color-amarillo);
      font-family: var(--font-display);
      font-size: 1.3rem;
    }

    .table-total-row.bs-total .value {
      color: var(--color-gris-claro);
      font-size: 0.85rem;
    }

    /* ---- ORDER INFO ROW ---- */
    .order-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid var(--color-gris-oscuro);
    }

    .order-info-item {
      display: flex;
      flex-direction: column;
    }

    .order-info-item .info-label {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--color-gris-oscuro);
      margin-bottom: 2px;
    }

    .order-info-item .info-value {
      font-size: 0.85rem;
      color: var(--color-gris-claro);
    }

    .order-info-item .info-value strong {
      color: var(--color-blanco);
    }

    /* ---- WHATSAPP BUTTON ---- */
    .whatsapp-section {
      animation: fadeInUp 0.6s ease 0.55s both;
      margin-bottom: 20px;
    }

    .whatsapp-section p {
      color: var(--color-gris-claro);
      font-size: 0.9rem;
      margin-bottom: 16px;
    }

    .btn-whatsapp-confirm {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: #25D366;
      color: var(--color-blanco);
      padding: 18px 48px;
      border-radius: var(--radius-full);
      font-family: var(--font-heading);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      box-shadow: 0 4px 16px rgba(37, 211, 102, 0.3);
      transition: var(--transition);
      width: 100%;
      max-width: 440px;
    }

    .btn-whatsapp-confirm:hover {
      background: #1da851;
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(37, 211, 102, 0.4);
    }

    .btn-whatsapp-confirm svg {
      width: 24px;
      height: 24px;
      fill: currentColor;
      flex-shrink: 0;
    }

    /* ---- ETA ---- */
    .eta-display {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: rgba(255, 184, 28, 0.08);
      border: 1px solid rgba(255, 184, 28, 0.15);
      border-radius: var(--radius-full);
      padding: 12px 24px;
      margin-bottom: 24px;
      animation: fadeInUp 0.6s ease 0.5s both;
    }

    .eta-display .eta-icon {
      font-size: 1.3rem;
    }

    .eta-display .eta-text {
      font-size: 0.85rem;
      color: var(--color-gris-claro);
    }

    .eta-display .eta-text strong {
      color: var(--color-amarillo);
      font-family: var(--font-heading);
      font-size: 1rem;
    }

    /* ---- THANK YOU MESSAGE ---- */
    .thanks-message {
      font-family: var(--font-body);
      font-size: 0.95rem;
      color: var(--color-gris);
      max-width: 400px;
      margin: 0 auto 28px;
      font-style: italic;
      animation: fadeInUp 0.6s ease 0.6s both;
    }

    /* ---- ACTION BUTTONS ---- */
    .confirmacion-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
      flex-wrap: wrap;
      animation: fadeInUp 0.6s ease 0.65s both;
    }

    /* ---- CONFETTI PARTICLES (pure CSS) ---- */
    .confetti-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
    }

    .confetti-particle {
      position: absolute;
      width: 8px;
      height: 8px;
      border-radius: 2px;
      animation: confettiFall linear forwards;
      opacity: 0;
    }

    @keyframes confettiFall {
      0% {
        transform: translateY(-20px) rotate(0deg);
        opacity: 0;
      }
      10% {
        opacity: 0.9;
      }
      90% {
        opacity: 0.6;
      }
      100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
      }
    }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 600px) {
      .confirmacion-page main {
        padding: 20px 16px 40px;
      }

      .checkmark-container {
        width: 72px;
        height: 72px;
        margin-bottom: 20px;
      }

      .checkmark-circle {
        width: 72px;
        height: 72px;
      }

      .checkmark-circle svg {
        width: 36px;
        height: 36px;
      }

      .checkmark-ring {
        top: -6px;
        left: -6px;
        right: -6px;
        bottom: -6px;
      }

      .checkmark-ring:nth-child(2) {
        top: -12px;
        left: -12px;
        right: -12px;
        bottom: -12px;
      }

      .order-summary-card {
        padding: 20px;
      }

      .order-info-grid {
        grid-template-columns: 1fr;
      }

      .order-number-display {
        font-size: 1.2rem;
      }

      .btn-whatsapp-confirm {
        padding: 16px 32px;
        font-size: 0.85rem;
      }

      .confirmacion-actions .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>

  <!-- ===== HEADER (sin carrito badge) ===== -->
  <header class="header" id="main-header">
    <div class="container">
      <a href="index.php" class="header-logo" aria-label="Maná Fast Food - Inicio">
        <span style="font-family:'Anton',sans-serif;font-size:1.7rem;text-transform:uppercase;letter-spacing:2px;color:var(--color-rojo);text-shadow:0 2px 12px rgba(211,18,18,0.5);line-height:1;">
          MANÁ
          <span style="color:var(--color-amarillo);font-size:0.75rem;display:block;letter-spacing:4px;text-shadow:none;">FAST FOOD</span>
        </span>
      </a>

      <nav class="header-nav" id="main-nav">
        <a href="index.php" data-nav>Inicio</a>
        <a href="menu.php" data-nav>Menú</a>
        <a href="login.php" class="btn btn-sm btn-outline-rojo" style="padding:6px 16px;font-size:0.7rem;" data-nav>Entrar</a>
        <a href="registro.php" class="btn btn-sm btn-primary" style="padding:6px 16px;font-size:0.7rem;" data-nav>Registro</a>
      </nav>

      <button class="hamburger" id="hamburger-btn" aria-label="Abrir menú de navegación">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>

  <!-- ===== CONFIRMACIÓN PAGE ===== -->
  <div class="confirmacion-page">

    <!-- Confetti Container -->
    <div class="confetti-container" id="confetti-container"></div>

    <main>
      <div class="confirmacion-container">

        <!-- Checkmark animado -->
        <div class="checkmark-container">
          <div class="checkmark-circle">
            <svg viewBox="0 0 24 24">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <div class="checkmark-ring"></div>
          <div class="checkmark-ring"></div>
        </div>

        <!-- Título -->
        <h1 class="confirmacion-title">
          ¡PEDIDO
          <span class="highlight">CONFIRMADO!</span>
        </h1>
        <p class="confirmacion-subtitle">Hemos recibido tu pedido y ya está en proceso.</p>

        <!-- Número de pedido -->
        <div class="order-number-display">
          #<span id="order-number">MANA-00000</span>
        </div>
        <div class="order-number-label">Número de Pedido</div>

        <!-- ETA -->
        <div class="eta-display" id="eta-display">
          <span class="eta-icon">⏱️</span>
          <span class="eta-text">Tiempo estimado: <strong id="eta-time">25 — 35 minutos</strong></span>
        </div>

        <!-- Order Summary Card -->
        <div class="order-summary-card" id="order-summary-card">
          <h3>Resumen Final</h3>
          <table id="order-items-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th style="text-align:center;">Cant.</th>
                <th style="text-align:right;">Total</th>
              </tr>
            </thead>
            <tbody id="order-items-body">
              <!-- Rendered by JS -->
            </tbody>
          </table>
          <div class="table-totals">
            <div class="table-total-row">
              <span class="label">Subtotal</span>
              <span class="value" id="order-subtotal">$0.00</span>
            </div>
            <div class="table-total-row" id="order-discount-row" style="display:none;">
              <span class="label">Descuento</span>
              <span class="value" style="color:var(--color-success);" id="order-discount">-$0.00</span>
            </div>
            <div class="table-total-row grand-total">
              <span class="label">Total</span>
              <span class="value" id="order-total-usd">$0.00</span>
            </div>
            <div class="table-total-row bs-total" id="order-total-bs-row">
              <span class="label">Total en Bs.</span>
              <span class="value" id="order-total-bs">Bs. 0,00</span>
            </div>
          </div>

          <!-- Order Info -->
          <div class="order-info-grid" id="order-info-grid">
            <div class="order-info-item">
              <span class="info-label">Tipo de Pedido</span>
              <span class="info-value"><strong id="order-tipo">Delivery</strong></span>
            </div>
            <div class="order-info-item">
              <span class="info-label">Método de Pago</span>
              <span class="info-value"><strong id="order-pago">PagoMóvil</strong></span>
            </div>
            <div class="order-info-item" id="order-direccion-item">
              <span class="info-label">Dirección</span>
              <span class="info-value" id="order-direccion">—</span>
            </div>
            <div class="order-info-item">
              <span class="info-label">Fecha</span>
              <span class="info-value" id="order-fecha">—</span>
            </div>
          </div>
        </div>

        <!-- WhatsApp -->
        <div class="whatsapp-section">
          <p>Envía tu pedido a cocina por WhatsApp para activar la preparación al instante:</p>
          <a href="#" target="_blank" class="btn-whatsapp-confirm" id="btn-whatsapp-order" rel="noopener">
            <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            ENVIAR A WHATSAPP →
          </a>
        </div>

        <!-- Mensaje de agradecimiento -->
        <p class="thanks-message">"Gracias por elegir Maná. Tu comida va directo al corazón."</p>

        <!-- Acciones -->
        <div class="confirmacion-actions">
          <a href="menu.php" class="btn btn-primary btn-lg">PEDIR DE NUEVO</a>
          <a href="index.php" class="btn btn-outline btn-lg">VOLVER AL INICIO</a>
        </div>

      </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <div id="footer-placeholder"></div>

  </div>

  <!-- ===== SCRIPTS ===== -->
  <script src="js/app.js"></script>
  <script src="js/cart.js"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — CONFIRMACION.JS
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      /* ---- Generar confetti ---- */
      function generateConfetti() {
        var container = document.getElementById('confetti-container');
        if (!container) return;

        var colors = ['#D31212', '#FFB81C', '#FFFFFF', '#28a745', '#FF6B6B', '#FFD93D'];
        var shapes = ['circle', 'square'];

        for (var i = 0; i < 60; i++) {
          var particle = document.createElement('div');
          particle.className = 'confetti-particle';
          var color = colors[Math.floor(Math.random() * colors.length)];
          var size = 4 + Math.random() * 8;
          var left = Math.random() * 100;
          var duration = 2 + Math.random() * 3;
          var delay = Math.random() * 2;
          var shape = shapes[Math.floor(Math.random() * shapes.length)];

          particle.style.cssText =
            'left:' + left + '%;' +
            'width:' + size + 'px;' +
            'height:' + size + 'px;' +
            'background:' + color + ';' +
            'border-radius:' + (shape === 'circle' ? '50%' : '2px') + ';' +
            'animation-duration:' + duration + 's;' +
            'animation-delay:' + delay + 's;';

          container.appendChild(particle);
        }
      }

      /* ---- Obtener datos del pedido desde sessionStorage ---- */
      function getOrderData() {
        try {
          var stored = sessionStorage.getItem('mana_last_order');
          if (stored) {
            return JSON.parse(stored);
          }
        } catch (e) {
          console.warn('Error leyendo orden de sessionStorage:', e);
        }
        return null;
      }

      /* ---- Renderizar resumen del pedido ---- */
      function renderOrderSummary(orderData) {
        if (!orderData) {
          // No hay datos de orden - mostrar placeholder
          document.getElementById('order-number').textContent = 'MANA-' + String(Math.floor(Math.random() * 90000) + 10000);
          return;
        }

        // Número de pedido
        document.getElementById('order-number').textContent = orderData.id || ('MANA-' + String(Math.floor(Math.random() * 90000) + 10000));

        // Items de la tabla
        var tbody = document.getElementById('order-items-body');
        if (tbody && orderData.items) {
          var html = '';
          orderData.items.forEach(function(item) {
            var itemTotal = (item.precio * item.cantidad);
            html += '<tr>' +
              '<td><span class="item-name">' + item.nombre + '</span></td>' +
              '<td style="text-align:center;">' + item.cantidad + '</td>' +
              '<td style="text-align:right;"><span class="item-price">' + App.formatUSD(itemTotal) + '</span></td>' +
            '</tr>';
          });
          tbody.innerHTML = html;
        }

        // Totales
        var subtotal = orderData.subtotalUSD || 0;
        var descuento = orderData.descuentoUSD || 0;
        var totalUSD = orderData.totalUSD || 0;
        var totalBS = orderData.totalBS || 0;
        var tasaBCV = orderData.tasaBCV || 0;

        document.getElementById('order-subtotal').textContent = App.formatUSD(subtotal);

        var discountRow = document.getElementById('order-discount-row');
        if (descuento > 0) {
          discountRow.style.display = 'flex';
          document.getElementById('order-discount').textContent = '-' + App.formatUSD(descuento);
        } else {
          discountRow.style.display = 'none';
        }

        document.getElementById('order-total-usd').textContent = App.formatUSD(totalUSD);

        var bsRow = document.getElementById('order-total-bs-row');
        if (tasaBCV > 0) {
          bsRow.style.display = 'flex';
          document.getElementById('order-total-bs').textContent = App.formatBS(totalBS);
        } else {
          bsRow.style.display = 'none';
        }

        // Tipo de pedido
        var tipo = orderData.tipo || 'delivery';
        document.getElementById('order-tipo').textContent = tipo === 'delivery' ? '📍 Delivery' : '📍 Retiro en local';

        // Método de pago (determinado por datos disponibles)
        document.getElementById('order-pago').textContent = 'PagoMóvil / Transferencia';

        // Dirección
        var direccionItem = document.getElementById('order-direccion-item');
        var direccionEl = document.getElementById('order-direccion');
        if (tipo === 'delivery' && orderData.direccion) {
          var dir = typeof orderData.direccion === 'object' ? orderData.direccion.direccion : orderData.direccion;
          direccionEl.textContent = dir || '—';
          direccionItem.style.display = 'block';
        } else {
          direccionItem.style.display = 'none';
        }

        // Fecha
        var fechaEl = document.getElementById('order-fecha');
        if (orderData.fecha) {
          try {
            var d = new Date(orderData.fecha);
            fechaEl.textContent = d.toLocaleDateString('es-VE', {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            });
          } catch (e) {
            fechaEl.textContent = new Date().toLocaleDateString('es-VE');
          }
        } else {
          fechaEl.textContent = new Date().toLocaleDateString('es-VE');
        }

        // Generar enlace WhatsApp
        generateWhatsAppLink(orderData);
      }

      /* ---- Generar enlace WhatsApp ---- */
      function generateWhatsAppLink(orderData) {
        var numeroTienda = '+584121234567'; // Configurable
        var items = orderData.items || [];
        var totalUSD = orderData.totalUSD || 0;
        var totalBS = orderData.totalBS || 0;
        var tipo = orderData.tipo || 'delivery';
        var notas = orderData.notas || '';

        var mensaje = '🍔 *NUEVO PEDIDO — MANÁ FAST FOOD* 🍔\n\n';
        mensaje += '*Pedido #' + (orderData.id || '———') + '*\n\n';
        mensaje += '*Productos:*\n';

        items.forEach(function(item) {
          mensaje += '• ' + item.nombre + ' x' + item.cantidad + ' = $' + (item.precio * item.cantidad).toFixed(2) + '\n';
        });

        mensaje += '\n*Total: $' + totalUSD.toFixed(2) + '*';
        if (totalBS > 0) {
          mensaje += ' (Bs. ' + totalBS.toFixed(2) + ')';
        }

        mensaje += '\n*Tipo:* ' + (tipo === 'delivery' ? 'Delivery' : 'Retiro en local');

        if (tipo === 'delivery' && orderData.direccion) {
          var dir = typeof orderData.direccion === 'object' ? orderData.direccion.direccion : orderData.direccion;
          if (dir) mensaje += '\n*Dirección:* ' + dir;
          var tel = typeof orderData.direccion === 'object' ? orderData.direccion.telefono : '';
          if (tel) mensaje += '\n*Teléfono:* ' + tel;
        }

        if (notas) {
          mensaje += '\n*Notas:* ' + notas;
        }

        mensaje += '\n\n✅ *Gracias por tu pedido!*';

        var numero = numeroTienda.replace(/[^0-9]/g, '');
        var waUrl = 'https://wa.me/' + numero + '?text=' + encodeURIComponent(mensaje);

        var btn = document.getElementById('btn-whatsapp-order');
        if (btn) {
          btn.href = waUrl;
        }
      }

      /* ---- Vaciar carrito (el pedido ya fue realizado) ---- */
      function clearCartAfterOrder() {
        try {
          if (typeof Cart !== 'undefined' && Cart.clearCart) {
            Cart.clearCart();
          } else {
            localStorage.removeItem('mana_cart');
          }
        } catch (e) {
          console.warn('Error al limpiar carrito:', e);
        }

        // Ocultar badge del carrito
        var badge = document.getElementById('cart-badge');
        if (badge) badge.style.display = 'none';
      }

      /* ---- Inicializar ---- */
      var orderData = getOrderData();
      renderOrderSummary(orderData);
      generateConfetti();
      clearCartAfterOrder();

      // Si no hay datos en sessionStorage, mostrar mensaje informativo
      if (!orderData) {
        // Intentar obtener del carrito como fallback
        try {
          var cart = Cart.getCart();
          if (cart && cart.items && cart.items.length > 0) {
            var totals = Cart.calcularTotales();
            var fallbackData = {
              id: 'MANA-' + String(Math.floor(Math.random() * 90000) + 10000),
              items: cart.items,
              tipo: cart.tipo || 'delivery',
              notas: cart.notas || '',
              direccion: cart.direccion,
              totalUSD: totals.totalUSD,
              totalBS: totals.totalBS,
              tasaBCV: totals.tasaBCV,
              subtotalUSD: totals.subtotalUSD,
              descuentoUSD: totals.descuentoUSD,
              fecha: new Date().toISOString(),
            };
            renderOrderSummary(fallbackData);
          }
        } catch (e) {
          console.warn('No se pudieron cargar datos del pedido:', e);
        }
      }
    });
  </script>

</body>
</html>
