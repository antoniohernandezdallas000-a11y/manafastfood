<?php
// Cargar datos dinámicos desde la base de datos
$tasaDB_path = __DIR__ . '/../../backend/config/database.php';
$tasaModel_path = __DIR__ . '/../../backend/models/TasaBcv.php';

if (file_exists($tasaDB_path) && file_exists($tasaModel_path)) {
    require_once $tasaDB_path;
    require_once $tasaModel_path;
    
    try {
        $tasaUltima = TasaBcv::getUltima();
        $tasa_actual = $tasaUltima ? (float)$tasaUltima['tasa_usd_bs'] : 62.45;
        $tasa_timestamp = $tasaUltima ? $tasaUltima['created_at'] : date('d/m/Y H:i:s');
        $modo_actual = TasaBcv::getModo();
        $historial = TasaBcv::getHistorial(10);
    } catch (Exception $e) {
        // Fallback si falla la DB
        $modo_actual = 'automatica';
        $tasa_actual = 62.45;
        $tasa_timestamp = date('d/m/Y H:i:s');
        $historial = [];
    }
} else {
    // Fallback completo si no existe backend
    $modo_actual = 'automatica';
    $tasa_actual = 62.45;
    $tasa_timestamp = date('d/m/Y H:i:s');
    $historial = [];
}

