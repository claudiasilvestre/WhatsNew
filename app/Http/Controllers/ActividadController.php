<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use Illuminate\Support\Facades\DB;

class ActividadController extends Controller
{
    public function actividad_usuario($usuario_id) {
        $actividad = DB::table('actividad')
                                ->leftJoin('audiovisual', 'actividad.audiovisual_id', 'audiovisual.id')
                                ->leftJoin('capitulo', 'actividad.capitulo_id', 'capitulo.id')
                                ->leftJoin('temporada', 'capitulo.temporada_id', 'temporada.id')
                                ->leftJoin('audiovisual as audiovisual_capitulo', 'temporada.audiovisual_id', 'audiovisual_capitulo.id')
                                ->leftJoin('temporada as temporada_actividad', 'actividad.temporada_id', 'temporada_actividad.id')
                                ->leftJoin('audiovisual as audiovisual_temporada', 'temporada_actividad.audiovisual_id', 'audiovisual_temporada.id')
                                ->where('persona_id', $usuario_id)
                                ->select('tipo', 'audiovisual.tipoAudiovisual_id', 'audiovisual.titulo as titulo_audiovisual', 
                                'capitulo.nombre','temporada.numero as numero_temporada', 'capitulo.numero as numero_capitulo', 
                                'audiovisual_capitulo.titulo as titulo_audiovisual_capitulo', 'temporada_actividad.numero as 
                                numero_temporada_actividad', 'audiovisual_temporada.titulo as titulo_audiovisual_temporada')
                                ->get();

        return response()->json($actividad);
    }
}
