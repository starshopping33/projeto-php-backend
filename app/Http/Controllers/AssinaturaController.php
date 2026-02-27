<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssinaturaController extends Controller
{
    // ✅ Criar assinatura
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
            ->where('ends_at', '>', now())
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

   
    public function cancel()
    {
        $user = auth()->user();

        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'Nenhuma assinatura ativa encontrada.'
            ], 404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now()
        ]);

        return response()->json([
            'message' => 'Assinatura cancelada com sucesso.'
        ]);
    }

   
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'plan_price_id' => 'required|exists:plan_prices,id',
        ]);

        $user = auth()->user();

        $current = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$current) {
            return response()->json([
                'message' => 'Nenhuma assinatura ativa encontrada.'
            ], 404);
        }

       
        $current->update([
            'status' => 'upgraded',
            'ends_at' => now()
        ]);

        
        $newSubscription = Subscription::create([
            'user_id'       => $user->id,
            'plan_id'       => $request->plan_id,
            'plan_price_id' => $request->plan_price_id,
            'status'        => 'active',
            'started_at'    => now(),
            'ends_at'       => now()->addMonth(),
        ]);

        return response()->json([
            'message' => 'Upgrade realizado com sucesso.',
            'subscription' => $newSubscription
        ]);
    }
}