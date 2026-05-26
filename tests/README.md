# MANÁ FAST FOOD - SUITE DE TESTS

Suite completa de tests para el sistema de pedidos de Maná Fast Food.

### 🏗️ Estructura

```
tests/
├── unit/                      # Tests unitarios (PHPUnit con Mocks)
│   ├── test_auth.php          # Autenticación: registro, login, JWT, roles
│   ├── test_productos.php     # Productos: CRUD, búsqueda, ofertas
│   ├── test_pedidos.php       # Pedidos: creación, estados, pagos
│   └── test_checkout.php      # Checkout: PagoMóvil, tasa BCV, uploads
│
├── integration/               # Tests de integración (BD real)
│   └── test_flujo_completo.php # Flujo completo registro → pago → admin
│
├── e2e/                       # Tests end-to-end (Playwright)
│   ├── test_carrito_playwright.spec.js   # Menú → Carrito → Checkout
│   ├── test_checkout_playwright.spec.js  # Checkout → Pago → Confirmación
│   └── test_admin_playwright.spec.js     # Admin: login, CRUD, gestión
│
├── fixtures/
│   └── data.php               # Datos de prueba reutilizables
│
├── helpers/
│   └── TestHelper.php         # Clase utilitaria para tests
│
├── bootstrap.php              # Bootstrap global de PHPUnit
├── phpunit.xml                # Configuración de PHPUnit
├── playwright.config.js       # Configuración de Playwright
└── README.md                  # Este archivo
```

---

## 📦 Requisitos

- **PHP 8.0+** con extensiones: `pdo_mysql`, `mbstring`, `gd`
- **Composer** (gestor de dependencias PHP)
- **Node.js 18+** (para tests E2E con Playwright)
- **MySQL 8.0+** (para tests de integración)

---

## 🔧 Instalación

```bash
# 1. Instalar dependencias PHP (PHPUnit, Dotenv)
composer install

# 2. Instalar dependencias Node.js (Playwright)
npm install

# 3. Instalar navegadores Playwright
npx playwright install

# 4. Copiar y configurar .env
cp .env.example .env
# Editar .env con los datos de tu BD de test
```

### Configurar base de datos de test

```sql
CREATE DATABASE IF NOT EXISTS mana_fast_food_test;
USE mana_fast_food_test;
SOURCE sql/schema.sql;
```

Luego en `.env`:
```
DB_NAME=mana_fast_food_test
```

---

## 🚀 Ejecutar Tests

### Tests Unitarios (Rápidos - no requieren BD)

```bash
# Todos los tests unitarios
vendor/bin/phpunit --testsuite Unit

# Test específico
vendor/bin/phpunit tests/unit/test_auth.php
vendor/bin/phpunit tests/unit/test_productos.php
vendor/bin/phpunit tests/unit/test_pedidos.php
vendor/bin/phpunit tests/unit/test_checkout.php

# Con más detalle
vendor/bin/phpunit --testsuite Unit --verbose --debug
```

### Tests de Integración (Requieren BD MySQL)

```bash
# Todos los tests de integración
vendor/bin/phpunit --testsuite Integration

# Test específico
vendor/bin/phpunit tests/integration/test_flujo_completo.php
```

### Tests E2E (Requieren servidor PHP + Playwright)

**Paso 1:** Iniciar servidor PHP en otra terminal:

```bash
# Desde la raíz del proyecto
php -S localhost:8000
```

**Paso 2:** Ejecutar tests:

```bash
# Todos los tests E2E
npx playwright test

# Test específico
npx playwright test tests/e2e/test_carrito_playwright.spec.js

# Con interfaz gráfica
npx playwright test --ui

# Modo debug (paso a paso)
npx playwright test --debug

# Solo un navegador
npx playwright test --project=Chromium

# Ver reporte HTML
npx playwright show-report
```

### Cobertura de Código

```bash
vendor/bin/phpunit --coverage-html tests/coverage
```

Abrir `tests/coverage/index.html` en el navegador.

---

## 📊 Resumen de Cobertura por Módulo

| Módulo | Cobertura Estimada | Tests | Cubre |
|--------|-------------------|-------|-------|
 | **Autenticación** | 90% | 15 tests | Registro, login, JWT, roles, permisos |
| **Productos** | 85% | 12 tests | CRUD, búsqueda, categorías, ofertas |
| **Pedidos** | 85% | 12 tests | Creación, estados, pagos, permisos |
| **Checkout/Pago** | 80% | 16 tests | PagoMóvil, tasa BCV, uploads, referencias |
| **Flujo Completo** | 75% | 14 tests | Integración BD real |
| **Frontend E2E** | 70% | 30+ casos | Carrito, checkout, admin panel |

---

## 🎯 3 Casos Críticos Antes de Cada Deploy

### 🔴 CASO 1: Registro + Login + Crear Pedido (Flujo Cliente)
```
Registrar usuario → Login → Obtener token → 
Listar productos → Crear pedido → Verificar pedido creado
```
**Por qué es crítico:** Es el flujo principal del negocio. Si falla, ningún cliente puede pedir.

### 🔴 CASO 2: Checkout + Pago + WhatsApp
```
Navegar checkout → Llenar datos delivery → 
Ver montos USD/BS → Ingresar referencia → 
Subir capture → Confirmar pago → Ver éxito → Ver WhatsApp
```
**Por qué es crítico:** Es donde se genera el ingreso. Un error aquí significa pérdida de ventas.

### 🔴 CASO 3: Admin CRUD + Cambio de Estado
```
Login admin → Dashboard stats → Ver productos →
Crear/editar producto → Ver pedidos → 
Cambiar estado de pedido → Gestionar PagoMóvil →
Configurar tasa BCV
```
**Por qué es crítico:** El admin gestiona todo el negocio desde aquí. Sin esto, no hay operación.

---

## 🔄 CI/CD Integración

### GitHub Actions (`.github/workflows/tests.yml`)

El workflow ejecuta:
1. Tests unitarios (sin BD, rápidos)
2. Tests de integración (requiere MySQL service)
3. Tests E2E (requiere servidor PHP + Playwright)

```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: mana_fast_food_test
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - run: composer install
      - run: vendor/bin/phpunit --testsuite Unit
      - run: mysql -h127.0.0.1 -uroot -proot mana_fast_food_test < sql/schema.sql
      - run: vendor/bin/phpunit --testsuite Integration
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
      - run: npm install
      - run: npx playwright install chromium
      - run: php -S localhost:8000 & npx playwright test --project=Chromium
```

---

## 📝 Notas para Desarrolladores

### Escribir nuevos tests

**Unitarios:**
- Cada test prueba UNA sola función/comportamiento
- Usar mocks de PDO, nunca BD real
- Nombrar en español: `test_que_hace_esto`

**Integración:**
- Usar BD `mana_fast_food_test` separada
- Limpiar datos en `tearDown`
- No asumir datos existentes

**E2E:**
- Usar `localStorage` para setup de carrito
- Selectores por `data-*` attributes cuando sea posible
- Usar `test.describe` para agrupar por flujo

### Debugging

```bash
# PHPUnit: filtrar por nombre de test
vendor/bin/phpunit --filter test_login

# Playwright: modo debug con pausas
npx playwright test --debug

# Playwright: test específico con trace
npx playwright test tests/e2e/test_carrito_playwright.spec.js --trace on
```

---

*Generado por TESTRON · QA Engineer · Maná Fast Food © 2026*
