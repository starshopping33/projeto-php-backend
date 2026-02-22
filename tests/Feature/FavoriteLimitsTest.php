<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class FavoriteLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tier1_cannot_create_more_than_20_favorites(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 20; $i++) {
            $response = $this->postJson('/api/favorite/criar', [
                'music_id' => 'track-' . $i,
            ]);

            $response->assertStatus(200)->assertJson(['success' => true]);
        }

        $response = $this->postJson('/api/favorite/criar', [
            'music_id' => 'track-extra',
        ]);

        $response->assertStatus(403)->assertJson(['success' => false]);
    }

    public function test_ownership_required_for_favorite_update(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user, 'sanctum');
        $create = $this->postJson('/api/favorite/criar', ['music_id' => 'track-1']);
        $create->assertStatus(200)->assertJson(['success' => true]);

        $favorite = $create->json('data');

        $this->actingAs($other, 'sanctum');
        $update = $this->putJson('/api/favorite/atualizar/' . ($favorite['id'] ?? 1), ['music_id' => 'track-1']);

        $update->assertStatus(403)->assertJson(['success' => false]);
    }
}
