<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return ResponseService::error('Não autorizado', null, 401);
        }

        if (($user->role ?? null) !== 'admin') {
            Log::warning('Acesso negado pelo middleware admin', ['user_id' => $user->id ?? null]);
            return ResponseService::error('Acesso negado: requer administrador', null, 403);
        }

        return $next($request);
    }
}
