<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionCapitulo;
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
     * Get viewings with logged in user.
     *
     * @return void
     */
    public function test_get_viewings_logged_in_user()
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
}
