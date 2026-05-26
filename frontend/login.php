<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión — Maná Fast Food</title>
  <meta name="description" content="Inicia sesión en tu cuenta Maná Fast Food y accede a tus pedidos.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — LOGIN.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    .auth-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: calc(var(--header-height) + 40px) 20px 40px;
      background:
        radial-gradient(ellipse at 30% 50%, rgba(211,18,18,0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 70% 50%, rgba(255,184,28,0.04) 0%, transparent 60%),
        var(--color-bg);
    }

    .auth-container {
      width: 100%;
      max-width: 420px;
    }

    .auth-card {
      background: var(--color-bg-card);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-lg);
      padding: 40px 36px;
      box-shadow: var(--shadow-card);
      transition: var(--transition);
    }

    .auth-card:hover {
      border-color: var(--color-rojo);
      box-shadow: 0 12px 40px rgba(211,18,18,0.15);
    }

    .auth-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .auth-header .auth-logo {
      font-family: var(--font-display);
      font-size: 2rem;
      color: var(--color-rojo);
      text-transform: uppercase;
      letter-spacing: 3px;
      text-shadow: 0 2px 12px rgba(211,18,18,0.4);
      display: block;
      margin-bottom: 8px;
    }

    .auth-header .auth-logo span {
      color: var(--color-amarillo);
      font-family: var(--font-display);
      font-size: 0.9rem;
      display: block;
      letter-spacing: 6px;
    }

    .auth-header h1 {
      font-family: var(--font-heading);
      font-size: 1.2rem;
      color: var(--color-blanco);
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .auth-header p {
      color: var(--color-gris);
      font-size: 0.9rem;
      margin-top: 6px;
    }

    .auth-divider {
      height: 1px;
      background: var(--color-gris-oscuro);
      margin: 24px 0;
    }

    .auth-footer-links {
      text-align: center;
      margin-top: 20px;
    }

    .auth-footer-links a {
      color: var(--color-amarillo);
      font-family: var(--font-heading);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: var(--transition);
    }

    .auth-footer-links a:hover {
      color: var(--color-blanco);
    }

    .auth-footer-links .sep {
      color: var(--color-gris-oscuro);
      margin: 0 12px;
    }

    .auth-back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--color-gris);
      font-size: 0.85rem;
      margin-top: 16px;
      transition: var(--transition);
    }

    .auth-back-link:hover {
      color: var(--color-blanco);
    }

    .auth-error-msg {
      background: rgba(211,18,18,0.1);
      border: 1px solid rgba(211,18,18,0.3);
      border-radius: var(--radius-sm);
      padding: 12px 16px;
      color: var(--color-rojo);
      font-size: 0.85rem;
      font-weight: 700;
      margin-bottom: 20px;
      display: none;
    }

    .auth-error-msg.visible {
      display: block;
      animation: shake 0.4s ease;
    }

    /* ---- Responsive ---- */
    @media (max-width: 480px) {
      .auth-card {
        padding: 28px 20px;
      }

      .auth-page {
        padding-left: 12px;
        padding-right: 12px;
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
        <a href="registro.php" class="btn btn-sm btn-primary" style="padding:6px 16px;font-size:0.7rem;" data-nav>Registro</a>
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

  <!-- ===== LOGIN PAGE ===== -->
  <main class="auth-page">
    <div class="auth-container">

      <div class="auth-card">
        <div class="auth-header">
          <span class="auth-logo">
            MANÁ
            <span>FAST FOOD</span>
          </span>
          <h1>Iniciar Sesión</h1>
          <p>Entra para pedir más rápido</p>
        </div>

        <!-- Mensaje de error global -->
        <div class="auth-error-msg" id="login-error-msg"></div>

        <!-- Formulario -->
        <form id="login-form" class="auth-form" novalidate>
          <div class="form-group">
            <label class="form-label" for="login-email">Email</label>
            <input type="email" id="login-email" class="form-input" placeholder="correo@ejemplo.com" required autocomplete="email" inputmode="email">
          </div>

          <div class="form-group">
            <label class="form-label" for="login-password">Contraseña</label>
            <input type="password" id="login-password" class="form-input" placeholder="••••••••" required minlength="6" autocomplete="current-password">
          </div>

          <div style="text-align:right;margin-bottom:20px;">
            <a href="#" style="font-size:0.8rem;color:var(--color-amarillo);font-weight:700;" onclick="alert('Contacta al administrador para recuperar tu contraseña.');return false;">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width:100%;" id="login-submit-btn">
            ENTRAR
          </button>
        </form>

        <div class="auth-divider"></div>

        <div class="auth-footer-links">
          <a href="registro.php">¿No tienes cuenta? Regístrate aquí</a>
        </div>

        <div style="text-align:center;margin-top:12px;">
          <a href="index.php" class="auth-back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Volver al inicio
          </a>
        </div>
      </div>

    </div>
  </main>

  <!-- ===== FOOTER ===== -->
  <div id="footer-placeholder"></div>

  <!-- ===== SCRIPTS ===== -->
  <script src="js/app.js"></script>
  <script src="js/auth.js?v=2"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — LOGIN.JS
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      var form = document.getElementById('login-form');
      var errorMsg = document.getElementById('login-error-msg');
      var submitBtn = document.getElementById('login-submit-btn');
      var emailInput = document.getElementById('login-email');
      var passwordInput = document.getElementById('login-password');

      if (!form) return;

      // Obtener redirect de la URL
      var redirectUrl = (function() {
        var params = new URLSearchParams(window.location.search);
        return params.get('redirect') || 'index.php';
      })();

      form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Limpiar errores previos
        errorMsg.classList.remove('visible');
        errorMsg.textContent = '';
        document.querySelectorAll('.form-input.error').forEach(function(el) {
          el.classList.remove('error');
        });

        var email = emailInput.value.trim();
        var password = passwordInput.value;

        // Validación rápida
        if (!email) {
          showFieldError(emailInput, 'Ingresa tu email');
          return;
        }
        if (!password || password.length < 6) {
          showFieldError(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
          return;
        }

        // Estado de carga
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Entrando…';

        try {
          if (typeof Auth !== 'undefined' && Auth.login) {
            await Auth.login(email, password);
            // Verificar rol del usuario desde localStorage
            var userData = JSON.parse(localStorage.getItem('auth_user') || '{}');
            var destino = (userData.rol === 'admin' || userData.rol === 'superadmin')
              ? 'admin/dashboard.php'
              : redirectUrl;
            App.showToast(userData.rol === 'admin' ? '¡Bienvenido Jefe!' : '¡Bienvenido de vuelta!', 'success');
            window.location.href = destino;
          } else {
            // Fallback: simulación para desarrollo sin backend
            var esAdmin = email === 'admin@mana.com';
            var rol = esAdmin ? 'admin' : 'cliente';
            var nombre = esAdmin ? 'Administrador Maná' : 'Cliente Maná';
            
            App.showToast(esAdmin ? '¡Bienvenido Jefe!' : '¡Bienvenido!', 'success');
            localStorage.setItem('auth_token', 'demo_token_' + Date.now());
            localStorage.setItem('auth_user', JSON.stringify({
              id: 1,
              nombre: nombre,
              email: email,
              rol: rol
            }));
            // Admin al dashboard, cliente al index
            var destino = esAdmin ? 'admin/dashboard.php' : redirectUrl;
            setTimeout(function() {
              window.location.href = destino;
            }, 500);
          }
        } catch (error) {
          var field = error.field || 'general';
          var message = error.message || 'Credenciales inválidas';

          if (field === 'general' || field === 'email') {
            showGlobalError(message);
            if (field === 'email') {
              emailInput.classList.add('error');
            }
          } else if (field === 'password') {
            passwordInput.classList.add('error');
            showGlobalError(message);
          } else {
            showGlobalError(message);
          }
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'ENTRAR';
        }
      });

      function showFieldError(input, message) {
        input.classList.add('error');
        showGlobalError(message);
        input.focus();
      }

      function showGlobalError(message) {
        errorMsg.textContent = message;
        errorMsg.classList.add('visible');
      }

      // Limpiar error al escribir
      emailInput.addEventListener('input', function() {
        this.classList.remove('error');
        errorMsg.classList.remove('visible');
      });
      passwordInput.addEventListener('input', function() {
        this.classList.remove('error');
        errorMsg.classList.remove('visible');
      });
    });
  </script>

</body>
</html>



