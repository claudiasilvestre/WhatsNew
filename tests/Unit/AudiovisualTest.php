<?php

namespace Tests\Unit;

use App\Models\TipoPersona;
use App\Models\TipoParticipante;
use App\Models\Persona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Participacion;
use App\Models\SeguimientoAudiovisual;
use App\Models\Proveedor;
use App\Models\ProveedorAudiovisual;
use App\Models\Valoracion;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionCapitulo;
use App\Models\VisualizacionTemporada;
use App\Models\Genero;
use App\Models\Idioma;
use App\Models\Actividad;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AudiovisualTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario, $tipoAudiovisual, $pelicula;
    
    /**
     * Inicializa el test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->usuario = Persona::factory()->create();

        $this->tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Pelicula',
        ]);

        $this->pelicula = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Kill Bill',
        ]);
    }

    /**
     * Obtiene películas y series.
     *
     * @return void
     */
    public function test_obtener_peliculas_series()
    {
        $this->actingAs($this->usuario);

        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Breaking Bad',
        ]);

        $response = $this->getJson('/api/audiovisuales');

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('peliculas', 1, fn ($json) =>
                        $json->where('id', strval($this->pelicula->id))
                             ->etc()
                        )->has('series', 1, fn ($json) =>
                            $json->where('id', strval($serie->id))
                                ->etc()
                        )
                 );
    }

    /**
     * Intenta obtener películas y series sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_peliculas_series_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/audiovisuales');

        $response->assertUnauthorized();
    }

    /**
     * Obtiene un audiovisual por su ID.
     *
     * @return void
     */
    public function test_obtener_audiovisual()
    {
        $this->actingAs($this->usuario);

        $response = $this->getJson('/api/audiovisuales/'.$this->pelicula->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                    ->first(fn ($json) =>
                        $json->where('id', strval($this->pelicula->id))
                            ->etc()
                    )
                 );
    }

    /**
     * Intenta obtener un audiovisual por su ID sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_audiovisual_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/audiovisuales/'.$this->pelicula->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene las participaciones en audiovisuales de una persona por su ID.
     *
     * @return void
     */
    public function test_obtener_participaciones()
    {
        $this->actingAs($this->usuario);

        $tipoPersona = TipoPersona::create([
            'nombre' => 'Participante'
        ]);

        $tipoActor = TipoParticipante::create([
            'nombre' => 'Actor'
        ]);

        $actor = Persona::create([
            'tipoPersona_id' => $tipoPersona->id,
            'tipoParticipante_id' => $tipoActor->id,
            'nombre' => 'Uma Thurman'
        ]);

        Participacion::create([
            'persona_id' => $actor->id,
            'audiovisual_id' => $this->pelicula->id,
        ]);

        $response = $this->getJson('/api/audiovisuales-participacion/'.$actor->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                    ->first(fn ($json) =>
                        $json->where('audiovisual_id', strval($this->pelicula->id))
                             ->etc()
                    )
                 );
    }

    /**
     * Intenta obtener las participaciones en audiovisuales de una persona por su ID sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_participaciones_usuario_sin_sesion_iniciada()
    {
        $tipoPersona = TipoPersona::create([
            'nombre' => 'Participante'
        ]);

        $tipoActor = TipoParticipante::create([
            'nombre' => 'Actor'
        ]);

        $actor = Persona::create([
            'tipoPersona_id' => $tipoPersona->id,
            'tipoParticipante_id' => $tipoActor->id,
            'nombre' => 'Uma Thurman'
        ]);

        $response = $this->getJson('/api/audiovisuales-participacion/'.$actor->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene el estado de un seguimiento de un audiovisual para un usuario.
     *
     * @return void
     */
    public function test_obtener_estado_seguimiento_audiovisual()
    {
        $this->actingAs($this->usuario);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'estado' => 1,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(1, $response->getContent());
    }

    /**
     * Obtiene 0 como estado de seguimiento de un audiovisual para un usuario debido a que no existe el seguimiento.
     *
     * @return void
     */
    public function test_obtener_estado_seguimiento_audiovisual_seguimiento_no_existe()
    {
        $this->actingAs($this->usuario);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(0, $response->getContent());
    }

    /**
     * Intenta obtener el estado de un seguimiento de un audiovisual para un usuario sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_estado_seguimiento_audiovisual_usuario_sin_sesion_iniciada()
    {
        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Borra un seguimiento de un audiovisual para un usuario al ser del mismo tipo.
     *
     * @return void
     */
    public function test_borrar_seguimiento_audiovisual()
    {
        $this->actingAs($this->usuario);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'estado' => 3,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
            'tipo' => 3,
        ];

        $response = $this->call('POST', '/api/seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertFalse(SeguimientoAudiovisual::where('audiovisual_id', $this->pelicula->id)
                                                 ->where('persona_id', $this->usuario->id)
                                                 ->where('estado', 3)->exists());
    }

    /**
     * Crea un seguimiento de un audiovisual para un usuario y la actividad correspondiente.
     *
     * @return void
     */
    public function test_crear_seguimiento_audiovisual()
    {
        $this->actingAs($this->usuario);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
            'tipo' => 3,
        ];

        $response = $this->call('POST', '/api/seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(SeguimientoAudiovisual::where('audiovisual_id', $this->pelicula->id)
                                                ->where('persona_id', $this->usuario->id)
                                                ->where('estado', 3)->exists());
        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                   ->where('tipo', 3)
                                   ->where('audiovisual_id', $this->pelicula->id)
                                   ->exists());
    }

    /**
     * Actualiza un seguimiento de un audiovisual para un usuario a un seguimiento de tipo 1,
     * borra todas las visualizaciones de ese audiovisual para el usuario y crea la actividad correspondiente.
     *
     * @return void
     */
    public function test_actualizar_seguimiento_audiovisual_a_tipo_1()
    {
        $this->actingAs($this->usuario);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $serie->id,
            'persona_id' => $this->usuario->id,
            'estado' => 2,
        ]);

        VisualizacionCapitulo::create([
            'persona_id' => $this->usuario->id,
            'capitulo_id' => $capitulo->id
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $serie->id,
            'tipo' => 1,
        ];

        $response = $this->call('POST', '/api/seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(SeguimientoAudiovisual::where('audiovisual_id', $serie->id)
                                                 ->where('persona_id', $this->usuario->id)
                                                 ->where('estado', 1)
                                                 ->exists());
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->usuario->id)
                                                 ->where('capitulo_id', $capitulo->id)
                                                 ->exists());
        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                   ->where('tipo', 1)
                                   ->where('audiovisual_id', $serie->id)
                                   ->exists());
    }

    /**
     * Actualiza un seguimiento de un audiovisual para un usuario a un seguimiento de tipo 3 
     * y crea las visualizaciones y actividad correspondientes.
     *
     * @return void
     */
    public function test_actualizar_seguimiento_audiovisual_a_tipo_3()
    {
        $this->actingAs($this->usuario);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $serie->id,
            'persona_id' => $this->usuario->id,
            'estado' => 2,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $serie->id,
            'tipo' => 3,
        ];

        $response = $this->call('POST', '/api/seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(SeguimientoAudiovisual::where('audiovisual_id', $serie->id)
                                                 ->where('persona_id', $this->usuario->id)
                                                 ->where('estado', 3)
                                                 ->exists());
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->usuario->id)
                                                 ->where('capitulo_id', $capitulo->id)
                                                 ->exists());
        $this->assertTrue(VisualizacionTemporada::where('persona_id', $this->usuario->id)
                                                 ->where('temporada_id', $temporada->id)
                                                 ->exists());
        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                   ->where('tipo', 3)
                                   ->where('audiovisual_id', $serie->id)
                                   ->exists());
    }

    /**
     * Intenta crear un seguimiento de un audiovisual para un usuario sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_seguimiento_audiovisual_usuario_sin_sesion_iniciada()
    {
        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
            'tipo' => 3,
        ];

        $response = $this->call('POST', '/api/seguimiento-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene los proveedores de un audiovisual divididos por la condición que ofrece 
     * cada proveedor para visualizarlos.
     *
     * @return void
     */
    public function test_obtener_proveedores_audiovisual()
    {
        $this->actingAs($this->usuario);

        $proveedor = Proveedor::create([
            'nombre' => 'Amazon Prime Video'
        ]);

        ProveedorAudiovisual::create([
            'proveedor_id' => $proveedor->id,
            'audiovisual_id' => $this->pelicula->id,
            'disponibilidad' => 1,
        ]);

        ProveedorAudiovisual::create([
            'proveedor_id' => $proveedor->id,
            'audiovisual_id' => $this->pelicula->id,
            'disponibilidad' => 2,
        ]);

        ProveedorAudiovisual::create([
            'proveedor_id' => $proveedor->id,
            'audiovisual_id' => $this->pelicula->id,
            'disponibilidad' => 3,
        ]);

        $response = $this->getJson('/api/proveedores/'.$this->pelicula->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('stream', 1, fn ($json) =>
                        $json->where('proveedor_id', strval($proveedor->id))
                            ->etc()
                        )->has('alquilar', 1, fn ($json) =>
                        $json->where('proveedor_id', strval($proveedor->id))
                            ->etc()
                        )->has('comprar', 1, fn ($json) =>
                        $json->where('proveedor_id', strval($proveedor->id))
                            ->etc()
                        )
                 );
    }

    /**
     * Intenta obtener los proveedores de un audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_proveedores_audiovisual_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/proveedores/'.$this->pelicula->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene la colección de audiovisuales de un usuario.
     *
     * @return void
     */
    public function test_obtener_coleccion_audiovisuales_usuario()
    {
        $this->actingAs($this->usuario);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'estado' => 3,
        ]);

        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Breaking Bad',
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $serie->id,
            'persona_id' => $this->usuario->id,
            'estado' => 2,
        ]);

        $response = $this->getJson('/api/coleccion-usuario/'.$this->usuario->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('todo', 2)
                         ->has('series', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($serie->id))
                                ->etc()
                         )->has('peliculas', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($this->pelicula->id))
                                ->etc()
                         )
                 );
    }

    /**
     * Intenta obtener la colección de audiovisuales de un usuario.
     *
     * @return void
     */
    public function test_obtener_coleccion_audiovisuales_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/coleccion-usuario/'.$this->usuario->id);

        $response->assertUnauthorized();
    }

    /**
     * Actualiza una valoración existente de un usuario para un audiovisual y la puntuación del audiovisual.
     *
     * @return void
     */
    public function test_actualiza_valoracion_audiovisual()
    {
        $this->actingAs($this->usuario);

        Valoracion::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'puntuacion' => 5,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
            'puntuacion' => 4,
        ];

        $response = $this->call('POST', '/api/valoracion-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(Valoracion::where('audiovisual_id', $this->pelicula->id)
                                    ->where('persona_id', $this->usuario->id)
                                    ->where('puntuacion', 4)
                                    ->exists());
        $this->assertFalse(Valoracion::where('audiovisual_id', $this->pelicula->id)
                                     ->where('persona_id', $this->usuario->id)
                                     ->where('puntuacion', 5)
                                     ->exists());
        $this->assertTrue(Audiovisual::where('id', $this->pelicula->id)
                                     ->where('puntuacion', 4)
                                     ->exists());
    }

    /**
     * Crea una nueva valoración de un usuario para un audiovisual y actualiza la puntuación del audiovisual
     * y los puntos del usuario.
     *
     * @return void
     */
    public function test_crear_valoracion_audiovisual()
    {
        $this->actingAs($this->usuario);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
            'puntuacion' => 5,
        ];

        $response = $this->call('POST', '/api/valoracion-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(Valoracion::where('audiovisual_id', $this->pelicula->id)
                                    ->where('persona_id', $this->usuario->id)
                                    ->where('puntuacion', 5)
                                    ->exists());
        $this->assertTrue(Audiovisual::where('id', $this->pelicula->id)
                                     ->where('puntuacion', 5)
                                     ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 5)
                                 ->exists());
    }

    /**
     * Intenta crear una nueva valoración de un usuario para un audiovisual sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_valoracion_audiovisual_usuario_sin_sesion_iniciada()
    {
        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
            'puntuacion' => 5,
        ];

        $response = $this->call('POST', '/api/valoracion-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Comprueba la valoración de un usuario a un audiovisual.
     *
     * @return void
     */
    public function test_comprobar_valoracion_audiovisual()
    {
        $this->actingAs($this->usuario);

        Valoracion::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'puntuacion' => 5,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-valoracion-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(5, $response->getContent());
    }

    /**
     * Comprueba que se obtiene 0 como valoración de un usuario a un audiovisual debido a que no existe ninguna valoración.
     *
     * @return void
     */
    public function test_comprobar_valoracion_audiovisual_sin_que_exista_valoracion()
    {
        $this->actingAs($this->usuario);

        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-valoracion-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(0, $response->getContent());
    }

    /**
     * Intenta comprobar la valoración de un usuario a un audiovisual.
     *
     * @return void
     */
    public function test_comprobar_valoracion_audiovisual_usuario_sin_sesion_iniciada()
    {
        $request = [
            'usuario_id' => $this->usuario->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-valoracion-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene la colección de audiovisuales del usuario actual.
     *
     * @return void
     */
    public function test_obtener_coleccion_audiovisuales_usuario_actual()
    {
        $this->actingAs($this->usuario);

        $peliculaVista = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Fargo',
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'estado' => 1,
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $peliculaVista->id,
            'persona_id' => $this->usuario->id,
            'estado' => 3,
        ]);

        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $seriePendiente = Audiovisual::create([
            'id' => 3,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Breaking Bad',
        ]);

        $serieSeguida = Audiovisual::create([
            'id' => 4,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Lost',
        ]);

        $serieVista = Audiovisual::create([
            'id' => 5,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Fargo',
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $seriePendiente->id,
            'persona_id' => $this->usuario->id,
            'estado' => 1,
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $serieSeguida->id,
            'persona_id' => $this->usuario->id,
            'estado' => 2,
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $serieVista->id,
            'persona_id' => $this->usuario->id,
            'estado' => 3,
        ]);

        $response = $this->getJson('/api/mi-coleccion');

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('series', 3)
                         ->has('series_pendientes', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($seriePendiente->id))
                                ->etc()
                         )->has('series_siguiendo', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($serieSeguida->id))
                                ->etc()
                        )->has('series_vistas', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($serieVista->id))
                                ->etc()
                        )->has('peliculas', 2)
                         ->has('peliculas_pendientes', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($this->pelicula->id))
                                ->etc()
                         )->has('peliculas_vistas', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($peliculaVista->id))
                                ->etc()
                         )
                 );
    }

    /**
     * Intenta obtener la colección de audiovisuales del usuario actual.
     *
     * @return void
     */
    public function test_obtener_coleccion_audiovisuales_usuario_actual_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/mi-coleccion');

        $response->assertUnauthorized();
    }

    /**
     * Obtiene audiovisuales recomendados en base a audiovisuales 
     * sobre los que tiene creado un seguimiento el usuario actual.
     *
     * @return void
     */
    public function test_obtener_audiovisuales_recomendados()
    {
        $this->actingAs($this->usuario);

        $generoAccion = Genero::create([
            'id' => 1,
            'nombre' => 'Acción'
        ]);

        $generoThriller = Genero::create([
            'id' => 2,
            'nombre' => 'Thriller',
        ]);

        $idiomaIngles = Idioma::create([
            'nombre' => 'Inglés'
        ]);

        $idiomaEspanol = Idioma::create([
            'nombre' => 'Español'
        ]);

        Audiovisual::where('id', $this->pelicula->id)
                    ->update([
                        'genero_id' => $generoAccion->id,
                        'idioma_id' => $idiomaIngles->id,
                        'puntuacion' => 5,
                    ]);

        $pelicula2 = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'El inocente',
            'genero_id' => $generoThriller->id,
            'idioma_id' => $idiomaEspanol->id,
            'puntuacion' => 4,
        ]);

        $pelicula3 = Audiovisual::create([
            'id' => 3,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Fargo',
            'genero_id' => $generoAccion->id,
            'idioma_id' => $idiomaIngles->id,
            'puntuacion' => 4,
        ]);

        $pelicula4 = Audiovisual::create([
            'id' => 4,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'The Silence of the Lambs',
            'genero_id' => $generoAccion->id,
            'idioma_id' => $idiomaIngles->id,
            'puntuacion' => 5,
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'estado' => 3,
        ]);

        $response = $this->getJson('/api/recomendaciones');

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(2)
                    ->first(fn ($json) =>
                        $json->where('id', strval($pelicula4->id))
                            ->etc()
                    )
                 );
    }

    /**
     * Intenta obtener audiovisuales recomendados en base a audiovisuales 
     * sobre los que tiene creado un seguimiento el usuario actual sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_audiovisuales_recomendados_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/recomendaciones');

        $response->assertUnauthorized();
    }
}
