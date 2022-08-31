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
use App\Http\Controllers\BusquedaController;

Route::post('/registro', [PersonaController::class, 'registro']);
Route::post('/inicio-sesion', [AuthController::class, 'inicioSesion']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuario', [AuthController::class, 'usuario']);
    Route::post('/cierre-sesion', [AuthController::class, 'cierreSesion']);

    Route::resource('/audiovisuales', AudiovisualController::class);
    Route::get('/audiovisuales-participacion/{persona_id}', [AudiovisualController::class, 'participacion']);
    Route::get('/mi-coleccion', [AudiovisualController::class, 'mi_coleccion']);
    Route::get('/recomendaciones', [AudiovisualController::class, 'recomendaciones']);
    Route::post('/valoracion-audiovisual', [AudiovisualController::class, 'valoracion_audiovisual']);
    Route::get('/saber-seguimiento-audiovisual', [AudiovisualController::class, 'saber_seguimiento_audiovisual']);
    Route::post('/seguimiento-audiovisual', [AudiovisualController::class, 'seguimiento_audiovisual']);
    Route::get('/proveedores/{audiovisual_id}', [AudiovisualController::class, 'proveedores']);
    Route::get('/coleccion-usuario/{usuario_id}', [AudiovisualController::class, 'coleccion_usuario']);
    Route::get('/saber-valoracion-audiovisual', [AudiovisualController::class, 'saber_valoracion_audiovisual']);

    Route::post('/guardar-informacion', [PersonaController::class, 'guardarInformacion']);
    Route::put('/guardar-password', [PersonaController::class, 'guardarPassword']);
    Route::post('/seguimiento-usuario', [PersonaController::class, 'seguimientoUsuario']);
    Route::get('/siguiendo/{usuario_id}', [PersonaController::class, 'siguiendo']);
    Route::get('/seguidores/{usuario_id}', [PersonaController::class, 'seguidores']);
    Route::get('/personas-participacion/{audiovisual_id}', [PersonaController::class, 'participacion']);
    Route::resource('/personas', PersonaController::class);
    Route::get('/info-usuario/{usuario_id}', [PersonaController::class, 'info']);
    Route::get('/saber-seguimiento-usuario', [PersonaController::class, 'saberSeguimientoUsuario']);

    Route::get('/busqueda/{busqueda}', [BusquedaController::class, 'busqueda']);

    Route::get('/actividad-amigos', [ActividadController::class, 'actividadAmigos']);
    Route::post('/borrar-actividad/{actividad_id}', [ActividadController::class, 'borrarActividad']);
    Route::get('/actividad-usuario/{usuario_id}', [ActividadController::class, 'actividadUsuario']);

    Route::get('/capitulos-anterior-siguiente', [CapituloController::class, 'capitulos_anterior_siguiente']);
    Route::get('/saber-visualizacion-capitulo/{capitulo_id}', [CapituloController::class, 'saber_visualizacion_capitulo']);
    Route::post('/visualizacion-capitulo/{capitulo_id}', [CapituloController::class, 'visualizacion_capitulo']);
    Route::get('/capitulos/{temporada_id}', [CapituloController::class, 'index']);
    Route::resource('/capitulo', CapituloController::class);
    Route::get('/visualizaciones', [CapituloController::class, 'visualizaciones']);
    Route::post('/visualizacion-capitulo', [CapituloController::class, 'visualizacion']);

    Route::get('/temporadas/{idSerie}', [TemporadaController::class, 'index']);
    Route::get('/saber-visualizacion-temporada', [TemporadaController::class, 'saberVisualizacion']);
    Route::post('/visualizacion-temporada', [TemporadaController::class, 'visualizacion']);

    Route::post('/guardar-comentario-audiovisual', [ComentarioController::class, 'guardarAudiovisual']);
    Route::post('/guardar-comentario-capitulo', [ComentarioController::class, 'guardarCapitulo']);
    Route::post('/borrar-comentario-audiovisual/{comentario_id}', [ComentarioController::class, 'borrarAudiovisual']);
    Route::post('/borrar-comentario-capitulo/{comentario_id}', [ComentarioController::class, 'borrarCapitulo']);
    Route::post('/opinion-positiva-audiovisual', [ComentarioController::class, 'opinionPositivaAudiovisual']);
    Route::post('/opinion-negativa-audiovisual', [ComentarioController::class, 'opinionNegativaAudiovisual']);
    Route::post('/opinion-positiva-capitulo', [ComentarioController::class, 'opinionPositivaCapitulo']);
    Route::post('/opinion-negativa-capitulo', [ComentarioController::class, 'opinionNegativaCapitulo']);
    Route::get('/comentario-audiovisual', [ComentarioController::class, 'audiovisual']);
    Route::get('/comentario-capitulo', [ComentarioController::class, 'capitulo']);
});
