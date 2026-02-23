<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

function createNormalUser()
{
    try {
        $email = 'testuser_' . time() . '@example.com';
        $user = User::criar([
            'name' => 'Test User',
            'email' => $email,
            'password' => 'secret',
        ]);

        echo "NORMAL CREATED: id={$user->id} email={$user->email}\n";
        echo "profile_photo_base64 starts: " . substr($user->profile_photo_base64, 0, 80) . "\n";
    } catch (Throwable $e) {
        echo "NORMAL ERROR: " . $e->getMessage() . "\n";
    }
}

function createAdminUser()
{
    try {
        $email = 'adminuser_' . time() . '@example.com';
        $user = User::criarAdmin([
            'name' => 'Admin User',
            'email' => $email,
            'password' => 'secret',
        ]);

        echo "ADMIN CREATED: id={$user->id} email={$user->email}\n";
        echo "profile_photo_base64 starts: " . substr($user->profile_photo_base64, 0, 80) . "\n";
    } catch (Throwable $e) {
        echo "ADMIN ERROR: " . $e->getMessage() . "\n";
    }
}

createNormalUser();
createAdminUser();

echo "Done.\n";
