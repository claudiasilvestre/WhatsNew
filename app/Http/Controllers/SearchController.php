<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiovisual;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search($busqueda) {
        $user = Auth::user();

        $usuarios = Persona::query()
            ->where(DB::raw('lower(nombre)'), "LIKE", "%".strtolower($busqueda)."%")
            ->where('tipoPersona_id', 1)
            ->where('id', '!=', $user->id)
            ->get();
        
        $participantes = Persona::query()
            ->where(DB::raw('lower(nombre)'), "LIKE", "%".strtolower($busqueda)."%")
            ->where('tipoPersona_id', 2)
            ->get();

        $audiovisuales = Audiovisual::query()
            ->where(DB::raw('lower(titulo)'), "LIKE", "%".strtolower($busqueda)."%")
            ->get();

        return response()->json([
            'usuarios' => $usuarios,
            'audiovisuales' => $audiovisuales,
            'participantes' => $participantes,
        ]);
    }
}
