<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use App\Models\Usuario;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    public function listar()
    {
        $usuarios = User::all();

        return ResponseService::success('Listando usuários', $usuarios);
    }

    public function buscarId(int $id)
    {
        $usuario = User::findOrFail($id);
        return ResponseService::success("Usuário encontrado: $id", $usuario);
    }

    public function criar(UserRequest $request)
    {
        $usuario = User::criar($request->validated());

        return ResponseService::success('Usuário criado com sucesso', $usuario, 201);
    }


    public function atualizar(UserRequest $request, int $id)
    {
        $usuario = User::findOrFail($id);

        $dados = $request->validated();

        $usuario->atualizar($dados);

        return ResponseService::success('Usuário atualizado com sucesso', $usuario);
    }

    public function atualizarAcesso(Request $request, int $id)
    {
        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return ResponseService::error('Usuário não encontrado', null, 404);
            }

            $validate = $request->validate([
                'role' => ['required', 'string', 'in:usuario,admin'],
            ], [
                'role.required' => 'O campo role é obrigatório.',
                'role.in' => 'O role deve ser "usuario" ou "admin".',
            ]);

            $auth = Auth::user();
            if ($auth && $auth->id === $usuario->id) {
                return ResponseService::error('Alterar seu próprio nível de acesso não é permitido', null, 403);
            }

            $usuario->role = $validate['role'];
            $usuario->save();

            Log::info('Acesso de usuário atualizado', [
                'admin_id' => $auth?->id,
                'usuario_id' => $usuario->id,
                'novo_role' => $usuario->role,
            ]);

            return ResponseService::success('Acesso do usuário atualizado com sucesso', $usuario);
        } catch (ValidationException $ve) {
            return ResponseService::error('Erro de validação', $ve->errors(), 422);
        } catch (Throwable $e) {
            Log::error('Erro em UsuarioController::atualizarAcesso - ' . $e->getMessage(), ['exception' => $e]);
            return ResponseService::error('Erro interno ao atualizar acesso do usuário', null, 500);
        }
    }

    public function deletar(int $id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        
        return ResponseService::success('Usuário deletado com sucesso', null);
    }

    public function destroy(int $id)
    {
        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->forceDelete();

        return ResponseService::success('Usuário destruído com sucesso', null);
    }

    public function restore(int $id)
    {
        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->restore();

        return ResponseService::success('Usuário restaurado com sucesso', $usuario);
    }
}
