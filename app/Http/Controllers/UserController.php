<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\PhotoUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function listar()
    {
        $user = User::all();

        return ResponseService::success('Listando usuários', $user);
    }

    public function buscarId(int $id)
    {
        $user = User::findOrFail($id);

        return ResponseService::success("Usuário encontrado: $id", $user);
    }

    public function criar(UserRequest $request)
    {
        $user = User::criar($request->validated());

        return ResponseService::success('Usuário criado com sucesso', $user);
    }

    public function criarAdmin(UserRequest $request)
    {
        $user = User::criarAdmin($request->validated());

        return ResponseService::success('Administrador criado com sucesso', $user);
    }

    public function atualizar(UpdateUserRequest $request, int $id)
    {
        $user = User::findOrFail($id);

        $dados = $request->validated();

        $user->atualizar($dados);
        
        return ResponseService::success('Usuário atualizado com sucesso', $user);
    }

    public function atualizarSenha(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        $dados = $request->validated();

        $user->atualizarSenha($dados['password']);
        
        return ResponseService::success('Senha atualizada com sucesso',
                ['user' => [
            'name' => $user->name,
            ]
        ]);
    }

  public function updatePhoto(PhotoUserRequest $request)
{
    $user = $request->user();

    
    if (!$user->canEditProfile()) {
        return ResponseService::error(
            'Seu plano não permite alterar a foto de perfil.',
            null,
            403
        );
    }

   
    $user->update([
        'profile_photo_base64' => $request->profile_photo_base64
    ]);

    return ResponseService::success(
        'Foto de perfil atualizada com sucesso.',
        [
            'id' => $user->id,
            'name' => $user->name,
            'profile_photo_base64' => $user->profile_photo_base64
        ]
    );
}
    public function deletar(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return ResponseService::success('Usuário deletado com sucesso', null);
    }

    public function destroy(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return ResponseService::success('Usuário destruído com sucesso', null);
    }

    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return ResponseService::success('Usuário restaurado com sucesso', $user);
    }
}
