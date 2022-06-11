<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudiovisualController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\CapituloController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'user']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::resource('/audiovisuales', AudiovisualController::class);

Route::get('/temporadas/{idSerie}', [TemporadaController::class, 'index']);

Route::get('/capitulos/{temporada_id}', [CapituloController::class, 'index']);
Route::resource('/capitulo', CapituloController::class);

Route::get('/personas-participacion/{audiovisual_id}', [PersonaController::class, 'participacion']);
Route::get('/audiovisuales-participacion/{persona_id}', [AudiovisualController::class, 'participacion']);

Route::resource('/personas', PersonaController::class);

Route::get('/saber-seguimiento', [AudiovisualController::class, 'saber_seguimiento']);
Route::post('/seguimiento', [AudiovisualController::class, 'seguimiento']);

Route::get('/visualizaciones', [CapituloController::class, 'visualizaciones']);
Route::post('/visualizacion-capitulo', [CapituloController::class, 'visualizacion']);

Route::get('/saber-visualizacion-temporada', [TemporadaController::class, 'saber_visualizacion']);
Route::post('/visualizacion-temporada', [TemporadaController::class, 'visualizacion']);