// Productos para previsualización (pueden venir de DB o ser fijos)
$productos_preview = [
    ['nombre' => 'Maná Burger Clásica', 'precio_usd' => 8.99],
    ['nombre' => 'Maná Doble Carne', 'precio_usd' => 11.50],
    ['nombre' => 'Maná BBQ Bacon', 'precio_usd' => 13.99],
    ['nombre' => 'Hot Dog Maná Supreme', 'precio_usd' => 7.50],
    ['nombre' => 'Pepito de Pollo', 'precio_usd' => 9.99],
    ['nombre' => 'Coca-Cola 500ml', 'precio_usd' => 2.00],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasa BCV · Maná Fast Food</title>
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
        .content { padding:32px; max-width:1000px; }

        /* TASA CARD */
        .tasa-card {
            background: var(--mana-gray);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            padding: 32px;
            margin-bottom: 32px;
            text-align: center;
        }

        .tasa-card .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--mana-text-muted);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .tasa-card .value {
            font-family: var(--font-display);
            font-size: 56px;
            color: var(--mana-white);
            line-height: 1;
            margin-bottom: 4px;
        }

        .tasa-card .value .decimal {
            font-size: 32px;
            color: var(--mana-yellow);
        }

        .tasa-card .timestamp {
            font-size: 12px;
            color: var(--mana-text-muted);
            margin-bottom: 20px;
        }

        .tasa-card .modo-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .modo-automatica { background: rgba(34,197,94,0.15); color: var(--mana-success); }
        .modo-manual { background: rgba(255,184,28,0.15); color: var(--mana-yellow); }

        /* SWITCH */
        .modo-switch-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .modo-switch-container span {
            font-size: 13px;
            font-weight: 600;
            color: var(--mana-text-muted);
            transition: color 0.3s;
        }

        .modo-switch-container span.active { color: var(--mana-white); }

        .modo-switch {
            width: 60px;
            height: 30px;
            background: rgba(255,255,255,0.1);
            border-radius: 30px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-block;
            flex-shrink: 0;
        }

        .modo-switch.active { background: var(--mana-success); }

        .modo-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .modo-switch.active::after { transform: translateX(30px); }

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
        .btn-ghost:hover { border-color: var(--mana-text-muted); color: var(--mana-white); }

        .modo-content { margin-top: 24px; }

        .manual-input-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .manual-input-group input {
            width: 200px;
            padding: 12px 16px;
            background: var(--mana-black);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--mana-white);
            font-family: var(--font-body);
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            transition: border-color 0.2s;
        }

        .manual-input-group input:focus { outline: none; border-color: var(--mana-yellow); }

        .manual-input-group .symbol {
            font-size: 20px;
            font-weight: 700;
            color: var(--mana-text);
        }

        /* HISTORIAL */
        .section-title {
            font-family: var(--font-display);
            font-size: 18px;
            color: var(--mana-white);
            margin-bottom: 16px;
        }

        .table-container { background: var(--mana-gray); border-radius: 16px; border:1px solid rgba(255,255,255,0.05); overflow: hidden; margin-bottom: 32px; }

        table { width:100%; border-collapse:collapse; }
        table thead { background: rgba(255,255,255,0.03); }
        table th { padding:12px 20px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--mana-text-muted); font-weight:700; }
        table td { padding:12px 20px; font-size:13px; border-bottom:1px solid rgba(255,255,255,0.03); }
        table tr:last-child td { border-bottom:none; }
        table tr:hover td { background:rgba(255,255,255,0.02); }

        .text-muted { color: var(--mana-text-muted); }
        .text-success { color: var(--mana-success); }

        /* PREVIEW */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 12px;
        }

        .preview-item {
            background: var(--mana-gray);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .preview-item .name { font-size: 13px; color: var(--mana-text); }
        .preview-item .price { 
            font-family: var(--font-display);
            font-size: 15px;
            color: var(--mana-yellow);
            text-align: right;
        }
        .preview-item .price small {
            display: block;
            font-family: var(--font-body);
            font-size: 10px;
            color: var(--mana-text-muted);
            font-weight: 400;
        }

        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:90; }

        @media (max-width:768px) {
            .menu-toggle { display:block; }
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.active { display:block; }
            .main { margin-left:0; }
            .content { padding:20px 16px; }
            .topbar { padding:12px 16px; }
            .tasa-card .value { font-size: 40px; }
            .tasa-card .value .decimal { font-size: 24px; }
            .manual-input-group { flex-direction: column; }
            .manual-input-group input { width: 100%; }
            .modo-switch-container { flex-wrap: wrap; }
            .preview-grid { grid-template-columns: 1fr; }
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
        <a href="tasa.php" class="active"><span class="icon">💱</span> Tasa BCV</a>
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
            <h2>Tasa BCV</h2>
        </div>
        <div class="topbar-right"></div>
    </header>

    <div class="content">
        <!-- TASA CARD -->
        <div class="tasa-card">
            <div class="label">Tasa de Cambio BCV</div>
            <div class="value">
                <?php
                    $entero = floor($tasa_actual);
                    $decimal = round(($tasa_actual - $entero) * 100);
                ?>
                <span class="entero"><?php echo $entero; ?></span><span class="decimal">,<?php echo str_pad($decimal, 2, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="timestamp">Actualizado: <?php echo $tasa_timestamp; ?></div>
            <div class="modo-badge modo-<?php echo $modo_actual; ?>">
                ● Modo <?php echo $modo_actual === 'automatica' ? 'Automático (BCV API)' : 'Manual'; ?>
            </div>

            <!-- SWITCH MODO -->
            <div class="modo-switch-container">
                <span class="<?php echo $modo_actual === 'automatica' ? 'active' : ''; ?>" id="labelAuto">🤖 Automática</span>
                <span class="modo-switch <?php echo $modo_actual === 'automatica' ? 'active' : ''; ?>" id="modoSwitch" onclick="toggleModo()"></span>
                <span class="<?php echo $modo_actual === 'manual' ? 'active' : ''; ?>" id="labelManual">✏️ Manual</span>
            </div>

            <!-- CONTENIDO AUTOMÁTICA -->
            <div id="modoAutoContent" style="display:<?php echo $modo_actual === 'automatica' ? 'block' : 'none'; ?>;">
                <p style="font-size:13px; color:var(--mana-text-muted); margin-bottom:16px;">
                    La tasa se actualiza automáticamente desde la API del Banco Central de Venezuela cada 24 horas.
                </p>
                <button class="btn btn-primary" onclick="actualizarTasa()">🔄 Actualizar ahora</button>
            </div>

            <!-- CONTENIDO MANUAL -->
            <div id="modoManualContent" style="display:<?php echo $modo_actual === 'manual' ? 'block' : 'none'; ?>;">
                <div class="manual-input-group">
                    <span class="symbol">Bs.</span>
                    <input type="number" id="tasaManualInput" step="0.01" min="0" value="<?php echo $tasa_actual; ?>" placeholder="0.00">
                    <span class="symbol">por $1</span>
                </div>
                <button class="btn btn-yellow" onclick="guardarTasaManual()">💾 Guardar tasa manual</button>
            </div>
        </div>

        <!-- HISTORIAL -->
        <h3 class="section-title">📊 Historial de cambios</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tasa anterior</th>
                        <th>Tasa nueva</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                    <tr>
                        <td class="text-muted"><?php echo $h['fecha']; ?></td>
                        <td>Bs. <?php echo number_format($h['anterior'], 2); ?></td>
                        <td><strong>Bs. <?php echo number_format($h['nueva'], 2); ?></strong></td>
                        <td><span class="text-<?php echo $h['tipo'] === 'automática' ? 'muted' : 'success'; ?>"><?php echo ucfirst($h['tipo']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- PREVIEW PRECIOS EN BS -->
        <h3 class="section-title">💰 Precios actuales en Bolívares</h3>
        <p style="font-size:12px; color:var(--mana-text-muted); margin-bottom:16px;">
            Vista previa calculada con tasa de <strong>Bs. <?php echo number_format($tasa_actual, 2); ?></strong> por $1 USD
        </p>
        <div class="preview-grid">
            <?php foreach ($productos_preview as $p): ?>
            <?php $precio_bs = $p['precio_usd'] * $tasa_actual; ?>
            <div class="preview-item">
                <span class="name"><?php echo htmlspecialchars($p['nombre']); ?></span>
                <div class="price">
                    $<?php echo number_format($p['precio_usd'], 2); ?>
                    <small>Bs. <?php echo number_format($precio_bs, 2); ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// ===== CONFIGURACIÓN DINÁMICA =====
(function() {
    const path = window.location.pathname;
    const basePath = path.substring(0, path.indexOf('/frontend/'));
    window.API_BASE = basePath + '/backend/api/admin/tasa';
    window.CHECKOUT_API = basePath + '/backend/api/checkout';
})();

// ===== OBTENER TOKEN JWT DESDE localStorage =====
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token');
    const headers = { 'Content-Type': 'application/json' };
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    return headers;
}

// ===== SIDEBAR TOGGLE =====
document.getElementById('menuToggle').addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
});
document.getElementById('sidebarOverlay').addEventListener('click', function(){
    document.getElementById('sidebar').classList.remove('open');
    this.classList.remove('active');
});

