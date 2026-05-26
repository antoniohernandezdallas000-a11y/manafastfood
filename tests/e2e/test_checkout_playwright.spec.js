// @ts-check
const { test, expect } = require('@playwright/test');
const path = require('path');

/**
 * MANÁ FAST FOOD - E2E: FLUJO DE CHECKOUT
 * 
 * Prueba: Navegación al checkout, llenar datos de delivery,
 * verificar PagoMóvil, montos, subir capture, confirmar pago.
 */

const BASE_URL = process.env.BASE_URL || 'http://localhost:8000';

test.describe('Checkout - Flujo Completo de Pago', () => {
  
  test.beforeEach(async ({ page }) => {
    // Configurar carrito en localStorage antes de cada test
    await page.goto(BASE_URL + '/frontend/checkout.php');
    
    await page.evaluate(() => {
      const cart = {
        items: [
          { id: 'ham01', nombre: 'Sencilla', precio: 5.5, cantidad: 2, categoria: 'Hamburguesas', descripcion: 'Clásica' },
          { id: 'beb01', nombre: 'Refresco', precio: 1.5, cantidad: 1, categoria: 'Bebidas', descripcion: 'Coca-Cola' },
        ],
        notas: 'Sin hielo en el refresco',
        tipo: 'delivery',
        direccion: null,
      };
      localStorage.setItem('mana_cart', JSON.stringify(cart));
      localStorage.setItem('mana_tasa_bcv', '62.45');
    });
    
    await page.reload();
  });

  // ──────────────────────────────────────────────
  // TEST 1: Cargar checkout con datos del carrito
  // ──────────────────────────────────────────────
  test('debe cargar checkout con el resumen del pedido', async ({ page }) => {
    await page.waitForTimeout(2000);
    
    // Verificar que el indicador de pasos está visible
    const stepIndicator = page.locator('#custom-step-indicator');
    await expect(stepIndicator).toBeVisible({ timeout: 10000 });
    
    // Verificar paso 1 activo
    const step1 = page.locator('.custom-step[data-cstep="1"]');
    await expect(step1).toHaveClass(/done/); // Carrito ya completado
    
    const step2 = page.locator('.custom-step[data-cstep="2"]');
    await expect(step2).toHaveClass(/active/); // Pago activo
  });

  // ──────────────────────────────────────────────
  // TEST 2: Paso 1 activo - Datos del Pedido
  // ──────────────────────────────────────────────
  test('debe mostrar el paso 1 con formulario de delivery', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Verificar que step-1 está visible
    const step1Content = page.locator('#step-1');
    await expect(step1Content).toBeVisible({ timeout: 10000 });
    
    // Verificar que los campos de delivery están visibles
    const phoneInput = page.locator('#checkout-phone');
    await expect(phoneInput).toBeVisible();
    
    const addressInput = page.locator('#direccion-input');
    await expect(addressInput).toBeVisible();
    
    // Verificar el botón continuar
    const continueBtn = page.locator('#btn-go-step2');
    await expect(continueBtn).toBeVisible();
    await expect(continueBtn).toHaveText('CONTINUAR AL PAGO');
  });

  // ──────────────────────────────────────────────
  // TEST 3: Llenar datos de delivery
  // ──────────────────────────────────────────────
  test('debe llenar datos de delivery y pasar al paso 2', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Llenar teléfono
    const phoneInput = page.locator('#checkout-phone');
    await phoneInput.fill('04141234567');
    
    // Llenar dirección
    const addressInput = page.locator('#direccion-input');
    await addressInput.fill('Av. Principal, Edif. Test, Piso 5, Apto 5A, Caracas');
    
    // Llenar referencia
    const refDir = page.locator('#checkout-reference-dir');
    await refDir.fill('Frente al centro comercial, edificio blanco');
    
    // Hacer clic en CONTINUAR AL PAGO
    const continueBtn = page.locator('#btn-go-step2');
    await continueBtn.click();
    
    await page.waitForTimeout(1000);
    
    // Verificar paso 2 activo
    const step2 = page.locator('.custom-step[data-cstep="2"]');
    await expect(step2).toHaveClass(/active/);
    
    // Verificar que el paso 2 está visible
    const step2Content = page.locator('#step-2');
    await expect(step2Content).toBeVisible();
  });

  // ──────────────────────────────────────────────
  // TEST 4: Ver datos PagoMóvil en paso 2
  // ──────────────────────────────────────────────
  test('debe mostrar datos PagoMóvil en el paso 2', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Ir al paso 2 primero
    await page.locator('#checkout-phone').fill('04141234567');
    await page.locator('#direccion-input').fill('Dirección test');
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(1000);
    
    // Verificar que hay datos PagoMóvil
    const pagomovilContainer = page.locator('#pagomovil-cuentas');
    await expect(pagomovilContainer).toBeVisible();
    
    // Verificar que el título de la sección está visible
    await expect(page.locator('.pagomovil-info-box')).toBeVisible();
    await expect(page.locator('.info-title')).toContainText('PagoMóvil');
  });

  // ──────────────────────────────────────────────
  // TEST 5: Ver total USD y BS en checkout
  // ──────────────────────────────────────────────
  test('debe mostrar el total en USD y BS', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Ir al paso 2
    await page.locator('#checkout-phone').fill('04141234567');
    await page.locator('#direccion-input').fill('Dirección test');
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(1000);
    
    // Verificar total USD: (5.5 * 2) + (1.5 * 1) = 12.50
    const totalUSD = page.locator('#checkout-total-usd');
    await expect(totalUSD).toContainText('12.50');
    
    // Verificar total BS: 12.50 * 62.45 = 780.63
    const totalBS = page.locator('#checkout-total-bs');
    await expect(totalBS).toContainText('780.63');
  });

  // ──────────────────────────────────────────────
  // TEST 6: Ingresar número de referencia
  // ──────────────────────────────────────────────
  test('debe permitir ingresar referencia de pago', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Ir al paso 2
    await page.locator('#checkout-phone').fill('04141234567');
    await page.locator('#direccion-input').fill('Dirección test');
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(1000);
    
    // Ingresar referencia
    const refInput = page.locator('#referencia-input');
    await expect(refInput).toBeVisible();
    await refInput.fill('BCV-123456789');
    
    // Verificar que el valor se mantiene
    const value = await refInput.inputValue();
    test.expect(value).toBe('BCV-123456789');
  });

  // ──────────────────────────────────────────────
  // TEST 7: Subir archivo de imagen (simulado)
  // ──────────────────────────────────────────────
  test('debe subir un archivo de capture de pago', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Ir al paso 2
    await page.locator('#checkout-phone').fill('04141234567');
    await page.locator('#direccion-input').fill('Dirección test');
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(1000);
    
    // Verificar el input file
    const fileInput = page.locator('#capture-input');
    await expect(fileInput).toBeVisible();
    
    // Crear un archivo de imagen simulado
    const filePath = path.resolve(__dirname, '../fixtures/test_capture.jpg');
    
    // Subir archivo
    await fileInput.setInputFiles(filePath);
    
    // Verificar que el preview aparece
    const preview = page.locator('.file-upload-preview');
    await expect(preview).toBeVisible();
  });

  // ──────────────────────────────────────────────
  // TEST 8: Hacer clic en CONFIRMAR PAGO
  // ──────────────────────────────────────────────
  test('debe tener botón CONFIRMAR PAGO visible', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Ir al paso 2
    await page.locator('#checkout-phone').fill('04141234567');
    await page.locator('#direccion-input').fill('Dirección test');
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(1000);
    
    // Verificar botón de confirmar pago
    const confirmBtn = page.locator('#btn-confirmar-pago');
    await expect(confirmBtn).toBeVisible();
    await expect(confirmBtn).toContainText('CONFIRMAR PAGO');
  });

  // ──────────────────────────────────────────────
  // TEST 9: Validar que no se puede continuar sin teléfono
  // ──────────────────────────────────────────────
  test('debe mostrar error si no se ingresa teléfono en delivery', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Dejar teléfono vacío e intentar continuar
    await page.locator('#checkout-phone').fill('');
    await page.locator('#direccion-input').fill('Dirección test');
    
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(500);
    
    // Debe seguir en paso 1 (no avanzó porque hay error de validación)
    const step1Content = page.locator('#step-1');
    await expect(step1Content).toBeVisible();
    
    // Verificar que el campo teléfono tiene clase error
    const phoneInput = page.locator('#checkout-phone');
    await expect(phoneInput).toHaveClass(/error/);
  });

  // ──────────────────────────────────────────────
  // TEST 10: Volver al paso 1 desde paso 2
  // ──────────────────────────────────────────────
  test('debe volver al paso 1 desde paso 2', async ({ page }) => {
    await page.waitForTimeout(1500);
    
    // Ir al paso 2
    await page.locator('#checkout-phone').fill('04141234567');
    await page.locator('#direccion-input').fill('Dirección test');
    await page.locator('#btn-go-step2').click();
    await page.waitForTimeout(1000);
    
    // Verificar que estamos en paso 2
    await expect(page.locator('#step-2')).toBeVisible();
    
    // Hacer clic en "Volver a datos del pedido"
    const backBtn = page.locator('#btn-back-step1');
    await expect(backBtn).toBeVisible();
    await backBtn.click();
    await page.waitForTimeout(500);
    
    // Verificar que volvimos al paso 1
    await expect(page.locator('#step-1')).toBeVisible();
    await expect(page.locator('#step-2')).not.toBeVisible();
  });

  // ──────────────────────────────────────────────
  // TEST 11: Verificar resumen del pedido en sidebar
  // ──────────────────────────────────────────────
  test('debe mostrar resumen del pedido en sidebar', async ({ page }) => {
    await page.waitForTimeout(2000);
    
    // Verificar el contenedor del resumen
    const summaryContainer = page.locator('#checkout-summary');
    await expect(summaryContainer).toBeVisible({ timeout: 10000 });
    
    // Verificar que se ven los productos
    await expect(summaryContainer.locator('text=Sencilla')).toBeVisible();
    await expect(summaryContainer.locator('text=Refresco')).toBeVisible();
  });
});
