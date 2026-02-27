<?php

namespace App\Http\Controllers;


use App\Http\Requests\FavoriteRequest;
use App\Models\Favorite;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 

class FavoriteController extends Controller
{
    public function listar()
    {
        $favorites = Favorite::all();

        return ResponseService::success('Listando favoritos', $favorites);
    }

   public function listarFavoritosDoUsuario()
{
    return response()->json([
        'auth_check' => Auth::check(),
        'user' => Auth::user(),
        'id' => optional(Auth::user())->id
    ]);
}

    public function descurtir($musicId, Request $request)
{
    $user = $request->user();

    $favorite = Favorite::where('user_id', $user->id)
        ->where('music_id', $musicId)
        ->first();

    if (!$favorite) {
        return ResponseService::error(
            'Música não está nos favoritos.',
            null,
            404
        );
    }

    $favorite->forceDelete(); 

    return ResponseService::success(
        'Música removida dos favoritos.'
    );
}

   public function criar(FavoriteRequest $request)
{
    $user = $request->user();
    $dados = $request->validated();

    $subscription = $user->activeSubscription;

    if (!$subscription || !$subscription->plan) {
        return ResponseService::error(
            'Você precisa de um plano ativo para curtir músicas.',
            null,
            403
        );
    }

    $tier = (int) $subscription->plan->tier;

    if ($tier === 1) {

        $favoritesCount = $user->favorites()->count();

      
        $jaExiste = Favorite::where('user_id', $user->id)
            ->where('music_id', $dados['music_id'])
            ->exists();

        if (!$jaExiste && $favoritesCount >= 5) {
            return ResponseService::error(
                'Limite de 5 músicas curtidas atingido para o plano Tier 1.',
                null,
                403
            );
        }
    }

   
    $favorite = Favorite::updateOrCreate(
        [
            'user_id' => $user->id,
            'music_id' => $dados['music_id'],
        ],
        [
            'music_name' => $dados['music_name'],
            'artist_name' => $dados['artist_name'],
        ]
    );

    return ResponseService::success(
        'Favorito salvo com sucesso',
        $favorite
    );
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
