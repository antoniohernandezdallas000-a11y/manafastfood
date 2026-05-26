// @ts-check
const { defineConfig, devices } = require('@playwright/test');

/**
 * MANÁ FAST FOOD - PLAYWRIGHT CONFIGURATION
 * 
 * Configuración para tests E2E del frontend.
 * 
 * Uso:
 *   npx playwright test                    # Todos los tests E2E (headless)
 *   npx playwright test --ui              # UI interactiva
 *   npx playwright test --debug           # Modo debug
 *   npx playwright show-report            # Ver reporte HTML
 * 
 * Requisitos:
 *   - Servidor PHP corriendo en http://localhost:8000
 *   - Node.js 18+
 *   - npx playwright install
 */

module.exports = defineConfig({
  // Directorio de los tests
  testDir: './e2e',
  
  // Patrón para archivos de test
  testMatch: '**/*_playwright.spec.js',
  
  // Timeout global para cada test (30 segundos)
  timeout: 30000,
  
  // Timeout para expect
  expect: {
    timeout: 10000,
  },
  
  // Paralelismo
  fullyParallel: false,
  
  // Evitar que tests se ejecuten en paralelo si dependen del mismo estado
  workers: 1,
  
  // Reporters
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['list'],
    ['junit', { outputFile: 'playwright-junit.xml' }],
  ],
  
  // Configuración global del proyecto
  use: {
    // URL base del frontend
    baseURL: process.env.BASE_URL || 'http://localhost:8000/frontend',
    
    // Navegador headless por defecto
    headless: true,
    
    // Capturar screenshot solo en fallos
    screenshot: 'only-on-failure',
    
    // Grabar video en retry
    video: 'retry-with-video',
    
    // Ignorar errores HTTPS
    ignoreHTTPSErrors: true,
    
    // Locale
    locale: 'es-VE',
    
    // Timezone
    timezoneId: 'America/Caracas',
    
    // Viewport
    viewport: { width: 1280, height: 720 },
    
    // Rastrear acciones
    trace: 'retain-on-failure',
  },
  
  // Proyectos: navegadores
  projects: [
    {
      name: 'Chromium',
      use: { browserName: 'chromium' },
    },
    {
      name: 'Firefox',
      use: { browserName: 'firefox' },
    },
    {
      name: 'WebKit',
      use: { browserName: 'webkit' },
    },
  ],
  
  // No ejecutar tests locales en paralelo con múltiples proyectos
  // a menos que tengamos servidores separados
});
