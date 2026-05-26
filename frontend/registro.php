<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Regístrate — Maná Fast Food</title>
  <meta name="description" content="Regístrate en Maná Fast Food en segundos. Crea tu cuenta y empieza a pedir.">
  <meta name="theme-color" content="#111111">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ============================================================
       MANÁ FAST FOOD — REGISTRO.HTML (PAGE-SPECIFIC STYLES)
       ============================================================ */

    .auth-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: calc(var(--header-height) + 40px) 20px 40px;
      background:
        radial-gradient(ellipse at 30% 50%, rgba(255,184,28,0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 70% 50%, rgba(211,18,18,0.04) 0%, transparent 60%),
        var(--color-bg);
    }

    .auth-container {
      width: 100%;
      max-width: 480px;
    }

    .auth-card {
      background: var(--color-bg-card);
      border: 1px solid var(--color-gris-oscuro);
      border-radius: var(--radius-lg);
      padding: 44px 36px 36px;
      box-shadow: var(--shadow-card);
      transition: var(--transition);
    }

    .auth-card:hover {
      border-color: var(--color-amarillo);
      box-shadow: 0 12px 40px rgba(255,184,28,0.1);
    }

    .auth-header {
      text-align: center;
      margin-bottom: 28px;
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

    .auth-header .auth-quick-note {
      color: var(--color-gris);
      font-size: 0.85rem;
      margin-top: 8px;
      line-height: 1.5;
      max-width: 340px;
      margin-left: auto;
      margin-right: auto;
    }

    .auth-divider {
      height: 1px;
      background: var(--color-gris-oscuro);
      margin: 24px 0 20px;
    }

    .auth-footer-links {
      text-align: center;
      margin-top: 16px;
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

    .auth-success-msg {
      background: rgba(40,167,69,0.1);
      border: 1px solid rgba(40,167,69,0.3);
      border-radius: var(--radius-sm);
      padding: 12px 16px;
      color: var(--color-success);
      font-size: 0.85rem;
      font-weight: 700;
      margin-bottom: 20px;
      display: none;
    }

    .auth-success-msg.visible {
      display: block;
    }

    /* ---- Form compacto ---- */
    .form-row-duo {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
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

      .form-row-duo {
        grid-template-columns: 1fr;
        gap: 0;
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
        <a href="login.php" class="btn btn-sm btn-outline-rojo" style="padding:6px 16px;font-size:0.7rem;" data-nav>Entrar</a>
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

  <!-- ===== REGISTRO PAGE ===== -->
  <main class="auth-page">
    <div class="auth-container">

      <div class="auth-card">
        <div class="auth-header">
          <span class="auth-logo">
            MANÁ
            <span>FAST FOOD</span>
          </span>
          <h1>Crear Cuenta</h1>
          <p class="auth-quick-note">
            Regístrate en segundos. Solo lo esencial para que tu pedido llegue rápido.
          </p>
        </div>

        <!-- Mensajes -->
        <div class="auth-error-msg" id="register-error-msg"></div>
        <div class="auth-success-msg" id="register-success-msg"></div>

        <!-- Formulario ultra-rápido: 4 campos -->
        <form id="register-form" class="auth-form" novalidate>
          <div class="form-group">
            <label class="form-label" for="reg-nombre">Nombre Completo</label>
            <input type="text" id="reg-nombre" class="form-input" placeholder="Ej: María Pérez" required autocomplete="name" minlength="2">
          </div>

          <div class="form-row-duo">
            <div class="form-group">
              <label class="form-label" for="reg-telefono">Teléfono</label>
              <input type="tel" id="reg-telefono" class="form-input" placeholder="+58 412 123 4567" required autocomplete="tel" inputmode="tel">
            </div>
            <div class="form-group">
              <label class="form-label" for="reg-email">Email</label>
              <input type="email" id="reg-email" class="form-input" placeholder="correo@ejemplo.com" required autocomplete="email" inputmode="email">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="reg-password">Contraseña</label>
            <input type="password" id="reg-password" class="form-input" placeholder="Mínimo 6 caracteres" required minlength="6" autocomplete="new-password">
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width:100%;font-size:1rem;padding:18px 32px;margin-top:8px;" id="register-submit-btn">
            REGISTRARSE Y PEDIR
          </button>
        </form>

        <div class="auth-divider"></div>

        <div class="auth-footer-links">
          <a href="login.php">Ya tengo cuenta, entrar</a>
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
  <script src="js/auth.js"></script>
  <script>
    /* ============================================================
       MANÁ FAST FOOD — REGISTRO.JS
       ============================================================ */

    document.addEventListener('DOMContentLoaded', function() {
      'use strict';

      var form = document.getElementById('register-form');
      var errorMsg = document.getElementById('register-error-msg');
      var successMsg = document.getElementById('register-success-msg');
      var submitBtn = document.getElementById('register-submit-btn');

      var nombreInput = document.getElementById('reg-nombre');
      var telefonoInput = document.getElementById('reg-telefono');
      var emailInput = document.getElementById('reg-email');
      var passwordInput = document.getElementById('reg-password');

      if (!form) return;

      // Determinar redirección después del registro
      var redirectAfterRegister = (function() {
        var params = new URLSearchParams(window.location.search);
        return params.get('redirect') || (
          // Si venimos del carrito o checkout, volver allí
          document.referrer.includes('carrito.php') || document.referrer.includes('checkout.php')
            ? document.referrer
            : 'menu.php'
        );
      })();

      form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Limpiar estados
        errorMsg.classList.remove('visible');
        errorMsg.textContent = '';
        successMsg.classList.remove('visible');
        successMsg.textContent = '';
        document.querySelectorAll('.form-input.error').forEach(function(el) {
          el.classList.remove('error');
        });

        var nombre = nombreInput.value.trim();
        var telefono = telefonoInput.value.trim();
        var email = emailInput.value.trim();
        var password = passwordInput.value;

        // Validaciones del lado del cliente
        if (!nombre || nombre.length < 2) {
          showFieldError(nombreInput, 'El nombre debe tener al menos 2 caracteres');
          return;
        }

        if (!telefono) {
          showFieldError(telefonoInput, 'Ingresa un número de teléfono');
          return;
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          showFieldError(emailInput, 'Ingresa un email válido');
          return;
        }

        if (!password || password.length < 6) {
          showFieldError(passwordInput, 'La contraseña debe tener al menos 6 caracteres');
          return;
        }

        // Estado de carga
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Registrando…';

        try {
          if (typeof Auth !== 'undefined' && Auth.register) {
            await Auth.register(nombre, telefono, email, password);

            // Éxito
            successMsg.textContent = '¡Cuenta creada con éxito! Redirigiendo…';
            successMsg.classList.add('visible');

            App.showToast('¡Bienvenido a Maná Fast Food!', 'success');

            // Redirigir
            setTimeout(function() {
              window.location.href = redirectAfterRegister;
            }, 1200);

          } else {
            // Fallback: modo demo sin backend
            localStorage.setItem('auth_token', 'demo_token_' + Date.now());
            localStorage.setItem('auth_user', JSON.stringify({
              id: Date.now(),
              nombre: nombre,
              email: email,
              telefono: telefono,
              rol: 'cliente'
            }));

            successMsg.textContent = '¡Cuenta creada con éxito! Redirigiendo…';
            successMsg.classList.add('visible');

            App.showToast('¡Bienvenido a Maná Fast Food! (modo demo)', 'success');

            setTimeout(function() {
              window.location.href = redirectAfterRegister;
            }, 1200);
          }

        } catch (error) {
          var field = error.field || 'general';
          var message = error.message || 'Error al registrarse';

          if (field === 'nombre') {
            showFieldError(nombreInput, message);
          } else if (field === 'telefono') {
            showFieldError(telefonoInput, message);
          } else if (field === 'email') {
            showFieldError(emailInput, message);
          } else if (field === 'password') {
            showFieldError(passwordInput, message);
          } else {
            showGlobalError(message);
          }
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'REGISTRARSE Y PEDIR';
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

      // Limpiar errores al escribir
      [nombreInput, telefonoInput, emailInput, passwordInput].forEach(function(input) {
        input.addEventListener('input', function() {
          this.classList.remove('error');
          errorMsg.classList.remove('visible');
        });
      });

      // Formatear teléfono mientras se escribe
      telefonoInput.addEventListener('input', function() {
        var val = this.value.replace(/[^\d+]/g, '');
        if (val.length > 0 && !val.startsWith('+')) {
          val = '+' + val;
        }
        this.value = val;
      });
    });
  </script>

</body>
</html>



