<?php
// Sesion desactivada para desarrollo frontend
// En produccion conectar con backend/api/admin/auth

$productos = [
    ['id' => 1, 'nombre' => 'Maná Burger Clásica', 'categoria' => 'Hamburguesas', 'precio' => 8.99, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
    ['id' => 2, 'nombre' => 'Maná Doble Carne', 'categoria' => 'Hamburguesas', 'precio' => 11.50, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
    ['id' => 3, 'nombre' => 'Maná BBQ Bacon', 'categoria' => 'Hamburguesas Especiales', 'precio' => 13.99, 'descuento' => 15, 'tipo' => 'oferta', 'activo' => true, 'imagen' => ''],
    ['id' => 4, 'nombre' => 'Maná Chicken Crispy', 'categoria' => 'Hamburguesas Especiales', 'precio' => 12.50, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
    ['id' => 5, 'nombre' => 'Hot Dog Maná Supreme', 'categoria' => 'Hot Dogs', 'precio' => 7.50, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
    ['id' => 6, 'nombre' => 'Enrollado de Pepperoni', 'categoria' => 'Enrollados', 'precio' => 6.99, 'descuento' => 10, 'tipo' => 'oferta', 'activo' => true, 'imagen' => ''],
    ['id' => 7, 'nombre' => 'Pepito de Pollo', 'categoria' => 'Pepitos', 'precio' => 9.99, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
    ['id' => 8, 'nombre' => 'Papas Maná Cheddar', 'categoria' => 'Especiales Fritos', 'precio' => 6.50, 'descuento' => 0, 'tipo' => 'normal', 'activo' => false, 'imagen' => ''],
    ['id' => 9, 'nombre' => 'Extra Queso', 'categoria' => 'Extras', 'precio' => 1.50, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
    ['id' => 10, 'nombre' => 'Coca-Cola 500ml', 'categoria' => 'Bebidas', 'precio' => 2.00, 'descuento' => 0, 'tipo' => 'normal', 'activo' => true, 'imagen' => ''],
];

$categorias = ['Hamburguesas', 'Hamburguesas Especiales', 'Hot Dogs', 'Enrollados', 'Pepitos', 'Especiales Fritos', 'Extras', 'Bebidas'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos · Maná Fast Food</title>
    <link rel="icon" type="image/png" href="../favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root {
            --mana-black: #111111;
            --mana-red: #D31212;
            --mana-yellow: #FFB81C;
            --mana-white: #FFFFFF;
            --mana-gray: #1e1e1e;
            --mana-gray-light: #2a2a2a;
            --mana-text: #e0e0e0;
            --mana-text-muted: #888;
            --mana-sidebar: #0a0a0a;
            --mana-success: #22c55e;
            --mana-danger: #ef4444;
            --mana-warning: #f59e0b;
            --mana-info: #3b82f6;
            --font-display: 'Archivo Black', sans-serif;
            --font-title: 'Anton', sans-serif;
            --font-body: 'Montserrat', sans-serif;
            --sidebar-width: 250px;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: var(--font-body);
            background: var(--mana-black);
            color: var(--mana-text);
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR - copy from dashboard */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--mana-sidebar);
            border-right: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }

.sidebar-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: contain;
    flex-shrink: 0;
}

        .sidebar-brand h1 {
            font-family: var(--font-title);
            font-size: 18px;
            color: var(--mana-white);
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .sidebar-brand small {
            display: block;
            font-family: var(--font-body);
            font-size: 9px;
            color: var(--mana-text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--mana-text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.05);
            color: var(--mana-white);
        }

        .sidebar-nav a.active {
            background: var(--mana-red);
            color: var(--mana-white);
            font-weight: 700;
        }

        .sidebar-nav a .icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--mana-text-muted);
            text-decoration: none;
            font-size: 12px;
            transition: color 0.2s;
        }

        .sidebar-footer a:hover { color: var(--mana-danger); }

        .main { flex:1; margin-left:var(--sidebar-width); }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            background: rgba(17,17,17,0.95);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left { display:flex; align-items:center; gap:16px; }

        .menu-toggle { display:none; background:none; border:none; color:var(--mana-white); font-size:24px; cursor:pointer; padding:4px; }

        .topbar h2 { font-family:var(--font-display); font-size:20px; color:var(--mana-white); letter-spacing:0.5px; }

        .content { padding:32px; }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .section-header h3 {
            font-family: var(--font-display);
            font-size: 18px;
            color: var(--mana-white);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary { background: var(--mana-red); color: var(--mana-white); }
        .btn-primary:hover { background: #b01010; }
        .btn-yellow { background: var(--mana-yellow); color: var(--mana-black); }
        .btn-yellow:hover { background: #e5a518; }
        .btn-ghost { background: transparent; color: var(--mana-text-muted); border:1px solid rgba(255,255,255,0.1); }
        .btn-ghost:hover { border-color:var(--mana-text-muted); color:var(--mana-white); }
        .btn-sm { padding: 6px 12px; font-size: 11px; }
        .btn-danger { background: var(--mana-danger); color: white; }
        .btn-danger:hover { background: #dc2626; }

        .table-container {
            background: var(--mana-gray);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
        }

        table { width:100%; border-collapse:collapse; }

        table thead { background: rgba(255,255,255,0.03); }

        table th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--mana-text-muted);
            font-weight: 700;
        }

        table td {
            padding: 14px 20px;
            font-size: 13px;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            vertical-align: middle;
        }

        table tr:last-child td { border-bottom:none; }
        table tr:hover td { background: rgba(255,255,255,0.02); }

        .prod-img {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--mana-gray-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--mana-text-muted);
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-oferta { background: rgba(255,184,28,0.15); color: var(--mana-yellow); }
        .badge-normal { background: rgba(255,255,255,0.08); color: var(--mana-text-muted); }
        .badge-success { background: rgba(34,197,94,0.15); color: var(--mana-success); }
        .badge-danger { background: rgba(239,68,68,0.1); color: var(--mana-danger); }

        .toggle-switch {
            position: relative;
            width: 40px;
            height: 22px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-block;
        }

        .toggle-switch.active { background: var(--mana-success); }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .toggle-switch.active::after { transform: translateX(18px); }

        .actions { display:flex; gap:6px; }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--mana-gray);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.08);
            width: 100%;
            max-width: 580px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 0;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .modal-header h3 {
            font-family: var(--font-display);
            font-size: 16px;
            color: var(--mana-white);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--mana-text-muted);
            font-size: 22px;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
        }

        .modal-close:hover { color: var(--mana-white); }

        .modal-body { padding: 24px; }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--mana-text);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            background: var(--mana-black);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--mana-white);
            font-family: var(--font-body);
            font-size: 13px;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--mana-yellow);
        }

        .form-group textarea { min-height: 80px; resize: vertical; }

        .form-group select { cursor: pointer; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--mana-red);
            cursor: pointer;
        }

        .form-check label {
            font-size: 13px;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
            cursor: pointer;
            margin: 0;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .precio-oferta-preview {
            padding: 10px 14px;
            background: rgba(255,184,28,0.1);
            border: 1px solid rgba(255,184,28,0.2);
            border-radius: 8px;
            color: var(--mana-yellow);
            font-weight: 700;
            font-size: 14px;
            text-align: center;
        }

        .text-muted { color: var(--mana-text-muted); }

        /* ---- MOBILE ---- */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:90; }

        @media (max-width:768px) {
            .menu-toggle { display:block; }
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.active { display:block; }
            .main { margin-left:0; }
            .content { padding:20px 16px; }
            .topbar { padding:12px 16px; }
            .form-row { grid-template-columns:1fr; }
            table { font-size:12px; }
            table th, table td { padding:10px 12px; }
            .section-header { flex-direction:column; align-items:stretch; gap:12px; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="../../images/logo-mana.jpeg" alt="Maná" class="sidebar-logo" height="38">
        <div><h1>MANÁ</h1><small>Fast Food · Admin</small></div>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php"><span class="icon">📊</span> Dashboard</a>
        <a href="productos.php" class="active"><span class="icon">🍔</span> Productos</a>
        <a href="pedidos.php"><span class="icon">📋</span> Pedidos</a>
        <a href="pagos.php"><span class="icon">💳</span> PagoMóvil</a>
        <a href="tasa.php"><span class="icon">💱</span> Tasa BCV</a>
        <a href="ofertas.php"><span class="icon">🏷️</span> Ofertas</a>
    </nav>
    <div class="sidebar-footer">
        <a href="../index.php" target="_blank">← Ver sitio web</a>
        <a href="#" onclick="event.preventDefault(); if(typeof Auth!=='undefined' && Auth.logout) { Auth.logout(); } else { window.location.href='../logout.php'; }" style="margin-top:8px;">🚪 Cerrar sesión</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">☰</button>
            <h2>Productos</h2>
        </div>
        <div class="topbar-right"></div>
    </header>

    <div class="content">
        <div class="section-header">
            <h3>🍔 Todos los productos</h3>
            <button class="btn btn-primary" onclick="abrirModal()">+ NUEVO PRODUCTO</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio ($)</th>
                        <th>Desc.</th>
                        <th>Tipo</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr data-id="<?php echo $p['id']; ?>">
                        <td>
                            <div class="prod-img">🍔</div>
                        </td>
                        <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                        <td class="text-muted"><?php echo $p['categoria']; ?></td>
                        <td><strong>$<?php echo number_format($p['precio'], 2); ?></strong></td>
                        <td><?php echo $p['descuento'] > 0 ? '<span class="badge badge-oferta">-'.$p['descuento'].'%</span>' : '<span class="text-muted">—</span>'; ?></td>
                        <td><span class="badge badge-<?php echo $p['tipo']; ?>"><?php echo $p['tipo']; ?></span></td>
                        <td>
                            <span class="toggle-switch <?php echo $p['activo'] ? 'active' : ''; ?>" onclick="this.classList.toggle('active')"></span>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-ghost btn-sm" onclick="abrirModal(<?php echo $p['id']; ?>)">✏️</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>')">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL PRODUCTO -->
<div class="modal-overlay" id="modalProducto">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Nuevo Producto</h3>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="modal-body">
            <form id="productoForm">
                <input type="hidden" id="productoId" value="">

                <div class="form-group">
                    <label>Nombre del producto</label>
                    <input type="text" id="prodNombre" placeholder="Ej: Maná Burger Clásica" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Categoría</label>
                        <select id="prodCategoria">
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Precio (USD)</label>
                        <input type="number" id="prodPrecio" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción / Ingredientes</label>
                    <textarea id="prodDescripcion" placeholder="Describe los ingredientes del producto..."></textarea>
                </div>

                <div class="form-group">
                    <label>URL de imagen</label>
                    <input type="text" id="prodImagen" placeholder="https://ejemplo.com/imagen.jpg">
                </div>

                <div class="form-row">
                    <div class="form-check">
                        <input type="checkbox" id="prodActivo" checked>
                        <label for="prodActivo">Producto activo</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="prodOferta" onchange="toggleOferta()">
                        <label for="prodOferta">En oferta</label>
                    </div>
                </div>

                <div id="ofertaFields" style="display:none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Descuento (%)</label>
                            <input type="number" id="prodDescuento" step="0.01" min="0" max="100" placeholder="0" oninput="calcularPrecioOferta()">
                        </div>
                        <div class="form-group">
                            <label>Precio de oferta</label>
                            <div class="precio-oferta-preview" id="precioOfertaPreview">$0.00</div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarModal()">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarProducto()">Guardar Producto</button>
        </div>
    </div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal-overlay" id="modalEliminar">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <h3>¿Eliminar producto?</h3>
            <button class="modal-close" onclick="cerrarModalEliminar()">✕</button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px; color:var(--mana-text);">¿Estás seguro de eliminar <strong id="eliminarNombre"></strong>?</p>
            <p style="font-size:12px; color:var(--mana-text-muted); margin-top:8px;">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="eliminarId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarModalEliminar()">Cancelar</button>
            <button class="btn btn-danger" onclick="eliminarProducto()">Sí, eliminar</button>
        </div>
    </div>
</div>

<script>
// Sidebar toggle
document.getElementById('menuToggle').addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});
document.getElementById('sidebarOverlay').addEventListener('click', function(){
    document.getElementById('sidebar').classList.remove('open');
    this.classList.remove('active');
});

