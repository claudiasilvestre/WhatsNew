<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Audiovisual;
use Illuminate\Support\Facades\DB;
use App\Models\SeguimientoAudiovisual;
use App\Models\ProveedorAudiovisual;
use App\Models\Proveedor;
use App\Models\Actividad;
use App\Models\Valoracion;
use App\Models\VisualizacionTemporada;
use App\Models\VisualizacionCapitulo;
use App\Models\Temporada;
use App\Models\Capitulo;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentBasedRecommenderSystem;

class AudiovisualController extends Controller
{
    public function index() {
        $peliculas = Audiovisual::where('tipoAudiovisual_id', 1)->orderBy('fechaLanzamiento', 'DESC')->get();
        $series = Audiovisual::where('tipoAudiovisual_id', 2)->orderBy('fechaLanzamiento', 'DESC')->get();

        return response()->json([
            'peliculas' => $peliculas,
            'series' => $series,
        ]);
    }

    public function show($id) {
        $audiovisual = Audiovisual::where('id', $id)->get();

        return response()->json($audiovisual);
    }

    public function participacion($persona_id) {
        $personas = DB::table('audiovisual')
                                ->join('participacion', 'audiovisual.id', '=', 'participacion.audiovisual_id')
                                ->where('participacion.persona_id', '=', $persona_id)
                                ->get();

        return response()->json($personas);
    }

    public function saber_seguimiento_audiovisual(Request $request) {
        if (SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->exists()) {
            $seguimiento = SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->first();
            return $seguimiento->estado;
        }

        return 0;
    }

    public function seguimiento_audiovisual(Request $request) {
        if (SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->exists()) {            
            $seguimiento = SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->first();
            if ($seguimiento->estado === $request->tipo) {
                $seguimiento->delete();
                return false;
            }

            SeguimientoAudiovisual::where('persona_id', $request->usuario_id)
                ->where('audiovisual_id', $request->audiovisual_id)
                ->update(['estado' => $request->tipo]);

            Actividad::create([
                'persona_id' => $request->usuario_id,
                'tipo' => $request->tipo,
                'audiovisual_id' => $request->audiovisual_id,
            ]);
        } else {
            SeguimientoAudiovisual::create([
                'audiovisual_id' => $request->audiovisual_id,
                'persona_id' => $request->usuario_id,
                'estado' => $request->tipo,
            ]);

            Actividad::create([
                'persona_id' => $request->usuario_id,
                'tipo' => $request->tipo,
                'audiovisual_id' => $request->audiovisual_id,
            ]);
        }

        // Si tipo es 1 se borran los VisualizaciónTemporada y VisualizaciónCapitulo que existan de la serie para ese usuario
        if ($request->tipo === 1) {
            $temporadas = Temporada::where('audiovisual_id', $request->audiovisual_id)->get();

            foreach ($temporadas as $temporada) {
                if (VisualizacionTemporada::where('persona_id', $request->usuario_id)->where('temporada_id', $temporada->id)->exists()) { 
                    VisualizacionTemporada::where('persona_id', $request->usuario_id)
                    ->where('temporada_id', $temporada->id)
                    ->delete();
                }

                $capitulos = Capitulo::where('temporada_id', $temporada->id)->get();

                foreach ($capitulos as $capitulo) {
                    if (VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $capitulo->id)->exists()) { 
                        VisualizacionCapitulo::where('persona_id', $request->usuario_id)
                        ->where('capitulo_id', $capitulo->id)
                        ->delete();
                    }
                }
            }
        }

        // Si tipo es 3 se crea VisualizacionTemporada y VisualizacionCapitulo de todos los capítulos de la serie que no existan para ese usuario
        if ($request->tipo === 3) {
            $temporadas = Temporada::where('audiovisual_id', $request->audiovisual_id)->get();

            foreach ($temporadas as $temporada) {
                if (!VisualizacionTemporada::where('persona_id', $request->usuario_id)->where('temporada_id', $temporada->id)->exists()) { 
                    VisualizacionTemporada::create([
                        'temporada_id' => $temporada->id,
                        'persona_id' => $request->usuario_id,
                    ]);
                }

                $capitulos = Capitulo::where('temporada_id', $temporada->id)->get();

                foreach ($capitulos as $capitulo) {
                    if (!VisualizacionCapitulo::where('persona_id', $request->usuario_id)->where('capitulo_id', $capitulo->id)->exists()) { 
                        VisualizacionCapitulo::create([
                            'capitulo_id' => $capitulo->id,
                            'persona_id' => $request->usuario_id,
                        ]);
                    }
                }
            }
        }

