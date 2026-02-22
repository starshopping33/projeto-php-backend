<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use App\Models\Favorite;
use App\Services\ResponseService;

class FavoriteController extends Controller
{
    public function listar()
    {
        $favorites = Favorite::all();

        return ResponseService::success('Listando favoritos', $favorites);
    }

    public function buscarId(int $id)
    {
        $favorite = Favorite::findOrFail($id);

        return ResponseService::success("Favorito encontrado: $id", $favorite);
    }

    public function criar(FavoriteRequest $request)
    {
        $dados = $request->validated();
        $dados['user_id'] = $request->user()->id;
        $user = $request->user();

        $tier = 1;
        $subscription = $user->activeSubscription;
        if ($subscription && $subscription->plan) {
            $tier = (int) $subscription->plan->tier;
        }

        if ($tier === 1) {
            $count = $user->favorites()->count();
            if ($count >= 20) {
                return ResponseService::error('Limite de músicas curtidas atingido para o seu plano', null, 403);
            }
        }

        $favorite = Favorite::criar($dados);

        return ResponseService::success('Favorito criado com sucesso', $favorite);
    }

    public function atualizar(FavoriteRequest $request, int $id)
    {
        $favorite = Favorite::findOrFail($id);
        $user = $request->user();

        if (($favorite->user_id ?? null) !== $user->id && ($user->role ?? null) !== 'admin') {
            return ResponseService::error('Acesso negado: propriedade do recurso inválida', null, 403);
        }

        $dados = $request->validated();

        $favorite->update($dados);

        return ResponseService::success('Favorito atualizado com sucesso', $favorite);
    }

    public function deletar(int $id)
    {
        $favorite = Favorite::findOrFail($id);
        $favorite->delete();

        return ResponseService::success('Favorito deletado com sucesso', null);
    }

    public function destroy(int $id)
    {
        $favorite = Favorite::withTrashed()->findOrFail($id);
        $favorite->forceDelete();

        return ResponseService::success('Favorito destruído com sucesso', null);
    }

    public function restore(int $id)
    {
        $favorite = Favorite::withTrashed()->findOrFail($id);
        $favorite->restore();

        return ResponseService::success('Favorito restaurado com sucesso', $favorite);
    }
}
