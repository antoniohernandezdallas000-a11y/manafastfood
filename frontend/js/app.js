/* ============================================================
   MANÁ FAST FOOD - APP.JS
   Utilidades generales, menú mobile, header/footer dinámicos,
   notificaciones toast, Google Maps, formateo de moneda
   ============================================================ */

const App = (() => {
  'use strict';

  /* ---- CONFIGURACIÓN ---- */
  // Detectar automáticamente la ruta base del proyecto
  const getBasePath = () => {
    const scripts = document.getElementsByTagName('script');
    const currentScript = scripts[scripts.length - 1];
    const src = currentScript ? currentScript.src : '';
    // Extraer la parte /mana-fast-food/frontend/js/app.js → /mana-fast-food
    const match = src.match(/^https?:\/\/[^\/]+(\/[^\/]+)\/frontend\/js\//);
    if (match) return match[1];
    // Fallback: detectar desde la URL actual
    const pathParts = window.location.pathname.split('/');
    // Si estamos en /mana-fast-food/frontend/..., el base es /mana-fast-food
    if (pathParts.length >= 3) {
      return '/' + pathParts[1];
    }
    return '';
  };

  const BASE_PATH = getBasePath();
  // Apuntar directamente a los archivos PHP (el router tiene issues con PATH_INFO)
  // Usar la URL completa para evitar problemas de ruteo
  const API_BASE_DIR = BASE_PATH + '/backend/api';

  const CONFIG = {
    API_BASE: API_BASE_DIR,
    BASE_PATH: BASE_PATH,
    MAPS_API_KEY: '',        // Configurar desde backend o variable
    DEFAULT_LAT: 10.4806,    // Caracas
    DEFAULT_LNG: -66.9036,
    ZOOM: 15,
    TOAST_DURATION: 4000,
  };

  /* ---- INICIALIZACIÓN ---- */
  const init = () => {
    loadHeaderFooter();
    initMobileMenu();
    initScrollEffects();
    initSmoothScroll();
    observeAnimations();
  };

  /* ---- HEADER / FOOTER DINÁMICO ---- */
  const loadHeaderFooter = () => {
    const headerPlaceholder = document.getElementById('header-placeholder');
    const footerPlaceholder = document.getElementById('footer-placeholder');

    if (headerPlaceholder) {
      headerPlaceholder.innerHTML = getHeaderHTML();
      updateCartBadge();
      initMobileMenu(); // Re-inicializar después de insertar
    }

    if (footerPlaceholder) {
      footerPlaceholder.innerHTML = getFooterHTML();
    }
  };

  const getHeaderHTML = () => {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const isActive = (page) => currentPage === page ? 'active' : '';

    return `
      <header class="header" id="main-header">
        <div class="container">
          <a href="index.php" class="header-logo">
            <img src="${BASE_PATH}/images/logo-mana.jpeg" alt="Maná Fast Food" height="42">
            <span>Maná <span class="rojo">Fast Food</span></span>
          </a>

          <nav class="header-nav" id="main-nav">
            <a href="index.php" class="${isActive('index.php')}" data-nav>Inicio</a>
            <a href="menu.php" class="${isActive('menu.php')}" data-nav>Menú</a>
            <a href="ofertas.php" class="${isActive('ofertas.php')}" data-nav>Ofertas</a>
            <a href="nosotros.php" class="${isActive('nosotros.php')}" data-nav>Nosotros</a>
            <a href="contacto.php" class="${isActive('contacto.php')}" data-nav>Contacto</a>
            <a href="carrito.php" class="cart-btn" id="cart-header-btn">
              <svg viewBox="0 0 24 24" width="20" height="20"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
              <span>Carrito</span>
              <span class="cart-badge" id="cart-badge">0</span>
            </a>
          </nav>

          <button class="hamburger" id="hamburger-btn" aria-label="Menú">
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
      </header>
    `;
  };

  const getFooterHTML = () => {
    return `
      <footer class="footer">
        <div class="container">
          <div class="footer-grid">
            <div>
              <h3>Maná Fast Food</h3>
              <p>Sabor que alimenta el alma.<br>La mejor comida rápida de la ciudad.</p>
              <div class="footer-social">
                <a href="#" aria-label="Instagram">IG</a>
                <a href="#" aria-label="WhatsApp">WA</a>
                <a href="#" aria-label="Twitter">X</a>
                <a href="#" aria-label="TikTok">TK</a>
              </div>
            </div>
            <div>
              <h3>Horarios</h3>
              <p>Lun - Sáb: 11:00 AM - 11:00 PM</p>
              <p>Dom: 12:00 PM - 10:00 PM</p>
            </div>
            <div>
              <h3>Enlaces</h3>
              <a href="menu.php">Menú</a><br>
              <a href="ofertas.php">Ofertas</a><br>
              <a href="nosotros.php">Nosotros</a><br>
              <a href="contacto.php">Contacto</a>
            </div>
            <div>
              <h3>Contacto</h3>
              <p>+58 412-1234567</p>
              <p>info@manafastfood.com</p>
              <p>Caracas, Venezuela</p>
            </div>
          </div>
          <div class="footer-bottom">
            &copy; ${new Date().getFullYear()} Maná Fast Food. Todos los derechos reservados.
          </div>
        </div>
      </footer>
    `;
  };

  /* ---- MENÚ MOBILE ---- */
  const initMobileMenu = () => {
    const hamburger = document.getElementById('hamburger-btn');
    const nav = document.getElementById('main-nav');
    const navLinks = document.querySelectorAll('[data-nav]');

    if (!hamburger || !nav) return;

    const toggleMenu = () => {
      hamburger.classList.toggle('active');
      nav.classList.toggle('open');
      document.body.style.overflow = nav.classList.contains('open') ? 'hidden' : '';
    };

    const closeMenu = () => {
      hamburger.classList.remove('active');
      nav.classList.remove('open');
      document.body.style.overflow = '';
    };

    hamburger.addEventListener('click', toggleMenu);

    navLinks.forEach(link => {
      link.addEventListener('click', closeMenu);
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', (e) => {
      if (nav.classList.contains('open') &&
          !nav.contains(e.target) &&
          !hamburger.contains(e.target)) {
        closeMenu();
      }
    });

    // Cerrar al redimensionar a desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768) {
        closeMenu();
      }
    });
  };

  /* ---- SCROLL EFFECTS ---- */
  const initScrollEffects = () => {
    const header = document.getElementById('main-header');
    if (!header) return;

    let lastScroll = 0;

    window.addEventListener('scroll', () => {
      const currentScroll = window.scrollY;

      // Clase scrolled
      if (currentScroll > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }

      lastScroll = currentScroll;
    }, { passive: true });
  };

  /* ---- SMOOTH SCROLL ---- */
  const initSmoothScroll = () => {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', (e) => {
        const href = anchor.getAttribute('href');
        if (href && href.length > 1) {
          const target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            const offset = 80;
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
          }
        }
      });
    });
  };

  /* ---- INTERSECTION OBSERVER (fade-in) ---- */
  const observeAnimations = () => {
    if (!('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
      observer.observe(el);
    });
  };

  /* ---- TOAST NOTIFICATIONS ---- */
  const showToast = (message, type = 'info') => {
    const container = document.getElementById('toast-container');
    if (!container) {
      // Crear contenedor si no existe
      const newContainer = document.createElement('div');
      newContainer.id = 'toast-container';
      newContainer.className = 'toast-container';
      document.body.appendChild(newContainer);
      showToast(message, type);
      return;
    }

    const icons = {
      success: `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
      error: `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
      info: `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`,
      warning: `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      ${icons[type] || icons.info}
      <span class="toast-message">${message}</span>
      <button class="toast-close" aria-label="Cerrar">&times;</button>
    `;

    container.appendChild(toast);

    // Cerrar manual
    toast.querySelector('.toast-close').addEventListener('click', () => {
      closeToast(toast);
    });

    // Auto-cerrar
    const timeout = setTimeout(() => closeToast(toast), CONFIG.TOAST_DURATION);
    toast._timeout = timeout;
  };

  const closeToast = (toast) => {
    if (toast._timeout) clearTimeout(toast._timeout);
    toast.classList.add('fade-out');
    setTimeout(() => {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 300);
  };

  /* ---- FORMATEO DE MONEDA ---- */
  const formatUSD = (amount) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 2,
    }).format(amount);
  };

  const formatBS = (amount) => {
    return new Intl.NumberFormat('es-VE', {
      style: 'currency',
      currency: 'VES',
      minimumFractionDigits: 2,
    }).format(amount);
  };

  const formatCOP = (amount) => {
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      minimumFractionDigits: 0,
    }).format(amount);
  };

  /* ---- FETCH API WRAPPER ---- */
  const apiFetch = async (endpoint, options = {}) => {
    // Agregar .php si no tiene extensión (todos los endpoints api/ son archivos .php)
    const ext = endpoint.includes('.') ? '' : '.php';
    const url = `${CONFIG.API_BASE}${endpoint}${ext}`;
    const token = localStorage.getItem('auth_token');

    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers,
    };

    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    // Si hay FormData, no setear Content-Type
    if (options.body instanceof FormData) {
      delete headers['Content-Type'];
    }

    try {
      const response = await fetch(url, {
        ...options,
        headers,
      });

      // Intentar parsear JSON
      let data = null;
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        data = await response.json();
      } else {
        const text = await response.text();
        try {
          data = JSON.parse(text);
        } catch {
          data = { message: text };
        }
      }

      if (!response.ok) {
        const error = new Error(data.message || `Error ${response.status}`);
        error.status = response.status;
        error.data = data;
        throw error;
      }

      return data;
    } catch (error) {
      if (error.status) throw error;
      // Error de red
      const networkError = new Error('Error de conexión. Verifica tu internet.');
      networkError.status = 0;
      throw networkError;
    }
  };

  /* ---- GOOGLE MAPS ---- */
  let mapsInstance = null;
  let mapMarker = null;

  const loadGoogleMapsAPI = (callback) => {
    if (window.google && window.google.maps) {
      if (callback) callback();
      return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${CONFIG.MAPS_API_KEY}&libraries=places&callback=initGoogleMapsCallback`;
    script.async = true;
    script.defer = true;

    window.initGoogleMapsCallback = () => {
      if (callback) callback();
    };

    document.head.appendChild(script);
  };

  const initGoogleMaps = (elementId, lat = CONFIG.DEFAULT_LAT, lng = CONFIG.DEFAULT_LNG) => {
    const mapElement = document.getElementById(elementId);
    if (!mapElement) return;

    // Si no hay API key configurada, el iframe de OpenStreetMap se queda visible (no inicializar Google Maps)
    if (!CONFIG.MAPS_API_KEY || CONFIG.MAPS_API_KEY.trim() === '') {
      return;
    }

    // Ocultar el iframe OSM de respaldo y cargar Google Maps
    const osmIframe = mapElement.querySelector('iframe');
    if (osmIframe) {
      osmIframe.style.display = 'none';
    }

    loadGoogleMapsAPI(() => {
      const position = { lat, lng };

      mapsInstance = new google.maps.Map(mapElement, {
        center: position,
        zoom: CONFIG.ZOOM,
        styles: [
          { elementType: 'geometry', stylers: [{ color: '#1a1a1a' }] },
          { elementType: 'labels.text.stroke', stylers: [{ color: '#1a1a1a' }] },
          { elementType: 'labels.text.fill', stylers: [{ color: '#888888' }] },
          {
            featureType: 'road',
            elementType: 'geometry',
            stylers: [{ color: '#333333' }],
          },
          {
            featureType: 'road',
            elementType: 'labels.text.fill',
            stylers: [{ color: '#cccccc' }],
          },
          {
            featureType: 'poi',
            elementType: 'geometry',
            stylers: [{ color: '#222222' }],
          },
          {
            featureType: 'water',
            elementType: 'geometry',
            stylers: [{ color: '#0a1a2a' }],
          },
        ],
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
      });

      // Marcador
      mapMarker = new google.maps.Marker({
        position,
        map: mapsInstance,
        title: 'Maná Fast Food',
        animation: google.maps.Animation.DROP,
        icon: {
          url: 'img/marker-mana.png',
          scaledSize: new google.maps.Size(40, 40),
        },
      });

      // Autocompletado de dirección
      const input = document.getElementById('direccion-input');
      if (input) {
        const autocomplete = new google.maps.places.Autocomplete(input, {
          componentRestrictions: { country: 'VE' },
          types: ['address'],
        });

        autocomplete.addListener('place_changed', () => {
          const place = autocomplete.getPlace();
          if (place.geometry) {
            mapsInstance.setCenter(place.geometry.location);
            mapMarker.setPosition(place.geometry.location);
            mapsInstance.setZoom(16);
          }
        });
      }

      // Geolocalización
      const geoBtn = document.getElementById('geo-btn');
      if (geoBtn) {
        geoBtn.addEventListener('click', () => {
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              (position) => {
                const pos = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude,
                };
                mapsInstance.setCenter(pos);
                mapMarker.setPosition(pos);
                mapsInstance.setZoom(16);
                showToast('Ubicación encontrada', 'success');
              },
              () => {
                showToast('No se pudo obtener tu ubicación', 'error');
              }
            );
          } else {
            showToast('Geolocalización no soportada por tu navegador', 'error');
          }
        });
      }
    });
  };

  /* ---- CART BADGE UPDATE (se conecta con cart.js) ---- */
  const updateCartBadge = () => {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;

    try {
      const cart = JSON.parse(localStorage.getItem('mana_cart') || '{"items":[]}');
      const count = cart.items.reduce((sum, item) => sum + (item.cantidad || 0), 0);
      badge.textContent = count;

      if (count > 0) {
        badge.style.display = 'flex';
        badge.classList.remove('bounce');
        // Forzar reflow para reiniciar animación
        void badge.offsetWidth;
        badge.classList.add('bounce');
      } else {
        badge.style.display = 'none';
      }
    } catch {
      badge.textContent = '0';
      badge.style.display = 'none';
    }
  };

  // Escuchar evento de carrito actualizado
  document.addEventListener('cartUpdated', updateCartBadge);

  /* ---- UTILIDADES ---- */
  const getQueryParam = (name) => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  };

  const debounce = (fn, delay = 300) => {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  };

  const truncateText = (text, maxLength = 100) => {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
  };

  const generateId = () => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2, 9);
  };

  const getDeviceInfo = () => {
    return {
      userAgent: navigator.userAgent,
      platform: navigator.platform,
      language: navigator.language,
      screenSize: `${window.screen.width}x${window.screen.height}`,
    };
  };

  /* ---- API PÚBLICA ---- */
  return {
    init,
    showToast,
    formatUSD,
    formatBS,
    formatCOP,
    apiFetch,
    loadGoogleMapsAPI,
    initGoogleMaps,
    updateCartBadge,
    getQueryParam,
    debounce,
    truncateText,
    generateId,
    getDeviceInfo,
    CONFIG,
  };
})();

/* ---- INICIALIZAR EN DOM READY ---- */
document.addEventListener('DOMContentLoaded', () => {
  App.init();
});
