<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$sql = "CREATE TABLE `payments_temp` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `provider` varchar(50) NOT NULL DEFAULT 'mercado_pago',
  `provider_payment_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'BRL',
  `payment_method` varchar(50) NOT NULL,
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `raw_response` json DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
try{
    DB::statement($sql);
    echo "created payments_temp ok\n";
    DB::statement('DROP TABLE IF EXISTS payments_temp');
}catch(Exception $e){
    echo "failed: " . $e->getMessage() . "\n";
}
