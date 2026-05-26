<?php
/**
 * MANÁ FAST FOOD - Servicio de scraping de tasa BCV
 * 
 * Utiliza la API pública de rafnixg/bcv-api (open source)
 * como fuente confiable de la tasa oficial del BCV.
 * Fallback: intenta scraping directo del sitio BCV.
 * Fallback final: permite valor por defecto configurable.
 */

class BcvScraper
{
    private const BCV_API_URL = 'https://bcv-api.rafnixg.dev/rates/';
    private const BCV_WEB_URL = 'https://www.bcv.org.ve/';
    private const FINANZAS_URL = 'https://finanzasdigital.com/tasa-de-cambio-bcv-diaria/';
    private const TIMEOUT = 8;
    private const MAX_RETRIES = 1;

    /**
     * Obtener la tasa BCV actual desde la fuente primaria (API pública)
     * @return array ['success' => bool, 'rate' => float|null, 'source' => string, 'message' => string]
     */
    public static function obtenerTasa(): array
    {
        $errores = [];

        // 1º intento: API pública rafnixg/bcv-api (rápido)
        $resultado = self::intentarApiPublica();
        if ($resultado['success']) {
            return $resultado;
        }
        $errores[] = $resultado['message'];

        // 2º intento: Scraping directo del sitio BCV
        $resultado = self::intentarScrapingWeb();
        if ($resultado['success']) {
            return $resultado;
        }
        $errores[] = $resultado['message'];

        // 3º intento: Finanzas Digital (fuente alternativa confiable)
        $resultado = self::intentarFinanzasDigital();
        if ($resultado['success']) {
            return $resultado;
        }
        $errores[] = $resultado['message'];

        // Fallback final: no se pudo obtener
        return [
            'success' => false,
            'rate' => null,
            'source' => 'none',
            'message' => 'No se pudo obtener la tasa del BCV. Errores: ' . implode(' | ', $errores)
        ];
    }

