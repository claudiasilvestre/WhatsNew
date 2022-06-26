<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PersonaController extends Controller
{
    public function show($id) {
        $persona = Persona::where('id', $id)->get();

        return response()->json($persona);
    }

    public function participacion($audiovisual_id) {
        $personas = DB::table('persona')
                                ->join('participacion', 'persona.id', '=', 'participacion.persona_id')
                                ->where('participacion.audiovisual_id', '=', $audiovisual_id)
                                ->get();

        return response()->json($personas);
    }

    public function info($id) {
        $persona = Persona::where('id', $id)
                            ->select('nombre', 'usuario', 'email')
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

        Persona::where('id', $user->id)->update([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'email' => $request->email,
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
        ]);

        Persona::where('id', $user->id)->update(['password' => Hash::make($request->password)]);

        return response()->json(['msg' => 'Password changed successfully']);
    }
}
