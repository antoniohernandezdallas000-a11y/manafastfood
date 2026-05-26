<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pago — Maná Fast Food</title>
  <meta name="description" content="Completa tu pedido en Maná Fast Food. Datos de entrega y pago.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — CHECKOUT.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    .checkout-page {
      padding-top: calc(var(--header-height) + 24px);
      min-height: 100vh;
    }

    /* ---- CUSTOM STEP INDICATOR ---- */
    .custom-step-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 32px;
      padding: 20px 0;
      border-bottom: 1px solid var(--color-gris-oscuro);
    }

    .custom-step {
      display: flex;
      align-items: center;
      gap: 10px;
      font-family: var(--font-heading);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--color-gris-oscuro);
      transition: var(--transition);
    }

    .custom-step .step-num {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid var(--color-gris-oscuro);
      font-size: 0.75rem;
      transition: var(--transition);
      flex-shrink: 0;
    }

    .custom-step.active {
      color: var(--color-amarillo);
    }

    .custom-step.active .step-num {
      background: var(--color-amarillo);
      border-color: var(--color-amarillo);
      color: var(--color-bg);
    }

    .custom-step.done {
      color: var(--color-success);
    }

    .custom-step.done .step-num {
      background: var(--color-success);
      border-color: var(--color-success);
      color: var(--color-blanco);
    }

    .custom-step-line {
      width: 32px;
      height: 2px;
      background: var(--color-gris-oscuro);
      transition: var(--transition);
      flex-shrink: 0;
    }

    /* Steps responsive */
    @media (max-width: 600px) {
      .custom-step-indicator {
        gap: 6px;
        padding: 16px 0;
      }
      .custom-step {
        font-size: 0.6rem;
        gap: 6px;
      }
      .custom-step .step-num {
        width: 26px;
        height: 26px;
        font-size: 0.65rem;
      }
      .custom-step-line {
        width: 16px;
      }
    }

    @media (max-width: 400px) {
      .custom-step-indicator {
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
      }
      .custom-step .step-label {
        display: none;
      }
      .custom-step-line {
        width: 12px;
      }
    }

    .custom-step.done + .custom-step-line {
      background: var(--color-success);
    }

    .custom-step.active + .custom-step-line {
      background: var(--color-gris);
    }

    /* ---- STEP CONTENT ---- */
    .step-content {
      animation: fadeInUp 0.4s ease;
    }

    .step-content h2 {
      font-family: var(--font-display);
      font-size: clamp(1.5rem, 3vw, 2.2rem);
      text-transform: uppercase;
      color: var(--color-amarillo);
      margin-bottom: 24px;
      letter-spacing: 2px;
    }

    .step-content h2 .step-badge {
      font-family: var(--font-body);
      font-size: 0.8rem;
      color: var(--color-gris);
      text-transform: none;
      letter-spacing: 0;
      font-weight: 400;
      display: block;
      margin-top: 2px;
    }

    /* ---- RETIRO INFO CARD ---- */
    .retiro-card {
      background: var(--color-bg-alt);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-md);
      padding: 24px;
      text-align: center;
        }

    .retiro-card .local-icon {
      font-size: 3rem;
      margin-bottom: 12px;
    }

    .retiro-card h3 {
      font-family: var(--font-heading);
      font-size: 1rem;
      text-transform: uppercase;
      color: var(--color-blanco);
      margin-bottom: 4px;
    }

    .retiro-card p {
      color: var(--color-gris);
      font-size: 0.9rem;
    }

    .retiro-card .local-address {
      color: var(--color-amarillo);
      font-weight: 700;
      font-size: 1rem;
      margin-top: 8px;
    }

    .retiro-map-container {
      width: 100%;
      height: 200px;
      border-radius: var(--radius-sm);
      overflow: hidden;
      margin-top: 16px;
      border: 1px solid var(--color-gris-oscuro);
    }

    .retiro-map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
      filter: invert(0.9) hue-rotate(180deg);
    }

    /* ---- PAGOMÓVIL INFO BOX ---- */
    .pagomovil-info-box {
      background: linear-gradient(135deg, rgba(255,184,28,0.08) 0%, rgba(211,18,18,0.05) 100%);
      border: 2px solid var(--color-amarillo);
      border-radius: var(--radius-md);
      padding: 24px;
      margin-bottom: 24px;
      position: relative;
      overflow: hidden;
    }

    .pagomovil-info-box::before {
      content: '📱';
      position: absolute;
      top: -10px;
      right: -10px;
      font-size: 4rem;
      opacity: 0.1;
      transform: rotate(15deg);
    }

    .pagomovil-info-box .info-title {
      font-family: var(--font-heading);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--color-amarillo);
      margin-bottom: 16px;
    }

    .pagomovil-info-box .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .pagomovil-info-box .info-item {
      display: flex;
      flex-direction: column;
    }

    .pagomovil-info-box .info-label {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--color-gris);
      margin-bottom: 2px;
    }

    .pagomovil-info-box .info-value {
      font-family: var(--font-heading);
      font-size: 0.95rem;
      color: var(--color-blanco);
    }

    .pagomovil-info-box .info-value.amarillo {
      color: var(--color-amarillo);
    }

    /* ---- MONTO A PAGAR ---- */
    .monto-box {
      background: var(--color-bg-alt);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-md);
      padding: 20px 24px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }

    .monto-box .monto-label {
      font-family: var(--font-heading);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--color-gris-claro);
    }

    .monto-box .monto-usd {
      font-family: var(--font-display);
      font-size: 2rem;
      color: var(--color-amarillo);
      line-height: 1;
    }

    .monto-box .monto-bs {
      font-size: 0.85rem;
      color: var(--color-gris);
      text-align: right;
    }

    /* ---- STEP 3: SUCCESS ---- */
    .success-container {
      text-align: center;
      padding: 40px 20px;
    }

    .success-check {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: rgba(40,167,69,0.15);
      border: 3px solid var(--color-success);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      animation: successPop 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    }

    .success-check svg {
      width: 40px;
      height: 40px;
      stroke: var(--color-success);
      stroke-width: 3;
      fill: none;
      stroke-dasharray: 50;
      stroke-dashoffset: 50;
      animation: successDraw 0.5s ease 0.3s forwards;
    }

    @keyframes successPop {
      0% { transform: scale(0); opacity: 0; }
      60% { transform: scale(1.15); }
      100% { transform: scale(1); opacity: 1; }
    }

    @keyframes successDraw {
      to { stroke-dashoffset: 0; }
    }

    .success-container h2 {
      font-family: var(--font-display);
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      text-transform: uppercase;
      color: var(--color-amarillo);
      margin-bottom: 8px;
    }

    .success-container .order-number {
      font-family: var(--font-heading);
      font-size: 1.4rem;
      color: var(--color-blanco);
      margin-bottom: 20px;
      letter-spacing: 2px;
    }

    .success-container .order-number span {
      color: var(--color-amarillo);
    }

    .success-container .success-info {
      background: var(--color-bg-alt);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-md);
      padding: 20px;
      text-align: left;
      margin: 0 auto 24px;
      max-width: 420px;
    }

    .success-container .success-info p {
      font-size: 0.85rem;
      color: var(--color-gris);
      margin-bottom: 6px;
    }

    .success-container .success-info p strong {
      color: var(--color-blanco);
    }

    .success-container .whatsapp-msg {
      color: var(--color-gris-claro);
      font-size: 0.9rem;
      margin-bottom: 20px;
    }

    .btn-whatsapp-lg {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: #25D366;
      color: var(--color-blanco);
      padding: 18px 40px;
      border-radius: var(--radius-full);
      font-family: var(--font-heading);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      box-shadow: 0 4px 16px rgba(37,211,102,0.3);
      transition: var(--transition);
      width: 100%;
      max-width: 400px;
    }

    .btn-whatsapp-lg:hover {
      background: #1da851;
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(37,211,102,0.4);
    }

    .btn-whatsapp-lg svg {
      width: 24px;
      height: 24px;
      fill: currentColor;
    }

    /* ---- STEP TRANSITION BUTTON ---- */
    .btn-step-next {
      width: 100%;
      padding: 18px 32px;
      font-size: 1rem;
      letter-spacing: 2px;
      margin-top: 16px;
    }

    /* ---- INSTRUCTION TEXT ---- */
    .instruction-text {
      padding: 14px 18px;
      background: rgba(255,184,28,0.08);
      border-left: 4px solid var(--color-amarillo);
      border-radius: var(--radius-sm);
      font-size: 0.85rem;
      color: var(--color-gris-claro);
      margin-bottom: 20px;
      line-height: 1.6;
    }

    .instruction-text strong {
      color: var(--color-amarillo);
    }

    /* ---- SIDEBAR CHECKOUT SPECIFICS ---- */
    #checkout-summary h3 {
      font-family: var(--font-heading);
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--color-amarillo);
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--color-gris-oscuro);
    }

    /* ---- STEP HIDDEN HELPER ---- */
    .step-hidden {
      display: none !important;
    }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 968px) {
      .checkout-layout {
        grid-template-columns: 1fr;
      }

      .custom-step-indicator {
        gap: 8px;
        padding: 16px 0;
        flex-wrap: nowrap;
        overflow-x: auto;
        justify-content: flex-start;
      }

      .custom-step {
        font-size: 0.65rem;
        white-space: nowrap;
      }

      .custom-step .step-num {
        width: 26px;
        height: 26px;
        font-size: 0.65rem;
      }

      .custom-step-line {
        width: 16px;
      }

      .pagomovil-info-box .info-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 600px) {
      .checkout-page {
        padding-top: calc(var(--header-height) + 12px);
      }

      .checkout-form-section,
      .checkout-summary-section {
        padding: 20px;
      }

      .monto-box {
        flex-direction: column;
        text-align: center;
      }

      .monto-box .monto-bs {
        text-align: center;
      }

      .monto-box .monto-usd {
        font-size: 1.6rem;
      }

      .success-container {
        padding: 20px 0;
      }

      .success-check {
        width: 60px;
        height: 60px;
      }

      .success-check svg {
        width: 30px;
        height: 30px;
      }

      .retiro-card .info-grid {
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
        <a href="menu.php" data-nav>Menú</a>
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

  <!-- ===== CHECKOUT PAGE ===== -->
  <main class="checkout-page">

    <div class="container" id="checkout-app">

      <!-- Custom Step Indicator: Carrito → Pago → Confirmación -->
      <div class="custom-step-indicator" id="custom-step-indicator">
<div class="custom-step done" data-cstep="1">
<span class="step-num">✅</span>
<span class="step-label">Carrito</span>
</div>
<div class="custom-step-line"></div>
<div class="custom-step active" data-cstep="2">
<span class="step-num">2</span>
<span class="step-label">Pago</span>
</div>
<div class="custom-step-line"></div>
<div class="custom-step" data-cstep="3">
<span class="step-num">3</span>
<span class="step-label">Confirmación</span>
</div>
        <div class="custom-step-line"></div>
        <div class="custom-step active" data-cstep="2">
          <span class="step-num">2</span>
          <span>Pago</span>
        </div>
        <div class="custom-step-line"></div>
        <div class="custom-step" data-cstep="3">
          <span class="step-num">3</span>
          <span>Confirmación</span>
        </div>
      </div>

      <!-- Hidden step indicator for checkout.js (we use our own) -->
      <div id="step-indicator" style="display:none;"></div>

      <!-- Main layout: form + sidebar -->
      <div class="checkout-layout">

        <!-- LEFT: Form Section -->
        <div class="checkout-form-section">

          <!-- ===== STEP 1: DATOS DEL PEDIDO ===== -->
          <div id="step-1" class="step-content">
            <h2>
              Paso 1: Datos del Pedido
              <span class="step-badge">¿A dónde va tu maná?</span>
            </h2>

            <!-- Delivery / Retiro Toggle (checkout.js handles this) -->
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

            <!-- Delivery Form -->
            <div id="delivery-fields">
              <div class="form-group">
                <label class="form-label" for="checkout-phone">Teléfono de contacto <span style="color:var(--color-rojo);">*</span></label>
                <input type="tel" id="checkout-phone" class="form-input" placeholder="+58 412 123 4567" required autocomplete="tel" inputmode="tel">
              </div>

              <div class="form-group">
                <label class="form-label" for="direccion-input">Dirección de entrega <span style="color:var(--color-rojo);">*</span></label>
                <div style="position:relative;">
                  <textarea id="direccion-input" class="form-textarea" placeholder="Escribe tu dirección completa o selecciona en el mapa" rows="2" required></textarea>
                </div>
              </div>

              <!-- Google Maps Container (OpenStreetMap fallback + Google Maps JS si hay API key) -->
              <div class="map-container" id="checkout-map">
                <!-- Fallback iframe OpenStreetMap (gratuito, sin API key) -->
                <!-- OSM iframe apunta a Caracas. Se reemplaza por Google Maps si hay API key configurada -->
                <iframe
                  id="checkout-map-osm"
                  src="https://www.openstreetmap.org/export/embed.html?bbox=-66.95,10.46,-66.85,10.50&amp;layer=mapnik&amp;marker=10.4806,-66.9036"
                  width="100%"
                  height="100%"
                  style="border:0;display:block;"
                  loading="lazy"
                  allowfullscreen
                  title="Mapa de entrega - selecciona tu ubicación">
                </iframe>
              </div>

              <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
                <button type="button" class="btn btn-outline btn-sm" id="geo-btn" style="gap:8px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"/></svg>
                  USAR MI UBICACIÓN
                </button>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="checkout-reference-dir">Referencia de dirección</label>
                  <input type="text" id="checkout-reference-dir" class="form-input" placeholder="Calle, avenida, casa #, apto">
                </div>
                <div class="form-group">
                  <label class="form-label" for="checkout-city">Ciudad / Estado</label>
                  <input type="text" id="checkout-city" class="form-input" placeholder="Ej: Caracas, Distrito Capital" value="Caracas">
                </div>
              </div>
            </div>

            <!-- Retiro Info -->
            <div id="retiro-fields" style="display:none;">
              <div class="retiro-card">
                <div class="local-icon">📍</div>
                <h3>Retiras en nuestro local</h3>
                <p class="local-address">Av. Principal de Las Mercedes, CC Mercede Plaza, Local 2, Caracas</p>
                <p style="color:var(--color-gris);font-size:0.85rem;margin-top:8px;">
                  Horario: Lun - Sáb 11:00 AM - 11:00 PM · Dom 12:00 PM - 10:00 PM
                </p>
                <div class="retiro-map-container">
                  <iframe 
                    src="https://www.google.com/maps/embed?q=Av+Principal+de+Las+Mercedes,+Caracas&output=embed&hl=es"
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Ubicación del local Maná Fast Food">
                  </iframe>
                </div>
              </div>
            </div>

            <!-- Notas del pedido (checkout.js uses #notas-input) -->
            <div class="form-group" style="margin-top:20px;">
              <label class="form-label" for="notas-input">Notas del pedido</label>
              <textarea id="notas-input" class="form-textarea" placeholder="Ej: Sin cebolla, bien cocida, extra salsa, punto de la carne…" maxlength="500" rows="3"></textarea>
            </div>

            <button type="button" class="btn btn-primary btn-lg btn-step-next" id="btn-go-step2">
              CONTINUAR AL PAGO →
            </button>
          </div>

          <!-- ===== STEP 2: MÉTODO DE PAGO ===== -->
          <div id="step-2" class="step-content step-hidden">
            <h2>
              Paso 2: Método de Pago
              <span class="step-badge">Elige cómo pagar</span>
            </h2>

            <!-- PagoMóvil Info Box -->
            <div class="pagomovil-info-box">
              <div class="info-title">📱 PagoMóvil — Cuenta Activa</div>
              <div id="pagomovil-cuentas">
                <!-- Loaded by checkout.js -->
                <p style="color:var(--color-gris);font-size:0.85rem;">Cargando cuentas disponibles…</p>
              </div>
            </div>

            <!-- Monto a Pagar -->
            <div class="monto-box">
              <div>
                <div class="monto-label">Total a Pagar</div>
                <div class="monto-usd" id="checkout-total-usd">$0.00</div>
              </div>
              <div class="monto-bs" id="checkout-total-bs">
                Bs. 0,00
                <br><span style="font-size:0.7rem;color:var(--color-gris-oscuro);" id="checkout-tasa-label">Tasa BCV</span>
              </div>
            </div>

            <!-- Sel. Método de Pago (checkout.js renders into #payment-methods) -->
            <div id="payment-methods">
              <!-- Rendered by checkout.js -->
            </div>

            <!-- PagoMóvil Form -->
            <div id="pagomovil-form" style="display:block;">
              <div class="instruction-text">
                <strong>Instrucciones:</strong><br>
                1. Abre tu app bancaria y selecciona <strong>PagoMóvil</strong> → <strong>Pagar C2P</strong>.<br>
                2. Ingresa el número de teléfono y cédula de la cuenta mostrada arriba.<br>
                3. Ingresa el monto exacto en bolívares.<br>
                4. Tu banco te dará un <strong>número de referencia</strong> o clave de confirmación.<br>
                5. Ingresa esa referencia aquí y adjunta el comprobante si está disponible.
              </div>
            </div>

            <!-- Transfer Form (hidden by default) -->
            <div id="transferencia-form" style="display:none;">
              <div class="instruction-text">
                Realiza la transferencia a la cuenta indicada y luego ingresa la referencia.
              </div>
              <div id="transfer-cuentas" style="margin-bottom:16px;">
                <!-- Static transfer accounts if needed -->
              </div>
            </div>

            <!-- Referencia y Capture -->
            <div id="referencia-section" style="display:block;">
              <div style="margin-top:20px;">
                <div class="form-group">
                  <label class="form-label" for="referencia-input">Número de Referencia <span style="color:var(--color-rojo);">*</span></label>
                  <input type="text" id="referencia-input" class="form-input" placeholder="Ej: 1234567890" required autocomplete="off">
                </div>

                <div class="form-group">
                  <label class="form-label">Subir Comprobante (opcional)</label>
                  <div class="file-upload" id="file-upload-container">
                    <input type="file" id="capture-input" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="file-upload-icon">
                      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    </div>
                    <div class="file-upload-text">
                      Arrastra tu comprobante aquí o <strong>selecciona un archivo</strong><br>
                      <span style="font-size:0.75rem;color:var(--color-gris-oscuro);">JPG o PNG · Máximo 5MB</span>
                    </div>
                    <img class="file-upload-preview" alt="Vista previa del comprobante">
                  </div>
                </div>
              </div>
            </div>

            <!-- Botón Confirmar Pago -->
            <button type="button" class="btn btn-primary btn-lg" id="btn-confirmar-pago" style="width:100%;margin-top:20px;font-size:1.05rem;padding:20px 32px;">
              CONFIRMAR PAGO
            </button>

            <div style="text-align:center;margin-top:12px;">
              <button type="button" class="btn btn-outline btn-sm" id="btn-back-step1" style="font-size:0.75rem;">
                ← Volver a datos del pedido
              </button>
            </div>
          </div>

          <!-- ===== STEP 3: CONFIRMACIÓN (rendered by checkout.js) ===== -->
          <div id="step-3" class="step-content step-hidden">
            <div id="checkout-step-content">
              <!-- checkout.js renders success content here -->
            </div>
          </div>

        </div><!-- /checkout-form-section -->

        <!-- RIGHT: Summary Sidebar -->
        <div class="checkout-summary-section">
          <div id="checkout-summary">
            <!-- Rendered by checkout.js -->
            <p style="color:var(--color-gris);font-size:0.85rem;">Cargando resumen…</p>
          </div>
          <div id="invoice-container">
            <!-- Rendered by checkout.js if needed -->
          </div>

          <!-- Security badges -->
          <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--color-gris-oscuro);">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;font-size:0.75rem;color:var(--color-gris);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <span>Conexión segura (SSL)</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:0.75rem;color:var(--color-gris);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-amarillo)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <span>Pago protegido · Datos cifrados</span>
            </div>
          </div>
        </div>

      </div><!-- /checkout-layout -->
    </div>

  </main>

  <!-- ===== FOOTER ===== -->
  <div id="footer-placeholder"></div>

  <!-- ===== SCRIPTS ===== -->
  <script src="js/app.js"></script>
  <script src="js/cart.js"></script>
  <script src="js/auth.js"></script>
  <script src="js/checkout.js"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — CHECKOUT.JS (PAGE INTEGRATION)
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      var currentStep = 1; // 1 = Datos, 2 = Pago, 3 = Confirmación

      /* ---- Override checkout.js redirect to use carrito.html ---- */
      if (typeof Checkout !== 'undefined') {
        var originalInit = Checkout.initCheckout;
        Checkout.initCheckout = function() {
          var cart = Cart.getCart();
          if (!cart || !cart.items || cart.items.length === 0) {
            window.location.href = 'carrito.php';
            return;
          }
          // Call original but handle redirect
          originalInit.call(Checkout);
        };
      }

      /* ---- Initialize checkout (order summary, payment methods, etc.) ---- */
      if (typeof Checkout !== 'undefined' && document.getElementById('checkout-app')) {
        // initCheckout will run automatically from checkout.js DOMContentLoaded
        // But we need to wait for it to finish before our custom logic
        setTimeout(function() {
          afterCheckoutInit();
        }, 100);
      }

      function afterCheckoutInit() {
        // Update totals in step 2
        updateStep2Totals();

        // Update step indicator
        updateStepIndicator(1);

        // Show step 1, hide step 2 and 3
        showStep(1);

        // Attach step navigation
        attachStepNavigation();

        // Ensure delivery toggle works with our fields
        initDeliveryToggleCustom();

        // If already has checkout step indicator rendered, hide it (we use custom one)
        var csIndicator = document.getElementById('step-indicator');
        if (csIndicator) csIndicator.style.display = 'none';
      }

      /* ---- Update Totals in Step 2 ---- */
      function updateStep2Totals() {
        var totals = Cart.calcularTotales();
        var totalUsdEl = document.getElementById('checkout-total-usd');
        var totalBsEl = document.getElementById('checkout-total-bs');
        var tasaLabel = document.getElementById('checkout-tasa-label');

        if (totalUsdEl) {
          totalUsdEl.textContent = App.formatUSD(totals.totalUSD);
        }

        if (totalBsEl) {
          if (totals.tasaBCV > 0) {
            totalBsEl.innerHTML = App.formatBS(totals.totalBS) +
              '<br><span style="font-size:0.7rem;color:var(--color-gris-oscuro);">Tasa BCV: ' + App.formatBS(totals.tasaBCV) + '</span>';
          } else {
            totalBsEl.textContent = '—';
            if (tasaLabel) tasaLabel.textContent = '';
          }
        }
      }

      /* ---- Update Custom Step Indicator ---- */
      function updateStepIndicator(step) {
        var steps = document.querySelectorAll('.custom-step');
        var lines = document.querySelectorAll('.custom-step-line');

        steps.forEach(function(el, index) {
          var num = index + 1;
          el.classList.remove('active', 'done');
          if (num < step) {
            el.classList.add('done');
            el.querySelector('.step-num').textContent = '✓';
          } else if (num === step) {
            el.classList.add('active');
            el.querySelector('.step-num').textContent = num;
          } else {
            el.querySelector('.step-num').textContent = num;
          }
        });

        lines.forEach(function(el, index) {
          var num = index + 1;
          el.classList.remove('done');
          if (num < step) {
            el.classList.add('done');
          }
        });
      }

      /* ---- Show/Hide Steps ---- */
      function showStep(step) {
        var step1 = document.getElementById('step-1');
        var step2 = document.getElementById('step-2');
        var step3 = document.getElementById('step-3');

        step1.classList.toggle('step-hidden', step !== 1);
        step2.classList.toggle('step-hidden', step !== 2);
        step3.classList.toggle('step-hidden', step !== 3);

        currentStep = step;
        updateStepIndicator(step);

        // Scroll to top of form
        var formSection = document.querySelector('.checkout-form-section');
        if (formSection) {
          formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // If moving to step 2, refresh totals
        if (step === 2) {
          updateStep2Totals();
          // Trigger checkout.js to re-render payment methods if needed
          if (typeof Checkout !== 'undefined') {
            // Re-initialize payment methods display
            var payMethods = document.getElementById('payment-methods');
            if (payMethods && payMethods.children.length === 0 && Checkout.renderPaymentMethods) {
              // It should already be rendered by initCheckout
            }
          }
        }
      }

      /* ---- Step Navigation ---- */
      function attachStepNavigation() {
        // Step 1 → Step 2
        var btnGo2 = document.getElementById('btn-go-step2');
        if (btnGo2) {
          btnGo2.addEventListener('click', function() {
            if (validateStep1()) {
              saveStep1Data();
              showStep(2);
              App.showToast('Completa los datos de pago', 'info');
            }
          });
        }

        // Step 2 → Step 1 (back)
        var btnBack1 = document.getElementById('btn-back-step1');
        if (btnBack1) {
          btnBack1.addEventListener('click', function() {
            showStep(1);
          });
        }
      }

      /* ---- Validate Step 1 ---- */
      function validateStep1() {
        var tipo = getSelectedTipo();
        var errors = [];

        if (tipo === 'delivery') {
          var phone = document.getElementById('checkout-phone').value.trim();
          var address = document.getElementById('direccion-input').value.trim();

          if (!phone) {
            errors.push('Ingresa un teléfono de contacto');
            document.getElementById('checkout-phone').classList.add('error');
          } else if (!/^\+?[\d\s\-()]{7,15}$/.test(phone)) {
            errors.push('Ingresa un teléfono válido (ej: +584121234567)');
            document.getElementById('checkout-phone').classList.add('error');
          } else {
            document.getElementById('checkout-phone').classList.remove('error');
          }

          if (!address || address.length < 5) {
            errors.push('Ingresa una dirección de entrega válida');
            document.getElementById('direccion-input').classList.add('error');
          } else {
            document.getElementById('direccion-input').classList.remove('error');
          }

          if (errors.length > 0) {
            App.showToast(errors[0], 'error');
            return false;
          }
        }

        return true;
      }

      /* ---- Save Step 1 Data ---- */
      function saveStep1Data() {
        var tipo = getSelectedTipo();
        Cart.setTipo(tipo);

        if (tipo === 'delivery') {
          var phone = document.getElementById('checkout-phone').value.trim();
          var address = document.getElementById('direccion-input').value.trim();
          var refDir = document.getElementById('checkout-reference-dir').value.trim();
          var city = document.getElementById('checkout-city').value.trim();

          var direccionData = {
            telefono: phone,
            direccion: address,
            referencia: refDir,
            ciudad: city || 'Caracas',
          };

          Cart.setDireccion(direccionData);
        }

        // Notas ya se guardan automáticamente vía checkout.js
      }

      /* ---- Get Selected Tipo ---- */
      function getSelectedTipo() {
        var active = document.querySelector('.delivery-toggle-btn.active');
        return active ? active.dataset.tipo : 'delivery';
      }

      /* ---- Custom Delivery Toggle Logic ---- */
      function initDeliveryToggleCustom() {
        var toggleBtns = document.querySelectorAll('.delivery-toggle-btn');

        toggleBtns.forEach(function(btn) {
          btn.addEventListener('click', function() {
            var tipo = this.dataset.tipo;
            var deliveryFields = document.getElementById('delivery-fields');
            var retiroFields = document.getElementById('retiro-fields');

            if (tipo === 'delivery') {
              if (deliveryFields) deliveryFields.style.display = 'block';
              if (retiroFields) retiroFields.style.display = 'none';
            } else {
              if (deliveryFields) deliveryFields.style.display = 'none';
              if (retiroFields) retiroFields.style.display = 'block';
            }
          });
        });

        // Initialize based on cart data
        var cart = Cart.getCart();
        if (cart.tipo === 'retiro') {
          var retiroBtn = document.querySelector('.delivery-toggle-btn[data-tipo="retiro"]');
          if (retiroBtn) retiroBtn.click();
        }
      }

      /* ---- Override renderStep3 to also update our indicator ---- */
      if (typeof Checkout !== 'undefined' && Checkout.renderStep3) {
        var originalRenderStep3 = Checkout.renderStep3;
        Checkout.renderStep3 = function() {
          originalRenderStep3.call(Checkout);

          // Update our step indicator to show all done
          showStep(3);

          // Remove cart badge from header (ya no aplica)
          var cartBadge = document.getElementById('cart-badge');
          if (cartBadge) cartBadge.style.display = 'none';

          // Store order data in sessionStorage for confirmacion.html
          saveOrderToSession();

          // Replace the whatsapp link from checkout.js with our styled version
          enhanceWhatsAppButton();
        };
      }

      /* ---- Save Order to Session Storage ---- */
      function saveOrderToSession() {
        try {
          var cart = Cart.getCart();
          var totals = Cart.calcularTotales();

          var orderData = {
            id: 'MANA-' + String(Math.floor(Math.random() * 90000) + 10000),
            items: cart.items,
            tipo: cart.tipo,
            notas: cart.notas || '',
            direccion: cart.direccion,
            totalUSD: totals.totalUSD,
            totalBS: totals.totalBS,
            tasaBCV: totals.tasaBCV,
            subtotalUSD: totals.subtotalUSD,
            descuentoUSD: totals.descuentoUSD,
            fecha: new Date().toISOString(),
          };

          sessionStorage.setItem('mana_last_order', JSON.stringify(orderData));
        } catch (e) {
          console.warn('Error saving order to session:', e);
        }
      }

      /* ---- Enhance WhatsApp Button in Step 3 ---- */
      function enhanceWhatsAppButton() {
        var step3Container = document.getElementById('checkout-step-content');
        if (!step3Container) return;

        // Find the whatsapp link and replace with our styled version
        var waLink = step3Container.querySelector('a[href*="wa.me"]');
        if (waLink) {
          waLink.className = 'btn btn-whatsapp-lg';
          waLink.innerHTML =
            '<svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>' +
            'ENVIAR A WHATSAPP →';

          // Also add the text that the user wants
          var parentDiv = waLink.closest('div');
          if (parentDiv) {
            var infoP = document.createElement('p');
            infoP.className = 'whatsapp-msg';
            infoP.textContent = 'Tu pedido será procesado al instante. El botón te lleva a WhatsApp con toda la información lista para enviar a cocina.';
            parentDiv.insertBefore(infoP, waLink);
          }
        }
      }

      /* ---- Check if redirected from carrito ---- */
      // Clear any error states from previous visits
      document.querySelectorAll('.form-input.error').forEach(function(el) {
        el.classList.remove('error');
      });

      /* ---- Clean cart badge if empty ---- */
      if (typeof Cart !== 'undefined') {
        var cart = Cart.getCart();
        if (!cart.items || cart.items.length === 0) {
          // Will be redirected by initCheckout
        }
      }
    });
  </script>

</body>
</html>



