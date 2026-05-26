/* ============================================================
   MANÁ FAST FOOD - AUTH.JS
   Autenticación del lado del cliente con JWT.
   Registro, login, logout, verificación de sesión.
   ============================================================ */

const Auth = (() => {
  'use strict';

  const STORAGE_TOKEN_KEY = 'auth_token';
  const STORAGE_USER_KEY = 'auth_user';

  // Detectar base path dinámicamente
  const getBasePath = () => {
    const parts = window.location.pathname.split('/');
    if (parts.length >= 3) return '/' + parts[1];
    return '';
  };
  const BASE_PATH = window.App ? (App.CONFIG?.BASE_PATH || getBasePath()) : getBasePath();
  const API_BASE = BASE_PATH + '/backend/api/auth';

  /* ---- REGISTRO ---- */
  const register = async (nombre, telefono, email, password) => {
    // Validaciones del lado del cliente
    if (!nombre || nombre.trim().length < 2) {
      throw { field: 'nombre', message: 'El nombre debe tener al menos 2 caracteres' };
    }
    if (!telefono || !/^\+?[\d\s\-()]{7,15}$/.test(telefono)) {
      throw { field: 'telefono', message: 'Ingresa un teléfono válido' };
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      throw { field: 'email', message: 'Ingresa un email válido' };
    }
    if (!password || password.length < 6) {
      throw { field: 'password', message: 'La contraseña debe tener al menos 6 caracteres' };
    }

    try {
      const response = await fetch(`${API_BASE}/register.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          nombre: nombre.trim(),
          telefono: telefono.trim(),
          email: email.trim().toLowerCase(),
          password: password,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw {
          field: data.field || 'general',
          message: data.message || 'Error al registrarse',
          status: response.status,
        };
      }

      // Guardar token si se recibe
      var usuarioData = data.data?.usuario || data.user || {};
      var tokenData = data.data?.token || data.token;
      if (tokenData) {
        localStorage.setItem(STORAGE_TOKEN_KEY, tokenData);
        // Guardar datos básicos del usuario
        const userData = {
          id: usuarioData.id,
          nombre: usuarioData.nombre || nombre.trim(),
          email: usuarioData.email || email.trim().toLowerCase(),
          telefono: usuarioData.telefono || telefono.trim(),
          rol: usuarioData.rol || 'cliente',
        };
        localStorage.setItem(STORAGE_USER_KEY, JSON.stringify(userData));
      }

      return data;
    } catch (error) {
      if (error.field) throw error;
      throw { field: 'general', message: 'Error de conexión. Intenta de nuevo.' };
    }
  };

  /* ---- INICIO DE SESIÓN ---- */
  const login = async (email, password) => {
    if (!email || !password) {
      throw { field: 'general', message: 'Email y contraseña son requeridos' };
    }

    try {
      const response = await fetch(`${API_BASE}/login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: email.trim().toLowerCase(),
          password: password,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw {
          field: data.field || 'general',
          message: data.message || 'Credenciales inválidas',
          status: response.status,
        };
      }

      var tokenData = data.data?.token || data.token;
      if (!tokenData) {
        throw { field: 'general', message: 'Token no recibido del servidor' };
      }

      // Guardar token y datos del usuario
      // La API devuelve { data: { usuario: {...}, token: "..." } }
      var usuarioData = data.data?.usuario || data.user || {};
      var tokenData = data.data?.token || data.token;

      localStorage.setItem(STORAGE_TOKEN_KEY, tokenData);

      const userData = {
        id: usuarioData.id,
        nombre: usuarioData.nombre || '',
        email: usuarioData.email || email.trim().toLowerCase(),
        telefono: usuarioData.telefono || '',
        rol: usuarioData.rol || 'cliente',
      };
      localStorage.setItem(STORAGE_USER_KEY, JSON.stringify(userData));

      return data;
    } catch (error) {
      if (error.field) throw error;
      throw { field: 'general', message: 'Error de conexión. Verifica tu internet.' };
    }
  };

  /* ---- CERRAR SESIÓN ---- */
  const logout = () => {
    localStorage.removeItem(STORAGE_TOKEN_KEY);
    localStorage.removeItem(STORAGE_USER_KEY);

    // Disparar evento
    const event = new CustomEvent('authChanged', {
      detail: { loggedIn: false },
      bubbles: true,
    });
    document.dispatchEvent(event);

    // Redirigir al login si estamos en página protegida
    const protectedPages = ['checkout.php', 'perfil.php', 'admin/'];
    const currentPath = window.location.pathname;
    const isProtected = protectedPages.some(page => currentPath.includes(page));

    if (isProtected) {
      const loginUrl = BASE_PATH + '/frontend/login.php?redirect=' + encodeURIComponent(currentPath);
      window.location.href = loginUrl;
    }

    return true;
  };

  /* ---- VERIFICAR SI ESTÁ LOGUEADO ---- */
  const isLoggedIn = () => {
    const token = localStorage.getItem(STORAGE_TOKEN_KEY);
    if (!token) return false;

    try {
      // Decodificar payload del JWT
      const payload = decodeJWT(token);
      if (!payload) return false;

      // Verificar expiración
      const now = Math.floor(Date.now() / 1000);
      if (payload.exp && payload.exp < now) {
        // Token expirado - limpiar
        localStorage.removeItem(STORAGE_TOKEN_KEY);
        localStorage.removeItem(STORAGE_USER_KEY);
        return false;
      }

      return true;
    } catch (e) {
      // Token inválido
      localStorage.removeItem(STORAGE_TOKEN_KEY);
      localStorage.removeItem(STORAGE_USER_KEY);
      return false;
    }
  };

  /* ---- DECODIFICAR JWT (sin librería) ---- */
  const decodeJWT = (token) => {
    try {
      const parts = token.split('.');
      if (parts.length !== 3) return null;

      const payload = parts[1];
      // Reemplazar caracteres base64url
      const base64 = payload.replace(/-/g, '+').replace(/_/g, '/');
      const decoded = atob(base64);
      return JSON.parse(decoded);
    } catch (e) {
      return null;
    }
  };

  /* ---- OBTENER USUARIO ACTUAL ---- */
  const getCurrentUser = async () => {
    // Intentar desde localStorage primero
    const storedUser = localStorage.getItem(STORAGE_USER_KEY);
    if (storedUser) {
      try {
        return JSON.parse(storedUser);
      } catch { /* ignorar */ }
    }

    // Si hay token, hacer fetch al backend
    const token = localStorage.getItem(STORAGE_TOKEN_KEY);
    if (!token) return null;

    try {
      const response = await fetch(`${API_BASE}/me.php`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      });

      if (!response.ok) {
        if (response.status === 401) {
          logout();
        }
        return null;
      }

      const data = await response.json();
      if (data.user) {
        // Actualizar cache
        localStorage.setItem(STORAGE_USER_KEY, JSON.stringify(data.user));
        return data.user;
      }
      return null;
    } catch (e) {
      // Error de red - devolver datos cacheados si existen
      if (storedUser) {
        try { return JSON.parse(storedUser); } catch { return null; }
      }
      return null;
    }
  };

  /* ---- OBTENER TOKEN ---- */
  const getToken = () => {
    return localStorage.getItem(STORAGE_TOKEN_KEY);
  };

  /* ---- VERIFICAR SI ES ADMIN ---- */
  const isAdmin = async () => {
    const user = await getCurrentUser();
    return user && (user.rol === 'admin' || user.rol === 'superadmin');
  };

  /* ---- REDIRIGIR SI NO ESTÁ LOGUEADO ---- */
  const requireAuth = () => {
    if (!isLoggedIn()) {
      const currentPath = window.location.pathname;
      const redirect = encodeURIComponent(currentPath);
      window.location.href = BASE_PATH + '/frontend/login.php?redirect=' + redirect;
      return false;
    }
    return true;
  };

  /* ---- REDIRIGIR SI NO ES ADMIN ---- */
  const requireAdmin = async () => {
    if (!isLoggedIn()) {
      window.location.href = BASE_PATH + '/frontend/login.php?redirect=' + encodeURIComponent(window.location.pathname);
      return false;
    }

    const admin = await isAdmin();
    if (!admin) {
      window.location.href = '/';
      return false;
    }
    return true;
  };

  /* ---- FORMULARIO DE REGISTRO RÁPIDO ---- */
  const renderQuickRegisterForm = (containerId, onSuccess) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = `
      <form id="quick-register-form" class="auth-form">
        <h3 class="section-title" style="font-size:1.3rem;margin-bottom:20px;">Crear Cuenta</h3>
        <p class="section-subtitle" style="font-size:0.9rem;margin-bottom:24px;">
          Regístrate rápido para completar tu pedido
        </p>

        <div class="form-group">
          <label class="form-label" for="qr-nombre">Nombre Completo</label>
          <input type="text" id="qr-nombre" class="form-input" placeholder="Tu nombre" required autocomplete="name">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="qr-telefono">Teléfono</label>
            <input type="tel" id="qr-telefono" class="form-input" placeholder="+58 412 123 4567" required autocomplete="tel">
          </div>
          <div class="form-group">
            <label class="form-label" for="qr-email">Email</label>
            <input type="email" id="qr-email" class="form-input" placeholder="correo@ejemplo.com" required autocomplete="email">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="qr-password">Contraseña</label>
          <input type="password" id="qr-password" class="form-input" placeholder="Mínimo 6 caracteres" required minlength="6" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
          Registrarse y Continuar
        </button>

        <p style="text-align:center;margin-top:16px;font-size:0.85rem;color:var(--color-gris);">
          ¿Ya tienes cuenta?
          <a href="login.php" style="color:var(--color-amarillo);font-weight:700;">Inicia sesión</a>
        </p>
      </form>
    `;

    const form = document.getElementById('quick-register-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const submitBtn = form.querySelector('button[type="submit"]');
      const nombre = document.getElementById('qr-nombre').value.trim();
      const telefono = document.getElementById('qr-telefono').value.trim();
      const email = document.getElementById('qr-email').value.trim();
      const password = document.getElementById('qr-password').value;

      // Limpiar errores previos
      form.querySelectorAll('.form-error').forEach(el => el.remove());
      form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

      try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Registrando…';

        const result = await register(nombre, telefono, email, password);

        App.showToast('Cuenta creada con éxito', 'success');

        if (typeof onSuccess === 'function') {
          onSuccess(result);
        }

      } catch (error) {
        // Mostrar error en el campo correspondiente
        const fieldId = error.field === 'nombre' ? 'qr-nombre'
          : error.field === 'telefono' ? 'qr-telefono'
          : error.field === 'email' ? 'qr-email'
          : error.field === 'password' ? 'qr-password'
          : null;

        if (fieldId) {
          const input = document.getElementById(fieldId);
          if (input) {
            input.classList.add('error');
            const errorEl = document.createElement('span');
            errorEl.className = 'form-error';
            errorEl.textContent = error.message;
            input.parentNode.appendChild(errorEl);
          }
        } else {
          App.showToast(error.message || 'Error al registrarse', 'error');
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Registrarse y Continuar';
      }
    });
  };

  /* ---- FORMULARIO DE LOGIN ---- */
  const renderLoginForm = (containerId, onSuccess) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Obtener redirect de query params
    const redirect = App.getQueryParam('redirect') || '/';

    container.innerHTML = `
      <form id="login-form" class="auth-form">
        <h3 class="section-title" style="font-size:1.3rem;margin-bottom:20px;">Iniciar Sesión</h3>

        <div class="form-group">
          <label class="form-label" for="login-email">Email</label>
          <input type="email" id="login-email" class="form-input" placeholder="correo@ejemplo.com" required autocomplete="email">
        </div>

        <div class="form-group">
          <label class="form-label" for="login-password">Contraseña</label>
          <input type="password" id="login-password" class="form-input" placeholder="Tu contraseña" required autocomplete="current-password">
        </div>

        <div style="text-align:right;margin-bottom:16px;">
          <a href="recuperar.php" style="font-size:0.85rem;color:var(--color-amarillo);">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
          Iniciar Sesión
        </button>

        <p style="text-align:center;margin-top:16px;font-size:0.85rem;color:var(--color-gris);">
          ¿No tienes cuenta?
          <a href="register.php" style="color:var(--color-amarillo);font-weight:700;">Regístrate</a>
        </p>
      </form>
    `;

    const form = document.getElementById('login-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const submitBtn = form.querySelector('button[type="submit"]');
      const email = document.getElementById('login-email').value.trim();
      const password = document.getElementById('login-password').value;

      form.querySelectorAll('.form-error').forEach(el => el.remove());
      form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

      try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Entrando…';

        await login(email, password);

        // Verificar rol del usuario
        var userData = JSON.parse(localStorage.getItem('auth_user') || '{}');
        var destino = (userData.rol === 'admin' || userData.rol === 'superadmin')
          ? BASE_PATH + '/frontend/admin/dashboard.php'
          : redirect;

        App.showToast(userData.rol === 'admin' ? '¡Bienvenido Jefe!' : '¡Bienvenido de vuelta!', 'success');

        if (typeof onSuccess === 'function') {
          onSuccess();
        } else {
          window.location.href = destino;
        }

      } catch (error) {
        if (error.field === 'general' || !error.field) {
          App.showToast(error.message || 'Error al iniciar sesión', 'error');
        } else {
          const input = document.getElementById(`login-${error.field}`);
          if (input) {
            input.classList.add('error');
            const errorEl = document.createElement('span');
            errorEl.className = 'form-error';
            errorEl.textContent = error.message;
            input.parentNode.appendChild(errorEl);
          } else {
            App.showToast(error.message, 'error');
          }
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Iniciar Sesión';
      }
    });
  };

  /* ---- API PÚBLICA ---- */
  return {
    register,
    login,
    logout,
    isLoggedIn,
    getCurrentUser,
    getToken,
    isAdmin,
    requireAuth,
    requireAdmin,
    decodeJWT,
    renderQuickRegisterForm,
    renderLoginForm,
  };
})();

// Hacer accesible globalmente
window.Auth = Auth;

// Escuchar cambios de autenticación en todo el sitio
document.addEventListener('authChanged', (e) => {
  if (App && App.updateCartBadge) {
    App.updateCartBadge();
  }
});
