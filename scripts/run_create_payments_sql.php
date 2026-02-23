<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$sql = "CREATE TABLE `payments` (
`id` bigint unsigned not null auto_increment primary key,
`subscription_id` bigint unsigned not null,
`provider` varchar(50) not null default 'mercado_pago',
`provider_payment_id` varchar(255) null,
`amount` decimal(10, 2) not null,
`currency` varchar(10) not null default 'BRL',
`payment_method` varchar(50) not null,
`status` enum('pending', 'paid', 'failed', 'refunded') not null default 'pending',
`paid_at` datetime null,
`raw_response` json null,
`created_at` timestamp null,
`updated_at` timestamp null
) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = InnoDB";
try {
    DB::statement($sql);
    echo "created\n";
    DB::statement('DROP TABLE payments');
    echo "dropped\n";
} catch (Exception $e) {
    echo get_class($e)."\n". $e->getMessage();
}
