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
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $startedAt = Carbon::now();
        $endsAt    = Carbon::now()->addMonths(1); 

        $subscription = Subscription::create([
            'user_id'    => $request->user_id,
            'plan_id'    => $plan->id,
            'status'     => 'active',
            'started_at' => $startedAt,
            'ends_at'    => $endsAt,
        ]);

        return response()->json($subscription, 201);
    }
}
