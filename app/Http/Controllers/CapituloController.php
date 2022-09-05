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
use Illuminate\Support\Facades\Auth;

class CapituloController extends Controller
{
    /**
     * Consulta y devuelve los capítulos de una temporada por el ID de la temporada.
     * 
     * @param integer $temporada_id ID de la temporada cuyos capítulos se quieren consultar.
     *
     * @return Response
     */
    public function index($temporada_id) {
        $capitulos = Capitulo::where('temporada_id', $temporada_id)->get();

        return response()->json($capitulos);
    }

    /**
     * Consulta y devuelve un capítulo por su ID.
     * 
     * @param integer $id ID del capítulo que se quiere consultar.
     *
     * @return Response
     */
    public function show($id) {
        $capitulo = Capitulo::where('id', $id)->get();

        return response()->json($capitulo);
    }

    /**
     * Comprueba que capítulos se han visualizado.
     * 
     * @param Request $request Contiene los capítulos a comprobar su visualización y el usuario actual.
     *
     * @return Array
     */
    public function visualizaciones(Request $request) {
        $clicked = array_fill(0, sizeof($request->capitulos), false);
        for ($i = 0; $i < sizeof($request->capitulos); $i++) {
            $capitulo = json_decode($request->capitulos[$i]);
            if (VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $capitulo->id)->exists())
                $clicked[$i] = true;
        }

        return $clicked;
    }

    /**
     * Crea o borra visualización de capítulo, visualización de temporada y sus respectivas actividades.
     * 
     * @param integer $capitulo_id ID del capítulo.
     *
     * @return Response
     */
    public function visualizacionCapitulo($capitulo_id) {
        $usuario_id = Auth::id();

        $temporada = DB::table('temporada')
            ->join('capitulo', 'temporada.id', '=', 'capitulo.temporada_id')
            ->where('capitulo.id', $capitulo_id)
            ->get();
                
        $capitulos = Capitulo::where('temporada_id', $temporada[0]->temporada_id)->get();
        $count = 0;

        foreach($capitulos as $capitulo) {
            if (VisualizacionCapitulo::where('persona_id', $usuario_id)->where('capitulo_id', $capitulo->id)->exists())
                $count++;
        }

        if (!VisualizacionCapitulo::where('persona_id', $usuario_id)->where('capitulo_id', $capitulo_id)->exists()) {
            VisualizacionCapitulo::create([
                'capitulo_id' => $capitulo_id,
                'persona_id' => $usuario_id,
            ]);

            Actividad::create([
                'persona_id' => $usuario_id,
                'tipo' => 3,
                'capitulo_id' => $capitulo_id,
            ]);

            if ($count+1 == $temporada[0]->numeroCapitulos) {
                VisualizacionTemporada::create([
                    'temporada_id' => $temporada[0]->temporada_id,
                    'persona_id' => $usuario_id,
                ]);

                Actividad::create([
                    'persona_id' => $usuario_id,
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
            if ($count == $temporada[0]->numeroCapitulos) {
                VisualizacionTemporada::where('persona_id', $usuario_id)
                ->where('temporada_id', $temporada[0]->temporada_id)
                ->delete();

                $cambio = true;
            }
            else
                $cambio = false;
            
            VisualizacionCapitulo::where('persona_id', $usuario_id)
                ->where('capitulo_id', $capitulo_id)
                ->delete();

            return response()->json([
                'estado' => false,
                'cambio' => $cambio,
            ]);
        }
    }

    /**
     * Consulta y devuelve el capítulo anterior y el siguiente capítulo de uno dado en caso de que existan.
     * 
     * @param Request $request Contiene el ID del audiovisual al que pertenece el capítulo y el ID del capítulo.
     *
     * @return Response
     */
    public function capitulosAnteriorSiguiente(Request $request) {
        $anteriorCapitulo = DB::table('capitulo')
                                    ->join('temporada', 'capitulo.temporada_id', '=', 'temporada.id')
                                    ->join('audiovisual', 'temporada.audiovisual_id', '=', 'audiovisual.id')
                                    ->where('audiovisual.id', $request->audiovisual_id)
                                    ->where('capitulo.id', '<', $request->capitulo_id)
                                    ->select('capitulo.id')
                                    ->latest('capitulo.id')
                                    ->first();

        $anteriorCapitulo_id = '';
        if ($anteriorCapitulo)
            $anteriorCapitulo_id = $anteriorCapitulo->id;

        $siguienteCapitulo = DB::table('capitulo')
                                    ->join('temporada', 'capitulo.temporada_id', '=', 'temporada.id')
                                    ->join('audiovisual', 'temporada.audiovisual_id', '=', 'audiovisual.id')
                                    ->where('audiovisual.id', $request->audiovisual_id)
                                    ->where('capitulo.id', '>', $request->capitulo_id)
                                    ->select('capitulo.id')
                                    ->first();

        $siguienteCapitulo_id = '';
        if ($siguienteCapitulo)
            $siguienteCapitulo_id = $siguienteCapitulo->id;

        return response()->json([
            'anteriorCapitulo_id' => $anteriorCapitulo_id,
            'siguienteCapitulo_id' => $siguienteCapitulo_id,
        ]);
    }

    /**
     * Comprueba si el usuario actual ha visualizado un capítulo o no.
     * 
     * @param integer $capitulo_id ID del capítulo del que se quiere saber si lo ha visualizado el usuario actual.
     *
     * @return boolean
     */
    public function saberVisualizacionCapitulo($capitulo_id) {
        $usuario_id = Auth::id();

        if (VisualizacionCapitulo::where('persona_id', $usuario_id)->where('capitulo_id', $capitulo_id)->exists())
            return true;
        else
            return false;
    }
}
