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
use App\Models\Persona;
use App\Models\TipoParticipante;
use App\Models\TipoPersona;
use App\Models\Participacion;
use App\Models\Proveedor;
use App\Models\ProveedorAudiovisual;
use App\Models\Idioma;

class AudiovisualesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $APIKey = '38430b01858c3e78910493ba6a38a8b3';

        // Géneros películas
        $generosPeliculas = Http::get('https://api.themoviedb.org/3/genre/movie/list?api_key='.$APIKey.'&language=es-ES')['genres'];

        foreach ($generosPeliculas as $generoPelicula) {
            if (!Genero::where('id', '=', $generoPelicula['id'])->exists()) {
                $genero = new Genero;

                $genero->id = $generoPelicula['id'];
                $genero->nombre = $generoPelicula['name'];
    
                $genero->save();
            }
        }

        // Géneros series
        $generosSeries = Http::get('https://api.themoviedb.org/3/genre/tv/list?api_key='.$APIKey.'&language=es-ES')['genres'];

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

        // Idioma
        if (!Idioma::where('id', '=', 1)->exists() && !Idioma::where('id', '=', 2)->exists()) {
            $en = new Idioma;
            $es = new Idioma;

            $en->nombre = "Inglés";
            $es->nombre = "Español";

            $en->save();
            $es->save();
        }

        // TipoPersona
        if (!TipoPersona::where('id', '=', 1)->exists() && !TipoPersona::where('id', '=', 2)->exists()) {
            $usuario = new TipoPersona;
            $participante = new TipoPersona;

            $usuario->nombre = "Usuario";
            $participante->nombre = "Participante";

            $usuario->save();
            $participante->save();
        }

        // TipoParticipante
        if (!TipoParticipante::where('nombre', '=', "Actor")->exists() 
            && !TipoParticipante::where('nombre', '=', "Director")->exists()
            && !TipoParticipante::where('nombre', '=', "Guionista")->exists()) {
            $tipoParticipante = new TipoParticipante;
            $tipoParticipante->nombre = "Actor";
            $tipoParticipante->save();

            $tipoParticipante = new TipoParticipante;
            $tipoParticipante->nombre = "Director";
            $tipoParticipante->save();

            $tipoParticipante = new TipoParticipante;
            $tipoParticipante->nombre = "Guionista";
            $tipoParticipante->save();
        }

        // Proveedores
        $proveedores = Http::get('https://api.themoviedb.org/3/watch/providers/movie?api_key='.$APIKey.'&language=es-ES')['results'];
        foreach ($proveedores as $proveedor) {
            if (!Proveedor::where('id', $proveedor['provider_id'])->exists()) {
                $pv = new Proveedor;
                $pv->id = $proveedor['provider_id'];
                $pv->nombre = $proveedor['provider_name'];
                $pv->logo = "https://image.tmdb.org/t/p/w500".$proveedor['logo_path'];
                $pv->save();
            }
        }

        $proveedores = Http::get('https://api.themoviedb.org/3/watch/providers/tv?api_key='.$APIKey.'&language=es-ES')['results'];
        foreach ($proveedores as $proveedor) {
            if (!Proveedor::where('id', $proveedor['provider_id'])->exists()) {
                $pv = new Proveedor;
                $pv->id = $proveedor['provider_id'];
                $pv->nombre = $proveedor['provider_name'];
                $pv->logo = "https://image.tmdb.org/t/p/w500".$proveedor['logo_path'];
                $pv->save();
            }
        }

        // Películas
        for ($i = 1; $i <= 15; $i++) {
            $peliculas = Http::get('https://api.themoviedb.org/3/movie/popular?api_key='.$APIKey.'&language=es-ES&page='.$i)['results'];

            foreach ($peliculas as $p) {
                if (!Audiovisual::where('id', '=', $p['id'])->exists() &&
                    ($p['original_language'] == "en" || $p['original_language'] == "es")) {
                    $pelicula = new Audiovisual;
    
                    $pelicula->id = $p['id'];
                    $pelicula->tipoAudiovisual_id = 1;
                    if ($p['genre_ids'])
                        $pelicula->genero_id = $p['genre_ids'][0];
                    $pelicula->titulo = $p['title'];
                    $pelicula->tituloOriginal = $p['original_title'];
                    $pelicula->sinopsis = $p['overview'];
                    $pelicula->cartel = "https://image.tmdb.org/t/p/w500".$p['poster_path'];
                    if ($p['release_date']) {
                        $pelicula->fechaLanzamiento = $p['release_date'];
                        $pelicula->anno = date('Y', strtotime($p['release_date']));
                    }
                    if ($p['original_language'] == "en")
                        $pelicula->idioma_id = 1;
                    else
                        $pelicula->idioma_id = 2;
    
                    $pelicula->save();

                    // Cast y equipo
                    $cast_equipo = Http::get('https://api.themoviedb.org/3/movie/'.$pelicula->id.'/credits?api_key='.$APIKey.'&language=es-ES')->json();

                    for ($i = 0; $i < sizeof($cast_equipo['cast']); $i++) {
                        if (!Persona::where('nombre', '=', $cast_equipo['cast'][$i]['name'])->exists()) {
                            $participante = new Persona;
        
                            $participante->tipoPersona_id = 2;
                            $participante->tipoParticipante_id = 1;
            
                            $participante->nombre = $cast_equipo['cast'][$i]['name'];
                            if ($cast_equipo['cast'][$i]['profile_path']) $participante->foto = "https://image.tmdb.org/t/p/w500".$cast_equipo['cast'][$i]['profile_path'];
            
                            $participante->save();

                            $participacion = new Participacion;
                            $participacion->audiovisual_id = $pelicula->id;
                            $participacion->persona_id = $participante->id;
                            $participacion->personaje = $cast_equipo['cast'][$i]['character'];
                            $participacion->save();
                        } else {
                            $participante = Persona::where('nombre', '=', $cast_equipo['cast'][$i]['name'])->first();

                            if (!Participacion::where('audiovisual_id', $pelicula->id)
                                              ->where('persona_id', $participante->id)
                                              ->where('personaje', $cast_equipo['cast'][$i]['character'])
                                              ->exists()) {
                                $participacion = new Participacion;
                                $participacion->audiovisual_id = $pelicula->id;
                                $participacion->persona_id = $participante->id;
                                $participacion->personaje = $cast_equipo['cast'][$i]['character'];
                                $participacion->save();
                            }
                        }
                    }

                    for ($i = 0; $i < sizeof($cast_equipo['crew']); $i++) {
                        if ($cast_equipo['crew'][$i]['job'] === "Director" || $cast_equipo['crew'][$i]['job'] === "Screenplay") {
                            if (!Persona::where('nombre', '=', $cast_equipo['crew'][$i]['name'])->exists()) {
                                $participante = new Persona;
                
                                $participante->tipoPersona_id = 2;

                                if ($cast_equipo['crew'][$i]['job'] === "Director") $participante->tipoParticipante_id = 2;
                                else if ($cast_equipo['crew'][$i]['job'] === "Screenplay") $participante->tipoParticipante_id = 3;
                
                                $participante->nombre = $cast_equipo['crew'][$i]['name'];
                                if ($cast_equipo['crew'][$i]['profile_path']) $participante->foto = "https://image.tmdb.org/t/p/w500".$cast_equipo['crew'][$i]['profile_path'];
                
                                $participante->save();

                                $participacion = new Participacion;
                                $participacion->audiovisual_id = $pelicula->id;
                                $participacion->persona_id = $participante->id;
                                $participacion->save();
                            } else {
                                $participante = Persona::where('nombre', '=', $cast_equipo['crew'][$i]['name'])->first();
    
                                if (!Participacion::where('audiovisual_id', $pelicula->id)
                                                  ->where('persona_id', $participante->id)
                                                  ->exists()) {
                                    $participacion = new Participacion;
                                    $participacion->audiovisual_id = $pelicula->id;
                                    $participacion->persona_id = $participante->id;
                                    $participacion->save();
                                }
                            }
                        }
                    }

                    // Proveedores
                    $proveedores = Http::get('https://api.themoviedb.org/3/movie/'.$pelicula->id.'/watch/providers?api_key='.$APIKey)['results'];
                    if (array_key_exists("ES", $proveedores)) {
                        if (array_key_exists("flatrate", $proveedores['ES'])) {
                            foreach ($proveedores['ES']['flatrate'] as $proveedor) {
                                if (!ProveedorAudiovisual::where('proveedor_id', $proveedor['provider_id'])
                                                            ->where('audiovisual_id', $pelicula->id)
                                                            ->where('disponibilidad', 1)
                                                            ->exists() && Proveedor::where('id', $proveedor['provider_id'])->exists()) {
                                    $p_a = new ProveedorAudiovisual;
                                    $p_a->proveedor_id = $proveedor['provider_id'];
                                    $p_a->audiovisual_id = $pelicula->id;
                                    $p_a->disponibilidad = 1;
                                    $p_a->save();
                                }
                            }
                        }
                        if (array_key_exists("rent", $proveedores['ES'])) {
                            foreach ($proveedores['ES']['rent'] as $proveedor) {
                                if (!ProveedorAudiovisual::where('proveedor_id', $proveedor['provider_id'])
                                                            ->where('audiovisual_id', $pelicula->id)
                                                            ->where('disponibilidad', 2)
                                                            ->exists() && Proveedor::where('id', $proveedor['provider_id'])->exists()) {
                                    $p_a = new ProveedorAudiovisual;
                                    $p_a->proveedor_id = $proveedor['provider_id'];
                                    $p_a->audiovisual_id = $pelicula->id;
                                    $p_a->disponibilidad = 2;
                                    $p_a->save();
                                }
                            }
                        }
                        if (array_key_exists("buy", $proveedores['ES'])) {
                            foreach ($proveedores['ES']['buy'] as $proveedor) {
                                if (!ProveedorAudiovisual::where('proveedor_id', $proveedor['provider_id'])
                                                            ->where('audiovisual_id', $pelicula->id)
                                                            ->where('disponibilidad', 3)
                                                            ->exists() && Proveedor::where('id', $proveedor['provider_id'])->exists()) {
                                    $p_a = new ProveedorAudiovisual;
                                    $p_a->proveedor_id = $proveedor['provider_id'];
                                    $p_a->audiovisual_id = $pelicula->id;
                                    $p_a->disponibilidad = 3;
                                    $p_a->save();
                                }
                            }
                        }
                    }
                }
            }
        }

        // Series
        for ($i = 1; $i <= 5; $i++) {
            $series = Http::get('https://api.themoviedb.org/3/tv/popular?api_key='.$APIKey.'&language=es-ES&page='.$i)['results'];

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
                    if ($s['original_language'] == "en")
                        $serie->idioma_id = 1;
                    else
                        $serie->idioma_id = 2;
    
                    $serie->save();

                    // Proveedores
                    $proveedores = Http::get('https://api.themoviedb.org/3/tv/'.$serie->id.'/watch/providers?api_key='.$APIKey)['results'];
                    if (array_key_exists("ES", $proveedores)) {
                        if (array_key_exists("flatrate", $proveedores['ES'])) {
                            foreach ($proveedores['ES']['flatrate'] as $proveedor) {
                                if (!ProveedorAudiovisual::where('proveedor_id', $proveedor['provider_id'])
                                                            ->where('audiovisual_id', $serie->id)
                                                            ->where('disponibilidad', 1)
                                                            ->exists() && Proveedor::where('id', $proveedor['provider_id'])->exists()) {
                                    $p_a = new ProveedorAudiovisual;
                                    $p_a->proveedor_id = $proveedor['provider_id'];
                                    $p_a->audiovisual_id = $serie->id;
                                    $p_a->disponibilidad = 1;
                                    $p_a->save();
                                }
                            }
                        }
                    }
                }
            }
        }

        $series = Audiovisual::where('tipoAudiovisual_id', '=', 2)->get();

        foreach ($series as $serie) {
            // Número de temporadas y capítulos
            $detalles = Http::get('https://api.themoviedb.org/3/tv/'.$serie->id.'?api_key='.$APIKey.'&language=es-ES');
            
            if ($detalles) {
                $serie->numeroTemporadas = $detalles['number_of_seasons'];
                $serie->numeroCapitulos = $detalles['number_of_episodes'];

                $serie->save();

                // Temporadas
                for ($i = 0; $i < sizeof($detalles['seasons']); $i++) {
                    if ($detalles['seasons'][$i]['season_number'] != 0) {
                        $temporada = new Temporada;

                        $temporada->audiovisual_id = $serie->id;
                        $temporada->numero = $detalles['seasons'][$i]['season_number'];
                        $temporada->nombre = $detalles['seasons'][$i]['name'];
                        $temporada->numeroCapitulos = $detalles['seasons'][$i]['episode_count'];

                        $temporada->save();
                    }
                }

                // Estado de la serie
                if ($detalles['in_production'])
                    $serie->estado = 1;
                else
                    $serie->estado = 2;
                $serie->save();

                // Equipo y capítulos
                $temporadas = Temporada::where('audiovisual_id', '=', $serie->id)->get();
                foreach ($temporadas as $temporada) {
                    $temporadaApi = Http::get('https://api.themoviedb.org/3/tv/'.$serie->id.'/season/'.$temporada->numero.'?api_key='.$APIKey.'&language=es-ES')->json();

                    if ($temporadaApi != null && array_key_exists("episodes", $temporadaApi)) {
                        for ($i = 0; $i < sizeof($temporadaApi['episodes']); $i++) {

                            // Equipo
                            if ($temporada->numero === 1 && $i === 0) {
                                for ($j = 0; $j < sizeof($temporadaApi['episodes'][$i]['crew']); $j++) {
                                    if ($temporadaApi['episodes'][$i]['crew'][$j]['job'] === "Director" || $temporadaApi['episodes'][$i]['crew'][$j]['job'] === "Writer") {
                                        if (!Persona::where('nombre', '=', $temporadaApi['episodes'][$i]['crew'][$j]['name'])->exists()) {
                                            $participante = new Persona;

                                            $participante->tipoPersona_id = 2;
                                            if ($temporadaApi['episodes'][$i]['crew'][$j]['job'] === "Director") $participante->tipoParticipante_id = 2;
                                            else if ($temporadaApi['episodes'][$i]['crew'][$j]['job'] === "Writer") $participante->tipoParticipante_id = 3;
                                            $participante->nombre = $temporadaApi['episodes'][$i]['crew'][$j]['name'];
                                            if ($temporadaApi['episodes'][$i]['crew'][$j]['profile_path']) $participante->foto = "https://image.tmdb.org/t/p/w500".$temporadaApi['episodes'][$i]['crew'][$j]['profile_path'];

                                            $participante->save();

                                            $participacion = new Participacion;
                                            $participacion->audiovisual_id = $serie->id;
                                            $participacion->persona_id = $participante->id;
                                            $participacion->save();
                                        } else {
                                            $participante = Persona::where('nombre', '=', $temporadaApi['episodes'][$i]['crew'][$j]['name'])->first();
                                            if (!Participacion::where('audiovisual_id', $serie->id)->where('persona_id', $participante->id)->exists()) {
                                                $participacion = new Participacion;
                                                $participacion->audiovisual_id = $serie->id;
                                                $participacion->persona_id = $participante->id;
                                                $participacion->save();
                                            }
                                        }
                                    }
                                }
                            }

                            // Capítulos
                            $capitulo = new Capitulo;
        
                            $capitulo->temporada_id = $temporada->id;
                            $capitulo->numero = $temporadaApi['episodes'][$i]['episode_number'];
                            $capitulo->nombre = $temporadaApi['episodes'][$i]['name'];
                            $capitulo->sinopsis = $temporadaApi['episodes'][$i]['overview'];
                            $capitulo->cartel = "https://image.tmdb.org/t/p/w500".$temporadaApi['episodes'][$i]['still_path'];
                            if ($temporadaApi['episodes'][$i]['air_date']) {
                                $capitulo->fechaLanzamiento = $temporadaApi['episodes'][$i]['air_date'];
                            }
        
                            $capitulo->save();
                        }
                    }
                }

                // Cast
                $cast = Http::get('https://api.themoviedb.org/3/tv/'.$serie->id.'/credits?api_key='.$APIKey.'&language=es-ES')->json();

                for ($i = 0; $i < sizeof($cast['cast']); $i++) {
                    if (!Persona::where('nombre', '=', $cast['cast'][$i]['name'])->exists()) {
                        $participante = new Persona;

                        $participante->tipoPersona_id = 2;
                        $participante->tipoParticipante_id = 1;

                        $participante->nombre = $cast['cast'][$i]['name'];
                        if ($cast['cast'][$i]['profile_path']) $participante->foto = "https://image.tmdb.org/t/p/w500".$cast['cast'][$i]['profile_path'];

                        $participante->save();

                        $participacion = new Participacion;
                        $participacion->audiovisual_id = $serie->id;
                        $participacion->persona_id = $participante->id;
                        $participacion->personaje = $cast['cast'][$i]['character'];
                        $participacion->save();
                    } else {
                        $participante = Persona::where('nombre', '=', $cast['cast'][$i]['name'])->first();

                        $participacion = new Participacion;
                        $participacion->audiovisual_id = $serie->id;
                        $participacion->persona_id = $participante->id;
                        $participacion->personaje = $cast['cast'][$i]['character'];
                        $participacion->save();
                    }
                }
            }
        }
    }
}
