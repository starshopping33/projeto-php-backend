<?php

namespace App\Http\Controllers;
use App\Models\Plan; 
use App\Models\PlanPrice;
use Illuminate\Http\Request;

class PlanPriceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plan_id'        => 'required|exists:plans,id',
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'string|max:10',
            'billing_period' => 'required|in:weekly,monthly,quarterly,semiannual,yearly',
            'interval_count' => 'integer|min:1',
            'is_active'      => 'boolean',
        ]);

        $price = PlanPrice::create([
            'plan_id'        => $request->plan_id,
            'amount'         => $request->amount,
            'currency'       => $request->currency ?? 'BRL',
            'billing_period' => $request->billing_period,
            'interval_count' => $request->interval_count ?? 1,
            'is_active'      => $request->is_active ?? true,
        ]);

        return response()->json($price, 201);
    }

    public function index()
{
    $prices = PlanPrice::with('plan')
                ->where('is_active', true)
                ->get();

    return response()->json($prices);
}


}
