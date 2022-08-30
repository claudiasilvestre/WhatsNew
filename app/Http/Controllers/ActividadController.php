<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    /**
     * Consulta y devuelve la actividad de un usuario.
     * 
     * @param integer $usuario_id ID del usuario del que se quiere consultar su actividad.
     *
     * @return Response
     */
    public function actividadUsuario($usuario_id) {
        $actividad = DB::table('actividad')
                                ->leftJoin('audiovisual', 'actividad.audiovisual_id', 'audiovisual.id')
                                ->leftJoin('capitulo', 'actividad.capitulo_id', 'capitulo.id')
                                ->leftJoin('temporada', 'capitulo.temporada_id', 'temporada.id')
                                ->leftJoin('audiovisual as audiovisual_capitulo', 'temporada.audiovisual_id', 'audiovisual_capitulo.id')
                                ->leftJoin('temporada as temporada_actividad', 'actividad.temporada_id', 'temporada_actividad.id')
                                ->leftJoin('audiovisual as audiovisual_temporada', 'temporada_actividad.audiovisual_id', 'audiovisual_temporada.id')
                                ->where('persona_id', $usuario_id)
                                ->select('actividad.id', 'tipo', 'audiovisual.tipoAudiovisual_id', 'audiovisual.titulo as titulo_audiovisual', 
                                'capitulo.nombre','temporada.numero as numero_temporada', 'capitulo.numero as numero_capitulo', 
                                'audiovisual_capitulo.titulo as titulo_audiovisual_capitulo', 'temporada_actividad.numero as 
                                numero_temporada_actividad', 'audiovisual_temporada.titulo as titulo_audiovisual_temporada',
                                'audiovisual.id as id_audiovisual', 'audiovisual_capitulo.id as id_audiovisual_capitulo', 
                                'audiovisual_temporada.id as id_audiovisual_temporada', 'audiovisual.cartel as audiovisual_cartel', 
                                'audiovisual_capitulo.cartel as capitulo_cartel', 'audiovisual_temporada.cartel as temporada_cartel', 
                                'actividad.created_at', 'actividad.persona_id as usuario_id')
                                ->orderBy('actividad.created_at', 'desc')
                                ->get();

        return response()->json($actividad);
    }

    /**
     * Consulta y devuelve la actividad de los amigos del usuario actual.
     *
     * @return Response
     */
    public function actividadAmigos() {
        $usuario_id = Auth::id();

        $actividad = DB::table('actividad')
                                ->leftJoin('audiovisual', 'actividad.audiovisual_id', 'audiovisual.id')
                                ->leftJoin('capitulo', 'actividad.capitulo_id', 'capitulo.id')
                                ->leftJoin('temporada', 'capitulo.temporada_id', 'temporada.id')
                                ->leftJoin('audiovisual as audiovisual_capitulo', 'temporada.audiovisual_id', 'audiovisual_capitulo.id')
                                ->leftJoin('temporada as temporada_actividad', 'actividad.temporada_id', 'temporada_actividad.id')
                                ->leftJoin('audiovisual as audiovisual_temporada', 'temporada_actividad.audiovisual_id', 'audiovisual_temporada.id')
                                ->leftJoin('seguimiento_persona', 'actividad.persona_id', 'seguimiento_persona.persona_id')
                                ->leftJoin('persona', 'actividad.persona_id', 'persona.id')
                                ->where('seguimiento_persona.personaActual_id', $usuario_id)
                                ->select('tipo', 'audiovisual.tipoAudiovisual_id', 'audiovisual.titulo as titulo_audiovisual', 
                                'capitulo.nombre','temporada.numero as numero_temporada', 'capitulo.numero as numero_capitulo', 
                                'audiovisual_capitulo.titulo as titulo_audiovisual_capitulo', 'temporada_actividad.numero as 
                                numero_temporada_actividad', 'audiovisual_temporada.titulo as titulo_audiovisual_temporada',
                                'persona.nombre as usuario_nombre', 'persona.foto', 'audiovisual.id as id_audiovisual', 
                                'audiovisual_capitulo.id as id_audiovisual_capitulo', 'audiovisual_temporada.id as id_audiovisual_temporada', 
                                'audiovisual.cartel as audiovisual_cartel', 'audiovisual_capitulo.cartel as capitulo_cartel', 
                                'audiovisual_temporada.cartel as temporada_cartel', 'persona.id as usuario_id', 'actividad.created_at')
                                ->orderBy('actividad.created_at', 'desc')
                                ->get();

        return response()->json($actividad);
    }

    /**
     * Borra una actividad del usuario actual.
     * 
     * @param integer $actividad_id ID de la actividad que se desea borrar.
     *
     * @return void
     */
    public function borrarActividad($actividad_id) {
        Actividad::where('id', $actividad_id)->delete();
    }
}
