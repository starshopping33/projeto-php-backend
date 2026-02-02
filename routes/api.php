<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\UserController;


Route::get('/musicas-populares', [MusicController::class, 'topTracks']);
Route::get('/getplanos', [PlanoController::class, 'index']);
Route::post('/assinaturas', [AssinaturaController::class, 'store']);
Route::post('/planos', [PlanoController::class, 'store']);
Route::post('/usuarios', [UserController::class, 'store']);
