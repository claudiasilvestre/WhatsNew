<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiovisual;
use App\Models\Persona;

class SearchController extends Controller
{
    public function search($busqueda) {
        $participantes = Persona::query()
            ->where('nombre', 'LIKE', "%{$busqueda}%")
            ->get();

        $audiovisuales = Audiovisual::query()
            ->where('titulo', 'LIKE', "%{$busqueda}%")
            ->get();

        return response()->json([
            'audiovisuales' => $audiovisuales,
            'participantes' => $participantes,
        ]);
    }
}
