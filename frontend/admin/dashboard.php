<?php
// Sesion desactivada para desarrollo frontend
// En produccion conectar con backend/api/admin/auth

// Simular datos del dashboard
$stats = [
    'pedidos_hoy' => 18,
    'ingresos_usd' => 342.50,
    'productos_activos' => 36,
    'pedidos_pendientes' => 7
];

$ultimos_pedidos = [
    ['id' => 1042, 'cliente' => 'Carlos Mendoza', 'total' => 18.50, 'estado' => 'entregado', 'fecha' => '23/05/2026 11:30'],
    ['id' => 1041, 'cliente' => 'María Rodríguez', 'total' => 24.00, 'estado' => 'listo', 'fecha' => '23/05/2026 11:15'],
    ['id' => 1040, 'cliente' => 'José García', 'total' => 12.75, 'estado' => 'preparando', 'fecha' => '23/05/2026 10:50'],
    ['id' => 1039, 'cliente' => 'Ana Martínez', 'total' => 31.20, 'estado' => 'confirmado', 'fecha' => '23/05/2026 10:30'],
    ['id' => 1038, 'cliente' => 'Luis Pérez', 'total' => 9.99, 'estado' => 'pendiente', 'fecha' => '23/05/2026 10:10'],
    ['id' => 1037, 'cliente' => 'Sofía Hernández', 'total' => 45.00, 'estado' => 'entregado', 'fecha' => '23/05/2026 09:45'],
    ['id' => 1036, 'cliente' => 'Diego Castillo', 'total' => 15.50, 'estado' => 'entregado', 'fecha' => '23/05/2026 09:20'],
    ['id' => 1035, 'cliente' => 'Valentina Torres', 'total' => 22.30, 'estado' => 'cancelado', 'fecha' => '23/05/2026 08:55'],
    ['id' => 1034, 'cliente' => 'Andrés Rivas', 'total' => 8.75, 'estado' => 'entregado', 'fecha' => '23/05/2026 08:30'],
    ['id' => 1033, 'cliente' => 'Gabriela Linares', 'total' => 27.00, 'estado' => 'entregado', 'fecha' => '22/05/2026 18:20'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Maná Fast Food</title>
    <link rel="icon" type="image/png" href="../favicon.png">
    <?php
    $fonts_url = 'https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;500;600;700;800&display=swap';
    echo "<link href='$fonts_url' rel='stylesheet'>";
    ?>
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
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
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

        .sidebar-nav a .icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .sidebar-nav a .badge {
            margin-left: auto;
            background: var(--mana-yellow);
            color: var(--mana-black);
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
        }

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

        /* ===== MAIN ===== */
        .main {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 0;
        }

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

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--mana-white);
            font-size: 24px;
            cursor: pointer;
            padding: 4px;
        }

        .topbar h2 {
            font-family: var(--font-display);
            font-size: 20px;
            color: var(--mana-white);
            letter-spacing: 0.5px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar-date {
            font-size: 13px;
            color: var(--mana-text-muted);
        }

        .topbar-admin {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--mana-text);
        }

        .topbar-admin .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--mana-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: white;
        }

        .content {
            padding: 32px;
        }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--mana-gray);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--mana-yellow);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-card-icon.orders { background: rgba(211,18,18,0.15); color: var(--mana-red); }
        .stat-card-icon.revenue { background: rgba(255,184,28,0.15); color: var(--mana-yellow); }
        .stat-card-icon.products { background: rgba(34,197,94,0.15); color: var(--mana-success); }
        .stat-card-icon.pending { background: rgba(59,130,246,0.15); color: var(--mana-info); }

        .stat-card-value {
            font-family: var(--font-display);
            font-size: 32px;
            color: var(--mana-white);
            line-height: 1;
        }

        .stat-card-label {
            font-size: 12px;
            color: var(--mana-text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 6px;
            font-weight: 600;
        }

        /* ===== TABLE ===== */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
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

        .btn-primary {
            background: var(--mana-red);
            color: var(--mana-white);
        }

        .btn-primary:hover { background: #b01010; }

        .table-container {
            background: var(--mana-gray);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: rgba(255,255,255,0.03);
        }

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
        }

        table tr:last-child td { border-bottom: none; }

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

        .text-muted { color: var(--mana-text-muted); }
        .text-right { text-align: right; }

        /* ===== MOBILE ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 90;
        }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .menu-toggle { display: block; }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main { margin-left: 0; }

            .content { padding: 20px 16px; }

            .topbar { padding: 12px 16px; }

            .stats-grid { grid-template-columns: 1fr; gap: 12px; }

            .topbar-date { display: none; }

            table { font-size: 12px; }
            table th, table td { padding: 10px 12px; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="../../images/logo-mana.jpeg" alt="Maná" class="sidebar-logo" height="38">
        <div>
            <h1>MANÁ</h1>
            <small>Fast Food · Admin</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="active">
            <span class="icon">📊</span> Dashboard
        </a>
        <a href="productos.php">
            <span class="icon">🍔</span> Productos
        </a>
        <a href="pedidos.php">
            <span class="icon">📋</span> Pedidos
            <span class="badge">7</span>
        </a>
        <a href="pagos.php">
            <span class="icon">💳</span> PagoMóvil
        </a>
        <a href="tasa.php">
            <span class="icon">💱</span> Tasa BCV
        </a>
        <a href="ofertas.php">
            <span class="icon">🏷️</span> Ofertas
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../index.php" target="_blank">
            <span>←</span> Ver sitio web
        </a>
        <a href="#" onclick="event.preventDefault(); if(typeof Auth!=='undefined' && Auth.logout) { Auth.logout(); } else { window.location.href='../logout.php'; }" style="margin-top:8px;">
            <span>🚪</span> Cerrar sesión
        </a>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">☰</button>
            <h2>Dashboard</h2>
        </div>
        <div class="topbar-right">
            <span class="topbar-date"><?php echo date('d/m/Y · h:i A'); ?></span>
            <div class="topbar-admin">
                <span>Admin</span>
                <div class="avatar">A</div>
            </div>
        </div>
    </header>

    <div class="content">
        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-icon orders">📦</div>
                </div>
                <div class="stat-card-value"><?php echo $stats['pedidos_hoy']; ?></div>
                <div class="stat-card-label">Pedidos Hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-icon revenue">💰</div>
                </div>
                <div class="stat-card-value">$<?php echo number_format($stats['ingresos_usd'], 2); ?></div>
                <div class="stat-card-label">Ingresos del día</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-icon products">🍔</div>
                </div>
                <div class="stat-card-value"><?php echo $stats['productos_activos']; ?></div>
                <div class="stat-card-label">Productos Activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-icon pending">⏳</div>
                </div>
                <div class="stat-card-value"><?php echo $stats['pedidos_pendientes']; ?></div>
                <div class="stat-card-label">Pedidos Pendientes</div>
            </div>
        </div>

        <!-- ÚLTIMOS PEDIDOS -->
        <div class="section-header">
            <h3>Últimos Pedidos</h3>
            <a href="pedidos.php" class="btn btn-primary">Ver todos →</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimos_pedidos as $pedido): ?>
                    <tr>
                        <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                        <td><strong>$<?php echo number_format($pedido['total'], 2); ?></strong></td>
                        <td><span class="badge badge-<?php echo $pedido['estado']; ?>"><?php echo $pedido['estado']; ?></span></td>
                        <td class="text-muted"><?php echo $pedido['fecha']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Sidebar toggle
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });
}

if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
}
</script>
</body>
</html>



