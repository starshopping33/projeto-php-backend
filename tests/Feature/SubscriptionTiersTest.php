<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Planos;
use App\Models\Subscription;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionTiersTest extends TestCase
{
    use RefreshDatabase;

    private function makePlan(string $name, int $tier): Planos
    {
        return Planos::create([
            'name' => $name,
            'description' => $name,
            'tier' => $tier,
            'is_active' => 1
        ]);
    }

    private function subscribe(User $user, Planos $plan, string $status = 'active'): Subscription
    {
        $price = \App\Models\PlanPrice::create([
            'plan_id' => $plan->id,
            'amount' => 0,
            'currency' => 'BRL',
            'billing_period' => 'monthly',
            'interval_count' => 1,
            'is_active' => 1,
            'mercado_pago_plan_id' => null,
        ]);

        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'plan_price_id' => $price->id,
            'status' => $status,
            'started_at' => now()
        ]);
    }

    public function test_free_user_limits()
    {
        $plan = $this->makePlan('Free', 1);
        $user = User::criar(['name' => 'FreeUser','email'=>'free@example.com','password'=>'secret']);
        $this->subscribe($user, $plan);

        // create 20 favorites
        for ($i=0;$i<20;$i++) {
            Favorite::criar(['user_id' => $user->id, 'music_id' => $i + 1]);
        }

        $this->assertFalse($user->canLike());
        $this->assertEquals(0, $user->remainingLikes());

        // playlists limit: assume free <5 allowed, so create 5 and expect false
        for ($i=0;$i<5;$i++) {
            $user->playlists()->create(['name' => 'pl'.$i]);
        }

        $this->assertFalse($user->canCreatePlaylist());
        $this->assertFalse($user->canUseTheme());
        $this->assertFalse($user->hasHistoryFeature());
        $this->assertFalse($user->canAccessStats());
    }

    public function test_premium_permissions()
    {
        $plan = $this->makePlan('Premium', 2);
        $user = User::criar(['name' => 'PremiumUser','email'=>'prem@example.com','password'=>'secret']);
        $this->subscribe($user, $plan);

        // favorites unlimited
        for ($i=0;$i<50;$i++) {
            Favorite::criar(['user_id' => $user->id, 'music_id' => $i + 1]);
        }

        $this->assertTrue($user->canLike());
        $this->assertNull($user->remainingLikes());
        $this->assertTrue($user->canCreatePlaylist());
        $this->assertTrue($user->canUseTheme());
        $this->assertTrue($user->hasHistoryFeature());
        $this->assertFalse($user->canAccessStats());
    }

    public function test_pro_permissions()
    {
        $plan = $this->makePlan('Pro', 3);
        $user = User::criar(['name' => 'ProUser','email'=>'pro@example.com','password'=>'secret']);
        $this->subscribe($user, $plan);

        $this->assertTrue($user->canLike());
        $this->assertNull($user->remainingLikes());
        $this->assertTrue($user->canCreatePlaylist());
        $this->assertTrue($user->canUseTheme());
        $this->assertTrue($user->hasHistoryFeature());
        $this->assertTrue($user->canAccessStats());
        $this->assertTrue($user->canAddFavoriteArtist());
        $this->assertTrue($user->canEditProfile());
    }
}
