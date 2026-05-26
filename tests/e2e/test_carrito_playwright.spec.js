// @ts-check
const { test, expect } = require('@playwright/test');

/**
 * MANÁ FAST FOOD - E2E: FLUJO DEL CARRITO
 * 
 * Prueba: Navegación al menú, agregar productos al carrito,
 * verificar contenidos, actualizar cantidades, cambiar tipo de entrega.
 */

const BASE_URL = process.env.BASE_URL || 'http://localhost:8000';

test.describe('Carrito de Compras - Flujo Completo', () => {
  
  test.beforeEach(async ({ page }) => {
    // Limpiar localStorage antes de cada test
    await page.goto(BASE_URL + '/frontend/index.php');
    await page.evaluate(() => {
      localStorage.removeItem('mana_cart');
      localStorage.removeItem('mana_tasa_bcv');
    });
  });

  // ──────────────────────────────────────────────
  // TEST 1: Navegar a index.php
  // ──────────────────────────────────────────────
  test('debe navegar al índice y mostrar el título', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/index.php');
    
    await expect(page).toHaveTitle(/Maná Fast Food/i);
    
    // Verificar que el header se muestra
    const headerLogo = page.locator('.header-logo');
    await expect(headerLogo).toBeVisible();
  });

  // ──────────────────────────────────────────────
  // TEST 2: Ir a menu.php
  // ──────────────────────────────────────────────
  test('debe navegar al menú y mostrar productos', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/menu.php');
    
    await expect(page).toHaveTitle(/Menú/i);
    
    // Verificar que se ven las categorías
    const hamburguesasSection = page.locator('#hamburguesas');
    await expect(hamburguesasSection).toBeVisible();
    
    // Verificar los filtros
    const filterButtons = page.locator('.filter-btn');
    await expect(filterButtons.first()).toBeVisible();
    await expect(filterButtons).toHaveCount(9); // Todos + 8 categorías
  });

  // ──────────────────────────────────────────────
  // TEST 3: Agregar productos al carrito
  // ──────────────────────────────────────────────
  test('debe agregar 2 productos al carrito desde el menú', async ({ page }) => {
    // Configurar tasa BCV
    await page.goto(BASE_URL + '/frontend/menu.php');
    await page.evaluate(() => {
      localStorage.setItem('mana_tasa_bcv', '62.45');
    });
    await page.reload();
    
    // Esperar que los productos se rendericen
    await page.waitForSelector('.product-card', { timeout: 10000 });
    await page.waitForSelector('.add-to-cart', { timeout: 10000 });
    
    // Obtener todos los botones "Agregar +"
    const addButtons = page.locator('.add-to-cart');
    const buttonCount = await addButtons.count();
    
    // Debe haber al menos 2 botones para agregar
    test.expect(buttonCount).toBeGreaterThanOrEqual(2);
    
    // Agregar el primer producto disponible
    await addButtons.first().click();
    
    // Verificar que el badge del carrito se actualiza
    await page.waitForTimeout(500);
    const cartBadge = page.locator('#cart-badge');
    await expect(cartBadge).toHaveText('1');
    
    // Agregar el segundo producto
    await addButtons.nth(1).click();
    await page.waitForTimeout(500);
    
    // Verificar que el badge muestra 2 (o más si se acumuló)
    const badgeText = await cartBadge.textContent();
    test.expect(parseInt(badgeText)).toBeGreaterThanOrEqual(2);
    
    // Verificar en localStorage
    const cartData = await page.evaluate(() => {
      const data = localStorage.getItem('mana_cart');
      return data ? JSON.parse(data) : null;
    });
    
    test.expect(cartData).not.toBeNull();
    test.expect(cartData.items.length).toBeGreaterThanOrEqual(2);
  });

  // ──────────────────────────────────────────────
  // TEST 4: Ir a carrito.php y verificar productos
  // ──────────────────────────────────────────────
  test('debe mostrar los productos agregados en carrito.php', async ({ page }) => {
    // Primero agregar productos al carrito vía localStorage
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [
          { id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 2, categoria: 'Hamburguesas' },
          { id: 'ham02', nombre: 'Maná Burger', precio: 7.0, cantidad: 1, categoria: 'Hamburguesas' },
        ],
        notas: '',
        tipo: 'delivery',
        direccion: null,
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
      localStorage.setItem('mana_tasa_bcv', '62.45');
    });
    
    await page.reload();
    
    // Debe mostrar los productos en la tabla
    await page.waitForSelector('#cart-items-body', { timeout: 10000 });
    
    // Verificar que los productos están en el DOM
    const cartRows = page.locator('#cart-items-body tr');
    await expect(cartRows).toHaveCount(2);
    
    // Verificar nombres de productos
    await expect(page.locator('text=Sencilla')).toBeVisible();
    await expect(page.locator('text=Maná Burger')).toBeVisible();
    
    // Verificar que el subtotal se muestra
    await expect(page.locator('#subtotal-usd')).toBeVisible();
  });

  // ──────────────────────────────────────────────
  // TEST 5: Actualizar cantidad de un producto
  // ──────────────────────────────────────────────
  test('debe actualizar cantidad al hacer clic en +', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [
          { id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 1, categoria: 'Hamburguesas' },
        ],
        notas: '',
        tipo: 'delivery',
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
      localStorage.setItem('mana_tasa_bcv', '62.45');
    });
    
    await page.reload();
    await page.waitForSelector('.qty-plus', { timeout: 10000 });
    
    // Hacer clic en el botón +
    await page.locator('.qty-plus').click();
    await page.waitForTimeout(500);
    
    // Verificar que la cantidad cambió a 2
    const qtyValue = page.locator('.qty-value');
    await expect(qtyValue).toHaveText('2');
    
    // Verificar que el total se actualizó
    // Precio: 5.5 * 2 = 11.00
    const subtotal = page.locator('#subtotal-usd');
    await expect(subtotal).toContainText('11.00');
  });

  // ──────────────────────────────────────────────
  // TEST 6: Verificar que el total USD se actualiza
  // ──────────────────────────────────────────────
  test('debe actualizar el total USD al cambiar cantidades', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [
          { id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 2, categoria: 'Hamburguesas' },
          { id: 'hd01', nombre: 'Maná Sencillo', precio: 1.8, cantidad: 3, categoria: 'Hot Dogs' },
        ],
        notas: '',
        tipo: 'delivery',
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
      localStorage.setItem('mana_tasa_bcv', '62.45');
    });
    
    await page.reload();
    await page.waitForSelector('#total-usd', { timeout: 10000 });
    
    // Calcular: (5.5 * 2) + (1.8 * 3) = 11 + 5.4 = 16.40
    const totalUSD = page.locator('#total-usd');
    await expect(totalUSD).toContainText('16.40');
    
    // Verificar total en Bs: 16.40 * 62.45 = 1024.18
    const totalBS = page.locator('#total-bs');
    await expect(totalBS).toContainText('1.024');
  });

  // ──────────────────────────────────────────────
  // TEST 7: Cambiar tipo de entrega a Delivery
  // ──────────────────────────────────────────────
  test('debe cambiar tipo de entrega a Delivery y Retiro', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [{ id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 1, categoria: 'Hamburguesas' }],
        notas: '',
        tipo: 'delivery',
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
    });
    
    await page.reload();
    await page.waitForSelector('.delivery-toggle', { timeout: 10000 });
    
    // Verificar que Delivery está activo por defecto
    const deliveryBtn = page.locator('.delivery-toggle-btn[data-tipo="delivery"]');
    await expect(deliveryBtn).toHaveClass(/active/);
    
    // Cambiar a Retiro
    const retiroBtn = page.locator('.delivery-toggle-btn[data-tipo="retiro"]');
    await retiroBtn.click();
    await page.waitForTimeout(300);
    
    // Verificar que Retiro está activo
    await expect(retiroBtn).toHaveClass(/active/);
    await expect(deliveryBtn).not.toHaveClass(/active/);
    
    // Verificar que se guardó en localStorage
    const tipoGuardado = await page.evaluate(() => {
      return JSON.parse(localStorage.getItem('mana_cart')).tipo;
    });
    test.expect(tipoGuardado).toBe('retiro');
  });

  // ──────────────────────────────────────────────
  // TEST 8: Ingresar notas
  // ──────────────────────────────────────────────
  test('debe guardar notas al escribir en el textarea', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [{ id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 1, categoria: 'Hamburguesas' }],
        notas: '',
        tipo: 'delivery',
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
    });
    
    await page.reload();
    await page.waitForSelector('#cart-notas', { timeout: 10000 });
    
    // Escribir notas
    const notasInput = page.locator('#cart-notas');
    await notasInput.fill('Sin cebolla, punto tres cuartos');
    
    // Disparar evento blur para guardar
    await notasInput.blur();
    await page.waitForTimeout(300);
    
    // Verificar que se guardó en localStorage
    const notasGuardadas = await page.evaluate(() => {
      return JSON.parse(localStorage.getItem('mana_cart')).notas;
    });
    test.expect(notasGuardadas).toBe('Sin cebolla, punto tres cuartos');
  });

  // ──────────────────────────────────────────────
  // TEST 9: Botón "CONTINUAR AL PAGO" va a checkout
  // ──────────────────────────────────────────────
  test('debe navegar a checkout.php al hacer clic en continuar', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [{ id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 1, categoria: 'Hamburguesas' }],
        notas: '',
        tipo: 'delivery',
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
    });
    
    await page.reload();
    await page.waitForSelector('#btn-continuar', { timeout: 10000 });
    
    // Hacer clic en continuar al pago
    await page.locator('#btn-continuar').click();
    
    // Debe navegar a checkout.php
    await expect(page).toHaveURL(/checkout\.php/);
    
    // Verificar que el checkout carga datos del carrito
    await expect(page.locator('#checkout-app')).toBeVisible({ timeout: 10000 });
  });

  // ──────────────────────────────────────────────
  // TEST 10: Carrito vacío muestra mensaje
  // ──────────────────────────────────────────────
  test('debe mostrar mensaje de carrito vacío cuando no hay items', async ({ page }) => {
    await page.goto(BASE_URL + '/frontend/carrito.php');
    
    // Asegurar carrito vacío
    await page.evaluate(() => {
      localStorage.removeItem('mana_cart');
    });
    
    await page.reload();
    
    // Verificar mensaje de carrito vacío
    const emptyState = page.locator('#cart-empty-state');
    await expect(emptyState).toBeVisible({ timeout: 10000 });
    
    // Verificar que el botón "VER MENÚ" está visible
    await expect(page.locator('a[href="menu.php"]').filter({ hasText: 'VER MENÚ' })).toBeVisible();
  });
});
