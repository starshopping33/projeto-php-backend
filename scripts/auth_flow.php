<?php
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
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $resp, $err];
}

$base = 'http://127.0.0.1:8000/api';
$email = 'audit+' . substr(md5(uniqid('', true)),0,8) . '@gmail.com';
$pwd = 'Passw0rd!';

// Register
list($c,$r,$e) = http('POST', "$base/user/criar", ['name'=>'Audit User','email'=>$email,'password'=>$pwd,'password_confirmation'=>$pwd]);
echo "REGISTER status=$c\n";
echo "$r\n";
$reg = json_decode($r, true);
$userId = $reg['data']['id'] ?? null;

// Login
list($c,$r,$e) = http('POST', "$base/login", ['email'=>$email,'password'=>$pwd]);
echo "LOGIN status=$c\n";
echo "$r\n";
$login = json_decode($r, true);
$token = $login['data']['token'] ?? null;

// Verify token
$hdr = $token ? ["Authorization: Bearer $token"] : [];
list($c,$r,$e) = http('GET', "$base/login/verificarToken", null, $hdr);
echo "VERIFY status=$c\n";
echo "$r\n";

// Access protected route without token
list($c,$r,$e) = http('PUT', "$base/user/atualizar/".($userId?:1), ['name'=>'Changed'], []);
echo "PUT without token status=$c\n";
echo "$r\n";

// Access protected route with token (include required fields)
$updateEmail = 'audit+' . substr(md5(uniqid('', true)),0,8) . '@gmail.com';
$updateData = ['name'=>'Changed With Token','email'=>$updateEmail,'password'=>$pwd,'password_confirmation'=>$pwd];
list($c,$r,$e) = http('PUT', "$base/user/atualizar/".($userId?:1), $updateData, $hdr);
echo "PUT with token status=$c\n";
echo "$r\n";

// Logout
list($c,$r,$e) = http('POST', "$base/login/logout", null, $hdr);
echo "LOGOUT status=$c\n";
echo "$r\n";
