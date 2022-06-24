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
}
