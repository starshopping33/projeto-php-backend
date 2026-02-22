<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_hashed_on_update()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Updated Name',
            'email' => 'updated'.uniqid().'@iana.org',
            'password' => 'NewPass1!',
            'password_confirmation' => 'NewPass1!',
        ];

        $response = $this->putJson('/api/user/atualizar/'.$user->id, $payload);

        $response->assertStatus(200);

        $user->refresh();

        $this->assertTrue(Hash::check('NewPass1!', $user->password));
    }
}
