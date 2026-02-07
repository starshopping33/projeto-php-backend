<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Login;
use App\Services\ResponseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
       
        $user = Login::login(
            $request->email,
            $request->password
        );

        return ResponseService::success('Login realizado com sucesso',
        ['user' => [
            'name' => $user->name,
            ],
            'token' => $user->gerarToken()
        ]);

        return ResponseService::error('Email ou senha incorretos');
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
                'nome' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

}