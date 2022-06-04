<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'nombre' => 'required',
            'usuario' => 'required|unique:persona',
            'email' => 'required|unique:persona',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        Persona::create([
            'nombre' => $request->nombre,
            'usuario' => $request->usuario,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['msg' => 'Registered Successfully']);
    }

    public function login(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24);

        return response([
            'message' => 'Success'
        ])->withCookie($cookie);
    }

    public function user() {
        return Auth::user();
    }

    public function logout(Request $request) {
        $cookie = Cookie::forget('jwt');
        $request->user()->tokens()->delete();

        return response([
            'message' => 'Success'
        ])->withCookie($cookie);
    }
}