// Datos de productos (simulados del backend)
const productosData = <?php echo json_encode($productos); ?>;

function abrirModal(id) {
    const modal = document.getElementById('modalProducto');
    const form = document.getElementById('productoForm');
    form.reset();
    document.getElementById('productoId').value = '';
    document.getElementById('ofertaFields').style.display = 'none';
    document.getElementById('prodOferta').checked = false;
    document.getElementById('modalTitle').textContent = 'Nuevo Producto';

    if (id) {
        document.getElementById('modalTitle').textContent = 'Editar Producto';
        const prod = productosData.find(p => p.id === id);
        if (prod) {
            document.getElementById('productoId').value = prod.id;
            document.getElementById('prodNombre').value = prod.nombre;
            document.getElementById('prodCategoria').value = prod.categoria;
            document.getElementById('prodPrecio').value = prod.precio;
            document.getElementById('prodImagen').value = prod.imagen;
            document.getElementById('prodActivo').checked = prod.activo;
            if (prod.tipo === 'oferta') {
                document.getElementById('prodOferta').checked = true;
                document.getElementById('ofertaFields').style.display = 'block';
                document.getElementById('prodDescuento').value = prod.descuento;
                calcularPrecioOferta();
            }
        }
    }

    modal.classList.add('open');
}

function cerrarModal() {
    document.getElementById('modalProducto').classList.remove('open');
}

