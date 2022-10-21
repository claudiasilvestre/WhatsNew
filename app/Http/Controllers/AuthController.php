<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Comprueba que las credenciales de inicio de sesión son correctas e inicia la sesión del usuario.
     * 
     * @param Request $request Contiene el email y la contraseña del usuario que quiere iniciar sesión.
     *
     * @throws ValidationException Si las credenciales de inicio de sesión no coinciden con las almacenadas.
     * @return Response
     */
    public function inicioSesion(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'credenciales' => 'Correo o contraseña incorrectos. Vuelve a intentarlo.',
            ]);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24);

        return response([])->withCookie($cookie);
    }

    /**
     * Devuelve el usuario actual.
     * 
     * @return Persona
     */
    public function usuario() {
        return Auth::user();
    }

    /**
     * Cierra la sesión del usuario actual.
     * 
     * @param Request $request Contiene el token del usuario actual.
     *
     * @return Response
     */
    public function cierreSesion(Request $request) {
        $cookie = Cookie::forget('jwt');
        $request->user()->tokens()->delete();

        return response([])->withCookie($cookie);
    }
}
