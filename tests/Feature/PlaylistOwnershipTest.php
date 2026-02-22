<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Playlist;
use Laravel\Sanctum\Sanctum;

class PlaylistOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_delete_others_playlist()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $playlist = Playlist::create(['user_id' => $owner->id, 'name' => 'P1', 'description' => 'd']);

        Sanctum::actingAs($other);

        $response = $this->deleteJson('/api/playlist/deletar/'.$playlist->id);

        $response->assertStatus(403);
    }

    public function test_admin_can_restore_playlist()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $playlist = Playlist::create(['user_id' => $user->id, 'name' => 'P2', 'description' => 'd']);

        Sanctum::actingAs($user);
        $this->deleteJson('/api/playlist/deletar/'.$playlist->id)->assertStatus(200);

        Sanctum::actingAs($admin);
        $res = $this->postJson('/api/playlist/restore/'.$playlist->id);

        $res->assertStatus(200);
    }
}
