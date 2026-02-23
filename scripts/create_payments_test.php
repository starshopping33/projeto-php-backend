<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
try {
    DB::statement("CREATE TABLE payments_test (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        subscription_id BIGINT UNSIGNED NOT NULL,
        provider VARCHAR(50) NOT NULL DEFAULT 'mercado_pago',
        amount DECIMAL(10,2) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "created\n";
    DB::statement("DROP TABLE payments_test");
    echo "dropped\n";
} catch (Exception $e) {
    echo get_class($e)."\n". $e->getMessage();
}
