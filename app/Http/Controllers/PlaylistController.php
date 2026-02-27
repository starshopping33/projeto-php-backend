<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistRequest;
use App\Models\Playlist;
use App\Models\Music;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    
    public function listar()
    {
        $playlists = Playlist::all();
        return ResponseService::success('Listando playlists', $playlists);
    }

   
    public function buscarId(int $id)
    {
        $playlist = Playlist::with('musicas')->findOrFail($id);
        return ResponseService::success("Playlist encontrada: $id", $playlist);
    }

    
    public function criar(PlaylistRequest $request)
    {
        $dados = $request->validated();
        $user = $request->user();
        $dados['user_id'] = $user->id;

        $tier = 1;
        $subscription = $user->activeSubscription;
        if ($subscription && $subscription->plan) {
            $tier = (int) $subscription->plan->tier;
        }

        if ($tier === 2) {
            $count = $user->playlists()->count();
            if ($count >= 20) {
                return ResponseService::error('Limite de playlists atingido para o seu plano', null, 403);
            }
            unset($dados['cover'], $dados['is_collaborative'], $dados['folder_id']);
        }

        $playlist = Playlist::create($dados);
        return ResponseService::success('Playlist criada com sucesso', $playlist);
    }

   
    public function atualizar(PlaylistRequest $request, int $id)
    {
        $playlist = Playlist::findOrFail($id);
        $user = $request->user();

        if (($playlist->user_id ?? null) !== $user->id && ($user->role ?? null) !== 'admin') {
            return ResponseService::error('Acesso negado', null, 403);
        }

        $dados = $request->validated();
        $tier = 1;
        $subscription = $user->activeSubscription;
        if ($subscription && $subscription->plan) {
            $tier = (int) $subscription->plan->tier;
        }

        if ($tier === 1) {
            if (array_key_exists('name', $dados) || array_key_exists('order', $dados)) {
                return ResponseService::error('Atualização de nome/ordem requer plano Premium', null, 403);
            }
            if (array_key_exists('cover', $dados) || array_key_exists('is_collaborative', $dados)) {
                return ResponseService::error('Recursos avançados requerem plano Pro', null, 403);
            }
        }

        if ($tier === 2) {
            if (array_key_exists('is_collaborative', $dados) || array_key_exists('cover', $dados) || array_key_exists('folder_id', $dados)) {
                return ResponseService::error('Recursos avançados requerem plano Pro', null, 403);
            }
        }

        $playlist->update($dados);
        return ResponseService::success('Playlist atualizada com sucesso', $playlist);
    }

    
    public function deletar(int $id, Request $request)
    {
        $playlist = Playlist::findOrFail($id);
        $user = $request->user();

        if (($playlist->user_id ?? null) !== ($user->id ?? null) && ($user->role ?? null) !== 'admin') {
            return ResponseService::error('Acesso negado', null, 403);
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

    
    public function listarMusicas(int $playlistId)
    {
        $playlist = Playlist::with('musicas')->findOrFail($playlistId);
        return ResponseService::success('Músicas da playlist', $playlist->musicas);
    }

    public function adicionarMusica(int $playlistId, Request $request)
    {
        $playlist = Playlist::findOrFail($playlistId);
        $user = $request->user();

        if ($playlist->user_id !== $user->id && $user->role !== 'admin') {
            return ResponseService::error('Acesso negado', null, 403);
        }

        $dados = $request->validate([
            'music_id' => 'required|exists:musics,id',
        ]);

        
        if ($playlist->musicas()->where('music_id', $dados['music_id'])->exists()) {
            return ResponseService::error('Música já está na playlist', null, 409);
        }

        $playlist->musicas()->attach($dados['music_id']);
        return ResponseService::success('Música adicionada à playlist com sucesso', $playlist->load('musicas'));
    }

 
    public function removerMusica(int $playlistId, int $musicId, Request $request)
    {
        $playlist = Playlist::findOrFail($playlistId);
        $user = $request->user();

        if ($playlist->user_id !== $user->id && $user->role !== 'admin') {
            return ResponseService::error('Acesso negado', null, 403);
        }

        $playlist->musicas()->detach($musicId);
        return ResponseService::success('Música removida da playlist com sucesso', $playlist->load('musicas'));
    }
}