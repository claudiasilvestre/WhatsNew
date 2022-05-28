<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudiovisualController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\CapituloController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/audiovisuales', AudiovisualController::class);

Route::get('/temporadas/{idSerie}', [TemporadaController::class, 'index']);

Route::get('/capitulos/{temporada_id}', [CapituloController::class, 'index']);
Route::resource('/capitulo', CapituloController::class);

Route::get('/personas-participacion/{audiovisual_id}', [PersonaController::class, 'participacion']);
Route::get('/audiovisuales-participacion/{persona_id}', [AudiovisualController::class, 'participacion']);

Route::resource('/personas', PersonaController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
