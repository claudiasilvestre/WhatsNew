<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;

class TemporadaController extends Controller
{
    public function index($idSerie) {
        $temporadas = Temporada::where('audiovisual_id', $idSerie)->get();

        return response()->json($temporadas);
    }
}
