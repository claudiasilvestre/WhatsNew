<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\SeguimientoPersona;

class PersonaController extends Controller
{
    /**
     * Valida y registra a un nuevo usuario.
     * 
     * @param Request $request Contiene los datos de un nuevo usuario.
     *
     * @return void
     */
    public function registro(Request $request) {
        Validator::extend('without_spaces', function($attr, $value){
            return preg_match('/^\S*$/u', $value);
        });

        $request->validate([
            'nombre' => 'required',
            'usuario' => 'required|without_spaces|unique:persona',
            'email' => 'required|unique:persona',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'without_spaces' => 'El :attribute no puede contener espacios.'
        ], [
            'email' => 'correo',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmar contraseña'
        ]);

        Persona::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'foto' => '/img/blank-profile-picture2.jpg',
        ]);
    }

    /**
     * Consulta y devuelve a una persona por su ID.
     * 
     * @param integer $id ID de la persona que se quiere consultar.
     *
     * @return Response
     */
    public function show($id) {
        $persona = Persona::where('id', $id)->get();

        return response()->json($persona);
    }

    /**
     * Consulta y devuelve los participantes de un audiovisual.
     * 
     * @param integer $audiovisual_id ID del audiovisual al que pertenecen los participantes.
     *
     * @return Response
     */
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

    /**
     * Consulta y devuelve información sobre un usuario.
     * 
     * @param integer $id ID del usuario del que se quiere consultar información.
     *
     * @return Response
     */
    public function info($id) {
        $persona = Persona::where('id', $id)
                            ->select('foto', 'nombre', 'usuario', 'email')
                            ->first();

        return response()->json($persona);
    }

    /**
     * Actualiza la información del usuario actual.
     * 
     * @param Request $request Contiene la información a actualizar del usuario actual.
     *
     * @return void
     */
    public function guardarInformacion(Request $request) {
        $usuario = Auth::user();

        $request->validate([
            'nombre' => 'required',
            'usuario' => [
                'required',
                Rule::unique('persona')->ignore($usuario),
            ],
            'email' => [
                'required',
                Rule::unique('persona')->ignore($usuario),
            ],
        ]);

        if($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $file_name = time().$file->getClientOriginalName();
            $file->move(public_path('img'), $file_name);
            $ruta = '/img/'.$file_name;

            Persona::where('id', $usuario->id)->update([
                'nombre' => $request->nombre,
                'usuario' => $request->usuario,
                'email' => $request->email,
                'foto' => $ruta,
            ]);
            
        } else {
            Persona::where('id', $usuario->id)->update([
                'nombre' => $request->nombre,
                'usuario' => $request->usuario,
                'email' => $request->email,
            ]);
        }
    }

    /**
     * Actualiza la contraseña del usuario actual.
     * 
     * @param Request $request Contiene la contraseña actual y la contraseña nueva del usuario actual.
     *
     * @return void
     */
    public function guardarPassword(Request $request) {
        $usuario = Auth::user();

        if (!Hash::check($request->current_password, $usuario->password)) {
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

        Persona::where('id', $usuario->id)->update(['password' => Hash::make($request->password)]);
    }

    /**
     * Comprueba si el usuario actual sigue al usuario proporcionado o no.
     * 
     * @param Request $request Contiene el ID del usuario actual y el ID del usuario que se quiere saber si sigue.
     *
     * @return boolean
     */
    public function saberSeguimientoUsuario(Request $request) {
        if (SeguimientoPersona::where('personaActual_id', $request->usuarioActual_id)->where('persona_id', $request->usuario_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Crea o borra el seguimiento del usuario actual a otro usuario, modifica los seguidos o seguidores del usuario actual
     * y sus puntos.
     * 
     * @param Request $request Contiene el ID del usuario actual y el usuario que se quiere seguir o dejar de seguir.
     *
     * @return boolean
     */
    public function seguimientoUsuario(Request $request) {
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

    /**
     * Consulta y devuelve los usuarios que sigue un usuario, comprueba cuales de esos usuario sigue el usuario actual
     * y proporciona el estado de los botones Seguir/Siguiendo.
     * 
     * @param integer $usuario_id ID del usuario del que se quieren consultar los usuarios que sigue.
     *
     * @return Response
     */
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

    /**
     * Consulta y devuelve los seguidores de un usuario, comprueba cuales de esos usuario sigue el usuario actual
     * y proporciona el estado de los botones Seguir/Siguiendo.
     * 
     * @param integer $usuario_id ID del usuario del que se quieren consultar los seguidores.
     *
     * @return Response
     */
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
