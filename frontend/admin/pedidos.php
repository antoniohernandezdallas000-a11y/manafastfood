<?php
$estados_disponibles = ['pendiente', 'confirmado', 'preparando', 'listo', 'entregado', 'cancelado'];
$filtro_actual = isset($_GET['estado']) ? $_GET['estado'] : 'todos';

// Detectar base path para API
$base_path = '/mana-fast-food';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos · Maná Fast Food</title>
    <link rel="icon" type="image/png" href="../img/mana-icon.png">
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

        /* SIDEBAR */
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

        /* FILTERS */
        .filters {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 18px;
            border-radius: 20px;
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.1);
            background: transparent;
            color: var(--mana-text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .filter-btn:hover { border-color: var(--mana-text-muted); color: var(--mana-white); }

        .filter-btn.active { background: var(--mana-red); color: var(--mana-white); border-color: var(--mana-red); }

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
        .btn-success { background: var(--mana-success); color: white; }
        .btn-success:hover { background: #16a34a; }
        .btn-danger { background: var(--mana-danger); color: white; }
        .btn-danger:hover { background: #dc2626; }

        .table-container { background: var(--mana-gray); border-radius: 16px; border:1px solid rgba(255,255,255,0.05); overflow: hidden; }

        table { width:100%; border-collapse:collapse; }

        table thead { background: rgba(255,255,255,0.03); }

        table th { padding:14px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--mana-text-muted); font-weight:700; }

        table td { padding:14px 20px; font-size:13px; border-bottom:1px solid rgba(255,255,255,0.03); vertical-align:middle; }

        table tr:last-child td { border-bottom:none; }

        table tr:hover td { background: rgba(255,255,255,0.02); }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .badge-pendiente { background: rgba(245,158,11,0.15); color: var(--mana-warning); }
        .badge-confirmado { background: rgba(59,130,246,0.15); color: var(--mana-info); }
        .badge-preparando { background: rgba(211,18,18,0.15); color: var(--mana-red); }
        .badge-listo { background: rgba(255,184,28,0.15); color: var(--mana-yellow); }
        .badge-entregado { background: rgba(34,197,94,0.15); color: var(--mana-success); }
        .badge-cancelado { background: rgba(239,68,68,0.1); color: var(--mana-danger); }

        .estado-select {
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.1);
            background: var(--mana-black);
            color: var(--mana-text);
            font-family: var(--font-body);
            font-size: 12px;
            cursor: pointer;
        }

        .estado-select:focus { outline:none; border-color:var(--mana-yellow); }

        .text-muted { color: var(--mana-text-muted); }

        .actions { display:flex; gap:6px; }

        /* MODAL DETALLE */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:200; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }

        .modal { background:var(--mana-gray); border-radius:20px; border:1px solid rgba(255,255,255,0.08); width:100%; max-width:640px; max-height:90vh; overflow-y:auto; }

        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid rgba(255,255,255,0.05); }
        .modal-header h3 { font-family:var(--font-display); font-size:16px; color:var(--mana-white); }
        .modal-close { background:none; border:none; color:var(--mana-text-muted); font-size:22px; cursor:pointer; padding:4px; line-height:1; }
        .modal-close:hover { color:var(--mana-white); }

        .modal-body { padding:24px; }

        .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }

        .detail-item { }
        .detail-item label { display:block; font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--mana-text-muted); font-weight:700; margin-bottom:4px; }
        .detail-item span { font-size:14px; color:var(--mana-white); font-weight:500; }

        .detail-section { margin-bottom:20px; }
        .detail-section h4 { font-family:var(--font-display); font-size:14px; color:var(--mana-yellow); margin-bottom:12px; }

        .item-list { list-style:none; }
        .item-list li { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:13px; }
        .item-list li:last-child { border-bottom:none; }

        .modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:16px 24px; border-top:1px solid rgba(255,255,255,0.05); }

        .capture-img { max-width:100%; max-height:200px; border-radius:8px; margin-top:8px; }

        /* MOBILE */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:90; }

        @media (max-width:768px) {
            .menu-toggle { display:block; }
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.active { display:block; }
            .main { margin-left:0; }
            .content { padding:20px 16px; }
            .topbar { padding:12px 16px; }
            .detail-grid { grid-template-columns:1fr; }
            table { font-size:12px; }
            table th, table td { padding:10px 12px; }
            .filters { gap:6px; }
            .filter-btn { padding:6px 14px; font-size:11px; }
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
        <a href="pedidos.php" class="active"><span class="icon">📋</span> Pedidos</a>
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
            <h2>Pedidos</h2>
        </div>
        <div class="topbar-right"></div>
    </header>

    <div class="content">
        <!-- FILTROS -->
        <div class="filters">
            <?php
            $estados = ['todos' => 'Todos', 'pendiente' => 'Pendientes', 'confirmado' => 'Confirmados', 'preparando' => 'Preparando', 'listo' => 'Listos', 'entregado' => 'Entregados', 'cancelado' => 'Cancelados'];
            foreach ($estados as $key => $label):
                $active = ($filtro_actual === $key) ? 'active' : '';
                $url = $key === 'todos' ? 'pedidos.php' : 'pedidos.php?estado=' . $key;
            ?>
            <a href="<?php echo $url; ?>" class="filter-btn <?php echo $active; ?>"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </div>

        <!-- TABLA -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Items</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="pedidosTableBody">
                    <!-- Se llena dinámicamente con JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL DETALLE -->
<div class="modal-overlay" id="modalDetalle">
    <div class="modal">
        <div class="modal-header">
            <h3>Pedido <span id="detalleId"></span></h3>
            <button class="modal-close" onclick="cerrarDetalle()">✕</button>
        </div>
        <div class="modal-body" id="detalleBody">
            <!-- Se llena con JS -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarDetalle()">Cerrar</button>
        </div>
    </div>
</div>

<script>
const BASE_PATH = '/mana-fast-food';
const API_BASE = BASE_PATH + '/backend/api';
const ESTADOS = ['pendiente', 'confirmado', 'preparando', 'listo', 'entregado', 'cancelado'];
const MAP_BG = { pendiente:'rgba(245,158,11,0.1)', confirmado:'rgba(59,130,246,0.1)', preparando:'rgba(211,18,18,0.1)', listo:'rgba(255,184,28,0.1)', entregado:'rgba(34,197,94,0.1)', cancelado:'rgba(239,68,68,0.1)' };
const MAP_COLOR = { pendiente:'var(--mana-warning)', confirmado:'var(--mana-info)', preparando:'var(--mana-red)', listo:'var(--mana-yellow)', entregado:'var(--mana-success)', cancelado:'var(--mana-danger)' };

// --- UTIL: obtener token ---
function getToken() {
    return localStorage.getItem('auth_token');
}

function getAuthHeaders() {
    var t = getToken();
    return t ? { 'Authorization': 'Bearer ' + t, 'Content-Type': 'application/json' } : { 'Content-Type': 'application/json' };
}

// --- CARGAR PEDIDOS DESDE API ---
var pedidosData = [];

function cargarPedidos() {
    var tbody = document.getElementById('pedidosTableBody');
    if (!tbody) return;

    fetch(API_BASE + '/pedidos/listar.php', { headers: getAuthHeaders() })
        .then(function(r) {
            if (!r.ok) throw new Error('Error al cargar pedidos');
            return r.json();
        })
        .then(function(resp) {
            pedidosData = resp.data || [];
            renderPedidos();
        })
        .catch(function(err) {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--mana-text-muted);">❌ Error al cargar pedidos. ¿Has iniciado sesión?</td></tr>';
        });
}

