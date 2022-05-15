<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudiovisualController;
use App\Http\Controllers\CapituloController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^((?!audiovisuales|capitulos).)*$');

Route::resource('/audiovisuales', AudiovisualController::class);

Route::get('/capitulos/{idSerie}/{numeroTemporada}', [CapituloController::class, 'index']);
Route::resource('/capitulos', CapituloController::class);
