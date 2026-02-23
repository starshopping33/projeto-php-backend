<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use App\Models\Planos;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Folder;

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

$base = 'http://127.0.0.1:8000/api';

echo "STEP 5 - Plan & Feature System Tests\n";

// Ensure plans exist
$plans = [
    ['name' => 'Basic', 'tier' => 1, 'description' => 'Basic plan'],
    ['name' => 'Premium', 'tier' => 2, 'description' => 'Premium plan'],
    ['name' => 'Pro', 'tier' => 3, 'description' => 'Pro plan'],
];
$planRecords = [];
foreach ($plans as $p) {
    $plan = Planos::firstOrCreate(['name' => $p['name']], ['tier' => $p['tier'], 'description' => $p['description'], 'is_active' => 1]);
    $planRecords[$p['tier']] = $plan;
    echo "Ensured plan: {$p['name']} (tier {$p['tier']}) id={$plan->id}\n";
}

// Ensure at least one PlanPrice per plan (required by subscriptions)
use App\Models\PlanPrice;
foreach ($planRecords as $tier => $plan) {
    $pp = PlanPrice::firstOrCreate(['plan_id' => $plan->id], ['amount' => 0, 'currency' => 'BRL', 'billing_period' => 'monthly', 'interval_count' => 1, 'is_active' => 1]);
    $planRecords[$tier]->default_price_id = $pp->id;
    echo "Ensured plan_price for plan_id={$plan->id} => plan_price_id={$pp->id}\n";
}

$results = [];

foreach ([1,2,3] as $tier) {
    echo "\n-- Testing Tier $tier user --\n";
    $email = 'step5+' . $tier . substr(md5(uniqid('', true)),0,6) . '@gmail.com';
    $pwd = 'Passw0rd!';
    $user = User::criar(['name' => "TestTier$tier", 'email' => $email, 'password' => $pwd]);
    echo "Created user id={$user->id} email={$email}\n";

    // create active subscription
    $sub = Subscription::create([
        'user_id' => $user->id,
        'plan_id' => $planRecords[$tier]->id,
        'plan_price_id' => $planRecords[$tier]->default_price_id,
        'status' => 'active',
        'started_at' => now(),
        'ends_at' => null,
    ]);
    echo "Created subscription id={$sub->id} plan_id={$sub->plan_id}\n";

    // token
    $token = $user->gerarToken();
    $hdr = ["Authorization: Bearer $token"];

    // Test playlists creation
    $created = 0; $failed = 0; $lastCode = null;
    for ($i=1;$i<=22;$i++) {
        // name must match regex (letters, spaces, hyphens, apostrophes)
        $letter = chr(65 + ($i % 26));
        $payload = ['name' => "Playlist $letter", 'description' => 'testing'];
        // include advanced fields for tier 3
        if ($tier === 3 && $i === 1) {
            // create a folder for assignment
            $folder = Folder::criar(['user_id' => $user->id, 'name' => 'ProFolder']);
            $payload['cover'] = 'cover.png';
            $payload['is_collaborative'] = true;
            $payload['folder_id'] = $folder->id;
        }
        list($c,$r) = http('POST', "$base/playlist/criar", $payload, $hdr);
        $lastCode = $c;
        if ($c >=200 && $c < 300) { $created++; }
        else { $failed++; }
        echo "Playlist create #$i -> $c\n";
    }
    echo "Playlists created: $created, failed: $failed (lastCode=$lastCode)\n";

    // Test favorites creation (25 attempts)
    $fav_created = 0; $fav_failed = 0;
    for ($i=1;$i<=25;$i++) {
        $payload = ['music_id' => "track-$i"]; // FavoriteRequest expects music_id (string)
        list($c,$r) = http('POST', "$base/favorite/criar", $payload, $hdr);
        if ($c >=200 && $c<300) $fav_created++; else $fav_failed++;
    }
    echo "Favorites created: $fav_created, failed: $fav_failed\n";

    // Test folder creation
    list($c_folder,$r_folder) = http('POST', "$base/folder/criar", ['name'=>'MyFolder'], $hdr);
    echo "Folder create attempt -> $c_folder\n";

    // Test update playlist advanced field enforcement (try to set is_collaborative on first playlist)
    $pl = DB::table('playlists')->where('user_id', $user->id)->first();
    if ($pl) {
        // include required 'name' to satisfy validation while testing permission enforcement
        list($c_up,$r_up) = http('PUT', "$base/playlist/atualizar/{$pl->id}", ['name' => 'Updated Name', 'is_collaborative' => true], $hdr);
        echo "Update playlist is_collaborative -> $c_up\n";
    } else {
        echo "No playlist to update for user\n";
    }

    $results[$tier] = [
        'playlists_created' => $created,
        'playlists_failed' => $failed,
        'favorites_created' => $fav_created,
        'favorites_failed' => $fav_failed,
        'folder_create_code' => $c_folder ?? null,
        'playlist_update_code' => $c_up ?? null,
    ];
}

echo "\nSTEP 5 Results Summary:\n";
print_r($results);

echo "\nEnd of STEP 5\n";
