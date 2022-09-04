<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionTemporada;
use App\Models\Actividad;
use App\Models\VisualizacionCapitulo;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TemporadaTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario, $tipoAudiovisual, $serie;
    
    /**
     * Inicializa el test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->usuario = Persona::factory()->create();

        $this->tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $this->serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);
    }

    /**
     * Obtiene temporadas por el ID de su serie.
     *
     * @return void
     */
    public function test_obtener_temporadas()
    {
        $this->actingAs($this->usuario);

        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);
        
        $response = $this->getJson('/api/temporadas/'.$this->serie->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('id', $temporada->id)
                                 ->etc()
                         )
                 );
    }

    /**
     * Obtiene temporadas por el ID de su serie sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_temporadas_usuario_sin_sesion_iniciada()
    { 
        $response = $this->getJson('/api/temporadas/'.$this->serie->id);

        $response->assertUnauthorized();
    }

    /**
     * Comprueba que la visualización de temporada existe para el usuario actual.
     *
     * @return void
     */
    public function test_visualizacion_temporada_existe()
    {
        $this->actingAs($this->usuario);

        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $temporada->id,
            'persona_id' => $this->usuario->id,
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'temporada_id' => $temporada->id,
        ];
        
        $response = $this->call('GET', '/api/saber-visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Comprueba que la visualización de temporada no existe para el usuario actual.
     *
     * @return void
     */
    public function test_visualizacion_temporada_no_existe()
    {
        $this->actingAs($this->usuario);

        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'temporada_id' => $temporada->id,
        ];
        
        $response = $this->call('GET', '/api/saber-visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Comprueba visualización de temporada sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_visualizacion_temporada_usuario_sin_sesion_iniciada()
    {
        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $request = [
            'usuario_id' => $this->usuario->id,
            'temporada_id' => $temporada->id,
        ];

        $response = $this->getJson('/api/saber-visualizacion-temporada', $request);

        $response->assertUnauthorized();
    }

    /**
     * Crea visualización de temporada y actividad de esta y visualización de los capítulos de la temporada 
     * del usuario actual.
     *
     * @return void
     */
    public function test_crear_visualizacion_temporada()
    {
        $this->actingAs($this->usuario);

        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
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
            'usuario_id' => $this->usuario->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];
        
        $response = $this->call('POST', '/api/visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertTrue(VisualizacionTemporada::where('temporada_id', $temporada->id)
                                                ->where('persona_id', $this->usuario->id)
                                                ->exists());
        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                    ->where('tipo', 3)
                                    ->where('temporada_id', $temporada->id)
                                    ->exists());
        $this->assertTrue(VisualizacionCapitulo::where('capitulo_id', $capitulo->id)
                                                ->where('persona_id', $this->usuario->id)
                                                ->exists());
    }

    /**
     * Borra visualización de temporada y de sus capítulos del usuario actual.
     *
     * @return void
     */
    public function test_borrar_visualizacion_temporada()
    {
        $this->actingAs($this->usuario);

        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $visualizacionTemporada = VisualizacionTemporada::create([
            'temporada_id' => $temporada->id,
            'persona_id' => $this->usuario->id,
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $visualizacionCapitulo = VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo->id,
            'persona_id' => $this->usuario->id,
        ]);

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $this->usuario->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];
        
        $response = $this->call('POST', '/api/visualizacion-temporada', $request);

        $response->assertOk();
        $this->assertFalse(VisualizacionTemporada::where('id', $visualizacionTemporada->id)
                                                ->exists());
        $this->assertFalse(VisualizacionCapitulo::where('id', $visualizacionCapitulo->id)
                                                ->exists());
    }

    /**
     * Crea o borra visualización de temporada y de sus capítulos sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_o_borrar_visualizacion_temporada_usuario_sin_sesion_iniciada()
    {
        $temporada = Temporada::create([
            'audiovisual_id' => $this->serie->id,
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
            'usuario_id' => $this->usuario->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];

        $response = $this->postJson('/api/visualizacion-temporada', $request);

        $response->assertUnauthorized();
    }
}
