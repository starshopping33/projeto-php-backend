<?php

namespace App\Http\Controllers;

use App\Models\Planos;
use Illuminate\Http\Request;

class PlanoController extends Controller
{

       public function index()
    {
        $planos = Planos::all();
        return response()->json($planos);
    }


        public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'preco' => 'required|numeric',
            'duracao_em_dias' => 'required|integer',
            'descricao' => 'nullable|string'
        ]);

        $plano = Planos::create($request->only([
            'nome',
            'preco',
            'duracao_em_dias',
            'descricao'
        ]));

        return response()->json($plano, 201);
    }
}
