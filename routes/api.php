<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\PlanPriceController ;
use App\Http\Controllers\PaymentController ;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FolderController;

Route::prefix('user') -> group(function (){
    Route::get('', [UserController::class, 'listar']);
    Route::get('/{id}', [UserController::class, 'buscarId']);
    Route::post('/criar', [UserController::class, 'criar']);
    Route::middleware('auth:sanctum')->put('/atualizar/{id}', [UserController::class, 'atualizar']);
    Route::middleware('auth:sanctum')->delete('/deletar/{id}', [UserController::class, 'deletar']);
    Route::middleware('auth:sanctum')->put('/atualizarSenha', [UserController::class, 'atualizarSenha']);
    Route::middleware(['auth:sanctum', 'admin'])->post('/criarAdmin', [UserController::class, 'criarAdmin']);
    Route::middleware(['auth:sanctum', 'admin'])->delete('/destroy/{id}', [UserController::class, 'destroy']);
    Route::middleware(['auth:sanctum', 'admin'])->post('/restore/{id}', [UserController::class, 'restore']);
});

Route::prefix('login') -> group(function (){
    Route::post('', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/verificarToken', [LoginController::class, 'verificarToken']);
    Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
});


Route::get('/musicas/top', [MusicController::class, 'topTracks']);
Route::get('/musicas/tag/{tag}', [MusicController::class, 'topTracksByTag']);
Route::get('/plan-prices', [PlanPriceController::class, 'index']);
Route::get('/getplanos', [PlanoController::class, 'index']);
Route::post('/assinaturas', [AssinaturaController::class, 'store']);
Route::post('/planos', [PlanoController::class, 'store']);
Route::post('/usuarios', [UserController::class, 'store']);
Route::post('/plan-prices', [PlanPriceController::class, 'store']);
Route::post('/create-payment-intent', [PaymentController::class, 'createIntent']);
