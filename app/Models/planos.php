<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planos extends Model
{
    use SoftDeletes;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'description',
        'tier',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

   

    public static function tierJaExiste($tier)
    {
        return self::where('tier', $tier)->exists();
    }

    public static function planoAtivoNoTier($tier)
    {
        return self::where('tier', $tier)
            ->where('is_active', true)
            ->exists();
    }

    public function possuiAssinaturasAtivas()
    {
        return $this->subscriptions()
            ->where('status', 'ativa')
            ->exists();
    }
}