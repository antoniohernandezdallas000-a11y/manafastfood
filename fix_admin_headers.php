<?php
$files = [
    'productos.php' => 'Productos - Gesti&oacute;n del men&uacute;',
    'pagos.php'     => 'PagoM&oacute;vil - Cuentas del comercio',
    'tasa.php'      => 'Tasa BCV - Configuraci&oacute;n del tipo de cambio',
    'ofertas.php'   => 'Ofertas - Promociones activas',
];
$base = 'C:\xampp\htdocs\mana-fast-food\frontend\admin\\';

foreach ($files as $fileName => $title) {
    $path = $base . $fileName;
    $content = file_get_contents($path);

    // Find where the real HTML starts (after the last <?php header block)
    // Strategy: find the first occurrence of "<!DOCTYPE" or "<html" or "<head"
    $htmlPos = strpos($content, '<!DOCTYPE');
    if ($htmlPos === false) {
        $htmlPos = strpos($content, '<html');
    }
    if ($htmlPos === false) {
        $htmlPos = strpos($content, '<head');
    }

    if ($htmlPos === false) {
        echo "$fileName: could not find HTML start\n";
        continue;
    }

    // Build clean header with just the data array that was originally there
    $header = "<?php\n";
    $header .= "// Sesion desactivada para desarrollo frontend\n";
    $header .= "// En produccion conectar con backend/api/admin/auth\n\n";

    // Extract the original data arrays and page title from the original content
    // Look for patterns like "$productos = [" or "$cuentas = [" etc.
    preg_match_all('/\$[a-zA-Z_]+\s*=\s*\[/', $content, $matches, PREG_OFFSET_CAPTURE);

    $dataVars = '';
    foreach ($matches[0] as $match) {
        $varStart = $match[1];
        // Find the semicolon that ends this statement
        $semiPos = strpos($content, ';', $varStart);
        if ($semiPos !== false && $semiPos < $htmlPos) {
            $stmt = substr($content, $varStart, $semiPos - $varStart + 1);
            $dataVars .= $stmt . "\n";
        }
    }

    // Also capture the $estados_disponibles if present
    if (preg_match('/\$estados_disponibles\s*=\s*\[.*?\];/s', $content, $m)) {
        $dataVars .= $m[0] . "\n";
    }
    if (preg_match('/\$filtro_actual\s*=.*?;/', $content, $m)) {
        $dataVars .= $m[0] . "\n";
    }
    if (preg_match('/\$base_path\s*=.*?;/', $content, $m)) {
        $dataVars .= $m[0] . "\n";
    }

    $header .= $dataVars;

    $newContent = $header . substr($content, $htmlPos);
    file_put_contents($path, $newContent);
    echo "$fileName: fixed\n";
}
