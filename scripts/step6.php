<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use App\Models\Planos;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Folder;
use App\Models\Playlist;

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

echo "STEP 6 - Playlist System Tests\n";

// reuse plans
$planRecords = [];
foreach ([['name'=>'Basic','tier'=>1],['name'=>'Premium','tier'=>2],['name'=>'Pro','tier'=>3]] as $p) {
    $plan = Planos::firstOrCreate(['name'=>$p['name']], ['tier'=>$p['tier'],'is_active'=>1,'description'=>'']);
    $planRecords[$p['tier']] = $plan;
}
// ensure plan prices
use App\Models\PlanPrice;
foreach ($planRecords as $tier => $plan) {
    $pp = PlanPrice::firstOrCreate(['plan_id' => $plan->id], ['amount' => 0, 'currency' => 'BRL', 'billing_period' => 'monthly', 'interval_count' => 1, 'is_active' => 1]);
    $planRecords[$tier]->default_price_id = $pp->id;
}

$report = [];

foreach ([1,2,3] as $tier) {
    $tierLogs = [];
    echo "\n-- Tier $tier detailed tests --\n";
    $email = 'step6+' . $tier . substr(md5(uniqid('', true)),0,6) . '@gmail.com';
    $pwd = 'Passw0rd!';
    $user = User::criar(['name'=>"TestTier{$tier}","email"=>$email,'password'=>$pwd]);
    $sub = Subscription::create(['user_id'=>$user->id,'plan_id'=>$planRecords[$tier]->id,'plan_price_id'=>$planRecords[$tier]->default_price_id,'status'=>'active','started_at'=>now(),'ends_at'=>null]);
    $token = $user->gerarToken();
    $hdr = ["Authorization: Bearer $token"];
    echo "User {$user->id} / plan {$planRecords[$tier]->id}\n";

    // create 3 playlists
    $plIds = [];
    for ($i=1;$i<=3;$i++) {
        $letter = chr(64 + ($i + ($tier-1)*3));
        $name = "Playlist " . $letter;
        $payload = ['name'=>$name,'description'=>'desc'];
        if ($tier===3 && $i===1) { // pro extra
            $folder = Folder::criar(['user_id'=>$user->id,'name'=>'ProFolder']);
            $payload['folder_id'] = $folder->id;
            $payload['cover'] = 'c.png';
            $payload['is_collaborative'] = true;
        }
        list($c,$r) = http('POST', "$base/playlist/criar", $payload, $hdr);
        echo "create playlist $name -> $c\n";
        $item = array('method' => 'POST', 'url' => '/playlist/criar', 'payload' => $payload, 'code' => $c, 'body' => parseBody($r));
        $tierLogs[] = $item;
        if ($c>=200 && $c<300) {
            $body = json_decode($r,true);
            $plIds[] = $body['data']['id'] ?? null;
        }
    }

    // set order: swap orders via update; Tier1 can't change name/order
    $orderResults = [];
    foreach ($plIds as $idx => $pid) {
        if (!$pid) continue;
        $newOrder = $idx + 10;
        list($c,$r) = http('PUT', "$base/playlist/atualizar/{$pid}", ['name'=>'KeepName','order'=>$newOrder], $hdr);
        $orderResults[$pid] = $c;
        $item = array('method' => 'PUT', 'url' => '/playlist/atualizar/' . $pid, 'payload' => array('name' => 'KeepName', 'order' => $newOrder), 'code' => $c, 'body' => parseBody($r));
        $tierLogs[] = $item;
        echo "set order {$newOrder} for {$pid} -> $c\n";
    }

    // attempt to set is_collaborative (only allowed for Pro)
    $collabResults = [];
    if (!empty($plIds)) {
        $pid = $plIds[0];
        list($c,$r) = http('PUT', "$base/playlist/atualizar/{$pid}", ['name'=>'KeepName','is_collaborative'=>true], $hdr);
        $collabResults[$pid] = $c;
        $item = array('method' => 'PUT', 'url' => '/playlist/atualizar/' . $pid, 'payload' => array('name' => 'KeepName', 'is_collaborative' => true), 'code' => $c, 'body' => parseBody($r));
        $tierLogs[] = $item;
        echo "set collab on {$pid} -> $c\n";
    }

    // delete and restore a playlist
    $delResults = [];
    if (!empty($plIds)) {
        $pid = $plIds[0];
        list($c,$r) = http('DELETE', "$base/playlist/deletar/{$pid}", null, $hdr);
        $delResults['delete'] = $c;
        $item = array('method' => 'DELETE', 'url' => '/playlist/deletar/' . $pid, 'payload' => null, 'code' => $c, 'body' => parseBody($r));
        $tierLogs[] = $item;
        echo "soft delete {$pid} -> $c\n";
        list($c2,$r2) = http('POST', "$base/playlist/restore/{$pid}", null, $hdr);
        $delResults['restore'] = $c2;
        $item = array('method' => 'POST', 'url' => '/playlist/restore/' . $pid, 'payload' => null, 'code' => $c2, 'body' => parseBody($r2));
        $tierLogs[] = $item;
        echo "restore {$pid} -> $c2\n";
    }

    // verify listing and folder relation
    list($cList,$rList) = http('GET', "$base/playlist", null, $hdr);
    echo "list playlists -> $cList\n";
    $item = array('method' => 'GET', 'url' => '/playlist', 'payload' => null, 'code' => $cList, 'body' => parseBody($rList));
    $tierLogs[] = $item;

    $report[$tier] = ['created'=>$plIds,'order'=>$orderResults,'collab'=>$collabResults,'del'=>$delResults,'list_code'=>$cList,'logs'=>$tierLogs];
}

$outPath = __DIR__ . '/step6_output.txt';
$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'step6_' . uniqid() . '.tmp';
$written = false;
try {
    $written = file_put_contents($tmp, print_r($report,true));
    if ($written === false) {
        throw new Exception('Failed to write temp file');
    }
    // attempt atomic replace
    if (!@rename($tmp, $outPath)) {
        // fallback: copy then unlink
        if (!@copy($tmp, $outPath)) {
            throw new Exception('Failed to move temp file to target');
        }
        @unlink($tmp);
    }
    echo "\nSTEP 6 complete. Output saved to scripts/step6_output.txt\n";
} catch (Exception $e) {
    echo "\nSTEP 6 complete — failed to write output file: " . $e->getMessage() . "\n";
    // try to echo report for immediate inspection
    echo print_r($report,true);
}
