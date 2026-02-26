<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Models\Folder;
use App\Services\ResponseService;

class FolderController extends Controller
{
    public function listar()
    {
        $folders = Folder::all();

        return ResponseService::success('Listando pastas', $folders);
    }

    public function buscarId(int $id)
    {
        $folder = Folder::findOrFail($id);

        return ResponseService::success("Pasta encontrada: $id", $folder);
    }

    public function criar(FolderRequest $request)
    {
        $dados = $request->validated();
        $dados['user_id'] = $request->user()->id;
        $user = $request->user();

        $tier = 1;
        $subscription = $user->activeSubscription;
        if ($subscription && $subscription->plan) {
            $tier = (int) $subscription->plan->tier;
        }
        
        if ($tier < 3) {
            return ResponseService::error('Criação de pastas é permitida apenas para o plano Pro', null, 403);
        }

        $dados['user_id'] = $user->id;
        $folder = Folder::criar($dados);

        return ResponseService::success('Pasta criada com sucesso', $folder);
    }

    public function atualizar(FolderRequest $request, int $id)
    {
        $folder = Folder::findOrFail($id);
        $user = $request->user();

        if (($folder->user_id ?? null) !== $user->id && ($user->role ?? null) !== 'admin') {
            return ResponseService::error('Acesso negado: propriedade do recurso inválida', null, 403);
        }

        $dados = $request->validated();

        $folder->atualizar($dados);

        return ResponseService::success('Pasta atualizada com sucesso', $folder);
    }

    public function deletar(int $id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();

        return ResponseService::success('Pasta deletada com sucesso', null);
    }

    public function destroy(int $id)
    {
        $folder = Folder::withTrashed()->findOrFail($id);
        $folder->forceDelete();

        return ResponseService::success('Pasta destruída com sucesso', null);
    }

    public function restore(int $id)
    {
        $folder = Folder::withTrashed()->findOrFail($id);
        $folder->restore();

        return ResponseService::success('Pasta restaurada com sucesso', $folder);
    }
}
