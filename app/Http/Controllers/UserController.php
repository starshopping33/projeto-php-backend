<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name ?? 'Usuário Teste',
            'email' => $request->email ?? fake()->unique()->safeEmail(),
            'password' => Hash::make('123456'),
        ]);

        return response()->json($user, 201);
    }
}
