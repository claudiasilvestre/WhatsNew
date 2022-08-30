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
    public function inicioSesion(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'credenciales' => 'Las credenciales proporcionadas no son correctas.',
            ]);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24);

        return response([])->withCookie($cookie);
    }

    public function usuario() {
        return Auth::user();
    }

    public function cierreSesion(Request $request) {
        $cookie = Cookie::forget('jwt');
        $request->user()->tokens()->delete();

        return response([])->withCookie($cookie);
    }
}
