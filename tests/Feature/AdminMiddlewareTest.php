<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_allowed()
    {
        $admin = User::criarAdmin([
            'name' => 'TAdmin',
            'email' => 'admintest@example.com',
            'password' => 'secret'
        ]);
        // login the admin to the auth system
        auth()->login($admin);

        $middleware = new \App\Http\Middleware\Admin();

        $request = \Illuminate\Http\Request::create('/','GET');

        $nextCalled = false;

        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['ok' => true]);
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled, 'Middleware should allow admin and call next');
    }

    public function test_non_admin_access_denied()
    {
        $user = User::criar([
            'name' => 'TUser',
            'email' => 'usertest@example.com',
            'password' => 'secret'
        ]);
        auth()->login($user);

        $middleware = new \App\Http\Middleware\Admin();

        $request = \Illuminate\Http\Request::create('/','GET');

        $next = function ($req) {
            return response()->json(['ok' => true]);
        };

        $response = $middleware->handle($request, $next);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_new_user_gets_default_profile_photo()
    {
        $user = User::criar([
            'name' => 'PhotoUser',
            'email' => 'photouser@example.com',
            'password' => 'secret'
        ]);

        $this->assertNotEmpty($user->profile_photo_base64);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $user->profile_photo_base64);
    }
}
