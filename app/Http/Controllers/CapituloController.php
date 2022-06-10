<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionCapitulo;
use DB;

class CapituloController extends Controller
{
    public function index($temporada_id) {
        $capitulos = Capitulo::where('temporada_id', $temporada_id)->get();

        return response()->json($capitulos);
    }

    public function show($id) {
        $capitulo = Capitulo::where('id', $id)->get();

        return response()->json($capitulo);
    }

    public function visualizaciones(Request $request) {
        $clicked = array_fill(0, sizeof($request->capitulos), false);
        for ($i = 0; $i < sizeof($request->capitulos); $i++) {
            $capitulo = json_decode($request->capitulos[$i]);
            if (VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $capitulo->id)->exists())
                $clicked[$i] = true;
        }

        return $clicked;
    }

    public function visualizacion(Request $request) {
        if (!VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $request->capitulo_id)->exists()) {
            VisualizacionCapitulo::create([
                'capitulo_id' => $request->capitulo_id,
                'persona_id' => $request->usuario_id,
            ]);

            return true;
        } else {
            VisualizacionCapitulo::where('persona_id', $request->usuario_id)
                ->where('capitulo_id', $request->capitulo_id)
                ->delete();

            return false;
        }
    }
}
