<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class PlaylistLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tier1_cannot_create_more_than_20_playlists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 20; $i++) {
            $letter = chr(65 + ($i % 26));
            $response = $this->postJson('/api/playlist/criar', [
                'name' => 'Playlist ' . $letter,
            ]);

            $response->assertStatus(200)->assertJson(['success' => true]);
        }

        $response = $this->postJson('/api/playlist/criar', [
            'name' => 'Playlist extra',
        ]);

        $response->assertStatus(403)->assertJson(['success' => false]);
    }

    public function test_tier1_cannot_edit_name_or_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $create = $this->postJson('/api/playlist/criar', ['name' => 'Minha']);
        $create->assertStatus(200)->assertJson(['success' => true]);

        $playlist = $create->json('data');

        $update = $this->putJson('/api/playlist/atualizar/' . ($playlist['id'] ?? 1), [
            'name' => 'Novo nome',
            'order' => 1,
        ]);

        $update->assertStatus(403)->assertJson(['success' => false]);
    }
}
