<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Temporada;
use App\Models\Capitulo;
use DB;

class CapituloController extends Controller
{
    public function index($idSerie, $numeroTemporada) {
        $temporada = DB::table('temporada')->where('numero', $numeroTemporada)->where('audiovisual_id', $idSerie)->first();
        $capitulos = Capitulo::where('temporada_id', $temporada->id)->get();

        return response()->json($capitulos);
    }

    public function show($id) {
        $capitulo = Capitulo::where('id', $id)->get();

        return response()->json($capitulo);
    }
}