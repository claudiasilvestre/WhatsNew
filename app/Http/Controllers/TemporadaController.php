<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;
use App\Models\VisualizacionTemporada;
use App\Models\VisualizacionCapitulo;

class TemporadaController extends Controller
{
    public function index($idSerie) {
        $temporadas = Temporada::where('audiovisual_id', $idSerie)->get();

        return response()->json($temporadas);
    }

    public function saber_visualizacion(Request $request) {
        if (VisualizacionTemporada::where('persona_id', $request->usuario_id)->where('temporada_id', $request->temporada_id)->exists()) {
            return true;
        }

        return false;
    }

    public function visualizacion(Request $request) {
        if (!VisualizacionTemporada::where('persona_id', $request->usuario_id)->where('temporada_id', $request->temporada_id)->exists()) {
            VisualizacionTemporada::create([
                'temporada_id' => $request->temporada_id,
                'persona_id' => $request->usuario_id,
            ]);

            foreach ($request->capitulos as $capitulo) {
                if (!VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $capitulo['id'])->exists()) {
                    VisualizacionCapitulo::create([
                        'capitulo_id' => $capitulo['id'],
                        'persona_id' => $request->usuario_id,
                    ]);
                }
            }
            return true;
        } else {
            VisualizacionTemporada::where('persona_id', $request->usuario_id)
                ->where('temporada_id', $request->temporada_id)
                ->delete();

            foreach ($request->capitulos as $capitulo) {
                VisualizacionCapitulo::where('persona_id', $request->usuario_id)
                        ->where('capitulo_id', $capitulo['id'])
                        ->delete();
            }

            return false;
        }
    }
}
