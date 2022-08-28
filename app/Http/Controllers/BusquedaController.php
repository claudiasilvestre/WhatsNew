<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audiovisual;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

class BusquedaController extends Controller
{
    /**
     * Consulta y devuelve los usuarios, participantes o audiovisuales que contienen la búsqueda.
     * 
     * @param string $busqueda Búsqueda que realiza el usuario
     *
     * @return Response
     */
    public function busqueda($busqueda) {
        $usuarios = Persona::query()
            ->where('tipoPersona_id', 1)
            ->where(DB::raw('lower(nombre)'), "LIKE", "%".strtolower($busqueda)."%")
            ->orWhere(DB::raw('lower(usuario)'), "LIKE", "%".strtolower($busqueda)."%")
            ->get();
        
        $participantes = Persona::query()
            ->where('tipoPersona_id', 2)
            ->where(DB::raw('lower(nombre)'), "LIKE", "%".strtolower($busqueda)."%")
            ->get();

        $audiovisuales = Audiovisual::query()
            ->where(DB::raw('lower(titulo)'), "LIKE", "%".strtolower($busqueda)."%")
            ->get();

        return response()->json([
            'usuarios' => $usuarios,
            'participantes' => $participantes,
            'audiovisuales' => $audiovisuales,
        ]);
    }
}
