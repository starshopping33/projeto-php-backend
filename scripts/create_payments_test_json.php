<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
try {
    DB::statement("CREATE TABLE payments_test_json (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        subscription_id BIGINT UNSIGNED NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        raw_response JSON NULL,
        CONSTRAINT payments_test_json_subscription_id_foreign FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "created json fk\n";
    DB::statement("DROP TABLE payments_test_json");
    echo "dropped json fk\n";
} catch (Exception $e) {
    echo get_class($e)."\n". $e->getMessage();
}