// ===== TOGGLE MODO AUTO/MANUAL =====
function toggleModo() {
    const sw = document.getElementById('modoSwitch');
    const isAuto = sw.classList.contains('active');
    const nuevoModo = isAuto ? 'manual' : 'automatica';
    
    // Feedback visual inmediato
    sw.classList.toggle('active');
    document.getElementById('labelAuto').classList.toggle('active');
    document.getElementById('labelManual').classList.toggle('active');
    
    if (nuevoModo === 'automatica') {
        document.getElementById('modoManualContent').style.display = 'none';
        document.getElementById('modoAutoContent').style.display = 'block';
    } else {
        document.getElementById('modoAutoContent').style.display = 'none';
        document.getElementById('modoManualContent').style.display = 'block';
    }

    // Llamar API para guardar el cambio
    fetch(window.API_BASE + '/toggle-modo.php', {
        method: 'PATCH',
        headers: getAuthHeaders(),
        body: JSON.stringify({ modo: nuevoModo })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Modo cambiado a:', nuevoModo);
            if (typeof App !== 'undefined') App.showToast('Modo cambiado a ' + nuevoModo, 'success');
        }
    })
    .catch(err => {
        console.error('Error al cambiar modo:', err);
        // Revertir UI en caso de error
        sw.classList.toggle('active');
        document.getElementById('labelAuto').classList.toggle('active');
        document.getElementById('labelManual').classList.toggle('active');
    });
}

