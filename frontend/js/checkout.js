/* ============================================================
   MANÁ FAST FOOD - CHECKOUT.JS
   Flujo completo de checkout: resumen, mapa, pagos,
   factura, enlace WhatsApp.
   ============================================================ */

const Checkout = (() => {
  'use strict';

  /* ---- ESTADO INTERNO ---- */
  let state = {
    step: 1,
    tipo: 'delivery',       // 'delivery' | 'retiro'
    metodoPago: 'pagomovil', // 'pagomovil' | 'transferencia' | 'paypal' | 'zelle'
    referencia: '',
    captureFile: null,
    cuentaPagoMovil: null,
    direccion: null,
    procesando: false,
  };

  /* ---- INICIALIZAR CHECKOUT ---- */
  const initCheckout = () => {
    const cart = Cart.getCart();
    if (!cart || !cart.items || cart.items.length === 0) {
      window.location.href = 'carrito.php';
      return;
    }

    renderOrderSummary();
    renderPaymentMethods();
    initStepIndicator();
    initEventListeners();
    initDeliveryToggle();

    // Cargar cuentas PagoMóvil activas
    loadCuentasPagoMovil();
  };

  /* ---- RENDERIZAR RESUMEN DEL PEDIDO ---- */
  const renderOrderSummary = () => {
    const container = document.getElementById('checkout-summary');
    if (!container) return;

    const cart = Cart.getCart();
    const tot = Cart.calcularTotales();

    let html = `
      <h3 style="font-family:var(--font-heading);font-size:1rem;text-transform:uppercase;color:var(--color-amarillo);margin-bottom:16px;">
        Resumen del Pedido
      </h3>
      <table class="order-summary-table">
        <thead>
          <tr>
            <th>Producto</th>
            <th style="text-align:center;">Cant.</th>
            <th style="text-align:right;">Total</th>
          </tr>
        </thead>
        <tbody>
    `;

    cart.items.forEach(item => {
      const itemTotal = item.precio * item.cantidad;
      html += `
        <tr>
          <td>
            <span class="item-name">${item.nombre}</span>
          </td>
          <td style="text-align:center;">${item.cantidad}</td>
          <td style="text-align:right;" class="item-total">${App.formatUSD(itemTotal)}</td>
        </tr>
      `;
    });

    html += `
        </tbody>
      </table>
      <div class="order-totals">
        <div class="order-total-row">
          <span class="label">Subtotal</span>
          <span class="value">${App.formatUSD(tot.subtotalUSD)}</span>
        </div>
    `;

    if (tot.descuentoPorcentaje > 0) {
      html += `
        <div class="order-total-row">
          <span class="label">Descuento (${tot.descuentoPorcentaje}%)</span>
          <span class="value" style="color:var(--color-success);">-${App.formatUSD(tot.descuentoUSD)}</span>
        </div>
      `;
    }

    if (tot.tasaBCV > 0) {
      html += `
        <div class="order-total-row">
          <span class="label">Total Bs. (tasa Bs. ${tot.tasaBCV.toFixed(2)})</span>
          <span class="value">${App.formatBS(tot.totalBS)}</span>
        </div>
      `;
    }

    html += `
        <div class="order-total-row total">
          <span class="label">Total a Pagar</span>
          <span class="value">${App.formatUSD(tot.totalUSD)}</span>
        </div>
      </div>
    `;

    // Mostrar ofertas/especiales
    if (cart.items.some(i => i.descuento > 0)) {
      html += `
        <div style="margin-top:12px;padding:12px;background:rgba(255,184,28,0.1);border-radius:var(--radius-sm);border:1px solid rgba(255,184,28,0.2);font-size:0.85rem;color:var(--color-amarillo);">
          🎉 ¡Oferta especial aplicada a tu pedido!
        </div>
      `;
    }

    container.innerHTML = html;
  };

  /* ---- INDICADOR DE PASOS ---- */
  const initStepIndicator = () => {
    const container = document.getElementById('step-indicator');
    if (!container) return;

    const steps = [
      { num: 1, label: 'Datos' },
      { num: 2, label: 'Pago' },
      { num: 3, label: 'Confirmar' },
    ];

    let html = '<div class="checkout-step-indicator">';
    steps.forEach((step, i) => {
      const status = step.num === state.step ? 'active'
        : step.num < state.step ? 'done' : '';

      html += `
        <div class="checkout-step ${status}">
          <span class="checkout-step-number">${step.num < state.step ? '✓' : step.num}</span>
          <span>${step.label}</span>
        </div>
      `;
      if (i < steps.length - 1) {
        html += `<div class="checkout-step-line ${step.num < state.step ? 'done' : ''}"></div>`;
      }
    });
    html += '</div>';

    container.innerHTML = html;
  };

  /* ---- DELIVERY / RETIRO TOGGLE ---- */
  const initDeliveryToggle = () => {
    const toggleBtns = document.querySelectorAll('.delivery-toggle-btn');
    if (!toggleBtns.length) return;

    toggleBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        toggleBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const tipo = btn.dataset.tipo || 'delivery';
        state.tipo = tipo;
        Cart.setTipo(tipo);

        const mapSection = document.getElementById('delivery-map-section');
        const direccionSection = document.getElementById('direccion-section');
        const retiroInfo = document.getElementById('retiro-info');

        if (tipo === 'delivery') {
          if (mapSection) mapSection.style.display = 'block';
          if (direccionSection) direccionSection.style.display = 'block';
          if (retiroInfo) retiroInfo.style.display = 'none';
        } else {
          if (mapSection) mapSection.style.display = 'none';
          if (direccionSection) direccionSection.style.display = 'none';
          if (retiroInfo) retiroInfo.style.display = 'block';
        }
      });
    });
  };

  /* ---- GOOGLE MAPS (integración) ---- */
  const initGoogleMaps = () => {
    App.initGoogleMaps('checkout-map', App.CONFIG.DEFAULT_LAT, App.CONFIG.DEFAULT_LNG);
  };

  /* ---- CARGAR CUENTAS PAGOMÓVIL ---- */
  const loadCuentasPagoMovil = async () => {
    const container = document.getElementById('pagomovil-cuentas');
    if (!container) return;

    try {
      const response = await App.apiFetch('/checkout/cuentas-pagomovil');
      const cuentas = response.cuentas || response || [];

      if (cuentas.length === 0) {
        // Cuentas por defecto si no hay backend
        renderDefaultCuentas(container);
        return;
      }

      let html = '<div style="margin-bottom:16px;"><label class="form-label">Selecciona la cuenta a depositar</label></div>';
      cuentas.forEach((cuenta, index) => {
        const isActive = cuenta.activa || index === 0;
        html += `
          <label class="payment-method-card ${isActive ? 'active' : ''}" style="cursor:pointer;display:block;margin-bottom:8px;text-align:left;" data-cuenta='${JSON.stringify(cuenta)}'>
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div class="name">${cuenta.banco}</div>
                <div style="font-size:0.9rem;color:var(--color-blanco);font-weight:700;margin:4px 0;">${cuenta.cedula}</div>
                <div style="font-size:0.8rem;color:var(--color-gris);">${cuenta.telefono}</div>
              </div>
              ${isActive ? '<span class="badge badge-verde">Activa</span>' : ''}
            </div>
          </label>
        `;
      });

      container.innerHTML = html;

      // Event listeners
      container.querySelectorAll('.payment-method-card').forEach(el => {
        el.addEventListener('click', () => {
          container.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
          el.classList.add('active');
          state.cuentaPagoMovil = JSON.parse(el.dataset.cuenta);
        });
      });

      // Seleccionar primera
      const first = container.querySelector('.payment-method-card');
      if (first) {
        state.cuentaPagoMovil = JSON.parse(first.dataset.cuenta);
      }

    } catch (e) {
      console.warn('Error cargando cuentas PagoMóvil, usando por defecto:', e);
      renderDefaultCuentas(container);
    }
  };

  const renderDefaultCuentas = (container) => {
    const cuentas = [
      { banco: 'Mercantil Banco', cedula: 'V-12.345.678', telefono: '+58 412-1234567', titular: 'Maná Fast Food C.A.', activa: true },
      { banco: 'Banco de Venezuela', cedula: 'V-12.345.678', telefono: '+58 414-7654321', titular: 'Maná Fast Food C.A.', activa: false },
    ];

    let html = '<div style="margin-bottom:16px;"><label class="form-label">Selecciona la cuenta a depositar</label></div>';
    cuentas.forEach((cuenta, index) => {
      const isActive = cuenta.activa || index === 0;
      html += `
        <label class="payment-method-card ${isActive ? 'active' : ''}" style="cursor:pointer;display:block;margin-bottom:8px;text-align:left;" data-cuenta='${JSON.stringify(cuenta)}'>
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div class="name">${cuenta.banco}</div>
              <div style="font-size:0.9rem;color:var(--color-blanco);font-weight:700;margin:4px 0;">${cuenta.cedula}</div>
              <div style="font-size:0.8rem;color:var(--color-gris);">${cuenta.telefono}</div>
              <div style="font-size:0.75rem;color:var(--color-gris-oscuro);">Titular: ${cuenta.titular}</div>
            </div>
            ${isActive ? '<span class="badge badge-verde">Activa</span>' : ''}
          </div>
        </label>
      `;
    });

    container.innerHTML = html;

    container.querySelectorAll('.payment-method-card').forEach(el => {
      el.addEventListener('click', () => {
        container.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        state.cuentaPagoMovil = JSON.parse(el.dataset.cuenta);
      });
    });

    const first = container.querySelector('.payment-method-card');
    if (first) {
      state.cuentaPagoMovil = JSON.parse(first.dataset.cuenta);
    }
  };

  /* ---- RENDERIZAR MÉTODOS DE PAGO ---- */
  const renderPaymentMethods = () => {
    const container = document.getElementById('payment-methods');
    if (!container) return;

    // Detectar ubicación del usuario para métodos relevantes
    const methods = [
      { id: 'pagomovil', nombre: 'PagoMóvil', desc: 'Venezuela - C2P', icon: '📱' },
      { id: 'transferencia', nombre: 'Transferencia', desc: 'Bancos nacionales', icon: '🏦' },
      { id: 'zelle', nombre: 'Zelle', desc: 'Internacional - USD', icon: '💵' },
      { id: 'paypal', nombre: 'PayPal', desc: 'Internacional', icon: '🌐' },
      { id: 'efectivo', nombre: 'Efectivo', desc: 'Solo retiro en local', icon: '💵' },
    ];

    let html = '<label class="form-label">Selecciona método de pago</label>';
    html += '<div class="payment-methods">';

    methods.forEach(method => {
      const isActive = method.id === state.metodoPago;
      html += `
        <div class="payment-method-card ${isActive ? 'active' : ''}" data-method="${method.id}">
          <div class="icon">${method.icon}</div>
          <div class="name">${method.nombre}</div>
          <div class="desc">${method.desc}</div>
        </div>
      `;
    });

    html += '</div>';
    container.innerHTML = html;

    container.querySelectorAll('.payment-method-card').forEach(card => {
      card.addEventListener('click', () => {
        container.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        state.metodoPago = card.dataset.method;
        togglePaymentForms();
      });
    });
  };

  /* ---- MOSTRAR/OCULTAR FORMULARIOS DE PAGO ---- */
  const togglePaymentForms = () => {
    const pagomovilForm = document.getElementById('pagomovil-form');
    const transferenciaForm = document.getElementById('transferencia-form');
    const referenciaSection = document.getElementById('referencia-section');

    if (pagomovilForm) pagomovilForm.style.display = 'none';
    if (transferenciaForm) transferenciaForm.style.display = 'none';
    if (referenciaSection) referenciaSection.style.display = 'none';

    if (state.metodoPago === 'pagomovil') {
      if (pagomovilForm) pagomovilForm.style.display = 'block';
    } else if (state.metodoPago === 'transferencia') {
      if (transferenciaForm) transferenciaForm.style.display = 'block';
    }

    // Los métodos que requieren referencia
    if (['pagomovil', 'transferencia', 'zelle'].includes(state.metodoPago)) {
      if (referenciaSection) referenciaSection.style.display = 'block';
    }
  };

  /* ---- GENERAR FACTURA ---- */
  const generarFactura = () => {
    const container = document.getElementById('invoice-container');
    if (!container) return;

    const cart = Cart.getCart();
    const tot = Cart.calcularTotales();
    const now = new Date();
    const facturaNum = 'FAC-' + now.getFullYear() +
      String(now.getMonth() + 1).padStart(2, '0') +
      String(now.getDate()).padStart(2, '0') + '-' +
      String(Math.floor(Math.random() * 9999)).padStart(4, '0');

    let html = `
      <div class="invoice" id="invoice">
        <div class="invoice-header">
          <h2>Maná Fast Food</h2>
          <p>RIF: J-XXXXXXXX-X</p>
          <p>${now.toLocaleDateString('es-VE', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
          <p style="font-weight:700;margin-top:4px;">Factura: ${facturaNum}</p>
        </div>
        <div class="invoice-body">
          <table>
            <thead>
              <tr>
                <th>Producto</th>
                <th style="text-align:center;">Cant.</th>
                <th style="text-align:right;">P.Unit</th>
                <th style="text-align:right;">Total</th>
              </tr>
            </thead>
            <tbody>
    `;

    cart.items.forEach(item => {
      html += `
        <tr>
          <td>${item.nombre}</td>
          <td style="text-align:center;">${item.cantidad}</td>
          <td style="text-align:right;">${App.formatUSD(item.precio)}</td>
          <td style="text-align:right;">${App.formatUSD(item.precio * item.cantidad)}</td>
        </tr>
      `;
    });

    if (tot.descuentoPorcentaje > 0) {
      html += `
        <tr>
          <td colspan="3" style="text-align:right;color:var(--color-warning);">Descuento (${tot.descuentoPorcentaje}%)</td>
          <td style="text-align:right;color:var(--color-warning);">-${App.formatUSD(tot.descuentoUSD)}</td>
        </tr>
      `;
    }

    html += `
        <tr class="total-row">
          <td colspan="3" style="text-align:right;font-weight:800;">TOTAL</td>
          <td style="text-align:right;font-weight:800;font-size:1.1rem;">${App.formatUSD(tot.totalUSD)}</td>
        </tr>
    `;

    if (tot.tasaBCV > 0) {
      html += `
        <tr>
          <td colspan="3" style="text-align:right;color:var(--color-gris-oscuro);font-size:0.8rem;">Total en Bs. (tasa ${tot.tasaBCV.toFixed(2)})</td>
          <td style="text-align:right;color:var(--color-gris-oscuro);font-size:0.8rem;">${App.formatBS(tot.totalBS)}</td>
        </tr>
      `;
    }

    html += `
          </tbody>
        </table>
        </div>
        <div class="invoice-footer">
          <p>Método de pago: ${getMetodoPagoNombre(state.metodoPago)}</p>
          <p>${state.tipo === 'delivery' ? '📍 Delivery' : '📍 Retiro en local'}</p>
          <p>¡Gracias por tu preferencia!</p>
        </div>
      </div>
    `;

    container.innerHTML = html;
    return facturaNum;
  };

  const getMetodoPagoNombre = (id) => {
    const map = {
      'pagomovil': 'PagoMóvil C2P',
      'transferencia': 'Transferencia Bancaria',
      'zelle': 'Zelle',
      'paypal': 'PayPal',
      'efectivo': 'Efectivo',
    };
    return map[id] || id;
  };

  /* ---- SUBIR REFERENCIA + CAPTURA ---- */
  const submitPago = async (referencia, captureFile) => {
    if (state.procesando) return;
    state.procesando = true;

    const btn = document.getElementById('btn-confirmar-pago');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner"></span> Procesando…';
    }

    try {
      const cart = Cart.getCart();
      const tot = Cart.calcularTotales();

      const formData = new FormData();
      formData.append('referencia', referencia);
      formData.append('metodo_pago', state.metodoPago);
      formData.append('tipo', state.tipo);
      formData.append('moneda', 'USD');
      formData.append('monto_usd', tot.totalUSD.toString());
      formData.append('monto_bs', tot.totalBS.toString());
      formData.append('tasa_bcv', tot.tasaBCV.toString());
      formData.append('items', JSON.stringify(cart.items));
      formData.append('notas', cart.notas || '');
      formData.append('direccion', state.tipo === 'delivery' ? JSON.stringify(cart.direccion || state.direccion) : '');

      if (state.cuentaPagoMovil) {
        formData.append('cuenta_banco', state.cuentaPagoMovil.banco);
        formData.append('cuenta_cedula', state.cuentaPagoMovil.cedula);
        formData.append('cuenta_telefono', state.cuentaPagoMovil.telefono);
      }

      if (captureFile) {
        formData.append('capture', captureFile);
      }

      const apiBase = window.App?.CONFIG?.API_BASE || '/mana-fast-food/backend/api';
      const response = await fetch(apiBase + '/pedidos/crear.php', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          // No Content-Type - FormData lo setea automáticamente
          ...(Auth.getToken() ? { 'Authorization': `Bearer ${Auth.getToken()}` } : {}),
        },
        body: formData,
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Error al procesar el pago');
      }

      // Éxito
      App.showToast('✅ Pedido registrado con éxito', 'success');

      // Guardar datos del pedido para WhatsApp
      state.pedidoData = {
        id: data.pedido?.id || data.id,
        referencia: referencia,
        facturaNum: data.factura_num || generarFactura(),
      };

      // Avanzar al paso 3
      state.step = 3;
      renderStep3();

      return data;

    } catch (error) {
      App.showToast(error.message || 'Error al procesar el pago', 'error');
      throw error;
    } finally {
      state.procesando = false;
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = 'Confirmar Pago';
      }
    }
  };

  /* ---- PASO 3: CONFIRMACIÓN Y WHATSAPP ---- */
  const renderStep3 = () => {
    const container = document.getElementById('checkout-step-content');
    if (!container) return;

    const pedidoId = state.pedidoData?.id || '—';
    const facturaNum = state.pedidoData?.facturaNum || generarFactura();
    const whatsappLink = generarEnlaceWhatsApp();

    container.innerHTML = `
      <div style="text-align:center;padding:40px 20px;">
        <div style="font-size:3rem;margin-bottom:16px;">✅</div>
        <h2 style="font-family:var(--font-display);font-size:2rem;color:var(--color-amarillo);margin-bottom:8px;">
          ¡Pedido Recibido!
        </h2>
        <p style="color:var(--color-gris);margin-bottom:24px;font-size:1.1rem;">
          Hemos recibido tu pedido <strong style="color:var(--color-blanco);">#${pedidoId}</strong>.
          Pronto nos pondremos en contacto para confirmar.
        </p>

        <div style="background:var(--color-bg-alt);border-radius:var(--radius-md);padding:24px;margin-bottom:24px;text-align:left;">
          <p style="margin-bottom:8px;"><strong>Factura:</strong> ${facturaNum}</p>
          <p style="margin-bottom:8px;"><strong>Referencia:</strong> ${state.pedidoData?.referencia || '—'}</p>
          <p style="margin-bottom:8px;"><strong>Método de pago:</strong> ${getMetodoPagoNombre(state.metodoPago)}</p>
          <p><strong>Estado:</strong> <span class="badge badge-amarillo">Pendiente de confirmación</span></p>
        </div>

        <p style="color:var(--color-gris-claro);margin-bottom:24px;">
          Envíanos el comprobante por WhatsApp para agilizar la confirmación:
        </p>

        <a href="${whatsappLink}" target="_blank" class="btn btn-whatsapp btn-lg" style="width:100%;max-width:400px;">
          📱 Enviar por WhatsApp
        </a>

        <div style="margin-top:32px;">
          <a href="index.php" class="btn btn-outline">Volver al Inicio</a>
          <a href="menu.php" class="btn btn-outline-amarillo" style="margin-left:12px;">Seguir Comprando</a>
        </div>
      </div>
    `;

    initStepIndicator();
  };

  /* ---- GENERAR ENLACE WHATSAPP ---- */
  const generarEnlaceWhatsApp = () => {
    const cart = Cart.getCart();
    const tot = Cart.calcularTotales();
    const numeroTienda = '+584121234567'; // Configurable

    let mensaje = '🍔 *NUEVO PEDIDO - MANÁ FAST FOOD* 🍔\n\n';
    mensaje += '*Productos:*\n';

    cart.items.forEach(item => {
      mensaje += `• ${item.nombre} x${item.cantidad} = $${(item.precio * item.cantidad).toFixed(2)}\n`;
    });

    mensaje += `\n*Total: $${tot.totalUSD.toFixed(2)}*`;

    if (tot.tasaBCV > 0) {
      mensaje += ` (Bs. ${tot.totalBS.toFixed(2)})`;
    }

    mensaje += `\n*Método de pago:* ${getMetodoPagoNombre(state.metodoPago)}`;
    mensaje += `\n*Referencia:* ${state.pedidoData?.referencia || '—'}`;
    mensaje += `\n*Tipo:* ${state.tipo === 'delivery' ? 'Delivery' : 'Retiro en local'}`;

    if (state.tipo === 'delivery' && state.direccion) {
      mensaje += `\n*Dirección:* ${state.direccion}`;
    } else if (state.tipo === 'delivery' && Cart.getCart().direccion) {
      mensaje += `\n*Dirección:* ${Cart.getCart().direccion}`;
    }

    if (state.cuentaPagoMovil) {
      mensaje += `\n*Banco:* ${state.cuentaPagoMovil.banco}`;
    }

    const notas = Cart.getCart().notas;
    if (notas) {
      mensaje += `\n*Notas:* ${notas}`;
    }

    mensaje += '\n\n✅ *Gracias por tu pedido!*';

    const mensajeCodificado = encodeURIComponent(mensaje);
    const numero = numeroTienda.replace(/[^0-9]/g, '');

    return `https://wa.me/${numero}?text=${mensajeCodificado}`;
  };

  /* ---- CONFIRMAR PEDIDO ---- */
  const confirmarPedido = async () => {
    const referencia = document.getElementById('referencia-input')?.value?.trim();
    const captureInput = document.getElementById('capture-input');
    const captureFile = captureInput?.files?.[0] || null;

    // Validar referencia según método
    if (['pagomovil', 'transferencia', 'zelle'].includes(state.metodoPago)) {
      if (!referencia) {
        App.showToast('Ingresa la referencia o número de pago', 'error');
        document.getElementById('referencia-input')?.focus();
        return;
      }

      if (referencia.length < 4) {
        App.showToast('La referencia debe tener al menos 4 caracteres', 'error');
        return;
      }
    }

    try {
      await submitPago(referencia, captureFile);
    } catch (e) {
      // Error ya manejado en submitPago
    }
  };

  /* ---- EVENT LISTENERS ---- */
  const initEventListeners = () => {
    // Botón de confirmar pago
    const btnConfirmar = document.getElementById('btn-confirmar-pago');
    if (btnConfirmar) {
      btnConfirmar.addEventListener('click', confirmarPedido);
    }

    // File input preview
    const captureInput = document.getElementById('capture-input');
    const fileUpload = document.getElementById('file-upload-container');
    if (captureInput && fileUpload) {
      captureInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
          // Validar tipo
          const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
          if (!validTypes.includes(file.type)) {
            App.showToast('Formato no soportado. Usa JPG o PNG', 'error');
            captureInput.value = '';
            return;
          }
          // Validar tamaño (max 5MB)
          if (file.size > 5 * 1024 * 1024) {
            App.showToast('La imagen no debe superar 5MB', 'error');
            captureInput.value = '';
            return;
          }

          state.captureFile = file;
          fileUpload.classList.add('has-file');
          const reader = new FileReader();
          reader.onload = (e) => {
            const preview = fileUpload.querySelector('.file-upload-preview');
            if (preview) {
              preview.src = e.target.result;
              preview.style.display = 'block';
            }
          };
          reader.readAsDataURL(file);
        } else {
          state.captureFile = null;
          fileUpload.classList.remove('has-file');
          const preview = fileUpload.querySelector('.file-upload-preview');
          if (preview) {
            preview.src = '';
            preview.style.display = 'none';
          }
        }
      });

      // Drag & drop
      fileUpload.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUpload.classList.add('dragover');
      });

      fileUpload.addEventListener('dragleave', () => {
        fileUpload.classList.remove('dragover');
      });

      fileUpload.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUpload.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
          captureInput.files = e.dataTransfer.files;
          captureInput.dispatchEvent(new Event('change'));
        }
      });
    }

    // Notas del pedido
    const notasInput = document.getElementById('notas-input');
    if (notasInput) {
      notasInput.addEventListener('change', () => {
        Cart.setNotas(notasInput.value);
      });
    }

    // Botón de geolocalización
    const geoBtn = document.getElementById('geo-btn');
    if (geoBtn) {
      geoBtn.addEventListener('click', () => {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (position) => {
              const coords = `${position.coords.latitude},${position.coords.longitude}`;
              const dirInput = document.getElementById('direccion-input');
              if (dirInput) {
                dirInput.value = `Ubicación actual: ${coords}`;
              }
              App.showToast('Ubicación obtenida', 'success');
            },
            () => App.showToast('No se pudo obtener ubicación', 'error')
          );
        } else {
          App.showToast('Geolocalización no disponible', 'error');
        }
      });
    }
  };

  /* ---- VERIFICAR AUTENTICACIÓN ANTES DE CHECKOUT ---- */
  const requireAuthForCheckout = () => {
    if (!Auth.isLoggedIn()) {
      // Mostrar opción de registro rápido en lugar de redirigir
      const authSection = document.getElementById('checkout-auth');
      const guestSection = document.getElementById('checkout-guest');
      if (authSection && guestSection) {
        authSection.style.display = 'none';
        guestSection.style.display = 'block';
        Auth.renderQuickRegisterForm('quick-register-container', () => {
          // Al registrarse exitosamente, continuar checkout
          authSection.style.display = 'block';
          guestSection.style.display = 'none';
          App.showToast('Cuenta creada, puedes continuar', 'success');
        });
      }
      return false;
    }
    return true;
  };

  /* ---- API PÚBLICA ---- */
  return {
    initCheckout,
    initGoogleMaps,
    generarFactura,
    generarEnlaceWhatsApp,
    confirmarPedido,
    submitPago,
    requireAuthForCheckout,
  };
})();

/* ---- INICIALIZAR EN DOM READY ---- */
document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('checkout-app')) {
    try {
      Checkout.initCheckout();
    } catch (e) {
      console.warn('⚠ Error en checkout:', e);
    }
  }
});
