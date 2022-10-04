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
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use App\Services\SistemaRecomendacionBasadoContenido;

class AudiovisualController extends Controller
{
    /**
     * Consulta y devuelve las 10 películas y las 10 series más recientes por fecha de lanzamiento.
     *
     * @return Response
     */
    public function index() {
        $peliculas = Audiovisual::where('tipoAudiovisual_id', 1)->orderBy('fechaLanzamiento', 'DESC')->take(10)->get();
        $series = Audiovisual::where('tipoAudiovisual_id', 2)->orderBy('fechaLanzamiento', 'DESC')->take(10)->get();

        return response()->json([
            'peliculas' => $peliculas,
            'series' => $series,
        ]);
    }

    /**
     * Consulta y devuelve un audiovisual por su ID.
     * 
     * @param integer $id ID del audiovisual que se quiere consultar.
     *
     * @return Response
     */
    public function show($id) {
        $audiovisual = Audiovisual::where('id', $id)->get();

        return response()->json($audiovisual);
    }

    /**
     * Consulta y devuelve los audiovisuales en los que ha participado una persona por el ID de la persona.
     * 
     * @param integer $persona_id ID de la persona cuyos audiovisuales en los que ha participado se quieren consultar.
     *
     * @return Response
     */
    public function participacion($persona_id) {
        $audiovisuales = DB::table('audiovisual')
                                ->join('participacion', 'audiovisual.id', '=', 'participacion.audiovisual_id')
                                ->where('participacion.persona_id', '=', $persona_id)
                                ->get();

        return response()->json($audiovisuales);
    }

    /**
     * Si existe un seguimiento del audiovisual para el usuario devuelve el estado, 
     * en caso de que no exista el seguimiento devuelve 0.
     * 
     * @param Request $request Contiene el ID del usuario actual y el ID del audiovisual.
     *
     * @return integer
     */
    public function saberSeguimientoAudiovisual(Request $request) {
        if (SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->exists()) {
            $seguimiento = SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->first();
            return $seguimiento->estado;
        }

        return 0;
    }

