<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;
use App\Models\VisualizacionTemporada;
use App\Models\VisualizacionCapitulo;
use App\Models\Actividad;
use App\Models\Capitulo;

class TemporadaController extends Controller
{
    /**
     * Consulta y devuelve las temporadas de una serie por el ID de la serie.
     * 
     * @param integer $idSerie ID de la serie cuyas temporadas se quieren consultar.
     *
     * @return Response
     */
    public function index($idSerie) {
        $temporadas = Temporada::where('audiovisual_id', $idSerie)->get();

        return response()->json($temporadas);
    }

    /**
     * Consulta y devuelve una temporada de una serie por el ID de un capítulo.
     * 
     * @param integer $idCapitulo ID del capítulo cuya temporada se quiere consultar.
     *
     * @return Response
     */
    public function temporada($idCapitulo) {
        $capitulo = Capitulo::where('id', $idCapitulo)->first();
        $temporada = $capitulo->temporada;

        return response()->json($temporada);
    }

    /**
     * Consulta y devuelve la primera temporada de una serie por el ID de la serie.
     * 
     * @param integer $idSerie ID de la serie cuya temporada se quiere consultar.
     *
     * @return Response
     */
    public function primeraTemporada($idSerie) {
        $temporada = Temporada::where('audiovisual_id', $idSerie)->first();

        return response()->json($temporada);
    }

    /**
     * Comprueba si el usuario actual ha visualizado una temporada o no.
     * 
     * @param Request $request Contiene el ID del usuario actual y el ID de la temporada.
     *
     * @return boolean
     */
    public function saberVisualizacion(Request $request) {
        if (VisualizacionTemporada::where('persona_id', $request->usuario_id)->where('temporada_id', $request->temporada_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Crea o borra visualización de temporada y de sus capítulos del usuario actual dependiendo de si existen ya
     * y crea la actividad correspondiente en caso de creación de visualización de temporada.
     * 
     * @param Request $request Contiene el ID del usuario actual, el ID de la temporada y los capítulos de la temporada.
     *
     * @return boolean
     */
    public function visualizacion(Request $request) {
        if (!VisualizacionTemporada::where('persona_id', $request->usuario_id)->where('temporada_id', $request->temporada_id)->exists()) {
            VisualizacionTemporada::create([
                'temporada_id' => $request->temporada_id,
                'persona_id' => $request->usuario_id,
            ]);

            Actividad::create([
                'persona_id' => $request->usuario_id,
                'tipo' => 3,
                'temporada_id' => $request->temporada_id,
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
