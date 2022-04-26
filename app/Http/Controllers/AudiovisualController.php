<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Genero;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;

class AudiovisualController extends Controller
{
    public function index() {
        $peliculas = Audiovisual::where('tipoAudiovisual_id', '=', 1)->orderBy('fechaLanzamiento', 'DESC')->get();
        $series = Audiovisual::where('tipoAudiovisual_id', '=', 2)->orderBy('fechaLanzamiento', 'DESC')->get();

        return response()->json([
            'peliculas' => $peliculas,
            'series' => $series,
        ]);
    }

    public function api() {

        // Géneros películas
        $generosPeliculas = Http::get('https://api.themoviedb.org/3/genre/movie/list?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES')['genres'];

        foreach ($generosPeliculas as $generoPelicula) {
            if (!Genero::where('id', '=', $generoPelicula['id'])->exists()) {
                $genero = new Genero;

                $genero->id = $generoPelicula['id'];
                $genero->nombre = $generoPelicula['name'];
    
                $genero->save();
            }
        }

        // Géneros series
        $generosSeries = Http::get('https://api.themoviedb.org/3/genre/tv/list?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES')['genres'];

        foreach ($generosSeries as $generoSerie) {
            if (!Genero::where('id', '=', $generoSerie['id'])->exists()) {
                $genero = new Genero;

                $genero->id = $generoSerie['id'];
                $genero->nombre = $generoSerie['name'];

                $genero->save();
            }
        }

        // TipoAudiovisual
        if (!TipoAudiovisual::where('id', '=', 1)->exists() && !TipoAudiovisual::where('id', '=', 2)->exists()) {
            $pelicula = new TipoAudiovisual;
            $serie = new TipoAudiovisual;

            $pelicula->nombre = "Película";
            $serie->nombre = "Serie";

            $pelicula->save();
            $serie->save();
        }

        // Películas
        for ($i = 1; $i <= 5; $i++) {
            $peliculas = Http::get('https://api.themoviedb.org/3/movie/popular?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES&page='.$i)['results'];

            foreach ($peliculas as $p) {
                if (!Audiovisual::where('id', '=', $p['id'])->exists()) {
                    $pelicula = new Audiovisual;
    
                    $pelicula->id = $p['id'];
                    $pelicula->tipoAudiovisual_id = 1;
                    $pelicula->genero_id = $p['genre_ids'][0];
                    $pelicula->titulo = $p['title'];
                    $pelicula->tituloOriginal = $p['original_title'];
                    $pelicula->sinopsis = $p['overview'];
                    $pelicula->cartel = "https://image.tmdb.org/t/p/w500".$p['poster_path'];
                    if ($p['release_date']) $pelicula->fechaLanzamiento = $p['release_date'];
                    $pelicula->puntuacion = $p['vote_average'];
    
                    $pelicula->save();
                }
            }
        }

        // Series
        for ($i = 1; $i <= 5; $i++) {
            $series = Http::get('https://api.themoviedb.org/3/tv/popular?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES&page='.$i)['results'];

            foreach ($series as $s) {
                if (!Audiovisual::where('id', '=', $s['id'])->exists()) {
                    $serie = new Audiovisual;
    
                    $serie->id = $s['id'];
                    $serie->tipoAudiovisual_id = 2;
                    if ($s['genre_ids']) $serie->genero_id = $s['genre_ids'][0];
                    $serie->titulo = $s['name'];
                    $serie->tituloOriginal = $s['original_name'];
                    $serie->sinopsis = $s['overview'];
                    $serie->cartel = "https://image.tmdb.org/t/p/w500".$s['poster_path'];
                    $serie->fechaLanzamiento = $s['first_air_date'];
                    $serie->puntuacion = $s['vote_average'];
    
                    $serie->save();
                }
            }
        }
    }
}