function toggleOferta() {
    const ofertaFields = document.getElementById('ofertaFields');
    ofertaFields.style.display = document.getElementById('prodOferta').checked ? 'block' : 'none';
    if (!document.getElementById('prodOferta').checked) {
        document.getElementById('prodDescuento').value = '';
        document.getElementById('precioOfertaPreview').textContent = '$0.00';
    }
}

function calcularPrecioOferta() {
    const precio = parseFloat(document.getElementById('prodPrecio').value) || 0;
    const descuento = parseFloat(document.getElementById('prodDescuento').value) || 0;
    const precioOferta = precio * (1 - descuento / 100);
    document.getElementById('precioOfertaPreview').textContent = '$' + precioOferta.toFixed(2);
}

// Recalcular cuando cambie el precio base
document.getElementById('prodPrecio').addEventListener('input', calcularPrecioOferta);

function guardarProducto() {
    const data = {
        id: document.getElementById('productoId').value,
        nombre: document.getElementById('prodNombre').value,
        categoria: document.getElementById('prodCategoria').value,
        precio: parseFloat(document.getElementById('prodPrecio').value),
        descripcion: document.getElementById('prodDescripcion').value,
        imagen: document.getElementById('prodImagen').value,
        activo: document.getElementById('prodActivo').checked,
        en_oferta: document.getElementById('prodOferta').checked,
        descuento: parseFloat(document.getElementById('prodDescuento').value) || 0
    };

    if (!data.nombre || !data.precio) {
        alert('Completa los campos obligatorios: nombre y precio.');
        return;
    }

    console.log('Guardando producto:', data);
    // Aquí iría POST a /api/admin/productos
    alert('Producto guardado correctamente.');
    cerrarModal();
}

let eliminarId = null;

function confirmarEliminar(id, nombre) {
    document.getElementById('eliminarId').value = id;
    document.getElementById('eliminarNombre').textContent = nombre;
    document.getElementById('modalEliminar').classList.add('open');
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').classList.remove('open');
}

function eliminarProducto() {
    const id = document.getElementById('eliminarId').value;
    console.log('Eliminando producto:', id);
    // Aquí iría DELETE a /api/admin/productos/{id}
    alert('Producto eliminado correctamente.');
    cerrarModalEliminar();
}
</script>
</body>
</html>


