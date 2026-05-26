/* ============================================================
   MANÁ FAST FOOD - CART.JS
   Carrito de compras completo con localStorage.
   Sincroniza entre páginas mediante evento 'cartUpdated'.
   ============================================================ */

const Cart = (() => {
  'use strict';

  const STORAGE_KEY = 'mana_cart';
  const TASA_KEY = 'mana_tasa_bcv';

  /* ---- ESTRUCTURA POR DEFECTO ---- */
  const getDefaultCart = () => ({
    items: [],
    notas: '',
    tipo: 'delivery', // 'delivery' | 'retiro'
    direccion: null,
  });

  /* ---- CARGAR CARRITO ---- */
  const getCart = () => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        // Validar estructura
        if (parsed && Array.isArray(parsed.items)) {
          return parsed;
        }
      }
    } catch (e) {
      console.warn('Error al leer carrito:', e);
    }
    return { ...getDefaultCart() };
  };

  /* ---- GUARDAR CARRITO ---- */
  const saveCart = (cart) => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
      dispatchCartEvent();
    } catch (e) {
      console.error('Error al guardar carrito:', e);
      App.showToast('Error al guardar el carrito', 'error');
    }
  };

  /* ---- DISPARAR EVENTO ---- */
  const dispatchCartEvent = () => {
    const event = new CustomEvent('cartUpdated', {
      detail: { cart: getCart() },
      bubbles: true,
    });
    document.dispatchEvent(event);
  };

  /* ---- AGREGAR PRODUCTO ---- */
  const addToCart = (producto) => {
    if (!producto || !producto.id || !producto.nombre) {
      App.showToast('Producto inválido', 'error');
      return false;
    }

    const cart = getCart();
    const cantidad = producto.cantidad || 1;
    const precio = parseFloat(producto.precio) || 0;

    if (precio <= 0) {
      App.showToast('Precio inválido', 'error');
      return false;
    }

    // Buscar si ya existe
    const existingIndex = cart.items.findIndex(item => item.id === producto.id);

    if (existingIndex >= 0) {
      // Incrementar cantidad
      cart.items[existingIndex].cantidad += cantidad;
    } else {
      // Agregar nuevo
      cart.items.push({
        id: producto.id,
        nombre: producto.nombre,
        precio: precio,
        cantidad: cantidad,
        imagen: producto.imagen || '',
        descripcion: producto.descripcion || '',
        categoria: producto.categoria || '',
      });
    }

    saveCart(cart);

    // Feedback visual
    const nombre = producto.nombre.length > 25
      ? producto.nombre.substring(0, 25) + '…'
      : producto.nombre;
    App.showToast(`✓ ${nombre} agregado al carrito`, 'success');

    return true;
  };

  /* ---- REMOVER PRODUCTO ---- */
  const removeFromCart = (id) => {
    const cart = getCart();
    const initialLength = cart.items.length;
    cart.items = cart.items.filter(item => item.id !== id);

    if (cart.items.length < initialLength) {
      saveCart(cart);
      App.showToast('Producto eliminado del carrito', 'info');
      return true;
    }
    return false;
  };

  /* ---- ACTUALIZAR CANTIDAD ---- */
  const updateQuantity = (id, cantidad) => {
    const cart = getCart();
    const item = cart.items.find(item => item.id === id);

    if (!item) return false;

    cantidad = parseInt(cantidad) || 1;

    if (cantidad <= 0) {
      return removeFromCart(id);
    }

    // Límite máximo por producto
    if (cantidad > 99) {
      App.showToast('Máximo 99 unidades por producto', 'warning');
      cantidad = 99;
    }

    item.cantidad = cantidad;
    saveCart(cart);
    return true;
  };

  /* ---- LIMPIAR CARRITO ---- */
  const clearCart = () => {
    saveCart({ ...getDefaultCart() });
    App.showToast('Carrito vaciado', 'info');
    dispatchCartEvent();
  };

  /* ---- CALCULAR TOTALES ---- */
  const calcularTotales = (tasaBCV = null) => {
    const cart = getCart();

    const subtotalUSD = cart.items.reduce((sum, item) => {
      return sum + (item.precio * item.cantidad);
    }, 0);

    // Intentar obtener tasa del localStorage
    if (!tasaBCV) {
      try {
        tasaBCV = parseFloat(localStorage.getItem(TASA_KEY)) || 0;
      } catch {
        tasaBCV = 0;
      }
    }

    const subtotalBS = tasaBCV > 0 ? subtotalUSD * tasaBCV : 0;

    // Descuento de oferta (si aplica)
    const descuentoPorcentaje = cart.items.reduce((maxDesc, item) => {
      return Math.max(maxDesc, item.descuento || 0);
    }, 0);

    const descuentoUSD = subtotalUSD * (descuentoPorcentaje / 100);
    const descuentoBS = subtotalBS * (descuentoPorcentaje / 100);

    const totalUSD = subtotalUSD - descuentoUSD;
    const totalBS = subtotalBS - descuentoBS;

    return {
      subtotalUSD: Math.round(subtotalUSD * 100) / 100,
      subtotalBS: Math.round(subtotalBS * 100) / 100,
      descuentoPorcentaje,
      descuentoUSD: Math.round(descuentoUSD * 100) / 100,
      descuentoBS: Math.round(descuentoBS * 100) / 100,
      totalUSD: Math.round(totalUSD * 100) / 100,
      totalBS: Math.round(totalBS * 100) / 100,
      tasaBCV: tasaBCV,
      cantidadItems: cart.items.reduce((sum, item) => sum + item.cantidad, 0),
    };
  };

  /* ---- RENDERIZAR CARRITO ---- */
  const renderCart = (containerId = 'cart-items-container') => {
    const container = document.getElementById(containerId);
    if (!container) return;

    const cart = getCart();
    const tot = calcularTotales();

    if (cart.items.length === 0) {
      container.innerHTML = `
        <div class="cart-empty">
          <div class="icon">🍔</div>
          <h2>Tu carrito está vacío</h2>
          <p>Agrega productos del menú para empezar tu pedido.</p>
          <a href="menu.php" class="btn btn-primary">Ver Menú</a>
        </div>
      `;
      return;
    }

    let html = '<div class="cart-items">';

    cart.items.forEach(item => {
      const itemTotal = (item.precio * item.cantidad);
      html += `
        <div class="cart-item" data-id="${item.id}">
          <div class="cart-item-image">
            ${item.imagen
              ? `<img src="${item.imagen}" alt="${item.nombre}" loading="lazy">`
              : `<div style="width:100%;height:100%;background:var(--color-bg-alt);display:flex;align-items:center;justify-content:center;color:var(--color-gris-oscuro);font-size:2rem;">🍔</div>`
            }
          </div>
          <div class="cart-item-info">
            <h3>${item.nombre}</h3>
            <div class="price">${App.formatUSD(item.precio)}</div>
          </div>
          <div class="cart-item-actions">
            <div class="qty-controls">
              <button class="qty-minus" data-id="${item.id}" aria-label="Disminuir">−</button>
              <span class="qty-value">${item.cantidad}</span>
              <button class="qty-plus" data-id="${item.id}" aria-label="Aumentar">+</button>
            </div>
            <div class="item-total">${App.formatUSD(itemTotal)}</div>
            <button class="btn-remove" data-id="${item.id}" aria-label="Eliminar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
              </svg>
            </button>
          </div>
        </div>
      `;
    });

    html += '</div>'; // .cart-items

    // Totales
    html += `
      <div class="order-totals" style="max-width:400px;margin-left:auto;">
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
          <span class="label">Total Bs.</span>
          <span class="value">${App.formatBS(tot.totalBS)}</span>
        </div>
      `;
    }

    html += `
        <div class="order-total-row total">
          <span class="label">Total USD</span>
          <span class="value">${App.formatUSD(tot.totalUSD)}</span>
        </div>
      </div>
    `;

    // Botones
    html += `
      <div style="display:flex;gap:16px;justify-content:flex-end;margin-top:24px;flex-wrap:wrap;">
        <button class="btn btn-outline btn-sm" id="clear-cart-btn">Vaciar Carrito</button>
        <a href="menu.php" class="btn btn-outline-amarillo btn-sm">Seguir Comprando</a>
        <a href="checkout.php" class="btn btn-primary">Proceder al Pago</a>
      </div>
    `;

    container.innerHTML = html;

    // Event listeners
    attachCartListeners(container);
  };

  /* ---- EVENT LISTENERS DEL RENDER ---- */
  const attachCartListeners = (container) => {
    // Botones de cantidad
    container.querySelectorAll('.qty-minus').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const item = getCart().items.find(i => i.id === id);
        if (item) updateQuantity(id, item.cantidad - 1);
      });
    });

    container.querySelectorAll('.qty-plus').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const item = getCart().items.find(i => i.id === id);
        if (item) updateQuantity(id, item.cantidad + 1);
      });
    });

    container.querySelectorAll('.btn-remove').forEach(btn => {
      btn.addEventListener('click', () => {
        removeFromCart(btn.dataset.id);
      });
    });

    const clearBtn = container.querySelector('#clear-cart-btn');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        if (confirm('¿Estás seguro de vaciar el carrito?')) {
          clearCart();
        }
      });
    }
  };

  /* ---- ESTABLECER TASA BCV ---- */
  const setTasaBCV = (tasa) => {
    if (tasa && !isNaN(tasa) && tasa > 0) {
      localStorage.setItem(TASA_KEY, tasa.toString());
      dispatchCartEvent();
      return true;
    }
    return false;
  };

  /* ---- OBTENER TASA BCV DESDE API ---- */
  const fetchTasaBCV = async () => {
    try {
      const apiBase = window.App?.CONFIG?.API_BASE || '/mana-fast-food/backend/api';
      const resp = await fetch(apiBase + '/checkout/tasa-bcv.php');
      const json = await resp.json();
      if (json.success && json.data) {
        const tasa = parseFloat(json.data.tasa_usd_bs);
        if (tasa > 0) {
          setTasaBCV(tasa);
          console.log(`✅ Tasa BCV actualizada: Bs. ${tasa} por USD`);
          return tasa;
        }
      }
      // Fallback: tasa por defecto
      const fallback = 60.00;
      setTasaBCV(fallback);
      console.warn(`⚠ No se pudo obtener tasa BCV, usando fallback: Bs. ${fallback}`);
      return fallback;
    } catch (e) {
      console.warn('⚠ Error al obtener tasa BCV:', e);
      const fallback = 60.00;
      setTasaBCV(fallback);
      return fallback;
    }
  };

  // Inicializar tasa BCV al cargar la página
  (function initTasa() {
    const stored = localStorage.getItem(TASA_KEY);
    if (!stored || stored === '0') {
      fetchTasaBCV();
    }
    // Si pasaron más de 30 minutos, refrescar
    const lastFetch = localStorage.getItem('mana_tasa_last_fetch');
    if (lastFetch && (Date.now() - parseInt(lastFetch)) > 1800000) {
      fetchTasaBCV();
    }
    // Guardar timestamp del fetch
    const origSet = setTasaBCV;
    const wrappedSetTasaBCV = (tasa) => {
      localStorage.setItem('mana_tasa_last_fetch', Date.now().toString());
      return origSet(tasa);
    };
    window.setTasaBCV = wrappedSetTasaBCV;
  })();

  /* ---- ACTUALIZAR NOTAS ---- */
  const setNotas = (notas) => {
    const cart = getCart();
    cart.notas = notas || '';
    saveCart(cart);
  };

  /* ---- ACTUALIZAR TIPO (delivery/retiro) ---- */
  const setTipo = (tipo) => {
    const cart = getCart();
    cart.tipo = tipo === 'retiro' ? 'retiro' : 'delivery';
    saveCart(cart);
  };

  /* ---- ACTUALIZAR DIRECCIÓN ---- */
  const setDireccion = (direccion) => {
    const cart = getCart();
    cart.direccion = direccion || null;
    saveCart(cart);
  };

  /* ---- API PÚBLICA ---- */
  return {
    getCart,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    calcularTotales,
    renderCart,
    setTasaBCV,
    fetchTasaBCV,
    setNotas,
    setTipo,
    setDireccion,
    dispatchCartEvent,
  };
})();

// Hacer accesible globalmente
window.Cart = Cart;