// --- RENDERIZAR TABLA ---
function renderPedidos() {
    var tbody = document.getElementById('pedidosTableBody');
    if (!tbody) return;

    var filtro = obtenerFiltro();
    var filtrados = filtro === 'todos' ? pedidosData : pedidosData.filter(function(p) { return p.estado === filtro; });

    if (filtrados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--mana-text-muted);">No hay pedidos ' + (filtro !== 'todos' ? filtro : '') + '</td></tr>';
        return;
    }

    var html = '';
    filtrados.forEach(function(p) {
        var tipoEntrega = p.tipo_entrega === 'delivery' ? 'Delivery' : 'Retiro';
        var items = p.items || (p.detalles ? p.detalles.length + ' producto(s)' : '—');
        var total = parseFloat(p.total_usd || p.total || 0);
        var fecha = p.created_at || p.fecha || '';
        var bg = MAP_BG[p.estado] || 'transparent';
        var color = MAP_COLOR[p.estado] || 'var(--mana-text)';

        html += '<tr>';
        html += '<td><strong>#' + p.id + '</strong></td>';
        html += '<td><strong>' + (p.usuario_nombre || p.cliente || '—') + '</strong><br><span class="text-muted" style="font-size:11px;">' + (p.telefono_contacto || p.usuario_telefono || p.telefono || '') + '</span></td>';
        html += '<td class="text-muted" style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + items + '</td>';
        html += '<td><span class="badge badge-' + (tipoEntrega === 'Delivery' ? 'preparando' : 'confirmado') + '" style="font-size:10px;">' + tipoEntrega + '</span></td>';
        html += '<td><strong>$' + total.toFixed(2) + '</strong></td>';
        html += '<td class="text-muted" style="font-size:11px;">' + fecha + '</td>';
        html += '<td><select class="estado-select" onchange="cambiarEstado(' + p.id + ', this.value)" style="background:' + bg + ';color:' + color + ';">';
        ESTADOS.forEach(function(e) {
            html += '<option value="' + e + '"' + (e === p.estado ? ' selected' : '') + '>' + e.charAt(0).toUpperCase() + e.slice(1) + '</option>';
        });
        html += '</select></td>';
        html += '<td><div class="actions">';
        html += '<button class="btn btn-ghost btn-sm" onclick="verDetalle(' + p.id + ')">👁️</button>';
        if (p.estado === 'preparando') {
            html += '<button class="btn btn-yellow btn-sm" onclick="marcarEstado(' + p.id + ', \'listo\')">✅ Listo</button>';
        } else if (p.estado === 'listo' && p.tipo_entrega === 'delivery') {
            html += '<button class="btn btn-success btn-sm" onclick="marcarEstado(' + p.id + ', \'entregado\')">📦 Entregado</button>';
        }
        html += '</div></td>';
        html += '</tr>';
    });

    tbody.innerHTML = html;
}