    /**
     * Si el seguimiento del audiovisual existe para el usuario se borra o se actualiza el seguimiento 
     * dependiendo del tipo de seguimiento recibido y se crea su corresponte actividad si procede, si el seguimiento 
     * del audiovisual no existe para el usuario se crea el seguimiento y su corresponte actividad. 
     * Si el tipo de seguimiento recibido es 1, significa que el audiovisual está pendiente para el usuario y se borran todas
     * las visualizaciones de este usuario con respecto al audiovisual. 
     * Si el tipo de seguimiento recibido es 3, significa que el audiovisual se ha visualizado al completo y se crea una 
     * visualización para cada capítulo y para cada temporada. Si se quita la visualización del audiovisual se borran las
     * visualizaciones de temporadas y capítulos.
     * 
     * @param Request $request Contiene el ID del usuario actual, el ID del audiovisual y el tipo de seguimiento.
     *
     * @return boolean
     */
    public function seguimientoAudiovisual(Request $request) {
        if (SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->exists()) {            
            $seguimiento = SeguimientoAudiovisual::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->first();

            // Si tipo es 3 y el estado del seguimiento actualmente es 3, se borra toda VisualizacionTemporada y VisualizacionCapitulo de esa serie para ese usuario.
            if ($seguimiento->estado == $request->tipo && $request->tipo == 3) {
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

            if ($seguimiento->estado == $request->tipo) {
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

        // Si tipo es 1 se borran los VisualizaciónTemporada y VisualizaciónCapitulo que existan de la serie para ese usuario.
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

        // Si tipo es 3 se crea VisualizacionTemporada y VisualizacionCapitulo de todos los capítulos de la serie que no existan para ese usuario.
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

    /**
     * Consulta y devuelve los proveedores de un audiovisual divididos por la condición que ofrece 
     * cada proveedor para visualizarlos.
     * 
     * @param integer $audiovisual_id ID del audiovisual cuyos proveedores se quieren consultar.
     *
     * @return Response
     */
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

    /**
     * Consulta y devuelve los audiovisuales que ha visto o sigue el usuario divididos por todos, los que son series
     * y los que son películas.
     * 
     * @param integer $usuario_id ID del usuario cuya colección de audiovisuales se quiere consultar.
     *
     * @return Response
     */
    public function coleccionUsuario($usuario_id) {
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

    /**
     * Si ya existe una valoración del usuario para el audiovisual, la actualiza con la nueva puntuación.
     * Si no, crea una valoración nueva e incrementa los puntos del usuario.
     * En cualquier caso, actualiza la puntuación del audiovisual.
     * 
     * @param Request $request Contiene el ID del audiovisual, el ID del usuario actual y la puntuación que da el usuario actual.
     *
     * @return void
     */
    public function valoracionAudiovisual(Request $request) {
        if (Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->exists()) {
            Valoracion::where('audiovisual_id', $request->audiovisual_id)
                      ->where('persona_id', $request->usuario_id)
                      ->update(['puntuacion' => $request->puntuacion]);

        } else {
            Valoracion::create([
                'audiovisual_id' => $request->audiovisual_id,
                'persona_id' => $request->usuario_id,
                'puntuacion' => $request->puntuacion,
            ]);

            Persona::where('id', $request->usuario_id)->increment('puntos', 5);
        }

        // Media de las valoraciones
        $num_valoraciones = Valoracion::where('audiovisual_id', $request->audiovisual_id)->count();
        $suma_valoraciones = Valoracion::where('audiovisual_id', $request->audiovisual_id)->sum('puntuacion');
        $puntuacion = $suma_valoraciones/$num_valoraciones;
        Audiovisual::where('id', $request->audiovisual_id)->update(['puntuacion' => $puntuacion]);
    }

    /**
     * Borra una valoración de un usuario para un audiovisual y actualiza la puntuación del audiovisual.
     * 
     * @param Request $request Contiene el ID del audiovisual y el ID del usuario actual.
     *
     * @return void
     */
    public function borrarValoracionAudiovisual(Request $request) {
        Valoracion::where('audiovisual_id', $request->audiovisual_id)
                  ->where('persona_id', $request->usuario_id)
                  ->delete();

        // Media de las valoraciones
        if ($num_valoraciones = Valoracion::where('audiovisual_id', $request->audiovisual_id)->count() > 0) {
            $suma_valoraciones = Valoracion::where('audiovisual_id', $request->audiovisual_id)->sum('puntuacion');
            $puntuacion = $suma_valoraciones/$num_valoraciones;
            Audiovisual::where('id', $request->audiovisual_id)->update(['puntuacion' => $puntuacion]);
        }
    }

    /**
     * Si existe una valoración del usuario al audiovisual devuelve la puntuación dada, 
     * en caso de que no exista la valoración devuelve 0.
     * 
     * @param Request $request Contiene el ID del audiovisual y el ID del usuario actual.
     *
     * @return integer
     */
    public function saberValoracionAudiovisual(Request $request) {
        if (Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->exists()) {
            $valoracion = Valoracion::where('audiovisual_id', $request->audiovisual_id)->where('persona_id', $request->usuario_id)->first();
            return $valoracion->puntuacion;
        }

        return 0;
    }

    /**
     * Consulta y devuelve los audiovisuales sobre los que tiene creado un seguimiento el usuario actual divididos por series,
     * series pendientes, series seguidas, series vistas, películas, películas pendientes y películas vistas.
     *
     * @return Response
     */
    public function miColeccion() {
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

    /**
     * Consulta los audiovisuales sobre los que existe un seguimiento por parte del usuario actual
     * y devuelve 10 audiovisuales como recomendación en base a estos.
     *
     * @return Response
     */
    public function recomendaciones() {
        $usuario_id = Auth::id();

        $audiovisuales = DB::table('audiovisual')
                            ->join('seguimiento_audiovisual', 'audiovisual.id', 'seguimiento_audiovisual.audiovisual_id')
                            ->where('persona_id', $usuario_id)
                            ->where('seguimiento_audiovisual.estado', '!=', 1)
                            ->get();

        $audiovisualesObject = [];

        foreach ($audiovisuales as $audiovisual) {
            $av = Audiovisual::find($audiovisual->audiovisual_id);
            array_push($audiovisualesObject, $av);
        }

        $content_engine = new SistemaRecomendacionBasadoContenido;

        $sugerencias = $content_engine->sugerenciasAudiovisuales($audiovisualesObject);

        return response()->json($sugerencias);
    }
}
