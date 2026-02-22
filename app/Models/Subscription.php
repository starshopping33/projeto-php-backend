<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_price_id',
        'status',
        'started_at',
        'ends_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
