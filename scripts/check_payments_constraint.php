<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$tables = DB::select("SHOW TABLES LIKE 'payments'");
$constraints = DB::select("SELECT CONSTRAINT_NAME,TABLE_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND CONSTRAINT_NAME='payments_subscription_id_foreign'");
print_r($tables);
print_r($constraints);
