<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionTemporada;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TemporadaTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $tipoAudiovisual;
    
    /**
     * Set up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->user = Persona::factory()->create();

        $this->tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);
    }

    /**
     * Get season with logged in user.
     *
     * @return void
     */
    public function test_get_season_logged_in_user()
    {
        $this->actingAs($this->user);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);
        
        $response = $this->getJson('/api/temporadas/'.$serie->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('audiovisual_id', strval($serie->id))
                                 ->etc()
                         )
                 );
    }

    /**
     * Get season without logged in user.
     *
     * @return void
     */
    public function test_get_season_not_logged_in_user()
    {
        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);
        
        $response = $this->getJson('/api/temporadas/'.$serie->id);

        $response->assertUnauthorized();
    }

    /**
     * Viewed season and viewed season exists.
     *
     * @return void
     */
    public function test_viewed_season_exists()
    {
        $this->actingAs($this->user);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $temporada->id,
            'persona_id' => $this->user->id,
        ]);

        $request = [
            'usuario_id' => $this->user->id,
            'temporada_id' => $temporada->id,
        ];
        
        $response = $this->call('GET', '/api/saber-visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Viewed season but viewed season doesn't exist.
     *
     * @return void
     */
    public function test_viewed_season_not_existing()
    {
        $this->actingAs($this->user);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $request = [
            'usuario_id' => $this->user->id,
            'temporada_id' => $temporada->id,
        ];
        
        $response = $this->call('GET', '/api/saber-visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Viewed season without logged in user.
     *
     * @return void
     */
    public function test_viewed_season_not_logged_in_user()
    {
        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $request = [
            'usuario_id' => $this->user->id,
            'temporada_id' => $temporada->id,
        ];

        $response = $this->getJson('/api/saber-visualizacion-temporada', $request);

        $response->assertUnauthorized();
    }

    /**
     * Viewing and viewed season exists.
     *
     * @return void
     */
    public function test_viewing_and_season_exists()
    {
        $this->actingAs($this->user);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $temporada->id,
            'persona_id' => $this->user->id,
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $this->user->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];
        
        $response = $this->call('POST', '/api/visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Viewing and viewed season doesn't exist.
     *
     * @return void
     */
    public function test_viewing_and_viewed_season_not_existing()
    {
        $this->actingAs($this->user);

        $serie = Audiovisual::create([
            'id' => 1,
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

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $this->user->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];
        
        $response = $this->call('POST', '/api/visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Viewing without logged in user.
     *
     * @return void
     */
    public function test_viewing_and_not_logged_in_user()
    {
        $serie = Audiovisual::create([
            'id' => 1,
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

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $this->user->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];

        $response = $this->postJson('/api/visualizacion-temporada', $request);

        $response->assertUnauthorized();
    }
}
