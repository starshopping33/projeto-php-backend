<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
       
        $user = User::login(
            $request->email,
            $request->password
        );

        if (!$user) {
            return ResponseService::error('Email ou senha incorretos');
        }

        return ResponseService::success('Login realizado com sucesso', [
            'user' => ['name' => $user->name],
            'token' => $user->gerarToken(),
            'id'   => $user->id,
        ]);

        }

    
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return ResponseService::error('Não autenticado');
        }

        $user->logout();

        return ResponseService::success('Logout realizado com sucesso');
    }


    public function verificarToken(Request $request)
    {
        $user = $request->user();

        return ResponseService::success('Autenticado', [
            'usuario' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

}