<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = file_get_contents(__DIR__ . '/../routes/api.php');
$pattern = "#\[\s*([^:]+)::class\s*,\s*'([^']+)'\s*\]#";
preg_match_all($pattern, $routes, $matches, PREG_SET_ORDER);

echo "API Route Validation Report\n";
echo "========================\n\n";

foreach ($matches as $m) {
    $class = trim($m[1]);
    $method = trim($m[2]);
    $controllerPath = __DIR__ . '/../app/Http/Controllers/' . $class . '.php';
    echo "Controller: $class -> method: $method\n";
    if (! file_exists($controllerPath)) {
        echo "  MISSING: controller file not found at $controllerPath\n\n";
        continue;
    }
    $content = file_get_contents($controllerPath);
    // crude method existence check
    if (preg_match('/function\s+' . preg_quote($method) . '\s*\(/', $content)) {
        echo "  OK: method exists in file\n\n";
    } else {
        echo "  MISSING: method not found in controller file\n\n";
    }
}

// Also list routes with middleware checks
$mwPattern = "#Route::middleware\(([^)]+)\)->(get|post|put|delete)\('([^']+)'\s*,#";
if (preg_match_all($mwPattern, $routes, $mm, PREG_SET_ORDER)) {
    echo "Routes using middleware (sample):\n";
    foreach ($mm as $r) {
        echo "  Middleware: " . trim($r[1]) . " -> method: " . $r[2] . " path: " . $r[3] . "\n";
    }
}
