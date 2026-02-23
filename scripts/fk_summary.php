<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$count = DB::select("SELECT COUNT(*) as c FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME='subscriptions'");
echo "FKs referencing subscriptions: " . ($count[0]->c ?? 0) . "\n";
$payments = DB::select("SELECT COUNT(*) as c FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments'");
echo "Payments table exists: " . ($payments[0]->c ? 'yes' : 'no') . "\n";
$mode = DB::select("SHOW VARIABLES LIKE 'sql_mode'");
echo "sql_mode: " . ($mode[0]->Value ?? '') . "\n";
$type = DB::select("SELECT @@version_comment as v, @@version as ver");
echo "version: " . ($type[0]->ver ?? '') . " " . ($type[0]->v ?? '') . "\n";
