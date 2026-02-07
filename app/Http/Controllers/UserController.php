<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Throwable;

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

    public function atualizar(UserRequest $request, int $id)
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
