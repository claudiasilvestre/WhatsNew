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
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComentarioTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $tipoAudiovisual, $pelicula;
    
    /**
     * Set up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->user = Persona::factory()->create();

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
     * Create audiovisual comment with logged in user.
     *
     * @return void
     */
    public function test_create_audiovisual_comment_logged_in_user()
    {
        $this->actingAs($this->user);

        $request = [
            'tipo_id' => $this->pelicula->id,
            'usuario_id' => $this->user->id,
            'texto' => 'Muy buena',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-audiovisual', $request);

        $response->assertOk();
        $this->assertTrue(ComentarioAudiovisual::where('audiovisual_id', $this->pelicula->id)
                                                ->where('persona_id', $this->user->id)
                                                ->where('texto', 'Muy buena')
                                                ->exists());
    }

    /**
     * Create audiovisual comment without logged in user.
     *
     * @return void
     */
    public function test_create_audiovisual_comment_not_logged_in_user()
    {
        $request = [
            'tipo_id' => $this->pelicula->id,
            'usuario_id' => $this->user->id,
            'texto' => 'Muy buena',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Create episode comment with logged in user.
     *
     * @return void
     */
    public function test_create_episode_comment_logged_in_user()
    {
        $this->actingAs($this->user);

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
            'usuario_id' => $this->user->id,
            'texto' => 'Muy bueno',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(ComentarioCapitulo::where('capitulo_id', $capitulo->id)
                                            ->where('persona_id', $this->user->id)
                                            ->where('texto', 'Muy bueno')
                                            ->exists());
    }

    /**
     * Create episode comment without logged in user.
     *
     * @return void
     */
    public function test_create_episode_comment_not_logged_in_user()
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
            'usuario_id' => $this->user->id,
            'texto' => 'Muy bueno',
        ];

        $response = $this->call('POST', '/api/guardar-comentario-capitulo', $request);

        $response->assertUnauthorized();
    }

    /**
     * Get audiovisual comments type 1 with logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_comments_type_1_logged_in_user()
    {
        $this->actingAs($this->user);

        $comentario1 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
            'texto' => 'Muy buena'
        ]);

        $comentario2 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
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
     * Get audiovisual comments type 2 with logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_comments_type_2_logged_in_user()
    {
        $this->actingAs($this->user);

        $comentario1 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
            'texto' => 'Muy buena'
        ]);

        $comentario2 = ComentarioAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
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
     * Get audiovisual comments without logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_comments_not_logged_in_user()
    {
        $request = [
            'tipo' => 1,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/comentario-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Get episode comments type 1 with logged in user.
     *
     * @return void
     */
    public function test_get_episode_comments_type_1_logged_in_user()
    {
        $this->actingAs($this->user);

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
            'persona_id' => $this->user->id,
            'texto' => 'Muy bueno'
        ]);

        $comentario2 = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->user->id,
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
     * Get episode comments type 2 with logged in user.
     *
     * @return void
     */
    public function test_get_episode_comments_type_2_logged_in_user()
    {
        $this->actingAs($this->user);

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
            'persona_id' => $this->user->id,
            'texto' => 'Muy buena'
        ]);

        $comentario2 = ComentarioCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->user->id,
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
     * Get episode comments without logged in user.
     *
     * @return void
     */
    public function test_get_episode_comments_not_logged_in_user()
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
}
