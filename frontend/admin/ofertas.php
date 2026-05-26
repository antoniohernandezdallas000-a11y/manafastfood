<?php
// Sesion desactivada para desarrollo frontend
// En produccion conectar con backend/api/admin/auth

$ofertas = [
    ['id' => 1, 'nombre' => 'Happy Hour Maná', 'tipo' => 'porcentaje', 'productos' => 'Maná Burger Clásica, Hot Dog Maná Supreme', 'descuento' => 15, 'inicio' => '20/05/2026', 'fin' => '27/05/2026', 'activa' => true],
    ['id' => 2, 'nombre' => 'Combo Familiar', 'tipo' => 'combo', 'productos' => '2x Maná Burger, 2x Papas, 2x Bebidas', 'descuento' => 20, 'inicio' => '01/05/2026', 'fin' => '31/05/2026', 'activa' => true],
    ['id' => 3, 'nombre' => 'Lunes de Enrollados', 'tipo' => 'precio_fijo', 'productos' => 'Enrollado de Pepperoni', 'descuento' => 10, 'inicio' => '01/05/2026', 'fin' => '30/06/2026', 'activa' => false],
];

$productos_disponibles = [
    'Maná Burger Clásica',
    'Maná Doble Carne',
    'Maná BBQ Bacon',
    'Maná Chicken Crispy',
    'Hot Dog Maná Supreme',
    'Enrollado de Pepperoni',
    'Enrollado de Jamón y Queso',
    'Pepito de Pollo',
    'Pepito de Res',
    'Papas Maná Cheddar',
    'Papas Maná Bacon',
    'Extra Queso',
    'Extra Tocineta',
    'Coca-Cola 500ml',
    'Pepsi 500ml',
    'Agua Mineral',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas · Maná Fast Food</title>
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

        .sidebar-brand { padding:24px 20px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:12px; }
        .sidebar-logo { width:40px; height:40px; border-radius:8px; object-fit:contain; flex-shrink:0; }
        .sidebar-brand h1 { font-family:var(--font-title); font-size:18px; color:var(--mana-white); letter-spacing:1px; line-height:1.1; }
        .sidebar-brand small { display:block; font-family:var(--font-body); font-size:9px; color:var(--mana-text-muted); text-transform:uppercase; letter-spacing:2px; }
        .sidebar-nav { padding:16px 12px; flex:1; display:flex; flex-direction:column; gap:2px; }
        .sidebar-nav a { display:flex; align-items:center; gap:12px; padding:12px 16px; color:var(--mana-text-muted); text-decoration:none; border-radius:8px; font-size:13px; font-weight:500; transition:all 0.2s ease; }
        .sidebar-nav a:hover { background:rgba(255,255,255,0.05); color:var(--mana-white); }
        .sidebar-nav a.active { background:var(--mana-red); color:var(--mana-white); font-weight:700; }
        .sidebar-nav a .icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
        .sidebar-footer { padding:16px 20px; border-top:1px solid rgba(255,255,255,0.05); }
        .sidebar-footer a { display:flex; align-items:center; gap:10px; color:var(--mana-text-muted); text-decoration:none; font-size:12px; transition:color 0.2s; }
        .sidebar-footer a:hover { color:var(--mana-danger); }

        .main { flex:1; margin-left:var(--sidebar-width); }

        .topbar { display:flex; align-items:center; justify-content:space-between; padding:16px 32px; border-bottom:1px solid rgba(255,255,255,0.05); background:rgba(17,17,17,0.95); backdrop-filter:blur(12px); position:sticky; top:0; z-index:50; }
        .topbar-left { display:flex; align-items:center; gap:16px; }
        .menu-toggle { display:none; background:none; border:none; color:var(--mana-white); font-size:24px; cursor:pointer; padding:4px; }
        .topbar h2 { font-family:var(--font-display); font-size:20px; color:var(--mana-white); letter-spacing:0.5px; }
        .content { padding:32px; }

        .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
        .section-header h3 { font-family:var(--font-display); font-size:18px; color:var(--mana-white); }

        .btn {
            display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:8px;
            font-family:var(--font-body); font-size:13px; font-weight:700; border:none; cursor:pointer; text-decoration:none; transition:all 0.2s ease;
        }
        .btn-primary { background:var(--mana-red); color:var(--mana-white); }
        .btn-primary:hover { background:#b01010; }
        .btn-ghost { background:transparent; color:var(--mana-text-muted); border:1px solid rgba(255,255,255,0.1); }
        .btn-ghost:hover { border-color:var(--mana-text-muted); color:var(--mana-white); }
        .btn-sm { padding:6px 12px; font-size:11px; }
        .btn-danger { background:var(--mana-danger); color:white; }
        .btn-danger:hover { background:#dc2626; }

        .table-container { background:var(--mana-gray); border-radius:16px; border:1px solid rgba(255,255,255,0.05); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        table thead { background:rgba(255,255,255,0.03); }
        table th { padding:14px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--mana-text-muted); font-weight:700; }
        table td { padding:14px 20px; font-size:13px; border-bottom:1px solid rgba(255,255,255,0.03); vertical-align:middle; }
        table tr:last-child td { border-bottom:none; }
        table tr:hover td { background:rgba(255,255,255,0.02); }

        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; }
        .badge-porcentaje { background:rgba(255,184,28,0.15); color:var(--mana-yellow); }
        .badge-precio_fijo { background:rgba(59,130,246,0.15); color:var(--mana-info); }
        .badge-combo { background:rgba(211,18,18,0.15); color:var(--mana-red); }
        .badge-activa { background:rgba(34,197,94,0.15); color:var(--mana-success); }
        .badge-inactiva { background:rgba(255,255,255,0.05); color:var(--mana-text-muted); }

        .toggle-switch { position:relative; width:40px; height:22px; background:rgba(255,255,255,0.1); border-radius:20px; cursor:pointer; transition:background 0.2s; display:inline-block; }
        .toggle-switch.active { background:var(--mana-success); }
        .toggle-switch::after { content:''; position:absolute; top:2px; left:2px; width:18px; height:18px; background:white; border-radius:50%; transition:transform 0.2s; }
        .toggle-switch.active::after { transform:translateX(18px); }

        .actions { display:flex; gap:6px; }
        .text-muted { color:var(--mana-text-muted); }

        /* MODAL */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:200; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }
        .modal { background:var(--mana-gray); border-radius:20px; border:1px solid rgba(255,255,255,0.08); width:100%; max-width:560px; max-height:90vh; overflow-y:auto; }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid rgba(255,255,255,0.05); }
        .modal-header h3 { font-family:var(--font-display); font-size:16px; color:var(--mana-white); }
        .modal-close { background:none; border:none; color:var(--mana-text-muted); font-size:22px; cursor:pointer; padding:4px; line-height:1; }
        .modal-close:hover { color:var(--mana-white); }
        .modal-body { padding:24px; }
        .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:16px 24px; border-top:1px solid rgba(255,255,255,0.05); }

        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-size:12px; font-weight:700; color:var(--mana-text); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
        .form-group input, .form-group select {
            width:100%; padding:10px 14px; background:var(--mana-black); border:1px solid rgba(255,255,255,0.1);
            border-radius:8px; color:var(--mana-white); font-family:var(--font-body); font-size:13px; transition:border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus { outline:none; border-color:var(--mana-yellow); }
        .form-group select[multiple] { min-height:120px; }
        .form-group select[multiple] option { padding:6px 8px; }
        .form-group select[multiple] option:checked { background:var(--mana-red); color:white; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-check { display:flex; align-items:center; gap:10px; padding:10px 0; }
        .form-check input[type="checkbox"] { width:18px; height:18px; accent-color:var(--mana-red); cursor:pointer; }
        .form-check label { font-size:13px; text-transform:none; letter-spacing:0; font-weight:500; cursor:pointer; margin:0; }

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
            table th, table td { padding:10px 12px; }
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
        <a href="productos.php"><span class="icon">🍔</span> Productos</a>
        <a href="pedidos.php"><span class="icon">📋</span> Pedidos</a>
        <a href="pagos.php"><span class="icon">💳</span> PagoMóvil</a>
        <a href="tasa.php"><span class="icon">💱</span> Tasa BCV</a>
        <a href="ofertas.php" class="active"><span class="icon">🏷️</span> Ofertas</a>
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
            <h2>Ofertas</h2>
        </div>
        <div class="topbar-right"></div>
    </header>

    <div class="content">
        <div class="section-header">
            <h3>🏷️ Ofertas y Promociones</h3>
            <button class="btn btn-primary" onclick="abrirModal()">+ NUEVA OFERTA</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Producto(s)</th>
                        <th>Dto. %</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Activa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ofertas as $o): ?>
                    <tr data-id="<?php echo $o['id']; ?>">
                        <td><strong><?php echo htmlspecialchars($o['nombre']); ?></strong></td>
                        <td><span class="badge badge-<?php echo $o['tipo']; ?>"><?php echo $o['tipo'] === 'precio_fijo' ? 'Precio Fijo' : ucfirst($o['tipo']); ?></span></td>
                        <td class="text-muted" style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($o['productos']); ?></td>
                        <td><strong><?php echo $o['descuento']; ?>%</strong></td>
                        <td class="text-muted"><?php echo $o['inicio']; ?></td>
                        <td class="text-muted"><?php echo $o['fin']; ?></td>
                        <td>
                            <span class="toggle-switch <?php echo $o['activa'] ? 'active' : ''; ?>" onclick="this.classList.toggle('active'); toggleOferta(<?php echo $o['id']; ?>)"></span>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-ghost btn-sm" onclick="abrirModal(<?php echo $o['id']; ?>)">✏️</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?php echo $o['id']; ?>, '<?php echo htmlspecialchars($o['nombre'], ENT_QUOTES); ?>')">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL OFERTA -->
<div class="modal-overlay" id="modalOferta">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Oferta</h3>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="modal-body">
            <form id="ofertaForm">
                <input type="hidden" id="ofertaId" value="">

                <div class="form-group">
                    <label>Nombre de la oferta</label>
                    <input type="text" id="ofertaNombre" placeholder="Ej: Happy Hour Maná" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo de oferta</label>
                        <select id="ofertaTipo" onchange="cambiarTipo()">
                            <option value="porcentaje">Descuento por porcentaje (%)</option>
                            <option value="precio_fijo">Precio fijo</option>
                            <option value="combo">Combo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descuento (%)</label>
                        <input type="number" id="ofertaDescuento" step="0.01" min="0" max="100" placeholder="0">
                    </div>
                </div>

                <div class="form-group">
                    <label>Productos asociados</label>
                    <select id="ofertaProductos" multiple>
                        <?php foreach ($productos_disponibles as $prod): ?>
                        <option value="<?php echo htmlspecialchars($prod); ?>"><?php echo htmlspecialchars($prod); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color:var(--mana-text-muted); font-size:11px;">Mantén presionado Ctrl (Windows) / Cmd (Mac) para seleccionar múltiples productos</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha de inicio</label>
                        <input type="date" id="ofertaInicio">
                    </div>
                    <div class="form-group">
                        <label>Fecha de fin</label>
                        <input type="date" id="ofertaFin">
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="ofertaActiva" checked>
                    <label for="ofertaActiva">Oferta activa</label>
                </div>

                <div style="background:rgba(255,184,28,0.08); border:1px solid rgba(255,184,28,0.15); border-radius:8px; padding:12px 16px; font-size:12px; color:var(--mana-yellow);">
                    ⚡ Las ofertas activas se reflejan automáticamente en los precios del menú público.
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarModal()">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarOferta()">Guardar Oferta</button>
        </div>
    </div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal-overlay" id="modalEliminar">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <h3>¿Eliminar oferta?</h3>
            <button class="modal-close" onclick="cerrarModalEliminar()">✕</button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px; color:var(--mana-text);">¿Estás seguro de eliminar <strong id="eliminarNombre"></strong>?</p>
            <p style="font-size:12px; color:var(--mana-text-muted); margin-top:8px;">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="eliminarId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarModalEliminar()">Cancelar</button>
            <button class="btn btn-danger" onclick="eliminarOferta()">Sí, eliminar</button>
        </div>
    </div>
</div>

<script>
const ofertasData = <?php echo json_encode($ofertas); ?>;

document.getElementById('menuToggle').addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});
document.getElementById('sidebarOverlay').addEventListener('click', function(){
    document.getElementById('sidebar').classList.remove('open');
    this.classList.remove('active');
});

