<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Audiovisual;
use Illuminate\Support\Facades\DB;
use App\Models\Seguimiento;

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

    public function saber_seguimiento(Request $request) {
        if (Seguimiento::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->exists()) {
            $seguimiento = Seguimiento::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->first();
            return $seguimiento->estado;
        }

        return 0;
    }

    public function seguimiento(Request $request) {
        if (Seguimiento::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->exists()) {
            $seguimiento = Seguimiento::where('persona_id', $request->usuario_id)->where('audiovisual_id', $request->audiovisual_id)->first();
            if ($seguimiento->estado === $request->tipo) {
                $seguimiento->delete();
                return false;
            }

            Seguimiento::where('persona_id', $request->usuario_id)
                ->where('audiovisual_id', $request->audiovisual_id)
                ->update(['estado' => $request->tipo]);
        } else {
            Seguimiento::create([
                'audiovisual_id' => $request->audiovisual_id,
                'persona_id' => $request->usuario_id,
                'estado' => $request->tipo,
            ]);
        }

        return true;
    }
}
