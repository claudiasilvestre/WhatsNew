<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComentarioAudiovisual;
use App\Models\ComentarioCapitulo;
use Illuminate\Support\Facades\DB;

class ComentarioController extends Controller
{
    public function guardarAudiovisual(Request $request) {
        $request->validate([
            'texto' => 'required',
        ]);

        ComentarioAudiovisual::create([
            'audiovisual_id' => $request->tipo_id,
            'persona_id' => $request->usuario_id,
            'texto' => $request->texto,
        ]);

        return response()->json(['msg' => 'Comentario añadido']);
    }

    public function guardarCapitulo(Request $request) {
        $request->validate([
            'texto' => 'required',
        ]);

        ComentarioCapitulo::create([
            'capitulo_id' => $request->tipo_id,
            'persona_id' => $request->usuario_id,
            'texto' => $request->texto,
        ]);

        return response()->json(['msg' => 'Comentario añadido']);
    }

    public function audiovisual($audiovisual_id) {
        $comentarios = DB::table('persona')
                                ->join('comentario_audiovisual', 'persona.id', '=', 'comentario_audiovisual.persona_id')
                                ->where('audiovisual_id', $audiovisual_id)
                                ->orderBy('comentario_audiovisual.created_at', 'desc')
                                ->get();

        return response()->json($comentarios);
    }

    public function capitulo($capitulo_id) {
        $comentarios = DB::table('persona')
                                ->join('comentario_capitulo', 'persona.id', '=', 'comentario_capitulo.persona_id')
                                ->where('capitulo_id', $capitulo_id)
                                ->orderBy('comentario_capitulo.created_at', 'desc')
                                ->get();

        return response()->json($comentarios);
    }
}
