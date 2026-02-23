<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Planos; 

class PlanPrice extends Model
{
    use HasFactory;

    protected $table = 'plan_prices';

    protected $fillable = [
        'plan_id',
        'amount',
        'currency',
        'billing_period',
        'interval_count',
        'is_active',
        'mercado_pago_plan_id',
    ];

    public function plan()
    {
        return $this->belongsTo(Planos::class);
    }
}