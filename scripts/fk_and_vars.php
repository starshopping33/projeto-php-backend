<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

echo "Foreign keys referencing subscriptions (KEY_COLUMN_USAGE):\n";
$res = DB::select("SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME='subscriptions'");
print_r($res);

echo "\nReferential constraints (TABLE_CONSTRAINTS):\n";
$res2 = DB::select("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND CONSTRAINT_TYPE='FOREIGN KEY' AND CONSTRAINT_NAME LIKE '%subscription%'");
print_r($res2);

echo "\nTABLES LIKE 'payments':\n";
$tbl = DB::select("SELECT TABLE_NAME, ENGINE, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='payments'");
print_r($tbl);

echo "\nSHOW VARIABLES LIKE 'sql_mode':\n";
$mode = DB::select("SHOW VARIABLES LIKE 'sql_mode'");
print_r($mode);

echo "\nSHOW VARIABLES LIKE 'innodb_%':\n";
$inn = DB::select("SHOW VARIABLES WHERE VARIABLE_NAME LIKE 'innodb%'");
print_r($inn);

echo "\nInnoDB status (brief):\n";
$stat = DB::select("SHOW ENGINE INNODB STATUS");
print_r(array_slice($stat,0,1));
