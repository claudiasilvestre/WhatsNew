<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionCapitulo;
use App\Models\VisualizacionTemporada;
use App\Models\Actividad;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CapituloTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $tipoAudiovisual, $serie, $temporada, $capitulo;
    
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

        $this->serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $this->temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
            'numeroCapitulos' => 2,
        ]);

        $this->capitulo = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 1,
        ]);
    }

    /**
     * Get episodes with logged in user.
     *
     * @return void
     */
    public function test_get_eposides_logged_in_user()
    {
        $this->actingAs($this->user);
        
        $response = $this->getJson('/api/capitulos/'.$this->temporada->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('id', $this->capitulo->id)
                                 ->etc()
                         )
                 );
    }

    /**
     * Get episodes without logged in user.
     *
     * @return void
     */
    public function test_get_eposides_not_logged_in_user()
    {
        $response = $this->getJson('/api/capitulos/'.$this->temporada->id);

        $response->assertUnauthorized();
    }

    /**
     * Get episode with logged in user.
     *
     * @return void
     */
    public function test_get_eposide_logged_in_user()
    {
        $this->actingAs($this->user);
        
        $response = $this->getJson('/api/capitulo/'.$this->capitulo->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('id', $this->capitulo->id)
                                 ->etc()
                         )
                 );
    }

    /**
     * Get episode without logged in user.
     *
     * @return void
     */
    public function test_get_eposide_not_logged_in_user()
    {
        $response = $this->getJson('/api/capitulo/'.$this->capitulo->id);

        $response->assertUnauthorized();
    }

    /**
     * Get viewings with episode viewing created.
     *
     * @return void
     */
    public function test_get_viewings_with_episode_viewing()
    {
        $this->actingAs($this->user);

        $capitulos = [];
        array_push($capitulos, $this->capitulo);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->user->id,
        ]);
        
        $request = [
            'capitulos' => $capitulos,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('GET', '/api/visualizaciones', $request);

        $response->assertOk();
        $this->assertTrue($response->getData()[0]);
    }

    /**
     * Get viewings without episode viewing created.
     *
     * @return void
     */
    public function test_get_viewings_without_episode_viewing()
    {
        $this->actingAs($this->user);

        $capitulos = [];
        array_push($capitulos, $this->capitulo);
        
        $request = [
            'capitulos' => $capitulos,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('GET', '/api/visualizaciones', $request);

        $response->assertOk();
        $this->assertFalse($response->getData()[0]);
    }

    /**
     * Get viewings without logged in user.
     *
     * @return void
     */
    public function test_get_viewings_not_logged_in_user()
    {
        $capitulos = [];
        array_push($capitulos, $this->capitulo);
        
        $request = [
            'capitulos' => $capitulos,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('GET', '/api/visualizaciones', $request);

        $response->assertUnauthorized();
    }

    /**
     * Create episode viewing and season viewing from all episodes.
     *
     * @return void
     */
    public function test_create_episode_viewing_and_season_viewing_from_all_episodes()
    {
        $this->actingAs($this->user);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo2->id,
            'persona_id' => $this->user->id,
        ]);
        
        $request = [
            'capitulo_id' => $this->capitulo->id,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('POST', '/api/visualizacion-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->user->id)
                                    ->where('capitulo_id', $this->capitulo->id)
                                    ->exists());

        $this->assertTrue(VisualizacionTemporada::where('persona_id', $this->user->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->user->id)
                                    ->where('temporada_id', $this->temporada->id)
                                    ->exists());
    }

    /**
     * Create episode viewing from all episodes.
     *
     * @return void
     */
    public function test_create_episode_viewing_from_all_episodes()
    {
        $this->actingAs($this->user);
        
        $request = [
            'capitulo_id' => $this->capitulo->id,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('POST', '/api/visualizacion-capitulo', $request);

        $response->assertOk();
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->user->id)
                                    ->where('capitulo_id', $this->capitulo->id)
                                    ->exists());

        $this->assertFalse(VisualizacionTemporada::where('persona_id', $this->user->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());

        $this->assertFalse(Actividad::where('persona_id', $this->user->id)
                                    ->where('temporada_id', $this->temporada->id)
                                    ->exists());
    }

    /**
     * Delete episode viewing and season viewing from all episodes.
     *
     * @return void
     */
    public function test_delete_episode_viewing_and_season_viewing_from_all_episodes()
    {
        $this->actingAs($this->user);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->user->id,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo2->id,
            'persona_id' => $this->user->id,
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $this->temporada->id,
            'persona_id' => $this->user->id,
        ]);
        
        $request = [
            'capitulo_id' => $this->capitulo->id,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('POST', '/api/visualizacion-capitulo', $request);

        $response->assertOk();
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertFalse(VisualizacionTemporada::where('persona_id', $this->user->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());
    }

    /**
     * Delete episode viewing from all episodes.
     *
     * @return void
     */
    public function test_delete_episode_viewing_from_all_episodes()
    {
        $this->actingAs($this->user);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->user->id,
        ]);
        
        $request = [
            'capitulo_id' => $this->capitulo->id,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('POST', '/api/visualizacion-capitulo', $request);

        $response->assertOk();
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());
    }

    /**
     * Create or delete episode viewing without logged in user from all episodes.
     *
     * @return void
     */
    public function test_create_or_delete_episode_viewing_not_logged_in_user_from_all_episodes()
    {
        $request = [
            'capitulo_id' => $this->capitulo->id,
            'usuario_id' => $this->user->id,
        ];

        $response = $this->call('POST', '/api/visualizacion-capitulo', $request);

        $response->assertUnauthorized();
    }

    /**
     * Previous and next episodes with logged in user.
     *
     * @return void
     */
    public function test_previous_next_episodes_logged_in_user()
    {
        $this->actingAs($this->user);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        $capitulo3 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 3,
        ]);

        $request =[
            'audiovisual_id' => $this->serie->id,
            'capitulo_id' => $capitulo2->id,
        ];
        
        $response = $this->call('GET', '/api/capitulos-anterior-siguiente', $request);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(2)
                          ->where('anteriorCapitulo_id', strval($this->capitulo->id))
                          ->where('siguienteCapitulo_id', strval($capitulo3->id))
                 );
    }

    /**
     * Previous and next episodes without logged in user.
     *
     * @return void
     */
    public function test_previous_next_eposides_not_logged_in_user()
    {
        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        $capitulo3 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 3,
        ]);

        $request =[
            'audiovisual_id' => $this->serie->id,
            'capitulo_id' => $capitulo2->id,
        ];

        $response = $this->call('GET', '/api/capitulos-anterior-siguiente', $request);

        $response->assertUnauthorized();
    }

    /**
     * Get episode viewing with logged in user.
     *
     * @return void
     */
    public function test_get_episode_viewing_logged_in_user()
    {
        $this->actingAs($this->user);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/saber-visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Get episode viewing without episode viewing exists.
     *
     * @return void
     */
    public function test_get_episode_viewing_and_viewing_not_existing()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/saber-visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Get episode viewing without logged in user.
     *
     * @return void
     */
    public function test_get_episode_viewing_not_logged_in_user()
    {
        $response = $this->getJson('/api/saber-visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertUnauthorized();
    }

    /**
     * Create episode viewing and season viewing from episode.
     *
     * @return void
     */
    public function test_create_episode_viewing_and_season_viewing_from_episode()
    {
        $this->actingAs($this->user);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo2->id,
            'persona_id' => $this->user->id,
        ]);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->user->id)
                                    ->where('capitulo_id', $this->capitulo->id)
                                    ->exists());

        $this->assertTrue(VisualizacionTemporada::where('persona_id', $this->user->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->user->id)
                                    ->where('temporada_id', $this->temporada->id)
                                    ->exists());
    }

    /**
     * Create episode viewing from episode.
     *
     * @return void
     */
    public function test_create_episode_viewing_from_episode()
    {
        $this->actingAs($this->user);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->user->id)
                                    ->where('capitulo_id', $this->capitulo->id)
                                    ->exists());

        $this->assertFalse(VisualizacionTemporada::where('persona_id', $this->user->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());

        $this->assertFalse(Actividad::where('persona_id', $this->user->id)
                                    ->where('temporada_id', $this->temporada->id)
                                    ->exists());
    }

    /**
     * Delete episode viewing and season viewing from episode.
     *
     * @return void
     */
    public function test_delete_episode_viewing_and_season_viewing_from_episode()
    {
        $this->actingAs($this->user);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->user->id,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo2->id,
            'persona_id' => $this->user->id,
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $this->temporada->id,
            'persona_id' => $this->user->id,
        ]);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertFalse(VisualizacionTemporada::where('persona_id', $this->user->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());
    }

    /**
     * Delete episode viewing from episode.
     *
     * @return void
     */
    public function test_delete_episode_viewing_from_episode()
    {
        $this->actingAs($this->user);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->user->id,
        ]);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->user->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());
    }

    /**
     * Create or delete episode viewing without logged in user from episode.
     *
     * @return void
     */
    public function test_create_or_delete_episode_viewing_not_logged_in_user_from_episode()
    {
        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertUnauthorized();
    }
}
