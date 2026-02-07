<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user') -> group(function (){
    Route::get('', [UserController::class, 'listar']);
    Route::get('/{id}', [UserController::class, 'buscarId']);
    Route::post('/criar', [UserController::class, 'criar']);
    Route::middleware('auth:sanctum')->put('/atualizar/{id}', [UserController::class, 'atualizar']);
    Route::middleware('auth:sanctum')->delete('/deletar/{id}', [UserController::class, 'deletar']);
    Route::middleware(['auth:sanctum', 'admin'])->put('/atualizarAcesso/{id}', [UserController::class, 'atualizarAcesso']);
    Route::middleware(['auth:sanctum', 'admin'])->delete('/destroy/{id}', [UserController::class, 'destroy']);
    Route::middleware(['auth:sanctum', 'admin'])->post('/restore/{id}', [UserController::class, 'restore']);
});

Route::prefix('login') -> group(function (){
    Route::post('', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/verificarToken', [LoginController::class, 'verificarToken']);
    Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
});
