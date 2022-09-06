<?php

namespace Tests\Unit;

use App\Models\ComentarioAudiovisual;
use App\Models\ComentarioCapitulo;
use App\Models\TipoPersona;
use App\Models\Persona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\OpinionComentarioAudiovisual;
use App\Models\OpinionComentarioCapitulo;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComentarioTest extends TestCase
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
     * Crea un nuevo comentario sobre un audiovisual e incrementa los puntos del usuario actual.
     *
     * @return void
     */
    public function test_crear_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $request = [
            'tipo_id' => $this->pelicula->id,
            'usuario_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(ComentarioAudiovisual::where('audiovisual_id', $this->pelicula->id)
                                               ->where('persona_id', $this->usuario->id)
                                               ->where('texto', 'Muy buena')
                                               ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 5)
                                 ->exists());
    }

    /**
     * Intenta crear un nuevo comentario sobre un audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_comentario_audiovisual_usuario_sin_sesion_iniciada()
    {
        $request = [
            'tipo_id' => $this->pelicula->id,
            'usuario_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Crea un nuevo comentario sobre un capítulo e incrementa los puntos del usuario actual.
     *
     * @return void
     */
    public function test_crear_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $request = [
            'tipo_id' => $capitulo->id,
            'usuario_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(ComentarioCapitulo::where('capitulo_id', $capitulo->id)
                                            ->where('persona_id', $this->usuario->id)
                                            ->where('texto', 'Muy bueno')
                                            ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 5)
                                 ->exists());
    }

    /**
     * Intenta crear un nuevo comentario sobre un capítulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_comentario_capitulo_usuario_sin_sesion_iniciada()
    {
        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
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

        $request = [
            'tipo_id' => $capitulo->id,
            'usuario_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-capitulo', $request);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene comentarios de tipo 1 sobre un audiovisual.
     *
     * @return void
     */
    public function test_obtener_comentarios_tipo_1_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario1 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena'
        ]);

        $comentario2 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'No está mal',
            'votosPositivos' => 1,
        ]);

        $request = [
            'tipo' => 1,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/comentario-audiovisual', $request);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('comentarios', 2, fn ($json) =>
                        $json->where('id', strval($comentario1->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Obtiene comentarios de tipo 2 sobre un audiovisual.
     *
     * @return void
     */
    public function test_obtener_comentarios_tipo_2_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario1 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena'
        ]);

        $comentario2 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'No está mal',
            'votosPositivos' => 1,
        ]);

        $request = [
            'tipo' => 2,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/comentario-audiovisual', $request);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('comentarios', 2, fn ($json) =>
                        $json->where('id', strval($comentario2->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Intenta obtener comentarios de tipo 1 sobre un audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_comentarios_tipo_1_audiovisual_usuario_sin_sesion_iniciada()
    {
        $request = [
            'tipo' => 1,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/comentario-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene comentarios de tipo 1 sobre un capítulo.
     *
     * @return void
     */
    public function test_obtener_comentarios_tipo_1_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario1 = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno'
        ]);

        $comentario2 = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'No está mal',
            'votosPositivos' => 1,
        ]);

        $request = [
            'tipo' => 1,
            'capitulo_id' => $capitulo->id,
        ];

        $response = $this->call('GET', '/api/comentario-capitulo', $request);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('comentarios', 2, fn ($json) =>
                        $json->where('id', strval($comentario1->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Obtiene comentarios de tipo 2 sobre un capítulo.
     *
     * @return void
     */
    public function test_obtener_comentarios_tipo_2_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario1 = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno'
        ]);

        $comentario2 = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'No está mal',
            'votosPositivos' => 1,
        ]);

        $request = [
            'tipo' => 2,
            'capitulo_id' => $capitulo->id,
        ];

        $response = $this->call('GET', '/api/comentario-capitulo', $request);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('comentarios', 2, fn ($json) =>
                        $json->where('id', strval($comentario2->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Intenta obtener comentarios de tipo 1 sobre un capítulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_comentarios_tipo_1_capitulo_usuario_sin_sesion_iniciada()
    {
        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
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
        
        $request = [
            'tipo' => 1,
            'capitulo_id' => $capitulo->id,
        ];

        $response = $this->call('GET', '/api/comentario-capitulo', $request);

        $response->assertUnauthorized();
    }

    /**
     * Borra un comentario sobre un audiovisual y decrementa los puntos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ]);

        Persona::where('id', $this->usuario->id)->update(['puntos' => 5]);

        $response = $this->call('POST', '/api/borrar-comentario-audiovisual/'.$comentario->id);

        $response->assertOk();
        $this->assertFalse(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 0)
                                 ->exists());
    }

    /**
     * Intenta borrar un comentario sobre un audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_borrar_comentario_audiovisual_usuario_sin_sesion_iniciada()
    {
        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ]);

        $response = $this->call('POST', '/api/borrar-comentario-audiovisual/'.$comentario->id);

        $response->assertUnauthorized();
    }

    /**
     * Borra un comentario sobre un capítulo y decrementa los puntos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ]);

        Persona::where('id', $this->usuario->id)->update(['puntos' => 5]);

        $response = $this->call('POST', '/api/borrar-comentario-capitulo/'.$comentario->id);

        $response->assertOk();
        $this->assertFalse(ComentarioCapitulo::where('id', $comentario->id)
                                             ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 0)
                                 ->exists());
    }

    /**
     * Intenta borrar un comentario sobre un capitulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_borrar_comentario_capitulo_usuario_sin_sesion_iniciada()
    {
        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
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

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ]);

        $response = $this->call('POST', '/api/borrar-comentario-capitulo/'.$comentario->id);

        $response->assertUnauthorized();
    }

    /**
     * Crea una opinión positiva de un comentario sobre un audiovisual para el usuario actual e 
     * incrementa los votos positivos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_crear_opinion_positiva_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioAudiovisual::where('persona_id', $this->usuario->id)
                                                      ->where('comentarioAudiovisual_id', $comentario->id)
                                                      ->where('opinion', true)
                                                      ->exists());
        $this->assertTrue(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->where('votosPositivos', 1)
                                                ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 1)
                                 ->exists());
    }

    /**
     * Actualiza la opinión a positiva de un comentario sobre un audiovisual del usuario actual (la cual era negativa),
     * incrementa los votos positivos y decrementa los votos negativos del comentario.
     *
     * @return void
     */
    public function test_actualizar_opinion_a_positiva_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
            'votosNegativos' => 1,
        ]);

        $opinion = OpinionComentarioAudiovisual::create([
            'persona_id' => $this->usuario->id,
            'comentarioAudiovisual_id' => $comentario->id,
            'opinion' => false,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioAudiovisual::where('id', $opinion->id)
                                                      ->where('opinion', true)
                                                      ->exists());
        $this->assertTrue(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->where('votosPositivos', 1)
                                                ->where('votosNegativos', 0)
                                                ->exists());
    }

    /**
     * Borra la opinión positiva de un comentario sobre un audiovisual del usuario actual
     * y decrementa los votos positivos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_opinion_positiva_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
            'votosPositivos' => 1,
        ]);

        $opinion = OpinionComentarioAudiovisual::create([
            'persona_id' => $this->usuario->id,
            'comentarioAudiovisual_id' => $comentario->id,
            'opinion' => true,
        ]);

        Persona::where('id', $this->usuario->id)->update(['puntos' => 1]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-audiovisual', $request);

        $response->assertOk();
        $this->assertFalse(OpinionComentarioAudiovisual::where('id', $opinion->id)
                                                        ->exists());
        $this->assertTrue(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->where('votosPositivos', 0)
                                                ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 0)
                                 ->exists());
    }

    /**
     * Intenta crear una opinión positiva de un comentario sobre un audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_opinion_positiva_comentario_audiovisual_usuario_sin_sesion_iniciada()
    {
        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Crea una opinión negativa de un comentario sobre un audiovisual para el usuario actual e 
     * incrementa los votos negativos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_crear_opinion_negativa_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioAudiovisual::where('persona_id', $this->usuario->id)
                                                      ->where('comentarioAudiovisual_id', $comentario->id)
                                                      ->where('opinion', false)
                                                      ->exists());
        $this->assertTrue(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->where('votosNegativos', 1)
                                                ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 1)
                                 ->exists());
    }

    /**
     * Actualiza la opinión a negativa de un comentario sobre un audiovisual del usuario actual (la cual era positiva),
     * incrementa los votos negativos y decrementa los votos positivos del comentario.
     *
     * @return void
     */
    public function test_actualizar_opinion_a_negativa_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
            'votosPositivos' => 1,
        ]);

        $opinion = OpinionComentarioAudiovisual::create([
            'persona_id' => $this->usuario->id,
            'comentarioAudiovisual_id' => $comentario->id,
            'opinion' => true,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioAudiovisual::where('id', $opinion->id)
                                                      ->where('opinion', false)
                                                      ->exists());
        $this->assertTrue(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->where('votosPositivos', 0)
                                                ->where('votosNegativos', 1)
                                                ->exists());
    }

    /**
     * Borra la opinión negativa de un comentario sobre un audiovisual del usuario actual
     * y decrementa los votos negativos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_opinion_negativa_comentario_audiovisual()
    {
        $this->actingAs($this->usuario);

        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
            'votosNegativos' => 1,
        ]);

        $opinion = OpinionComentarioAudiovisual::create([
            'persona_id' => $this->usuario->id,
            'comentarioAudiovisual_id' => $comentario->id,
            'opinion' => false,
        ]);

        Persona::where('id', $this->usuario->id)->update(['puntos' => 1]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-audiovisual', $request);

        $response->assertOk();
        $this->assertFalse(OpinionComentarioAudiovisual::where('id', $opinion->id)
                                                        ->exists());
        $this->assertTrue(ComentarioAudiovisual::where('id', $comentario->id)
                                                ->where('votosNegativos', 0)
                                                ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 0)
                                 ->exists());
    }

    /**
     * Intenta crear una opinión negativa de un comentario sobre un audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_opinion_negativa_comentario_audiovisual_usuario_sin_sesion_iniciada()
    {
        $comentario = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy buena',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Crea una opinión positiva de un comentario sobre un capitulo para el usuario actual e 
     * incrementa los votos positivos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_crear_opinion_positiva_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioCapitulo::where('persona_id', $this->usuario->id)
                                                    ->where('comentarioCapitulo_id', $comentario->id)
                                                    ->where('opinion', true)
                                                    ->exists());
        $this->assertTrue(ComentarioCapitulo::where('id', $comentario->id)
                                            ->where('votosPositivos', 1)
                                            ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 1)
                                 ->exists());
    }

    /**
     * Actualiza la opinión a positiva de un comentario sobre un capitulo del usuario actual (la cual era negativa),
     * incrementa los votos positivos y decrementa los votos negativos del comentario.
     *
     * @return void
     */
    public function test_actualizar_opinion_a_positiva_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
            'votosNegativos' => 1,
        ]);

        $opinion = OpinionComentarioCapitulo::create([
            'persona_id' => $this->usuario->id,
            'comentarioCapitulo_id' => $comentario->id,
            'opinion' => false,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioCapitulo::where('id', $opinion->id)
                                                    ->where('opinion', true)
                                                    ->exists());
        $this->assertTrue(ComentarioCapitulo::where('id', $comentario->id)
                                            ->where('votosPositivos', 1)
                                            ->where('votosNegativos', 0)
                                            ->exists());
    }

    /**
     * Borra la opinión positiva de un comentario sobre un capitulo del usuario actual
     * y decrementa los votos positivos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_opinion_positiva_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
            'votosPositivos' => 1,
        ]);

        $opinion = OpinionComentarioCapitulo::create([
            'persona_id' => $this->usuario->id,
            'comentarioCapitulo_id' => $comentario->id,
            'opinion' => true,
        ]);

        Persona::where('id', $this->usuario->id)->update(['puntos' => 1]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-capitulo', $request);

        $response->assertOk();
        $this->assertFalse(OpinionComentarioCapitulo::where('id', $opinion->id)
                                                    ->exists());
        $this->assertTrue(ComentarioCapitulo::where('id', $comentario->id)
                                            ->where('votosPositivos', 0)
                                            ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 0)
                                 ->exists());
    }

    /**
     * Intenta crear una opinión positiva de un comentario sobre un capitulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_opinion_positiva_comentario_capitulo_usuario_sin_sesion_iniciada()
    {
        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
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

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-positiva-capitulo', $request);

        $response->assertUnauthorized();
    }

    /**
     * Crea una opinión negativa de un comentario sobre un capitulo para el usuario actual e 
     * incrementa los votos negativos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_crear_opinion_negativa_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioCapitulo::where('persona_id', $this->usuario->id)
                                                    ->where('comentarioCapitulo_id', $comentario->id)
                                                    ->where('opinion', false)
                                                    ->exists());
        $this->assertTrue(ComentarioCapitulo::where('id', $comentario->id)
                                            ->where('votosNegativos', 1)
                                            ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 1)
                                 ->exists());
    }

    /**
     * Actualiza la opinión a negativa de un comentario sobre un capitulo del usuario actual (la cual era positiva),
     * incrementa los votos negativos y decrementa los votos positivos del comentario.
     *
     * @return void
     */
    public function test_actualizar_opinion_a_negativa_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
            'votosPositivos' => 1,
        ]);

        $opinion = OpinionComentarioCapitulo::create([
            'persona_id' => $this->usuario->id,
            'comentarioCapitulo_id' => $comentario->id,
            'opinion' => true,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(OpinionComentarioCapitulo::where('id', $opinion->id)
                                                    ->where('opinion', false)
                                                    ->exists());
        $this->assertTrue(ComentarioCapitulo::where('id', $comentario->id)
                                            ->where('votosPositivos', 0)
                                            ->where('votosNegativos', 1)
                                            ->exists());
    }

    /**
     * Borra la opinión negativa de un comentario sobre un capitulo del usuario actual
     * y decrementa los votos negativos del comentario y los puntos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_opinion_negativa_comentario_capitulo()
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

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
            'votosNegativos' => 1,
        ]);

        $opinion = OpinionComentarioCapitulo::create([
            'persona_id' => $this->usuario->id,
            'comentarioCapitulo_id' => $comentario->id,
            'opinion' => false,
        ]);

        Persona::where('id', $this->usuario->id)->update(['puntos' => 1]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-capitulo', $request);

        $response->assertOk();
        $this->assertFalse(OpinionComentarioCapitulo::where('id', $opinion->id)
                                                    ->exists());
        $this->assertTrue(ComentarioCapitulo::where('id', $comentario->id)
                                            ->where('votosNegativos', 0)
                                            ->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)
                                 ->where('puntos', 0)
                                 ->exists());
    }

    /**
     * Intenta crear una opinión negativa de un comentario sobre un capitulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_opinion_negativa_comentario_capitulo_usuario_sin_sesion_iniciada()
    {
        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
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

        $comentario = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
            'texto' => 'Muy bueno',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'comentario_id' => $comentario->id,
        ];

        $response = $this->call('POST', '/api/opinion-negativa-capitulo', $request);

        $response->assertUnauthorized();
    }
}