    /**
     * Intentar obtener tasa desde la API pública
     */
    private static function intentarApiPublica(): array
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::BCV_API_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'ManaFastFood/1.0',
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if ($data && isset($data['rate'])) {
                    $tasa = floatval($data['rate']);
                    if ($tasa > 0) {
                        return [
                            'success' => true,
                            'rate' => $tasa,
                            'source' => 'bcv-api.rafnixg.dev',
                            'message' => "Tasa obtenida desde API pública: Bs. {$tasa} por USD",
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            error_log("BcvScraper: Error en API pública: " . $e->getMessage());
        }

        return ['success' => false, 'rate' => null, 'source' => 'api-publica', 'message' => 'API pública no disponible.'];
    }

    /**
     * Intentar scraping directo del sitio web del BCV
     */
    private static function intentarScrapingWeb(): array
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::BCV_WEB_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 8,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);

            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $html) {
                $tasa = self::extraerTasaDelHtml($html);
                if ($tasa !== null && $tasa > 0) {
                    return [
                        'success' => true,
                        'rate' => $tasa,
                        'source' => 'bcv.org.ve (scraping)',
                        'message' => "Tasa obtenida desde BCV web: Bs. {$tasa} por USD",
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("BcvScraper: Error en scraping web: " . $e->getMessage());
        }

        return ['success' => false, 'rate' => null, 'source' => 'scraping-web', 'message' => 'Sitio BCV no disponible.'];
    }

    /**
     * Extraer la tasa USD del HTML del BCV
     * Busca patrones comunes en la estructura del sitio
     */
    private static function extraerTasaDelHtml(string $html): ?float
    {
        // Patrón 1: <div id="dolar"> ... <strong class="strong-tb">530,50470000</strong>
        if (preg_match('/<div[^>]*id\s*=\s*["\']dolar["\'][^>]*>.*?<strong[^>]*class\s*=\s*["\']strong-tb["\'][^>]*>\s*([\d.,]+)\s*<\/strong>/is', $html, $matches)) {
            $tasa = self::limpiarNumero($matches[1]);
            if ($tasa > 0 && $tasa < 10000) return $tasa;
        }

        // Patrón 2: <strong class="strong-tb">530,50470000</strong> (genérico, cerca de USD)
        if (preg_match('/USD.*?<strong[^>]*class\s*=\s*["\']strong-tb["\'][^>]*>\s*([\d.,]+)\s*<\/strong>/is', $html, $matches)) {
            $tasa = self::limpiarNumero($matches[1]);
            if ($tasa > 0 && $tasa < 10000) return $tasa;
        }

        // Patrón 3: <div id="dolar"> ... valor entre tags
        if (preg_match('/<div[^>]*id\s*=\s*["\']dolar["\'][^>]*>.*?centrado.*?>\s*<strong[^>]*>([\d.,]+)\s*<\//is', $html, $matches)) {
            $tasa = self::limpiarNumero($matches[1]);
            if ($tasa > 0 && $tasa < 10000) return $tasa;
        }

        // Patrón 4: número grande cerca de "USD" (formato venezolano: 530,50470000)
        if (preg_match('/(?:USD|dolar|dollar)\s*[^0-9]*([\d]{2,3}(?:[.,]\d{3})*[.,]\d{2,})/i', $html, $matches)) {
            $tasa = self::limpiarNumero($matches[1]);
            if ($tasa > 0 && $tasa < 10000) return $tasa;
        }

        return null;
    }

    /**
     * Limpiar número con formato venezolano (ej. "526,8694" o "1.234,56")
     */
    private static function limpiarNumero(string $valor): float
    {
        // Quitar espacios
        $valor = trim($valor);

        // Detectar formato: si hay coma como decimal (ej. 526,8694)
        if (preg_match('/^\d{1,3}(?:\.\d{3})*,\d+$/', $valor)) {
            // Formato 1.234.567,89 → quitar puntos, cambiar coma por punto
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (preg_match('/^\d+,\d{2}$/', $valor)) {
            // Formato 1234,56 → cambiar coma por punto
            $valor = str_replace(',', '.', $valor);
        } elseif (preg_match('/^\d+\.\d+$/', $valor)) {
            // Formato 1234.56 → ya está en formato inglés
            // No hacer nada
        } else {
            // Intentar limpieza genérica: eliminar puntos (separadores de miles)
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        return floatval($valor);
    }

    /**
     * Intentar obtener tasa desde Finanzas Digital (fuente alternativa)
     * Publican la tasa BCV diaria con formato consistente
     */
    private static function intentarFinanzasDigital(): array
    {
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::FINANZAS_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => self::TIMEOUT,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);

            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $html) {
                // Buscar patrón: "Tasa de Cambio BCV" + fecha + número
                // Ej: "Tasa de Cambio BCV 22 de mayo de 2026: 526,8694 Bs/USD"
                if (preg_match('/Tasa\s+de\s+Cambio\s+BCV\s+\d+[^:]+:\s*([\d.,]+)\s*Bs/i', $html, $matches)) {
                    $tasa = self::limpiarNumero($matches[1]);
                    if ($tasa > 0 && $tasa < 10000) {
                        return [
                            'success' => true,
                            'rate' => $tasa,
                            'source' => 'finanzasdigital.com',
                            'message' => "Tasa obtenida desde Finanzas Digital: Bs. {$tasa} por USD",
                        ];
                    }
                }
                // Buscar en título de página o meta description
                if (preg_match('/:?\s*([\d.,]+)\s*Bs/i', $html, $matches)) {
                    $tasa = self::limpiarNumero($matches[1]);
                    if ($tasa > 0 && $tasa < 10000) {
                        return [
                            'success' => true,
                            'rate' => $tasa,
                            'source' => 'finanzasdigital.com (alt)',
                            'message' => "Tasa obtenida desde Finanzas Digital: Bs. {$tasa} por USD",
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            error_log("BcvScraper: Error en Finanzas Digital: " . $e->getMessage());
        }

        return [
            'success' => false,
            'rate' => null,
            'source' => 'finanzas-digital',
            'message' => 'No se pudo obtener tasa desde Finanzas Digital.'
        ];
    }

    /**
     * Probar conectividad con las fuentes
     */
    public static function diagnosticar(): array
    {
        $resultado = [
            'api_publica' => false,
            'scraping_web' => false,
            'finanzas_digital' => false,
            'curl_disponible' => function_exists('curl_version'),
            'curl_version' => function_exists('curl_version') ? curl_version()['version'] : 'N/A',
            'fecha_hora' => date('Y-m-d H:i:s'),
        ];

        if ($resultado['curl_disponible']) {
            // Probar API pública
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::BCV_API_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_exec($ch);
            $resultado['api_publica'] = curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);

            // Probar scraping web BCV
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::BCV_WEB_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            curl_exec($ch);
            $resultado['scraping_web'] = curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);

            // Probar Finanzas Digital
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => self::FINANZAS_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            curl_exec($ch);
            $resultado['finanzas_digital'] = curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);
        }

        return $resultado;
    }
}
