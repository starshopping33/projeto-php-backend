<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssinaturaController extends Controller
{
    public function store(Request $request)
    {
      
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'user_id' => 'required|exists:users,id',
            'plan_price_id' => 'required|exists:plan_prices,id', 
        ]);

      
        $subscription = Subscription::create([
            'user_id'       => $request->user_id,
            'plan_id'       => $request->plan_id,
            'plan_price_id' => $request->plan_price_id, 
            'status'        => 'active',
            'started_at'    => Carbon::now(),
            'ends_at'       => Carbon::now()->addMonth(),
        ]);

     
        return response()->json([
            'message' => 'Assinatura criada com sucesso!',
            'data'    => $subscription
        ], 201);
    }
}