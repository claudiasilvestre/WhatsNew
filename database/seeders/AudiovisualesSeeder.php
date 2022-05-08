<?php

namespace Database\Seeders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genero;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;

class AudiovisualesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
        for ($i = 1; $i <= 10; $i++) {
            $peliculas = Http::get('https://api.themoviedb.org/3/movie/popular?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES&page='.$i)['results'];

            foreach ($peliculas as $p) {
                if (!Audiovisual::where('id', '=', $p['id'])->exists() &&
                    ($p['original_language'] == "en" || $p['original_language'] == "es")) {
                    $pelicula = new Audiovisual;
    
                    $pelicula->id = $p['id'];
                    $pelicula->tipoAudiovisual_id = 1;
                    $pelicula->genero_id = $p['genre_ids'][0];
                    $pelicula->titulo = $p['title'];
                    $pelicula->tituloOriginal = $p['original_title'];
                    $pelicula->sinopsis = $p['overview'];
                    $pelicula->cartel = "https://image.tmdb.org/t/p/w500".$p['poster_path'];
                    if ($p['release_date']) {
                        $pelicula->fechaLanzamiento = $p['release_date'];
                        $pelicula->anno = date('Y', strtotime($p['release_date']));
                    }
                    $pelicula->puntuacion = $p['vote_average'];
    
                    $pelicula->save();
                }
            }
        }

        // Series
        for ($i = 1; $i <= 8; $i++) {
            $series = Http::get('https://api.themoviedb.org/3/tv/popular?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES&page='.$i)['results'];

            foreach ($series as $s) {
                if (!Audiovisual::where('id', '=', $s['id'])->exists() && 
                        ($s['original_language'] == "en" || $s['original_language'] == "es") &&
                        $s['poster_path']) {
                    $serie = new Audiovisual;
    
                    $serie->id = $s['id'];
                    $serie->tipoAudiovisual_id = 2;
                    if ($s['genre_ids']) $serie->genero_id = $s['genre_ids'][0];
                    $serie->titulo = $s['name'];
                    $serie->tituloOriginal = $s['original_name'];
                    $serie->sinopsis = $s['overview'];
                    $serie->cartel = "https://image.tmdb.org/t/p/w500".$s['poster_path'];
                    if ($s['first_air_date']) {
                        $serie->fechaLanzamiento = $s['first_air_date'];
                        $serie->anno = date('Y', strtotime($s['first_air_date']));
                    }
                    $serie->puntuacion = $s['vote_average'];
    
                    $serie->save();
                }
            }
        }

        $series = Audiovisual::where('tipoAudiovisual_id', '=', 2)->get();

        foreach ($series as $serie) {
            // Número de temporadas y capítulos
            $detalles = Http::get('https://api.themoviedb.org/3/tv/'.$serie->id.'?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES');
            $serie->numeroTemporadas = $detalles['number_of_seasons'];
            $serie->numeroCapitulos = $detalles['number_of_episodes'];

            $serie->save();

            // Temporadas
            for ($i = 1; $i < sizeof($detalles['seasons']); $i++) {
                $temporada = new Temporada;

                $temporada->audiovisual_id = $serie->id;
                $temporada->numero = $i;
                $temporada->nombre = $detalles['seasons'][$i]['name'];
                $temporada->numeroCapitulos = $detalles['seasons'][$i]['episode_count'];

                $temporada->save();
            }

            // Capítulos
            $temporadas = Temporada::where('audiovisual_id', '=', $serie->id)->get();
            foreach ($temporadas as $temporada) {
                $temporadaApi = Http::get('https://api.themoviedb.org/3/tv/'.$serie->id.'/season/'.$temporada->numero.'?api_key=38430b01858c3e78910493ba6a38a8b3&language=es-ES')->json();

                if(array_key_exists("episodes", $temporadaApi)) {
                    for ($i = 0; $i < sizeof($temporadaApi['episodes']); $i++) {
                        $capitulo = new Capitulo;
    
                        $capitulo->temporada_id = $temporada->id;
                        $capitulo->numero = $temporadaApi['episodes'][$i]['episode_number'];
                        $capitulo->nombre = $temporadaApi['episodes'][$i]['name'];
                        $capitulo->sinopsis = $temporadaApi['episodes'][$i]['overview'];
    
                        $capitulo->save();
                    }
                }
            }
        }
    }
}
