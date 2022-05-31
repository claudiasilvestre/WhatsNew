<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()->json(Auth::user(), 200);
        }
     
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