// ===== ACTUALIZAR TASA DESDE BCV (Automática) =====
function actualizarTasa(event) {
    event = event || window.event;
    const btn = event && event.target ? event.target : document.querySelector('.btn-primary');
    const originalText = btn.textContent || btn.innerText;
    btn.disabled = true;
    btn.textContent = '⏳ Consultando BCV...';
    
    console.log('🔍 Consultando tasa BCV desde API pública...');
    
    fetch(window.API_BASE + '/actualizar.php', {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify({ modo: 'automatica' })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;
        
        if (data.success) {
            console.log('✅ Tasa obtenida:', data.data.tasa);
            if (typeof App !== 'undefined') {
                App.showToast(data.message, 'success');
            } else {
                alert('✅ ' + data.message);
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            console.error('❌ Error:', data.message);
            const msg = data.message || 'Error desconocido';
            const sugerencia = msg.includes('token') || msg.includes('autenticación') || msg.includes('Auth')
                ? 'Inicia sesión nuevamente.'
                : '• Sin conexión a internet\n• El sitio del BCV no está disponible\n• Usa el modo manual para ingresar la tasa';
            if (typeof App !== 'undefined') {
                App.showToast('Error: ' + msg, 'error');
            } else {
                alert('❌ ' + msg + '\n\n' + sugerencia);
            }
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = originalText;
        console.error('Error de red:', err);
        const msg = 'Error de conexión con el servidor.';
        if (typeof App !== 'undefined') {
            App.showToast(msg, 'error');
        } else {
            alert('❌ ' + msg + '\n\n¿Tienes conexión a internet? Usa modo manual.');
        }
    });
}

// ===== GUARDAR TASA MANUAL =====
function guardarTasaManual() {
    const input = document.getElementById('tasaManualInput');
    const tasa = input.value.trim();
    
    if (!tasa || parseFloat(tasa) <= 0) {
        alert('⚠ Ingresa una tasa válida mayor a 0.\nEjemplo: 526.87');
        input.focus();
        return;
    }

    const tasaNum = parseFloat(tasa);
    const btn = document.querySelector('#modoManualContent .btn-yellow');
    const originalText = btn.textContent || btn.innerText;
    btn.disabled = true;
    btn.textContent = '⏳ Guardando...';

    console.log('💾 Guardando tasa manual:', tasaNum);
    
    fetch(window.API_BASE + '/actualizar.php', {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify({ 
            tasa: tasaNum,
            modo: 'manual'
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;
        
        if (data.success) {
            console.log('✅ Tasa manual guardada:', data.data.tasa);
            if (typeof App !== 'undefined') {
                App.showToast(data.message, 'success');
            } else {
                alert('✅ ' + data.message);
            }
            setTimeout(() => location.reload(), 1500);
        } else {
            console.error('❌ Error al guardar:', data.message);
            const msg = data.message || 'Error desconocido';
            const sugerencia = (msg.includes('token') || msg.includes('autenticación') || msg.includes('Auth'))
                ? 'Tu sesión expiró. Inicia sesión otra vez.'
                : 'Intenta de nuevo.';
            if (typeof App !== 'undefined') {
                App.showToast('Error: ' + msg, 'error');
            } else {
                alert('❌ ' + msg + '\n\n' + sugerencia);
            }
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = originalText;
        console.error('Error de red:', err);
        if (typeof App !== 'undefined') {
            App.showToast('Error de conexión', 'error');
        } else {
            alert('❌ Error de conexión con el servidor');
        }
    });
}
</script>
</body>
</html>


