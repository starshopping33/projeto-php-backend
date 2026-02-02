<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\planos;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssinaturaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'user_id'  => 'required|exists:users,id'
        ]);

        $plano = Planos::findOrFail($request->plano_id);

        $dataInicio = Carbon::now();
        $dataFim = Carbon::now()->addDays($plano->duracao_em_dias);

        $assinatura = Assinatura::create([
            'user_id'     => $request->user_id,
            'plano_id'    => $plano->id,
            'status'      => 'ativa',
            'data_inicio' => $dataInicio,
            'data_fim'    => $dataFim,
        ]);

        return response()->json($assinatura, 201);
    }
}