function abrirModal(id) {
    const modal = document.getElementById('modalOferta');
    document.getElementById('ofertaForm').reset();
    document.getElementById('ofertaId').value = '';
    document.getElementById('modalTitle').textContent = 'Nueva Oferta';
    document.getElementById('ofertaActiva').checked = true;

    if (id) {
        document.getElementById('modalTitle').textContent = 'Editar Oferta';
        const o = ofertasData.find(x => x.id === id);
        if (o) {
            document.getElementById('ofertaId').value = o.id;
            document.getElementById('ofertaNombre').value = o.nombre;
            document.getElementById('ofertaTipo').value = o.tipo;
            document.getElementById('ofertaDescuento').value = o.descuento;
            document.getElementById('ofertaActiva').checked = o.activa;
            // Parse dates if they exist
            if (o.inicio) {
                const [d, m, y] = o.inicio.split('/');
                document.getElementById('ofertaInicio').value = `${y}-${m}-${d}`;
            }
            if (o.fin) {
                const [d, m, y] = o.fin.split('/');
                document.getElementById('ofertaFin').value = `${y}-${m}-${d}`;
            }
            // Select products
            const select = document.getElementById('ofertaProductos');
            const prods = o.productos.split(', ');
            for (let opt of select.options) {
                if (prods.includes(opt.value)) {
                    opt.selected = true;
                }
            }
        }
    }

    modal.classList.add('open');
}

