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
        'plan_price_id' => 'required|exists:plan_prices,id',
    ]);

    $user = $request->user();

   
    $existingSubscription = Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->first();

    if ($existingSubscription) {
        return response()->json([
            'message' => 'Você já possui uma assinatura ativa.'
        ], 400);
    }

   
    $subscription = Subscription::create([
        'user_id'       => $user->id,
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

public function check()
{
    $user = auth()->user();

    $subscription = Subscription::where('user_id', $user->id)
        ->where('status', 'active')
        ->first();

    if ($subscription) {
        return response()->json([
            'subscribed' => true,
            'subscription' => $subscription
        ]);
    }

    return response()->json([
        'subscribed' => false
    ]);
}

}