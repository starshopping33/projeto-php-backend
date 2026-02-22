<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$res = DB::select("SELECT CONSTRAINT_SCHEMA,CONSTRAINT_NAME,TABLE_NAME,REFERENCED_TABLE_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND CONSTRAINT_NAME LIKE 'payments%'");
print_r($res);
$res2 = DB::select("SELECT CONSTRAINT_SCHEMA,CONSTRAINT_NAME,TABLE_NAME,REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME='payments'");
print_r($res2);