function cerrarModal() {
    document.getElementById('modalOferta').classList.remove('open');
}

function cambiarTipo() {
    // UI hint based on type
    const tipo = document.getElementById('ofertaTipo').value;
    const descLabel = document.querySelector('.form-group:has(#ofertaDescuento) label');
    if (tipo === 'precio_fijo') {
        descLabel.textContent = 'PRECIO FIJO ($)';
    } else {
        descLabel.textContent = 'DESCUENTO (%)';
    }
}

function guardarOferta() {
    const select = document.getElementById('ofertaProductos');
    const selected = Array.from(select.selectedOptions).map(o => o.value);

    const data = {
        id: document.getElementById('ofertaId').value,
        nombre: document.getElementById('ofertaNombre').value,
        tipo: document.getElementById('ofertaTipo').value,
        descuento: parseFloat(document.getElementById('ofertaDescuento').value) || 0,
        productos: selected,
        inicio: document.getElementById('ofertaInicio').value,
        fin: document.getElementById('ofertaFin').value,
        activa: document.getElementById('ofertaActiva').checked
    };

    if (!data.nombre || selected.length === 0) {
        alert('Completa el nombre y selecciona al menos un producto.');
        return;
    }

    console.log('Guardando oferta:', data);
    // POST a /api/admin/ofertas
    alert('Oferta guardada correctamente. Los cambios se reflejarán en el menú público.');
    cerrarModal();
}

function toggleOferta(id) {
    console.log('Toggle oferta #' + id);
    // POST a /api/admin/ofertas/{id}/toggle
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

function eliminarOferta() {
    const id = document.getElementById('eliminarId').value;
    console.log('Eliminando oferta #' + id);
    // DELETE a /api/admin/ofertas/{id}
    alert('Oferta eliminada correctamente.');
    cerrarModalEliminar();
}
</script>
</body>
</html>


