<?php
$files = ['productos.php', 'pagos.php', 'tasa.php', 'ofertas.php'];
$base = 'C:\xampp\htdocs\mana-fast-food\frontend\admin\\';

foreach ($files as $f) {
    $path = $base . $f;
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    $clean = [];
    $skipping = false;

    foreach ($lines as $i => $line) {
        // Line 0: keep <?php
        if ($i === 0) { $clean[] = $line; continue; }

        // Lines 1-3: comments, keep once
        if ($i <= 3 && preg_match('/^\/\//', trim($line))) { 
            if (!in_array($line, $clean)) $clean[] = $line;
            continue; 
        }
        if ($i <= 3 && trim($line) === '') { 
            if (!in_array($line, $clean)) $clean[] = $line;
            continue; 
        }

        // Skip duplicate <?php and its following comments/header/exit
        if (preg_match('/^<\?php/', trim($line))) {
            $skipping = true;
            continue;
        }
        if ($skipping && (preg_match('/^\/\//', trim($line)) || preg_match('/header\(/', $line) || preg_match('/exit/', $line) || trim($line) === '' || preg_match('/^\}$/', trim($line)) || preg_match('/if\s*\(/', $line))) {
            continue;
        }
        $skipping = false;
        $clean[] = $line;
    }

    $newContent = implode("\n", $clean);
    file_put_contents($path, $newContent);
    echo "$f: fixed (" . count($lines) . " -> " . count($clean) . " lines)\n";
}
