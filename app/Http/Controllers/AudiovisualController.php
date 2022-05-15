<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Audiovisual;

class AudiovisualController extends Controller
{
    public function index() {
        $peliculas = Audiovisual::where('tipoAudiovisual_id', 1)->orderBy('fechaLanzamiento', 'DESC')->get();
        $series = Audiovisual::where('tipoAudiovisual_id', 2)->orderBy('fechaLanzamiento', 'DESC')->get();

        return response()->json([
            'peliculas' => $peliculas,
            'series' => $series,
        ]);
    }

    public function show($id) {
        $audiovisual = Audiovisual::where('id', $id)->get();

        return response()->json($audiovisual);
    }
}
