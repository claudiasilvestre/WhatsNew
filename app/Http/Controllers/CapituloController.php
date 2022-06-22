<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionCapitulo;
use App\Models\VisualizacionTemporada;
use App\Models\Actividad;
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
        $temporada = DB::table('temporada')
            ->join('capitulo', 'temporada.id', '=', 'capitulo.temporada_id')
            ->where('capitulo.id', $request->capitulo_id)
            ->get();
                
        $capitulos = Capitulo::where('temporada_id', $temporada[0]->temporada_id)->get();
        $count = 0;

        foreach($capitulos as $capitulo) {
            if (VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $capitulo->id)->exists())
                $count++;
        }

        if (!VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $request->capitulo_id)->exists()) {
            VisualizacionCapitulo::create([
                'capitulo_id' => $request->capitulo_id,
                'persona_id' => $request->usuario_id,
            ]);

            Actividad::create([
                'persona_id' => $request->usuario_id,
                'tipo' => 3,
                'capitulo_id' => $request->capitulo_id,
            ]);

            if ($count+1 === $temporada[0]->numeroCapitulos) {
                VisualizacionTemporada::create([
                    'temporada_id' => $temporada[0]->temporada_id,
                    'persona_id' => $request->usuario_id,
                ]);

                Actividad::create([
                    'persona_id' => $request->usuario_id,
                    'tipo' => 3,
                    'temporada_id' => $temporada[0]->temporada_id,
                ]);

                $cambio = true;
            }
            else
                $cambio = false;

            return response()->json([
                'estado' => true,
                'cambio' => $cambio,
            ]);
        } else {
            if ($count === $temporada[0]->numeroCapitulos) {
                VisualizacionTemporada::where('persona_id', $request->usuario_id)
                ->where('temporada_id', $temporada[0]->temporada_id)
                ->delete();

                $cambio = true;
            }
            else
                $cambio = false;
            
            VisualizacionCapitulo::where('persona_id', $request->usuario_id)
                ->where('capitulo_id', $request->capitulo_id)
                ->delete();

            return response()->json([
                'estado' => false,
                'cambio' => $cambio,
            ]);
        }
    }
}
