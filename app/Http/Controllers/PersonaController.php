<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\SeguimientoPersona;

class PersonaController extends Controller
{
    public function show($id) {
        $persona = Persona::where('id', $id)->get();

        return response()->json($persona);
    }

    public function participacion($audiovisual_id) {
        $personas_reparto = DB::table('persona')
                                ->join('participacion', 'persona.id', '=', 'participacion.persona_id')
                                ->where('participacion.audiovisual_id', '=', $audiovisual_id)
                                ->where('persona.tipoParticipante_id', '=', 1)
                                ->get();

        $personas_equipo = DB::table('persona')
                                ->join('participacion', 'persona.id', '=', 'participacion.persona_id')
                                ->where('participacion.audiovisual_id', '=', $audiovisual_id)
                                ->where('persona.tipoParticipante_id', '!=', 1)
                                ->get();

        return response()->json([
            'personas_reparto' => $personas_reparto,
            'personas_equipo' => $personas_equipo,
        ]);
    }

    public function info($id) {
        $persona = Persona::where('id', $id)
                            ->select('foto', 'nombre', 'usuario', 'email')
                            ->first();

        return response()->json($persona);
    }

    public function guardar_informacion(Request $request) {
        $user = Auth::user();

        $request->validate([
            'nombre' => 'required',
            'usuario' => [
                'required',
                Rule::unique('persona')->ignore($user),
            ],
            'email' => [
                'required',
                Rule::unique('persona')->ignore($user),
            ],
        ]);

        $images_path = public_path('images');
        $file_name = $request->file->getClientOriginalName();
        $generated_new_name = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move($images_path, $generated_new_name);

        Persona::where('id', $user->id)->update([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'foto' => $file_name,
        ]);

        return response()->json(['msg' => 'Data changed successfully']);
    }

    public function guardar_password(Request $request) {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña proporcionada no coincide con la actual.'],
            ]);
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [], [
            'password' => 'contraseña',
            'password_confirmation' => 'confirmar nueva contraseña'
        ]);

        Persona::where('id', $user->id)->update(['password' => Hash::make($request->password)]);
    }

    public function saber_seguimiento_usuario(Request $request) {
        if (SeguimientoPersona::where('personaActual_id', $request->usuarioActual_id)->where('persona_id', $request->usuario_id)->exists()) {
            return true;
        }

        return false;
    }

    public function seguimiento_usuario(Request $request) {
        if (SeguimientoPersona::where('personaActual_id', $request->usuarioActual_id)->where('persona_id', $request->usuario_id)->exists()) {
            $seguimiento = SeguimientoPersona::where('personaActual_id', $request->usuarioActual_id)->where('persona_id', $request->usuario_id)->first();
            $seguimiento->delete();

            Persona::where('id', $request->usuarioActual_id)
                ->decrement('seguidos');

            Persona::where('id', $request->usuario_id)
                ->decrement('seguidores');

            Persona::where('id', $request->usuario_id)->decrement('puntos', 5);

            return false;
        } else {
            SeguimientoPersona::create([
                'personaActual_id' => $request->usuarioActual_id,
                'persona_id' => $request->usuario_id,
            ]);

            Persona::where('id', $request->usuarioActual_id)
                ->increment('seguidos');

            Persona::where('id', $request->usuario_id)
                ->increment('seguidores');

            Persona::where('id', $request->usuario_id)->increment('puntos', 5);

            return true;
        }
    }

    public function siguiendo($usuario_id) {
        $usuarioActual_id = Auth::id();

        $siguiendo = DB::table('persona')
                                ->join('seguimiento_persona', 'persona.id', 'seguimiento_persona.persona_id')
                                ->where('seguimiento_persona.personaActual_id', $usuario_id)
                                ->select('persona.id', 'persona.foto', 'persona.nombre', 'persona_id')
                                ->get();

        $clicked = array_fill(0, sizeof($siguiendo), false);
        $btnSeguimiento = array_fill(0, sizeof($siguiendo), "Seguir");
        for ($i = 0; $i < sizeof($siguiendo); $i++) {
            if (SeguimientoPersona::where('personaActual_id', $usuarioActual_id)->where('persona_id', $siguiendo[$i]->persona_id)->exists()) {
                $clicked[$i] = true;
                $btnSeguimiento[$i] = "Siguiendo";
            }
        }

        return response()->json([
            'siguiendo' => $siguiendo,
            'clicked' => $clicked,
            'btnSeguimiento' => $btnSeguimiento,
        ]);
    }

    public function seguidores($usuario_id) {
        $usuarioActual_id = Auth::id();

        $seguidores = DB::table('persona')
                                ->join('seguimiento_persona', 'persona.id', 'seguimiento_persona.personaActual_id')
                                ->where('seguimiento_persona.persona_id', $usuario_id)
                                ->select('persona.id', 'persona.foto', 'persona.nombre', 'personaActual_id')
                                ->get();

        $clicked = array_fill(0, sizeof($seguidores), false);
        $btnSeguimiento = array_fill(0, sizeof($seguidores), "Seguir");
        for ($i = 0; $i < sizeof($seguidores); $i++) {
            if (SeguimientoPersona::where('personaActual_id', $usuarioActual_id)->where('persona_id', $seguidores[$i]->personaActual_id)->exists()) {
                $clicked[$i] = true;
                $btnSeguimiento[$i] = "Siguiendo";
            }
        }

        return response()->json([
            'seguidores' => $seguidores,
            'clicked' => $clicked,
            'btnSeguimiento' => $btnSeguimiento,
        ]);
    }
}
