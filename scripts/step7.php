<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

function http($method, $url, $data = null, $headers = []) {
    $ch = curl_init();
    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json','Accept: application/json'], $headers),
    ];
    if ($data !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($data);
    }
    curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $resp];
}

function parseBody($r) {
    $d = json_decode($r, true);
    return $d === null ? $r : $d;
}

$base = 'http://127.0.0.1:8000/api';

echo "STEP 7 - Music API integration tests\n";

$report = [];

// top tracks
list($c,$r) = http('GET', "$base/musicas/top");
echo "GET /musicas/top -> $c\n";
$report['top'] = ['code'=>$c,'body'=>parseBody($r)];

// by tag
$tags = ['rock','pop','jazz'];
$report['tags'] = [];
foreach ($tags as $tag) {
    list($c,$r) = http('GET', "$base/musicas/tag/" . urlencode($tag));
    echo "GET /musicas/tag/{$tag} -> $c\n";
    $report['tags'][$tag] = ['code'=>$c,'body'=>parseBody($r)];
}

// write output
$outFile = __DIR__ . '/step7_output.txt';
$tmp = __DIR__ . '/step7_output.tmp';
file_put_contents($tmp, "STEP 7 results:\n" . print_r($report, true));
if (!@rename($tmp, $outFile)) {
    echo "failed to write output\n";
}

echo "STEP 7 finished - output at scripts/step7_output.txt\n";