        return true;
    }

    public function proveedores($audiovisual_id) {
        $stream = DB::table('proveedor')
                                ->join('proveedor_audiovisual', 'proveedor.id', '=', 'proveedor_audiovisual.proveedor_id')
                                ->where('proveedor_audiovisual.audiovisual_id', $audiovisual_id)
                                ->where('disponibilidad', 1)
                                ->get();
        $alquilar = DB::table('proveedor')
                                ->join('proveedor_audiovisual', 'proveedor.id', '=', 'proveedor_audiovisual.proveedor_id')
                                ->where('proveedor_audiovisual.audiovisual_id', $audiovisual_id)
                                ->where('disponibilidad', 2)
                                ->get();
        $comprar = DB::table('proveedor')
                                ->join('proveedor_audiovisual', 'proveedor.id', '=', 'proveedor_audiovisual.proveedor_id')
                                ->where('proveedor_audiovisual.audiovisual_id', $audiovisual_id)
                                ->where('disponibilidad', 3)
                                ->get();

        return response()->json([
            'stream' => $stream,
            'alquilar' => $alquilar,
            'comprar' => $comprar,
        ]);
    }

    public function coleccion_usuario($usuario_id) {
        $todo = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '!=', 1)
                                ->get();
        $series = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '!=', 1)
                                ->where('tipoAudiovisual_id', 2)
                                ->get();
        $peliculas = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '!=', 1)
                                ->where('tipoAudiovisual_id', 1)
                                ->get();

        return response()->json([
            'todo' => $todo,
            'series' => $series,
            'peliculas' => $peliculas,
        ]);
    }

    public function valoracion_audiovisual(Request $request) {
        if (Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->exists()) {
            Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->delete();
        }
        
        Valoracion::create([
            'audiovisual_id' => $request->audiovisual_id,
            'persona_id' => $request->usuario_id,
            'puntuacion' => $request->puntuacion,
        ]);

        return response()->json(['msg' => 'Valoración añadida']);
    }

    public function saber_valoracion_audiovisual(Request $request) {
        if (Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->exists()) {
            $valoracion = Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->first();
            return $valoracion->puntuacion;
        }

        return 0;
    }

    public function mi_coleccion() {
        $usuario_id = Auth::id();
        
        $series = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('tipoAudiovisual_id', 2)
                                ->get();
                            
        $series_pendientes = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '=', 1)
                                ->where('tipoAudiovisual_id', 2)
                                ->get();

        $series_siguiendo = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '=', 2)
                                ->where('tipoAudiovisual_id', 2)
                                ->get();

        $series_vistas = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '=', 3)
                                ->where('tipoAudiovisual_id', 2)
                                ->get();

        $peliculas = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('tipoAudiovisual_id', 1)
                                ->get();

        $peliculas_pendientes = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '=', 1)
                                ->where('tipoAudiovisual_id', 1)
                                ->get();

        $peliculas_vistas = DB::table('audiovisual')
                                ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                                ->where('persona_id', $usuario_id)
                                ->where('seguimiento_audiovisual.estado', '=', 3)
                                ->where('tipoAudiovisual_id', 1)
                                ->get();

        return response()->json([
            'series' => $series,
            'series_pendientes' => $series_pendientes,
            'series_siguiendo' => $series_siguiendo,
            'series_vistas' => $series_vistas,
            'peliculas' => $peliculas,
            'peliculas_pendientes' => $peliculas_pendientes,
            'peliculas_vistas' => $peliculas_vistas,
        ]);
    }

    public function recomendaciones() {
        $usuario_id = Auth::id();

        $audiovisuales = DB::table('audiovisual')
                            ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                            ->where('persona_id', $usuario_id)
                            ->get();

        $audiovisualesObject = [];

        foreach ($audiovisuales as $audiovisual) {
            $av = Audiovisual::find($audiovisual->audiovisual_id);
            array_push($audiovisualesObject, $av);
        }

        $content_engine = new ContentBasedRecommenderSystem;

        $sugerencias = $content_engine->sugerenciasAudiovisuales($audiovisualesObject);

        return response()->json($sugerencias);
    }
}