function obtenerFiltro() {
    var params = new URLSearchParams(window.location.search);
    return params.get('estado') || 'todos';
}

// --- CAMBIAR ESTADO VÍA API ---
function cambiarEstado(id, estado) {
    fetch(API_BASE + '/pedidos/actualizar-estado.php', {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify({ id: id, estado: estado })
    })
    .then(function(r) { return r.json(); })
    .then(function(resp) {
        if (resp.success) {
            // Actualizar en memoria
            pedidosData.forEach(function(p) {
                if (p.id === id) p.estado = estado;
            });
            renderPedidos();
            mostrarToast('Pedido #' + id + ' → ' + estado, 'success');
        } else {
            mostrarToast('Error: ' + (resp.message || 'desconocido'), 'error');
        }
    })
    .catch(function(err) {
        mostrarToast('Error de conexión', 'error');
    });
}

function marcarEstado(id, estado) {
    cambiarEstado(id, estado);
}

// --- TOAST ---
function mostrarToast(msg, tipo) {
    var t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 24px;border-radius:8px;font-family:Montserrat,sans-serif;font-size:13px;font-weight:700;z-index:999;animation:fadeIn 0.3s ease;';
    t.style.background = tipo === 'success' ? 'var(--mana-success,#22c55e)' : 'var(--mana-danger,#ef4444)';
    t.style.color = '#fff';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function() { t.style.opacity = '0'; t.style.transition = 'opacity 0.5s'; setTimeout(function() { t.remove(); }, 500); }, 2500);
}

