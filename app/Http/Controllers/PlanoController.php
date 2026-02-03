<?php

namespace App\Http\Controllers;

use App\Models\Planos;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    public function index()
    {
        return response()->json(Planos::all());
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'tier'        => 'required|integer|min:1',
            'is_active'   => 'boolean'
        ]);

        $plano = Planos::create([
            'name'        => $request->name,
            'description' => $request->description,
            'tier'        => $request->tier,
            'is_active'   => $request->is_active ?? true
        ]);

        return response()->json($plano, 201);
    }
}


