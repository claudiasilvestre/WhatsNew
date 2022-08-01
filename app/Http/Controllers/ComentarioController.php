<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComentarioAudiovisual;
use App\Models\ComentarioCapitulo;
use App\Models\OpinionComentarioAudiovisual;
use App\Models\OpinionComentarioCapitulo;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function guardarAudiovisual(Request $request) {
        ComentarioAudiovisual::create([
            'audiovisual_id' => $request->tipo_id,
            'persona_id' => $request->usuario_id,
            'texto' => $request->texto,
        ]);

        $usuario_id = Auth::id();

        Persona::where('id', $usuario_id)->increment('puntos', 5);
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

        $usuario_id = Auth::id();

        Persona::where('id', $usuario_id)->increment('puntos', 5);
    }

    public function audiovisual(Request $request) {
        if ($request->tipo == 1)
            $comentarios = DB::table('persona')
                                    ->join('comentario_audiovisual', 'persona.id', '=', 'comentario_audiovisual.persona_id')
                                    ->where('audiovisual_id', $request->audiovisual_id)
                                    ->orderBy('comentario_audiovisual.created_at', 'desc')
                                    ->get();
            
        else
            $comentarios = DB::table('persona')
                                    ->join('comentario_audiovisual', 'persona.id', '=', 'comentario_audiovisual.persona_id')
                                    ->where('audiovisual_id', $request->audiovisual_id)
                                    ->orderBy('comentario_audiovisual.votosPositivos', 'desc')
                                    ->orderBy('comentario_audiovisual.created_at', 'desc')
                                    ->get();

        $clickedLike = [];
        $clickedDislike = [];

        foreach ($comentarios as $comentario) {
            if (OpinionComentarioAudiovisual::where('comentarioAudiovisual_id', $comentario->id)->where('opinion', true)->exists())
                array_push($clickedLike, true);
            else
                array_push($clickedLike, false);

            if (OpinionComentarioAudiovisual::where('comentarioAudiovisual_id', $comentario->id)->where('opinion', false)->exists())
                array_push($clickedDislike, true);
            else
                array_push($clickedDislike, false);
        }

        return response()->json([
            'comentarios' => $comentarios,
            'clickedLike' => $clickedLike,
            'clickedDislike' => $clickedDislike,
        ]);
    }

    public function capitulo(Request $request) {
        if ($request->tipo == 1)
            $comentarios = DB::table('persona')
                                    ->join('comentario_capitulo', 'persona.id', '=', 'comentario_capitulo.persona_id')
                                    ->where('capitulo_id', $request->capitulo_id)
                                    ->orderBy('comentario_capitulo.created_at', 'desc')
                                    ->get();
        else
            $comentarios = DB::table('persona')
                                    ->join('comentario_capitulo', 'persona.id', '=', 'comentario_capitulo.persona_id')
                                    ->where('capitulo_id', $request->capitulo_id)
                                    ->orderBy('comentario_capitulo.votosPositivos', 'desc')
                                    ->orderBy('comentario_capitulo.created_at', 'desc')
                                    ->get();

        $clickedLike = [];
        $clickedDislike = [];
        
        foreach ($comentarios as $comentario) {
            if (OpinionComentarioCapitulo::where('comentarioCapitulo_id', $comentario->id)->where('opinion', true)->exists())
                array_push($clickedLike, true);
            else
                array_push($clickedLike, false);

            if (OpinionComentarioCapitulo::where('comentarioCapitulo_id', $comentario->id)->where('opinion', false)->exists())
                array_push($clickedDislike, true);
            else
                array_push($clickedDislike, false);
        }

        return response()->json([
            'comentarios' => $comentarios,
            'clickedLike' => $clickedLike,
            'clickedDislike' => $clickedDislike,
        ]);
    }

    public function borrarAudiovisual($comentario_id) {
        ComentarioAudiovisual::where('id', $comentario_id)
                                ->delete();

        $usuario_id = Auth::id();

        Persona::where('id', $usuario_id)->decrement('puntos', 5);
    }

    public function borrarCapitulo($comentario_id) {
        ComentarioCapitulo::where('id', $comentario_id)
                            ->delete();

        $usuario_id = Auth::id();

        Persona::where('id', $usuario_id)->decrement('puntos', 5);
    }

    public function opinionPositivaAudiovisual(Request $request) {
        $usuario_id = Auth::id();

        if (!OpinionComentarioAudiovisual::where('persona_id', $request->usuario_id)->where('comentarioAudiovisual_id', $request->comentario_id)->exists()) {
            OpinionComentarioAudiovisual::create([
                'persona_id' => $request->usuario_id,
                'comentarioAudiovisual_id' => $request->comentario_id,
                'opinion' => true,
            ]);

            ComentarioAudiovisual::where('id', $request->comentario_id)
                ->increment('votosPositivos');

            Persona::where('id', $usuario_id)->increment('puntos', 1);

        } else {
            $opinionComentario = OpinionComentarioAudiovisual::where('persona_id', $request->usuario_id)
                                                        ->where('comentarioAudiovisual_id', $request->comentario_id)
                                                        ->first();
            
            if (!$opinionComentario->opinion) {
                $opinionComentario->opinion = true;
                $opinionComentario->save();

                ComentarioAudiovisual::where('id', $request->comentario_id)
                    ->increment('votosPositivos');
                ComentarioAudiovisual::where('id', $request->comentario_id)
                    ->decrement('votosNegativos');
            } else {
                $opinionComentario->delete();

                ComentarioAudiovisual::where('id', $request->comentario_id)
                    ->decrement('votosPositivos');

                Persona::where('id', $usuario_id)->decrement('puntos', 1);
            }
        }
    }

    public function opinionNegativaAudiovisual(Request $request) {
        $usuario_id = Auth::id();

        if (!OpinionComentarioAudiovisual::where('persona_id', $request->usuario_id)->where('comentarioAudiovisual_id', $request->comentario_id)->exists()) {
            OpinionComentarioAudiovisual::create([
                'persona_id' => $request->usuario_id,
                'comentarioAudiovisual_id' => $request->comentario_id,
                'opinion' => false,
            ]);

            ComentarioAudiovisual::where('id', $request->comentario_id)
                ->increment('votosNegativos');

            Persona::where('id', $usuario_id)->increment('puntos', 1);

        } else {
            $opinionComentario = OpinionComentarioAudiovisual::where('persona_id', $request->usuario_id)
                                                        ->where('comentarioAudiovisual_id', $request->comentario_id)
                                                        ->first();
            
            if ($opinionComentario->opinion) {
                $opinionComentario->opinion = false;
                $opinionComentario->save();

                ComentarioAudiovisual::where('id', $request->comentario_id)
                    ->increment('votosNegativos');
                ComentarioAudiovisual::where('id', $request->comentario_id)
                    ->decrement('votosPositivos');
            } else {
                $opinionComentario->delete();

                ComentarioAudiovisual::where('id', $request->comentario_id)
                    ->decrement('votosNegativos');

                Persona::where('id', $usuario_id)->decrement('puntos', 1);
            }
        }
    }

    public function opinionPositivaCapitulo(Request $request) {
        $usuario_id = Auth::id();

        if (!OpinionComentarioCapitulo::where('persona_id', $request->usuario_id)->where('comentarioCapitulo_id', $request->comentario_id)->exists()) {
            OpinionComentarioCapitulo::create([
                'persona_id' => $request->usuario_id,
                'comentarioCapitulo_id' => $request->comentario_id,
                'opinion' => true,
            ]);

            ComentarioCapitulo::where('id', $request->comentario_id)
                ->increment('votosPositivos');

            Persona::where('id', $usuario_id)->increment('puntos', 1);

        } else {
            $opinionComentario = OpinionComentarioCapitulo::where('persona_id', $request->usuario_id)
                                                        ->where('comentarioCapitulo_id', $request->comentario_id)
                                                        ->first();
            
            if (!$opinionComentario->opinion) {
                $opinionComentario->opinion = true;
                $opinionComentario->save();

                ComentarioCapitulo::where('id', $request->comentario_id)
                    ->increment('votosPositivos');
                ComentarioCapitulo::where('id', $request->comentario_id)
                    ->decrement('votosNegativos');
            } else {
                $opinionComentario->delete();

                ComentarioCapitulo::where('id', $request->comentario_id)
                    ->decrement('votosPositivos');

                Persona::where('id', $usuario_id)->decrement('puntos', 1);
            }
        }
    }

    public function opinionNegativaCapitulo(Request $request) {
        $usuario_id = Auth::id();

        if (!OpinionComentarioCapitulo::where('persona_id', $request->usuario_id)->where('comentarioCapitulo_id', $request->comentario_id)->exists()) {
            OpinionComentarioCapitulo::create([
                'persona_id' => $request->usuario_id,
                'comentarioCapitulo_id' => $request->comentario_id,
                'opinion' => false,
            ]);

            ComentarioCapitulo::where('id', $request->comentario_id)
                ->increment('votosNegativos');

            Persona::where('id', $usuario_id)->increment('puntos', 1);

        } else {
            $opinionComentario = OpinionComentarioCapitulo::where('persona_id', $request->usuario_id)
                                                        ->where('comentarioCapitulo_id', $request->comentario_id)
                                                        ->first();
            
            if ($opinionComentario->opinion) {
                $opinionComentario->opinion = false;
                $opinionComentario->save();

                ComentarioCapitulo::where('id', $request->comentario_id)
                    ->increment('votosNegativos');
                ComentarioCapitulo::where('id', $request->comentario_id)
                    ->decrement('votosPositivos');
            } else {
                $opinionComentario->delete();

                ComentarioCapitulo::where('id', $request->comentario_id)
                    ->decrement('votosNegativos');

                Persona::where('id', $usuario_id)->decrement('puntos', 1);
            }
        }
    }
}