// --- MODAL DETALLE ---
function verDetalle(id) {
    var pedido = null;
    for (var i = 0; i < pedidosData.length; i++) {
        if (pedidosData[i].id === id) { pedido = pedidosData[i]; break; }
    }
    if (!pedido) return;

    document.getElementById('detalleId').textContent = '#' + pedido.id;

    var items = (pedido.items || '').split(',').map(function(i) { return i.trim(); });
    var itemsHtml = '';
    if (items.length > 0 && items[0] !== '') {
        items.forEach(function(item) { itemsHtml += '<li><span>' + item + '</span></li>'; });
    } else {
        itemsHtml = '<li style="color:var(--mana-text-muted);">Sin detalle de productos</li>';
    }

    document.getElementById('detalleBody').innerHTML = [
        '<div class="detail-grid">',
            '<div class="detail-item"><label>Cliente</label><span>' + (pedido.usuario_nombre || pedido.cliente || '—') + '</span></div>',
            '<div class="detail-item"><label>Teléfono</label><span>' + (pedido.telefono_contacto || pedido.usuario_telefono || pedido.telefono || '—') + '</span></div>',
            '<div class="detail-item"><label>Tipo de entrega</label><span>' + (pedido.tipo_entrega === 'delivery' ? 'Delivery' : 'Retiro') + '</span></div>',
            '<div class="detail-item"><label>Total</label><span>$' + parseFloat(pedido.total_usd || pedido.total || 0).toFixed(2) + '</span></div>',
            '<div class="detail-item"><label>Referencia de pago</label><span>' + (pedido.referencia_pago || pedido.ref_pago || '<span class="text-muted">—</span>') + '</span></div>',
            '<div class="detail-item"><label>Estado</label><span><span class="badge badge-' + pedido.estado + '">' + pedido.estado + '</span></span></div>',
        '</div>'
    ].join('');

    if (pedido.direccion) {
        document.getElementById('detalleBody').innerHTML += [
            '<div class="detail-section"><h4>📍 Dirección de entrega</h4><p style="font-size:13px;color:var(--mana-text);">' + pedido.direccion + '</p></div>'
        ].join('');
    }

    document.getElementById('detalleBody').innerHTML += [
        '<div class="detail-section"><h4>🧾 Items del pedido</h4><ul class="item-list">' + itemsHtml + '</ul></div>'
    ].join('');

    if (pedido.notas) {
        document.getElementById('detalleBody').innerHTML += [
            '<div class="detail-section"><h4>📝 Notas</h4><p style="font-size:13px;color:var(--mana-text);">' + pedido.notas + '</p></div>'
        ].join('');
    }

    document.getElementById('detalleBody').innerHTML += [
        '<div class="detail-section"><h4>📸 Capture de pago</h4>',
        '<div style="width:100%;height:120px;background:rgba(255,255,255,0.03);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--mana-text-muted);font-size:13px;">',
            (pedido.referencia_pago || pedido.ref_pago) ? '🖼️ Imagen de referencia disponible' : 'Sin capture de pago',
        '</div></div>'
    ].join('');

    document.getElementById('modalDetalle').classList.add('open');
}

function cerrarDetalle() {
    document.getElementById('modalDetalle').classList.remove('open');
}

// Sidebar toggle
document.getElementById('menuToggle').addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});
document.getElementById('sidebarOverlay').addEventListener('click', function(){
    document.getElementById('sidebar').classList.remove('open');
    this.classList.remove('active');
});

// Iniciar: cargar pedidos automáticamente
document.addEventListener('DOMContentLoaded', cargarPedidos);
</script>
</body>
</html>



