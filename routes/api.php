<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudiovisualController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\CapituloController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\SearchController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/guardar-informacion', [PersonaController::class, 'guardar_informacion']);
    Route::put('/guardar-password', [PersonaController::class, 'guardar_password']);
    Route::get('/search/{busqueda}', [SearchController::class, 'search']);
    Route::get('/mi_coleccion', [AudiovisualController::class, 'mi_coleccion']);
    Route::get('/actividad_amigos', [ActividadController::class, 'actividad_amigos']);
    Route::get('/recomendaciones', [AudiovisualController::class, 'recomendaciones']);
    Route::post('/borrar-actividad/{actividad_id}', [ActividadController::class, 'borrar_actividad']);
    Route::get('/capitulos-anterior-siguiente', [CapituloController::class, 'capitulos_anterior_siguiente']);
    Route::get('/saber-visualizacion-capitulo/{capitulo_id}', [CapituloController::class, 'saber_visualizacion_capitulo']);
    Route::post('/visualizacion-capitulo/{capitulo_id}', [CapituloController::class, 'visualizacion_capitulo']);
});

Route::resource('/audiovisuales', AudiovisualController::class);

Route::get('/temporadas/{idSerie}', [TemporadaController::class, 'index']);

Route::get('/capitulos/{temporada_id}', [CapituloController::class, 'index']);
Route::resource('/capitulo', CapituloController::class);

Route::get('/personas-participacion/{audiovisual_id}', [PersonaController::class, 'participacion']);
Route::get('/audiovisuales-participacion/{persona_id}', [AudiovisualController::class, 'participacion']);

Route::resource('/personas', PersonaController::class);

Route::get('/saber-seguimiento-audiovisual', [AudiovisualController::class, 'saber_seguimiento_audiovisual']);
Route::post('/seguimiento-audiovisual', [AudiovisualController::class, 'seguimiento_audiovisual']);

Route::get('/visualizaciones', [CapituloController::class, 'visualizaciones']);
Route::post('/visualizacion-capitulo', [CapituloController::class, 'visualizacion']);

Route::get('/saber-visualizacion-temporada', [TemporadaController::class, 'saber_visualizacion']);
Route::post('/visualizacion-temporada', [TemporadaController::class, 'visualizacion']);

Route::get('/proveedores/{audiovisual_id}', [AudiovisualController::class, 'proveedores']);

Route::post('/guardar-comentario-audiovisual', [ComentarioController::class, 'guardarAudiovisual']);
Route::post('/guardar-comentario-capitulo', [ComentarioController::class, 'guardarCapitulo']);
Route::get('/comentario-audiovisual', [ComentarioController::class, 'audiovisual']);
Route::get('/comentario-capitulo', [ComentarioController::class, 'capitulo']);
Route::post('/borrar-comentario-audiovisual/{comentario_id}', [ComentarioController::class, 'borrarAudiovisual']);
Route::post('/borrar-comentario-capitulo/{comentario_id}', [ComentarioController::class, 'borrarCapitulo']);

Route::post('/opinion-positiva-audiovisual', [ComentarioController::class, 'opinionPositivaAudiovisual']);
Route::post('/opinion-negativa-audiovisual', [ComentarioController::class, 'opinionNegativaAudiovisual']);
Route::post('/opinion-positiva-capitulo', [ComentarioController::class, 'opinionPositivaCapitulo']);
Route::post('/opinion-negativa-capitulo', [ComentarioController::class, 'opinionNegativaCapitulo']);

Route::get('/actividad-usuario/{usuario_id}', [ActividadController::class, 'actividad_usuario']);
Route::get('/coleccion-usuario/{usuario_id}', [AudiovisualController::class, 'coleccion_usuario']);
Route::get('/info-usuario/{usuario_id}', [PersonaController::class, 'info']);
Route::get('/saber-seguimiento-usuario', [PersonaController::class, 'saber_seguimiento_usuario']);
Route::post('/seguimiento-usuario', [PersonaController::class, 'seguimiento_usuario']);

Route::post('/valoracion-audiovisual', [AudiovisualController::class, 'valoracion_audiovisual']);
Route::get('/saber-valoracion-audiovisual', [AudiovisualController::class, 'saber_valoracion_audiovisual']);
