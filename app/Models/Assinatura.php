<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Plano;
use App\Models\User;

class Assinatura extends Model
{
    use HasFactory;

    protected $table = 'assinaturas';

    protected $fillable = [
        'user_id',
        'plano_id',
        'status',
        'data_inicio',
        'data_fim',
        'gateway_subscription_id'
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim'    => 'datetime',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
