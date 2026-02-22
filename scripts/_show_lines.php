<?php
$path = __DIR__ . '/step6.php';
$lines = file($path);
foreach ($lines as $i => $line) {
    echo ($i+1) . ': ' . rtrim($line, "\n") . PHP_EOL;
}
