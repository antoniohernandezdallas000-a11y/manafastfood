<?php
// Sesion desactivada para desarrollo frontend
// En produccion conectar con backend/api/admin/auth

$cuentas = [
    ['id' => 1, 'banco' => 'Mercantil', 'codigo' => '0105', 'titular' => 'Maná Fast Food C.A.', 'telefono' => '04141234567', 'cedula' => 'J-12345678-9', 'tipo' => 'corriente', 'activa' => true],
    ['id' => 2, 'banco' => 'BDV', 'codigo' => '0102', 'titular' => 'Maná Fast Food C.A.', 'telefono' => '04141234568', 'cedula' => 'J-12345678-9', 'tipo' => 'ahorro', 'activa' => false],
    ['id' => 3, 'banco' => 'Banesco', 'codigo' => '0134', 'titular' => 'Maná Fast Food C.A.', 'telefono' => '04141234569', 'cedula' => 'J-12345678-9', 'tipo' => 'corriente', 'activa' => false],
];

$bancos = [
    '0102' => 'Banco de Venezuela (BDV)',
    '0104' => 'Venezolano de Crédito',
    '0105' => 'Mercantil Banco',
    '0108' => 'Banco Provincial (BBVA)',
    '0114' => 'Bancaribe',
    '0115' => 'Banco Exterior',
    '0121' => 'Banco Industrial de Venezuela',
    '0128' => 'Banco Caroní',
    '0134' => 'Banesco',
    '0137' => 'Banco Sofitasa',
    '0138' => 'Banco del Tesoro',
    '0146' => 'Banco de la Gente',
    '0149' => 'Banco del Pueblo',
    '0151' => 'BFC Banco',
    '0156' => '100% Banco',
    '0157' => 'Del Sur Banco',
    '0163' => 'Bancrecer',
    '0168' => 'Banco Activo',
    '0171' => 'Banco Nacional de Crédito',
    '0172' => 'Bancamiga',
    '0174' => 'Banco Plaza',
    '0175' => 'Banco Bicentenario',
    '0176' => 'Nova Banco',
    '0177' => 'Banco Magisterio',
    '0182' => 'Banco Banplus',
    '0190' => 'Banco Mi Banco',
    '0191' => 'Banco Exterior (BOD)',
    '0601' => 'Instituto Municipal de Crédito',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PagoMóvil · Maná Fast Food</title>
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
        .btn-yellow { background:var(--mana-yellow); color:var(--mana-black); }
        .btn-yellow:hover { background:#e5a518; }
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

        .badge-activa { background:rgba(34,197,94,0.15); color:var(--mana-success); padding:3px 12px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; display:inline-block; }
        .badge-inactiva { background:rgba(255,255,255,0.05); color:var(--mana-text-muted); padding:3px 12px; border-radius:20px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; display:inline-block; }

        .toggle-switch { position:relative; width:40px; height:22px; background:rgba(255,255,255,0.1); border-radius:20px; cursor:pointer; transition:background 0.2s; display:inline-block; }
        .toggle-switch.active { background:var(--mana-success); }
        .toggle-switch::after { content:''; position:absolute; top:2px; left:2px; width:18px; height:18px; background:white; border-radius:50%; transition:transform 0.2s; }
        .toggle-switch.active::after { transform:translateX(18px); }

        .actions { display:flex; gap:6px; }

        .text-muted { color:var(--mana-text-muted); }

        .badge-banco { display:inline-block; padding:4px 10px; border-radius:6px; background:rgba(255,255,255,0.05); color:var(--mana-text); font-size:12px; font-weight:600; }

        /* MODAL */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:200; align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }
        .modal { background:var(--mana-gray); border-radius:20px; border:1px solid rgba(255,255,255,0.08); width:100%; max-width:520px; max-height:90vh; overflow-y:auto; }
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
        .form-group select { cursor:pointer; }

        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-check { display:flex; align-items:center; gap:10px; padding:10px 0; }
        .form-check input[type="checkbox"] { width:18px; height:18px; accent-color:var(--mana-red); cursor:pointer; }
        .form-check label { font-size:13px; text-transform:none; letter-spacing:0; font-weight:500; cursor:pointer; margin:0; }

        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:90; }

        /* ACTIVA HIGHLIGHT */
        .tr-active { border-left:3px solid var(--mana-success); }

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
        <a href="pagos.php" class="active"><span class="icon">💳</span> PagoMóvil</a>
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
            <h2>PagoMóvil</h2>
        </div>
        <div class="topbar-right"></div>
    </header>

    <div class="content">
        <div class="section-header">
            <h3>💳 Cuentas PagoMóvil</h3>
            <button class="btn btn-yellow" onclick="abrirModal()">+ NUEVA CUENTA</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Banco</th>
                        <th>Titular</th>
                        <th>Teléfono</th>
                        <th>Cédula/RIF</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cuentas as $c): ?>
                    <tr class="<?php echo $c['activa'] ? 'tr-active' : ''; ?>" data-id="<?php echo $c['id']; ?>">
                        <td><span class="badge-banco"><?php echo htmlspecialchars($c['banco']); ?></span></td>
                        <td><?php echo htmlspecialchars($c['titular']); ?></td>
                        <td><?php echo $c['telefono']; ?></td>
                        <td><?php echo $c['cedula']; ?></td>
                        <td>
                            <span class="badge-activa">● ACTIVA</span>
                            <span class="badge-inactiva">Inactiva</span>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-ghost btn-sm" onclick="abrirModal(<?php echo $c['id']; ?>)">✏️</button>
                                <button class="btn btn-sm" style="background:var(--mana-success);color:white;" onclick="activarCuenta(<?php echo $c['id']; ?>)">Activar</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['banco'], ENT_QUOTES); ?>')">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CUENTA -->
