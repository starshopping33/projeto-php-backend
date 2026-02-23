<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$tables = [
    'users', 'plans', 'features', 'plan_features', 'plan_prices', 'subscriptions', 'payments', 'playlists', 'favorites', 'folders'
];

echo "DB Validation Report\n";
echo "====================\n\n";

foreach ($tables as $t) {
    $exists = DB::select("SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?", [$t]);
    $has = ($exists[0]->c ?? 0) > 0;
    echo "Table: $t -> " . ($has ? "EXISTS" : "MISSING") . "\n";
    if ($has) {
        $r = DB::select("SHOW CREATE TABLE `$t`");
        if (isset($r[0])) {
            echo "SHOW CREATE TABLE $t:\n";
            echo $r[0]->{"Create Table"} . "\n";
        }
        echo "\n";
    }
}

// Check FK constraints of interest
$fk_checks = [
    ['from' => 'subscriptions', 'col' => 'user_id', 'to' => 'users', 'to_col' => 'id'],
    ['from' => 'subscriptions', 'col' => 'plan_id', 'to' => 'plans', 'to_col' => 'id'],
    ['from' => 'subscriptions', 'col' => 'plan_price_id', 'to' => 'plan_prices', 'to_col' => 'id'],
    ['from' => 'payments', 'col' => 'subscription_id', 'to' => 'subscriptions', 'to_col' => 'id'],
    ['from' => 'playlists', 'col' => 'user_id', 'to' => 'users', 'to_col' => 'id'],
    ['from' => 'favorites', 'col' => 'user_id', 'to' => 'users', 'to_col' => 'id'],
];

echo "Foreign Key Checks:\n";
foreach ($fk_checks as $fk) {
    $from = $fk['from']; $col = $fk['col']; $to = $fk['to']; $to_col = $fk['to_col'];
    $res = DB::select("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? AND REFERENCED_TABLE_NAME=? AND REFERENCED_COLUMN_NAME=?", [$from, $col, $to, $to_col]);
    $ok = count($res) > 0;
    echo "$from.$col -> $to.$to_col : " . ($ok ? "OK" : "MISSING") . "\n";
    if ($ok) {
        // Show constraint detail
        foreach ($res as $r) {
            echo "  Constraint: " . ($r->CONSTRAINT_NAME ?? $r->constraint_name ?? 'n/a') . "\n";
        }
    }
    // Check column types
    $c1 = DB::select("SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?", [$from, $col]);
    $c2 = DB::select("SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?", [$to, $to_col]);
    if (isset($c1[0]) && isset($c2[0])) {
        echo "  $from.$col type: " . $c1[0]->COLUMN_TYPE . " nullable:" . $c1[0]->IS_NULLABLE . "\n";
        echo "  $to.$to_col type: " . $c2[0]->COLUMN_TYPE . " nullable:" . $c2[0]->IS_NULLABLE . "\n";
    }
}

echo "\nIndex checks for subscriptions.id and payments.subscription_id:\n";
$idx1 = DB::select("SELECT INDEX_NAME,SEQ_IN_INDEX,COLUMN_NAME,NON_UNIQUE FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='subscriptions' AND COLUMN_NAME='id'");
$idx2 = DB::select("SELECT INDEX_NAME,SEQ_IN_INDEX,COLUMN_NAME,NON_UNIQUE FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments' AND COLUMN_NAME='subscription_id'");

print_r($idx1);
print_r($idx2);

echo "\nEnd of report\n";
