<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistRequest;
use App\Models\Playlist;
use App\Services\ResponseService;

class PlaylistController extends Controller
{
    public function listar()
    {
        $playlists = Playlist::all();

        return ResponseService::success('Listando playlists', $playlists);
    }

    public function buscarId(int $id)
    {
        $playlist = Playlist::findOrFail($id);

        return ResponseService::success("Playlist encontrada: $id", $playlist);
    }

    public function criar(PlaylistRequest $request)
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
            $count = $user->playlists()->count();
            if ($count >= 20) {
                return ResponseService::error('Limite de playlists atingido para o seu plano', null, 403);
            }
            // block advanced fields for Tier 1
            unset($dados['cover']);
            unset($dados['is_collaborative']);
            unset($dados['folder_id']);
        }

        // Tier 2+: allowed to create unlimited playlists; Tier 3 extra handled elsewhere
        $playlist = Playlist::criar($dados);

        return ResponseService::success('Playlist criada com sucesso', $playlist);
    }

    public function atualizar(PlaylistRequest $request, int $id)
    {
        $playlist = Playlist::findOrFail($id);
        $user = $request->user();

        if (($playlist->user_id ?? null) !== $user->id && ($user->role ?? null) !== 'admin') {
            return ResponseService::error('Acesso negado: propriedade do recurso inválida', null, 403);
        }

        $dados = $request->validated();

        $tier = 1;
        $subscription = $user->activeSubscription;
        if ($subscription && $subscription->plan) {
            $tier = (int) $subscription->plan->tier;
        }

        // Tier 1 cannot edit name or order
        if ($tier === 1) {
            if (array_key_exists('name', $dados) || array_key_exists('order', $dados)) {
                return ResponseService::error('A atualização de nome/ordem requer plano Premium', null, 403);
            }
            // also cannot set cover or collaborative
            if (array_key_exists('cover', $dados) || array_key_exists('is_collaborative', $dados)) {
                return ResponseService::error('Recursos avançados de playlist requerem plano Pro', null, 403);
            }
        }

        // Tier 2 can edit name/order but not collaborative/cover/folders
        if ($tier === 2) {
            if (array_key_exists('is_collaborative', $dados) || array_key_exists('cover', $dados) || array_key_exists('folder_id', $dados)) {
                return ResponseService::error('Recursos avançados de playlist requerem plano Pro', null, 403);
            }
        }

        $playlist->atualizar($dados);

        return ResponseService::success('Playlist atualizada com sucesso', $playlist);
    }

    public function deletar(int $id)
    {
        $playlist = Playlist::findOrFail($id);
        $user = request()->user();

        if (($playlist->user_id ?? null) !== ($user->id ?? null) && ($user->role ?? null) !== 'admin') {
            return ResponseService::error('Acesso negado: propriedade do recurso inválida', null, 403);
        }

        $playlist->delete();

        return ResponseService::success('Playlist deletada com sucesso', null);
    }

    public function destroy(int $id)
    {
        $playlist = Playlist::withTrashed()->findOrFail($id);
        $playlist->forceDelete();

        return ResponseService::success('Playlist destruída com sucesso', null);
    }

    public function restore(int $id)
    {
        $playlist = Playlist::withTrashed()->findOrFail($id);
        $playlist->restore();

        return ResponseService::success('Playlist restaurada com sucesso', $playlist);
    }
}