<div class="modal-overlay" id="modalCuenta">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Cuenta PagoMóvil</h3>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="modal-body">
            <form id="cuentaForm">
                <input type="hidden" id="cuentaId" value="">

                <div class="form-group">
                    <label>Banco</label>
                    <select id="cuentaBanco">
                        <option value="">Seleccionar banco...</option>
                        <?php foreach ($bancos as $cod => $nombre): ?>
                        <option value="<?php echo $cod; ?>"><?php echo $nombre; ?> (<?php echo $cod; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Titular (nombre completo o razón social)</label>
                    <input type="text" id="cuentaTitular" placeholder="Ej: Maná Fast Food C.A." required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono PagoMóvil</label>
                        <input type="text" id="cuentaTelefono" placeholder="04141234567" required>
                    </div>
                    <div class="form-group">
                        <label>Cédula / RIF</label>
                        <input type="text" id="cuentaCedula" placeholder="J-12345678-9" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tipo de cuenta</label>
                        <select id="cuentaTipo">
                            <option value="ahorro">Ahorro</option>
                            <option value="corriente">Corriente</option>
                        </select>
                    </div>
                    <div class="form-check" style="align-self:flex-end; padding-bottom:18px;">
                        <input type="checkbox" id="cuentaActiva">
                        <label for="cuentaActiva">Marcar como cuenta activa</label>
                    </div>
                </div>

                <div style="background:rgba(255,184,28,0.08); border:1px solid rgba(255,184,28,0.15); border-radius:8px; padding:12px 16px; font-size:12px; color:var(--mana-yellow);">
                    ⚡ Al marcar esta cuenta como activa, las demás se desactivarán automáticamente.
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarModal()">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCuenta()">Guardar Cuenta</button>
        </div>
    </div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal-overlay" id="modalEliminar">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <h3>¿Eliminar cuenta?</h3>
            <button class="modal-close" onclick="cerrarModalEliminar()">✕</button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px; color:var(--mana-text);">¿Estás seguro de eliminar la cuenta de <strong id="eliminarNombre"></strong>?</p>
            <p style="font-size:12px; color:var(--mana-text-muted); margin-top:8px;">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="eliminarId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="cerrarModalEliminar()">Cancelar</button>
            <button class="btn btn-danger" onclick="eliminarCuenta()">Sí, eliminar</button>
        </div>
    </div>
</div>

<script>
const cuentasData = <?php echo json_encode($cuentas); ?>;
const bancosMap = <?php echo json_encode($bancos); ?>;

document.getElementById('menuToggle').addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});
document.getElementById('sidebarOverlay').addEventListener('click', function(){
    document.getElementById('sidebar').classList.remove('open');
    this.classList.remove('active');
});

function abrirModal(id) {
    const modal = document.getElementById('modalCuenta');
    document.getElementById('cuentaForm').reset();
    document.getElementById('cuentaId').value = '';
    document.getElementById('modalTitle').textContent = 'Nueva Cuenta PagoMóvil';

    if (id) {
        document.getElementById('modalTitle').textContent = 'Editar Cuenta PagoMóvil';
        const c = cuentasData.find(x => x.id === id);
        if (c) {
            document.getElementById('cuentaId').value = c.id;
            // Find the bank code
            for (const [cod, nombre] of Object.entries(bancosMap)) {
                if (nombre.includes(c.banco) || c.banco.includes(nombre)) {
                    document.getElementById('cuentaBanco').value = cod;
                    break;
                }
            }
            document.getElementById('cuentaTitular').value = c.titular;
            document.getElementById('cuentaTelefono').value = c.telefono;
            document.getElementById('cuentaCedula').value = c.cedula;
            document.getElementById('cuentaTipo').value = c.tipo;
            document.getElementById('cuentaActiva').checked = c.activa;
        }
    }

    modal.classList.add('open');
}

function cerrarModal() {
    document.getElementById('modalCuenta').classList.remove('open');
}

function guardarCuenta() {
    const data = {
        id: document.getElementById('cuentaId').value,
        banco_codigo: document.getElementById('cuentaBanco').value,
        titular: document.getElementById('cuentaTitular').value,
        telefono: document.getElementById('cuentaTelefono').value,
        cedula: document.getElementById('cuentaCedula').value,
        tipo: document.getElementById('cuentaTipo').value,
        activa: document.getElementById('cuentaActiva').checked
    };

    if (!data.banco_codigo || !data.titular || !data.telefono || !data.cedula) {
        alert('Completa todos los campos obligatorios.');
        return;
    }

    console.log('Guardando cuenta:', data);
    // POST a /api/admin/pagos
    alert('Cuenta guardada correctamente. ' + (data.activa ? 'Las demás cuentas han sido desactivadas.' : ''));
    cerrarModal();
}

function activarCuenta(id) {
    console.log('Activando cuenta #' + id);
    // POST a /api/admin/pagos/{id}/activar
    alert('Cuenta activada. Las demás cuentas han sido desactivadas.');
    location.reload();
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

function eliminarCuenta() {
    const id = document.getElementById('eliminarId').value;
    console.log('Eliminando cuenta #' + id);
    // DELETE a /api/admin/pagos/{id}
    alert('Cuenta eliminada correctamente.');
    cerrarModalEliminar();
}
</script>
</body>
</html>


