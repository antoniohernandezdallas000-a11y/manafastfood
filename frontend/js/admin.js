/* ============================================================
   MANÁ FAST FOOD - ADMIN.JS
   Panel de administración completo.
   Dashboard, productos, pedidos, pagos, tasa BCV, ofertas.
   ============================================================ */

const Admin = (() => {
  'use strict';

  /* ---- ESTADO ---- */
  let state = {
    currentSection: 'dashboard',
    productos: [],
    pedidos: [],
    cuentasPago: [],
    ofertas: [],
    tasaBCV: null,
    tasaManual: false,
    loading: false,
  };

  /* ---- INICIALIZAR ---- */
  const init = async () => {
    // Modo desarrollo: las páginas PHP ya tienen el contenido renderizado
    // Solo inicializar interactividad (modales, navegación, etc.)
    initNavigation();
    initModals();
    initActionButtons();
    
    // Intentar cargar datos desde la API, si falla no pasa nada
    // porque el PHP ya renderizó el contenido estático
    try {
      const container = document.getElementById('admin-content');
      if (container && container.children.length === 0) {
        await loadSection('dashboard');
      }
    } catch (e) {
      console.warn('⚠ API no disponible, usando datos estáticos del PHP');
    }
  };

  /* ---- NAVEGACIÓN ---- */
  const initNavigation = () => {
    const links = document.querySelectorAll('[data-admin-section]');
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const section = link.dataset.adminSection;
        loadSection(section);

        // Actualizar active
        links.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
      });
    });

    // Logout
    const logoutBtn = document.getElementById('admin-logout');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        Auth.logout();
        window.location.href = '/login.php';
      });
    }
  };

  /* ---- BOTONES DE ACCIÓN ---- */
  const initActionButtons = () => {
    // Marcar enlace activo según la página actual
    const currentPage = window.location.pathname.split('/').pop();
    const links = document.querySelectorAll('[data-admin-section]');
    links.forEach(link => {
      const section = link.dataset.adminSection;
      // Si la página actual contiene el nombre de la sección
      if (currentPage && currentPage.includes(section)) {
        link.classList.add('active');
      }
    });

    // Sidebar toggle en mobile
    const toggle = document.getElementById('menuToggle');
    if (toggle) {
      toggle.addEventListener('click', () => {
        document.getElementById('sidebar')?.classList.toggle('open');
        document.getElementById('sidebarOverlay')?.classList.toggle('active');
      });
    }

    // Cerrar sidebar overlay
    const overlay = document.getElementById('sidebarOverlay');
    if (overlay) {
      overlay.addEventListener('click', () => {
        document.getElementById('sidebar')?.classList.remove('open');
        overlay.classList.remove('active');
      });
    }
  };

  /* ---- MODALES (CRUD) ---- */
  const initModals = () => {
    const closeBtns = document.querySelectorAll('.modal-close');
    closeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        closeModal(btn.closest('.modal-overlay'));
      });
    });

    // Cerrar al hacer clic fuera
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          closeModal(overlay);
        }
      });
    });

    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(closeModal);
      }
    });
  };

  const openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  const closeModal = (modal) => {
    if (!modal) return;
    modal.classList.remove('open');
    document.body.style.overflow = '';
  };

  /* ---- CARGAR SECCIÓN ---- */
  const loadSection = async (section) => {
    state.currentSection = section;
    const container = document.getElementById('admin-content');
    if (!container) return;

    // Título
    const titles = {
      dashboard: 'Dashboard',
      productos: 'Gestión de Productos',
      pedidos: 'Gestión de Pedidos',
      pagos: 'Métodos de Pago',
      tasa: 'Tasa BCV',
      ofertas: 'Promociones y Ofertas',
    };
    document.getElementById('admin-section-title').textContent = titles[section] || 'Dashboard';

    // Mostrar loading
    container.innerHTML = '<div class="loading-spinner"></div>';

    switch (section) {
      case 'dashboard':
        await loadDashboard(container);
        break;
      case 'productos':
        await loadProductos(container);
        break;
      case 'pedidos':
        await loadPedidos(container);
        break;
      case 'pagos':
        await loadPagos(container);
        break;
      case 'tasa':
        await loadTasa(container);
        break;
      case 'ofertas':
        await loadOfertas(container);
        break;
      default:
        container.innerHTML = '<p>Sección no encontrada</p>';
    }
  };

  /* ============================================================
     DASHBOARD
     ============================================================ */
  const loadDashboard = async (container) => {
    try {
      const data = await App.apiFetch('/admin/dashboard');

      const stats = data.stats || {
        pedidos_hoy: 0,
        ingresos_hoy: 0,
        productos_vendidos: 0,
        pedidos_pendientes: 0,
        cambio_hoy: 12,
        cambio_ingresos: 8,
      };

      container.innerHTML = `
        <div class="stats-grid">
          <div class="stat-card animate-on-scroll">
            <div class="stat-icon">📋</div>
            <div class="stat-value">${stats.pedidos_hoy}</div>
            <div class="stat-label">Pedidos Hoy</div>
            <div class="stat-change ${stats.cambio_hoy >= 0 ? 'up' : 'down'}">
              ${stats.cambio_hoy >= 0 ? '↑' : '↓'} ${Math.abs(stats.cambio_hoy)}% vs ayer
            </div>
          </div>
          <div class="stat-card animate-on-scroll">
            <div class="stat-icon">💰</div>
            <div class="stat-value">${App.formatUSD(stats.ingresos_hoy || 0)}</div>
            <div class="stat-label">Ingresos Hoy</div>
            <div class="stat-change ${stats.cambio_ingresos >= 0 ? 'up' : 'down'}">
              ${stats.cambio_ingresos >= 0 ? '↑' : '↓'} ${Math.abs(stats.cambio_ingresos)}% vs ayer
            </div>
          </div>
          <div class="stat-card animate-on-scroll">
            <div class="stat-icon">🍔</div>
            <div class="stat-value">${stats.productos_vendidos}</div>
            <div class="stat-label">Productos Vendidos</div>
          </div>
          <div class="stat-card animate-on-scroll">
            <div class="stat-icon">⏳</div>
            <div class="stat-value">${stats.pedidos_pendientes}</div>
            <div class="stat-label">Pendientes</div>
          </div>
        </div>
        <div style="background:var(--color-bg-card);border:1px solid var(--color-gris-oscuro);border-radius:var(--radius-lg);padding:32px;">
          <h3 style="font-family:var(--font-heading);text-transform:uppercase;color:var(--color-blanco);margin-bottom:16px;">Pedidos Recientes</h3>
          <div id="dashboard-recent-orders">
            <div class="loading-spinner"></div>
          </div>
        </div>
      `;

      // Cargar pedidos recientes
      loadRecentOrders();

    } catch (e) {
      container.innerHTML = `
        <div class="stats-grid">
          <div class="stat-card"><div class="stat-value">—</div><div class="stat-label">Pedidos Hoy</div></div>
          <div class="stat-card"><div class="stat-value">—</div><div class="stat-label">Ingresos Hoy</div></div>
          <div class="stat-card"><div class="stat-value">—</div><div class="stat-label">Productos</div></div>
          <div class="stat-card"><div class="stat-value">—</div><div class="stat-label">Pendientes</div></div>
        </div>
        <div style="background:var(--color-bg-card);border:1px solid var(--color-gris-oscuro);border-radius:var(--radius-lg);padding:32px;text-align:center;">
          <p style="color:var(--color-gris);">${e.message || 'Conecta el backend para ver datos en vivo'}</p>
        </div>
      `;
    }
  };

  const loadRecentOrders = async () => {
    const container = document.getElementById('dashboard-recent-orders');
    if (!container) return;

    try {
      const data = await App.apiFetch('/admin/pedidos?limit=5');
      const pedidos = data.pedidos || data || [];

      if (pedidos.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No hay pedidos recientes</p></div>';
        return;
      }

      let html = '<div class="data-table-wrapper"><table class="data-table"><thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Fecha</th></tr></thead><tbody>';
      pedidos.forEach(p => {
        html += `
          <tr>
            <td><strong>#${p.id}</strong></td>
            <td>${p.cliente_nombre || p.nombre || '—'}</td>
            <td style="color:var(--color-amarillo);">${App.formatUSD(parseFloat(p.total_usd || p.total || 0))}</td>
            <td><span class="status-badge status-${p.estado || 'pendiente'}">${p.estado || 'pendiente'}</span></td>
            <td>${p.created_at ? new Date(p.created_at).toLocaleString('es-VE') : '—'}</td>
          </tr>
        `;
      });
      html += '</tbody></table></div>';
      container.innerHTML = html;

    } catch (e) {
      container.innerHTML = '<div class="empty-state"><p>Conecta el backend para ver pedidos recientes</p></div>';
    }
  };

  /* ============================================================
     PRODUCTOS - CRUD
     ============================================================ */
  const loadProductos = async (container) => {
    try {
      const data = await App.apiFetch('/admin/productos');
      state.productos = data.productos || data || [];
    } catch (e) {
      state.productos = [];
    }

    renderProductosTable(container);
  };

  const renderProductosTable = (container) => {
    const productos = state.productos;

    let html = `
      <div class="data-table-wrapper">
        <div class="data-table-toolbar">
          <h3>${productos.length} Productos</h3>
          <div class="actions">
            <button class="btn btn-primary btn-sm" id="btn-nuevo-producto">+ Nuevo Producto</button>
          </div>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Imagen</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Precio</th>
              <th>Oferta</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
    `;

    if (productos.length === 0) {
      html += `
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <div class="icon">🍔</div>
              <h3>No hay productos</h3>
              <p>Crea tu primer producto para empezar</p>
            </div>
          </td>
        </tr>
      `;
    } else {
      productos.forEach(p => {
        html += `
          <tr>
            <td>
              ${p.imagen
                ? `<img src="${p.imagen}" alt="${p.nombre}" style="width:50px;height:50px;object-fit:cover;border-radius:var(--radius-sm);">`
                : `<div style="width:50px;height:50px;background:var(--color-bg-alt);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🍔</div>`
              }
            </td>
            <td><strong>${p.nombre}</strong></td>
            <td><span class="badge badge-gris">${p.categoria || 'General'}</span></td>
            <td style="color:var(--color-amarillo);font-weight:700;">${App.formatUSD(parseFloat(p.precio))}</td>
            <td>
              ${p.oferta_activa
                ? `<span class="badge badge-amarillo">${p.descuento || 0}% OFF</span>`
                : '<span style="color:var(--color-gris-oscuro);">—</span>'
              }
            </td>
            <td>
              <label class="toggle-switch">
                <input type="checkbox" ${p.activo !== false ? 'checked' : ''} class="toggle-producto" data-id="${p.id}">
                <span class="toggle-slider"></span>
              </label>
            </td>
            <td class="actions-cell">
              <button class="btn-edit" data-id="${p.id}" title="Editar">✏️</button>
              <button class="btn-delete" data-id="${p.id}" title="Eliminar">🗑️</button>
            </td>
          </tr>
        `;
      });
    }

    html += `
          </tbody>
        </table>
      </div>
    `;

    container.innerHTML = html;

    // Event listeners
    container.querySelector('#btn-nuevo-producto')?.addEventListener('click', () => openProductoModal());
    container.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', () => openProductoModal(btn.dataset.id));
    });
    container.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => deleteProducto(btn.dataset.id));
    });
    container.querySelectorAll('.toggle-producto').forEach(toggle => {
      toggle.addEventListener('change', () => toggleProductoActivo(toggle.dataset.id, toggle.checked));
    });
  };

  /* ---- PRODUCTO: MODAL CRUD ---- */
  const openProductoModal = (id = null) => {
    const modal = document.getElementById('modal-producto');
    if (!modal) return;

    const producto = id ? state.productos.find(p => String(p.id) === String(id)) : null;
    const isEdit = !!producto;

    modal.querySelector('.modal-header h2').textContent = isEdit ? 'Editar Producto' : 'Nuevo Producto';

    const form = modal.querySelector('#form-producto');
    form.innerHTML = `
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Nombre del producto</label>
          <input type="text" class="form-input" name="nombre" value="${producto?.nombre || ''}" required placeholder="Ej: Mega Doble Burger">
        </div>
        <div class="form-group">
          <label class="form-label">Categoría</label>
          <select class="form-select" name="categoria">
            <option value="hamburguesas" ${producto?.categoria === 'hamburguesas' ? 'selected' : ''}>Hamburguesas</option>
            <option value="perros" ${producto?.categoria === 'perros' ? 'selected' : ''}>Perros Calientes</option>
            <option value="pizza" ${producto?.categoria === 'pizza' ? 'selected' : ''}>Pizza</option>
            <option value="pollo" ${producto?.categoria === 'pollo' ? 'selected' : ''}>Pollo</option>
            <option value="bebidas" ${producto?.categoria === 'bebidas' ? 'selected' : ''}>Bebidas</option>
            <option value="papas" ${producto?.categoria === 'papas' ? 'selected' : ''}>Papas / Acompañantes</option>
            <option value="postres" ${producto?.categoria === 'postres' ? 'selected' : ''}>Postres</option>
            <option value="combos" ${producto?.categoria === 'combos' ? 'selected' : ''}>Combos</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Descripción</label>
        <textarea class="form-textarea" name="descripcion" placeholder="Descripción del producto" rows="3">${producto?.descripcion || ''}</textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Precio (USD)</label>
          <input type="number" class="form-input" name="precio" value="${producto?.precio || ''}" step="0.01" min="0" required placeholder="0.00">
        </div>
        <div class="form-group">
          <label class="form-label">URL de Imagen</label>
          <input type="url" class="form-input" name="imagen" value="${producto?.imagen || ''}" placeholder="https://...">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">¿Oferta especial?</label>
          <label class="form-checkbox" style="margin-top:8px;">
            <input type="checkbox" name="oferta_activa" ${producto?.oferta_activa ? 'checked' : ''}>
            <span>Activar descuento</span>
          </label>
        </div>
        <div class="form-group" id="descuento-field" style="${producto?.oferta_activa ? '' : 'display:none;'}">
          <label class="form-label">% Descuento</label>
          <input type="number" class="form-input" name="descuento" value="${producto?.descuento || 0}" min="0" max="100" placeholder="0">
        </div>
      </div>
    `;

    // Toggle descuento field
    form.querySelector('[name="oferta_activa"]')?.addEventListener('change', (e) => {
      const field = document.getElementById('descuento-field');
      if (field) field.style.display = e.target.checked ? 'block' : 'none';
    });

    // Submit
    form.onsubmit = async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      data.precio = parseFloat(data.precio) || 0;
      data.descuento = parseFloat(data.descuento) || 0;
      data.oferta_activa = formData.get('oferta_activa') === 'on';

      try {
        if (isEdit) {
          await App.apiFetch(`/admin/productos/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
          });
          App.showToast('Producto actualizado', 'success');
        } else {
          await App.apiFetch('/admin/productos', {
            method: 'POST',
            body: JSON.stringify(data),
          });
          App.showToast('Producto creado', 'success');
        }

        closeModal(modal);
        loadProductos(document.getElementById('admin-content'));

      } catch (error) {
        App.showToast(error.message || 'Error al guardar producto', 'error');
      }
    };

    // Botón guardar
    const saveBtn = modal.querySelector('#btn-save-producto');
    const newSaveBtn = saveBtn.cloneNode(true);
    saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);
    newSaveBtn.addEventListener('click', () => form.requestSubmit());

    openModal('modal-producto');
  };

  /* ---- PRODUCTO: ELIMINAR ---- */
  const deleteProducto = async (id) => {
    if (!confirm('¿Eliminar este producto permanentemente?')) return;

    try {
      await App.apiFetch(`/admin/productos/${id}`, { method: 'DELETE' });
      App.showToast('Producto eliminado', 'success');
      loadProductos(document.getElementById('admin-content'));
    } catch (e) {
      App.showToast(e.message || 'Error al eliminar', 'error');
    }
  };

  /* ---- PRODUCTO: TOGGLE ACTIVO ---- */
  const toggleProductoActivo = async (id, activo) => {
    try {
      await App.apiFetch(`/admin/productos/${id}`, {
        method: 'PATCH',
        body: JSON.stringify({ activo }),
      });
    } catch (e) {
      App.showToast(e.message || 'Error al actualizar', 'error');
      loadProductos(document.getElementById('admin-content'));
    }
  };

  /* ============================================================
     PEDIDOS
     ============================================================ */
  const loadPedidos = async (container) => {
    try {
      const data = await App.apiFetch('/admin/pedidos');
      state.pedidos = data.pedidos || data || [];
    } catch (e) {
      state.pedidos = [];
    }

    renderPedidosTable(container);
  };

  const renderPedidosTable = (container) => {
    const pedidos = state.pedidos;

    const estados = ['pendiente', 'confirmado', 'preparando', 'listo', 'entregado', 'cancelado'];

    let html = `
      <div class="data-table-wrapper">
        <div class="data-table-toolbar">
          <h3>${pedidos.length} Pedidos</h3>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            ${estados.map(e => `
              <button class="btn btn-sm btn-outline filtro-estado" data-estado="${e}" style="text-transform:capitalize;">${e}</button>
            `).join('')}
            <button class="btn btn-sm btn-outline-rojo filtro-estado" data-estado="todos" style="font-weight:700;">Todos</button>
          </div>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Cliente</th>
              <th>Teléfono</th>
              <th>Total</th>
              <th>Método</th>
              <th>Estado</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="pedidos-tbody">
    `;

    if (pedidos.length === 0) {
      html += `
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <div class="icon">📋</div>
              <h3>No hay pedidos</h3>
              <p>Los pedidos aparecerán aquí cuando los clientes ordenen</p>
            </div>
          </td>
        </tr>
      `;
    } else {
      pedidos.forEach(p => {
        html += `
          <tr class="pedido-row" data-estado="${p.estado || 'pendiente'}">
            <td><strong>#${p.id}</strong></td>
            <td>${p.cliente_nombre || p.nombre || '—'}</td>
            <td>${p.cliente_telefono || p.telefono || '—'}</td>
            <td style="color:var(--color-amarillo);font-weight:700;">${App.formatUSD(parseFloat(p.total_usd || p.total || 0))}</td>
            <td><span class="badge badge-gris">${p.metodo_pago || '—'}</span></td>
            <td>
              <select class="select-estado" data-id="${p.id}" style="background:var(--color-bg-alt);color:var(--color-blanco);border:1px solid var(--color-gris-oscuro);border-radius:var(--radius-sm);padding:4px 8px;">
                ${estados.map(e => `<option value="${e}" ${p.estado === e ? 'selected' : ''}>${e}</option>`).join('')}
              </select>
            </td>
            <td style="font-size:0.8rem;">${p.created_at ? new Date(p.created_at).toLocaleString('es-VE') : '—'}</td>
            <td class="actions-cell">
              <button class="btn-ver-pedido" data-id="${p.id}" title="Ver detalle">👁️</button>
            </td>
          </tr>
        `;
      });
    }

    html += `
          </tbody>
        </table>
      </div>
    `;

    container.innerHTML = html;

    // Event listeners
    container.querySelectorAll('.filtro-estado').forEach(btn => {
      btn.addEventListener('click', () => {
        const estado = btn.dataset.estado;
        document.querySelectorAll('.pedido-row').forEach(row => {
          if (estado === 'todos' || row.dataset.estado === estado) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
        // Actualizar active
        container.querySelectorAll('.filtro-estado').forEach(b => b.classList.remove('btn-primary'));
        btn.classList.add('btn-primary');
        btn.classList.remove('btn-outline', 'btn-outline-rojo');
      });
    });

    container.querySelectorAll('.select-estado').forEach(select => {
      select.addEventListener('change', () => updatePedidoEstado(select.dataset.id, select.value));
    });

    container.querySelectorAll('.btn-ver-pedido').forEach(btn => {
      btn.addEventListener('click', () => verDetallePedido(btn.dataset.id));
    });
  };

  /* ---- PEDIDO: CAMBIAR ESTADO ---- */
  const updatePedidoEstado = async (id, estado) => {
    try {
      await App.apiFetch(`/admin/pedidos/${id}`, {
        method: 'PATCH',
        body: JSON.stringify({ estado }),
      });
      App.showToast(`Pedido #${id} → ${estado}`, 'success');
    } catch (e) {
      App.showToast(e.message || 'Error al actualizar estado', 'error');
      loadPedidos(document.getElementById('admin-content'));
    }
  };

  /* ---- PEDIDO: VER DETALLE ---- */
  const verDetallePedido = async (id) => {
    try {
      const data = await App.apiFetch(`/admin/pedidos/${id}`);
      const pedido = data.pedido || data;

      const modal = document.getElementById('modal-pedido-detalle');
      if (!modal) return;

      const body = modal.querySelector('.modal-body');
      const items = pedido.items || [];

      let html = `
        <div style="margin-bottom:16px;">
          <p><strong>Pedido #${pedido.id}</strong></p>
          <p>Cliente: ${pedido.cliente_nombre || pedido.nombre || '—'}</p>
          <p>Teléfono: ${pedido.cliente_telefono || pedido.telefono || '—'}</p>
          <p>Email: ${pedido.cliente_email || pedido.email || '—'}</p>
          <p>Fecha: ${pedido.created_at ? new Date(pedido.created_at).toLocaleString('es-VE') : '—'}</p>
          <p>Método de pago: ${pedido.metodo_pago || '—'}</p>
          <p>Referencia: ${pedido.referencia || '—'}</p>
          <p>Tipo: ${pedido.tipo || 'delivery'}</p>
          ${pedido.direccion ? `<p>Dirección: ${pedido.direccion}</p>` : ''}
          ${pedido.notas ? `<p>Notas: ${pedido.notas}</p>` : ''}
        </div>
        <h4 style="font-family:var(--font-heading);font-size:0.85rem;text-transform:uppercase;color:var(--color-amarillo);margin-bottom:12px;">Productos</h4>
        <table class="order-summary-table">
          <thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Total</th></tr></thead>
          <tbody>
      `;

      if (items.length > 0) {
        items.forEach(item => {
          const p = typeof item === 'object' ? item : { nombre: item, cantidad: 1, precio: 0 };
          html += `
            <tr>
              <td>${p.nombre || p.producto_nombre || '—'}</td>
              <td>${p.cantidad || p.cant || 1}</td>
              <td>${App.formatUSD(parseFloat(p.precio || 0))}</td>
              <td class="item-total">${App.formatUSD(parseFloat((p.precio || 0) * (p.cantidad || p.cant || 1)))}</td>
            </tr>
          `;
        });
      }

      // Si items es un string JSON, parsearlo
      if (typeof items === 'string') {
        try {
          const parsed = JSON.parse(items);
          if (Array.isArray(parsed)) {
            html += parsed.map(item => `
              <tr>
                <td>${item.nombre || '—'}</td>
                <td>${item.cantidad || 1}</td>
                <td>${App.formatUSD(parseFloat(item.precio || 0))}</td>
                <td class="item-total">${App.formatUSD(parseFloat((item.precio || 0) * (item.cantidad || 1)))}</td>
              </tr>
            `).join('');
          }
        } catch {}
      }

      const total = parseFloat(pedido.total_usd || pedido.total || 0);
      html += `
          </tbody>
        </table>
        <div style="text-align:right;margin-top:12px;font-family:var(--font-heading);font-size:1.2rem;color:var(--color-amarillo);">
          Total: ${App.formatUSD(total)}
        </div>
      `;

      // Captura
      if (pedido.capture_url || pedido.capture) {
        html += `
          <div style="margin-top:16px;">
            <p style="font-family:var(--font-heading);font-size:0.8rem;text-transform:uppercase;color:var(--color-gris);margin-bottom:8px;">Captura de pago</p>
            <img src="${pedido.capture_url || pedido.capture}" alt="Captura" style="max-width:100%;border-radius:var(--radius-sm);border:1px solid var(--color-gris-oscuro);">
          </div>
        `;
      }

      body.innerHTML = html;
      modal.querySelector('.modal-header h2').textContent = `Pedido #${pedido.id}`;
      openModal('modal-pedido-detalle');

    } catch (e) {
      App.showToast(e.message || 'Error al cargar detalle', 'error');
    }
  };

  /* ============================================================
     PAGOS - CRUD CUENTAS PAGOMÓVIL
     ============================================================ */
  const loadPagos = async (container) => {
    try {
      const data = await App.apiFetch('/admin/cuentas-pagomovil');
      state.cuentasPago = data.cuentas || data || [];
    } catch (e) {
      state.cuentasPago = [];
    }

    renderPagos(container);
  };

  const renderPagos = (container) => {
    const cuentas = state.cuentasPago;

    // Si no hay cuentas, mostrar defaults
    const defaultCuentas = cuentas.length > 0 ? cuentas : [
      { id: 1, banco: 'Mercantil', cedula: 'V-12.345.678', telefono: '+58 412-1234567', titular: 'Maná Fast Food', activa: true },
      { id: 2, banco: 'Banco de Venezuela', cedula: 'V-87.654.321', telefono: '+58 414-7654321', titular: 'Maná Fast Food', activa: false },
    ];

    const displayCuentas = cuentas.length > 0 ? cuentas : defaultCuentas;

    let html = `
      <div class="data-table-wrapper">
        <div class="data-table-toolbar">
          <h3>${displayCuentas.length} Cuentas PagoMóvil</h3>
          <button class="btn btn-primary btn-sm" id="btn-nueva-cuenta">+ Nueva Cuenta</button>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Banco</th>
              <th>Cédula</th>
              <th>Teléfono</th>
              <th>Titular</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
    `;

    displayCuentas.forEach(c => {
      html += `
        <tr>
          <td><strong>${c.banco}</strong></td>
          <td>${c.cedula}</td>
          <td>${c.telefono}</td>
          <td>${c.titular || '—'}</td>
          <td>
            ${c.activa
              ? '<span class="status-badge status-activo">Activa</span>'
              : '<span class="status-badge status-inactivo">Inactiva</span>'
            }
          </td>
          <td class="actions-cell">
            <button class="btn-edit-cuenta" data-id="${c.id}" title="Editar">✏️</button>
            <button class="btn-toggle-cuenta" data-id="${c.id}" title="${c.activa ? 'Desactivar' : 'Activar'}">
              ${c.activa ? '🔴' : '🟢'}
            </button>
            <button class="btn-delete-cuenta" data-id="${c.id}" title="Eliminar">🗑️</button>
          </td>
        </tr>
      `;
    });

    html += `
          </tbody>
        </table>
      </div>
    `;

    container.innerHTML = html;

    container.querySelector('#btn-nueva-cuenta')?.addEventListener('click', () => openCuentaModal());
    container.querySelectorAll('.btn-edit-cuenta').forEach(btn => {
      btn.addEventListener('click', () => openCuentaModal(btn.dataset.id));
    });
    container.querySelectorAll('.btn-toggle-cuenta').forEach(btn => {
      btn.addEventListener('click', () => toggleCuentaActiva(btn.dataset.id));
    });
    container.querySelectorAll('.btn-delete-cuenta').forEach(btn => {
      btn.addEventListener('click', () => deleteCuenta(btn.dataset.id));
    });
  };

  /* ---- CUENTA: MODAL ---- */
  const openCuentaModal = (id = null) => {
    const cuentas = state.cuentasPago;
    const cuenta = id ? cuentas.find(c => String(c.id) === String(id)) : null;

    const modal = document.getElementById('modal-cuenta');
    if (!modal) return;

    modal.querySelector('.modal-header h2').textContent = cuenta ? 'Editar Cuenta' : 'Nueva Cuenta';
    const form = modal.querySelector('#form-cuenta');

    form.innerHTML = `
      <div class="form-group">
        <label class="form-label">Banco</label>
        <select class="form-select" name="banco" required>
          <option value="">Seleccionar banco</option>
          <option value="Mercantil" ${cuenta?.banco === 'Mercantil' ? 'selected' : ''}>Mercantil Banco</option>
          <option value="BDV" ${cuenta?.banco === 'BDV' ? 'selected' : ''}>Banco de Venezuela</option>
          <option value="Banesco" ${cuenta?.banco === 'Banesco' ? 'selected' : ''}>Banesco</option>
          <option value="Provincial" ${cuenta?.banco === 'Provincial' ? 'selected' : ''}>BBVA Provincial</option>
          <option value="Bancaribe" ${cuenta?.banco === 'Bancaribe' ? 'selected' : ''}>Bancaribe</option>
          <option value="BVC" ${cuenta?.banco === 'BVC' ? 'selected' : ''}>BVC</option>
          <option value="Exterior" ${cuenta?.banco === 'Exterior' ? 'selected' : ''}>Banco Exterior</option>
          <option value="Tesoro" ${cuenta?.banco === 'Tesoro' ? 'selected' : ''}>Banco del Tesoro</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Cédula / RIF</label>
          <input type="text" class="form-input" name="cedula" value="${cuenta?.cedula || ''}" required placeholder="V-12.345.678">
        </div>
        <div class="form-group">
          <label class="form-label">Teléfono PagoMóvil</label>
          <input type="text" class="form-input" name="telefono" value="${cuenta?.telefono || ''}" required placeholder="+58 412-1234567">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Titular de la cuenta</label>
        <input type="text" class="form-input" name="titular" value="${cuenta?.titular || ''}" placeholder="Nombre del titular">
      </div>
      <div class="form-group">
        <label class="form-checkbox">
          <input type="checkbox" name="activa" ${cuenta?.activa ? 'checked' : ''}>
          <span>Cuenta activa (visible para clientes)</span>
        </label>
      </div>
    `;

    form.onsubmit = async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
      data.activa = formData.get('activa') === 'on';

      try {
        if (cuenta) {
          await App.apiFetch(`/admin/cuentas-pagomovil/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
          });
          App.showToast('Cuenta actualizada', 'success');
        } else {
          await App.apiFetch('/admin/cuentas-pagomovil', {
            method: 'POST',
            body: JSON.stringify(data),
          });
          App.showToast('Cuenta creada', 'success');
        }
        closeModal(modal);
        loadPagos(document.getElementById('admin-content'));
      } catch (error) {
        App.showToast(error.message || 'Error al guardar', 'error');
      }
    };

    // Botón guardar
    const saveBtn = modal.querySelector('#btn-save-cuenta');
    const newSaveBtn = saveBtn.cloneNode(true);
    saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);
    newSaveBtn.addEventListener('click', () => form.requestSubmit());

    openModal('modal-cuenta');
  };

  /* ---- CUENTA: TOGGLE ACTIVA ---- */
  const toggleCuentaActiva = async (id) => {
    try {
      await App.apiFetch(`/admin/cuentas-pagomovil/${id}/toggle`, { method: 'POST' });
      App.showToast('Estado de cuenta actualizado', 'success');
      loadPagos(document.getElementById('admin-content'));
    } catch (e) {
      App.showToast(e.message || 'Error al cambiar estado', 'error');
    }
  };

  /* ---- CUENTA: ELIMINAR ---- */
  const deleteCuenta = async (id) => {
    if (!confirm('¿Eliminar esta cuenta permanentemente?')) return;
    try {
      await App.apiFetch(`/admin/cuentas-pagomovil/${id}`, { method: 'DELETE' });
      App.showToast('Cuenta eliminada', 'success');
      loadPagos(document.getElementById('admin-content'));
    } catch (e) {
      App.showToast(e.message || 'Error al eliminar', 'error');
    }
  };

  /* ============================================================
     TASA BCV
     ============================================================ */
  const loadTasa = async (container) => {
    try {
      const data = await App.apiFetch('/admin/tasa-bcv');
      state.tasaBCV = data.tasa || null;
      state.tasaManual = data.manual || false;
    } catch (e) {
      state.tasaBCV = null;
      state.tasaManual = false;
    }

    renderTasaForm(container);
  };

  const renderTasaForm = (container) => {
    const tasa = state.tasaBCV;

    container.innerHTML = `
      <div style="background:var(--color-bg-card);border:1px solid var(--color-gris-oscuro);border-radius:var(--radius-lg);padding:32px;max-width:600px;">
        <h3 style="font-family:var(--font-heading);text-transform:uppercase;color:var(--color-blanco);margin-bottom:24px;">Configuración Tasa BCV</h3>

        <div class="form-group">
          <label class="form-label">Modo de tasa</label>
          <div style="display:flex;gap:16px;margin-top:8px;">
            <label class="form-checkbox">
              <input type="radio" name="tasa_modo" value="auto" ${!state.tasaManual ? 'checked' : ''}>
              <span>Automática (API BCV)</span>
            </label>
            <label class="form-checkbox">
              <input type="radio" name="tasa_modo" value="manual" ${state.tasaManual ? 'checked' : ''}>
              <span>Manual</span>
            </label>
          </div>
        </div>

        <div class="form-group" id="tasa-auto-info" style="${state.tasaManual ? 'display:none;' : ''}">
          <div style="background:rgba(255,184,28,0.1);border:1px solid rgba(255,184,28,0.2);border-radius:var(--radius-md);padding:20px;">
            <p style="font-size:0.9rem;color:var(--color-gris-claro);margin-bottom:12px;">
              La tasa se actualiza automáticamente desde el BCV.
            </p>
            ${tasa
              ? `<p style="font-size:1.5rem;font-family:var(--font-display);color:var(--color-amarillo);">Bs. ${parseFloat(tasa).toFixed(2)} / USD</p>
                 <p style="font-size:0.8rem;color:var(--color-gris);">Última actualización: ${new Date().toLocaleString('es-VE')}</p>`
              : `<p style="color:var(--color-gris);">Conecta el backend para obtener la tasa actual</p>`
            }
          </div>
        </div>

        <div class="form-group" id="tasa-manual-field" style="${state.tasaManual ? '' : 'display:none;'}">
          <label class="form-label">Tasa manual (Bs. por USD)</label>
          <input type="number" class="form-input" id="tasa-manual-input" value="${tasa || ''}" step="0.01" min="0" placeholder="0.00">
        </div>

        <button class="btn btn-primary" id="btn-guardar-tasa">Guardar Configuración</button>
      </div>
    `;

    // Toggle modo
    container.querySelectorAll('[name="tasa_modo"]').forEach(radio => {
      radio.addEventListener('change', () => {
        const isManual = document.querySelector('[name="tasa_modo"]:checked')?.value === 'manual';
        document.getElementById('tasa-auto-info').style.display = isManual ? 'none' : 'block';
        document.getElementById('tasa-manual-field').style.display = isManual ? 'block' : 'none';
        state.tasaManual = isManual;
      });
    });

    // Guardar
    container.querySelector('#btn-guardar-tasa')?.addEventListener('click', async () => {
      const isManual = document.querySelector('[name="tasa_modo"]:checked')?.value === 'manual';
      const tasaValue = isManual
        ? parseFloat(document.getElementById('tasa-manual-input')?.value)
        : null;

      if (isManual && (!tasaValue || tasaValue <= 0)) {
        App.showToast('Ingresa una tasa válida', 'error');
        return;
      }

      try {
        await App.apiFetch('/admin/tasa-bcv', {
          method: 'PUT',
          body: JSON.stringify({
            manual: isManual,
            tasa: tasaValue,
          }),
        });

        Cart.setTasaBCV(tasaValue);
        App.showToast('Tasa actualizada', 'success');
        loadTasa(container);

      } catch (e) {
        App.showToast(e.message || 'Error al guardar tasa', 'error');
      }
    });
  };

  /* ============================================================
     OFERTAS
     ============================================================ */
  const loadOfertas = async (container) => {
    try {
      const data = await App.apiFetch('/admin/ofertas');
      state.ofertas = data.ofertas || data || [];
    } catch (e) {
      state.ofertas = [];
    }

    renderOfertas(container);
  };

  const renderOfertas = (container) => {
    const ofertas = state.ofertas;

    let html = `
      <div class="data-table-wrapper">
        <div class="data-table-toolbar">
          <h3>${ofertas.length} Promociones</h3>
          <button class="btn btn-primary btn-sm" id="btn-nueva-oferta">+ Nueva Oferta</button>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Tipo</th>
              <th>Descuento</th>
              <th>Vigencia</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
    `;

    if (ofertas.length === 0) {
      html += `
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <div class="icon">🏷️</div>
              <h3>No hay promociones</h3>
              <p>Crea ofertas para atraer más clientes</p>
              <button class="btn btn-primary btn-sm" id="btn-nueva-oferta-empty">+ Crear Oferta</button>
            </div>
          </td>
        </tr>
      `;
    } else {
      ofertas.forEach(o => {
        const activa = o.activa !== false;
        html += `
          <tr>
            <td><strong>${o.nombre}</strong></td>
            <td><span class="badge badge-gris">${o.tipo || 'porcentaje'}</span></td>
            <td style="color:var(--color-amarillo);font-weight:700;">${o.descuento}${o.tipo === 'porcentaje' ? '%' : '$'} OFF</td>
            <td style="font-size:0.8rem;">${o.fecha_inicio ? new Date(o.fecha_inicio).toLocaleDateString('es-VE') : '—'} → ${o.fecha_fin ? new Date(o.fecha_fin).toLocaleDateString('es-VE') : '—'}</td>
            <td>
              ${activa
                ? '<span class="status-badge status-activo">Activa</span>'
                : '<span class="status-badge status-inactivo">Inactiva</span>'
              }
            </td>
            <td class="actions-cell">
              <button class="btn-edit-oferta" data-id="${o.id}" title="Editar">✏️</button>
              <button class="btn-toggle-oferta" data-id="${o.id}" title="${activa ? 'Desactivar' : 'Activar'}">${activa ? '🔴' : '🟢'}</button>
              <button class="btn-delete-oferta" data-id="${o.id}" title="Eliminar">🗑️</button>
            </td>
          </tr>
        `;
      });
    }

    html += `
          </tbody>
        </table>
      </div>
    `;

    container.innerHTML = html;

    container.querySelector('#btn-nueva-oferta')?.addEventListener('click', () => openOfertaModal());
    container.querySelector('#btn-nueva-oferta-empty')?.addEventListener('click', () => openOfertaModal());
    container.querySelectorAll('.btn-edit-oferta').forEach(btn => {
      btn.addEventListener('click', () => openOfertaModal(btn.dataset.id));
    });
    container.querySelectorAll('.btn-toggle-oferta').forEach(btn => {
      btn.addEventListener('click', () => toggleOferta(btn.dataset.id));
    });
    container.querySelectorAll('.btn-delete-oferta').forEach(btn => {
      btn.addEventListener('click', () => deleteOferta(btn.dataset.id));
    });
  };

  /* ---- OFERTA: MODAL ---- */
  const openOfertaModal = (id = null) => {
    const oferta = id ? state.ofertas.find(o => String(o.id) === String(id)) : null;

    const modal = document.getElementById('modal-oferta');
    if (!modal) return;

    modal.querySelector('.modal-header h2').textContent = oferta ? 'Editar Oferta' : 'Nueva Oferta';
    const form = modal.querySelector('#form-oferta');

    form.innerHTML = `
      <div class="form-group">
        <label class="form-label">Nombre de la promoción</label>
        <input type="text" class="form-input" name="nombre" value="${oferta?.nombre || ''}" required placeholder="Ej: 2x1 en Hamburguesas">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Tipo</label>
          <select class="form-select" name="tipo">
            <option value="porcentaje" ${oferta?.tipo === 'porcentaje' ? 'selected' : ''}>Porcentaje (%)</option>
            <option value="monto_fijo" ${oferta?.tipo === 'monto_fijo' ? 'selected' : ''}>Monto Fijo ($)</option>
            <option value="2x1" ${oferta?.tipo === '2x1' ? 'selected' : ''}>2x1</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Descuento</label>
          <input type="number" class="form-input" name="descuento" value="${oferta?.descuento || 0}" min="0" required placeholder="0">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Fecha de inicio</label>
          <input type="date" class="form-input" name="fecha_inicio" value="${oferta?.fecha_inicio || ''}">
        </div>
        <div class="form-group">
          <label class="form-label">Fecha de fin</label>
          <input type="date" class="form-input" name="fecha_fin" value="${oferta?.fecha_fin || ''}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Productos aplicables (IDs separados por coma)</label>
        <input type="text" class="form-input" name="productos_ids" value="${(oferta?.productos_ids || []).join(', ')}" placeholder="1, 3, 5">
      </div>
      <div class="form-group">
        <label class="form-checkbox">
          <input type="checkbox" name="activa" ${oferta?.activa !== false ? 'checked' : ''}>
          <span>Activar promoción</span>
        </label>
      </div>
    `;

    form.onsubmit = async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      data.descuento = parseFloat(data.descuento) || 0;
      data.activa = formData.get('activa') === 'on';
      data.productos_ids = (data.productos_ids || '').split(',').map(s => s.trim()).filter(Boolean);

      try {
        if (oferta) {
          await App.apiFetch(`/admin/ofertas/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
          });
          App.showToast('Oferta actualizada', 'success');
        } else {
          await App.apiFetch('/admin/ofertas', {
            method: 'POST',
            body: JSON.stringify(data),
          });
          App.showToast('Oferta creada', 'success');
        }
        closeModal(modal);
        loadOfertas(document.getElementById('admin-content'));
      } catch (error) {
        App.showToast(error.message || 'Error al guardar oferta', 'error');
      }
    };

    const saveBtn = modal.querySelector('#btn-save-oferta');
    const newSaveBtn = saveBtn.cloneNode(true);
    saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);
    newSaveBtn.addEventListener('click', () => form.requestSubmit());

    openModal('modal-oferta');
  };

  /* ---- OFERTA: TOGGLE ---- */
  const toggleOferta = async (id) => {
    try {
      await App.apiFetch(`/admin/ofertas/${id}/toggle`, { method: 'POST' });
      App.showToast('Estado de oferta actualizado', 'success');
      loadOfertas(document.getElementById('admin-content'));
    } catch (e) {
      App.showToast(e.message || 'Error al cambiar estado', 'error');
    }
  };

  /* ---- OFERTA: ELIMINAR ---- */
  const deleteOferta = async (id) => {
    if (!confirm('¿Eliminar esta oferta?')) return;
    try {
      await App.apiFetch(`/admin/ofertas/${id}`, { method: 'DELETE' });
      App.showToast('Oferta eliminada', 'success');
      loadOfertas(document.getElementById('admin-content'));
    } catch (e) {
      App.showToast(e.message || 'Error al eliminar', 'error');
    }
  };

  /* ---- API PÚBLICA ---- */
  return {
    init,
    loadSection,
    openModal,
    closeModal,
  };
})();

/* ---- INICIALIZAR EN DOM READY ---- */
document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('admin-app')) {
    Admin.init();
  }
});
