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

    protected $usuario, $tipoAudiovisual, $serie, $temporada, $capitulo;
    
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
     * Obtiene capítulos por el ID de su temporada.
     *
     * @return void
     */
    public function test_obtener_capitulos()
    {
        $this->actingAs($this->usuario);
        
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
     * Intenta obtener capítulos por el ID de su temporada sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_capitulos_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/capitulos/'.$this->temporada->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene un capítulo por su ID.
     *
     * @return void
     */
    public function test_obtener_capitulo()
    {
        $this->actingAs($this->usuario);
        
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
     * Intenta obtener un capítulo por su ID sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_capitulo_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/capitulo/'.$this->capitulo->id);

        $response->assertUnauthorized();
    }

    /**
     * Comprueba que el único capítulo existente de la temporada se ha visualizado.
     *
     * @return void
     */
    public function test_comprobar_visualizacion_capitulos()
    {
        $this->actingAs($this->usuario);

        $capitulos = [];
        array_push($capitulos, $this->capitulo);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->usuario->id,
        ]);
        
        $request = [
            'capitulos' => $capitulos,
            'usuario_id' => $this->usuario->id,
        ];

        $response = $this->call('GET', '/api/visualizaciones', $request);

        $response->assertOk();
        $this->assertTrue($response->getData()[0]);
    }

    /**
     * Comprueba que el único capítulo existente de la temporada no se ha visualizado.
     *
     * @return void
     */
    public function test_comprobar_visualizacion_capitulos_inexistente()
    {
        $this->actingAs($this->usuario);

        $capitulos = [];
        array_push($capitulos, $this->capitulo);
        
        $request = [
            'capitulos' => $capitulos,
            'usuario_id' => $this->usuario->id,
        ];

        $response = $this->call('GET', '/api/visualizaciones', $request);

        $response->assertOk();
        $this->assertFalse($response->getData()[0]);
    }

    /**
     * Intenta comprobar la visualización del único capítulo de la temporada sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_comprobar_visualizacion_capitulos_usuario_sin_sesion_iniciada()
    {
        $capitulos = [];
        array_push($capitulos, $this->capitulo);
        
        $request = [
            'capitulos' => $capitulos,
            'usuario_id' => $this->usuario->id,
        ];

        $response = $this->call('GET', '/api/visualizaciones', $request);

        $response->assertUnauthorized();
    }

    /**
     * Crear visualización de capítulo, visualización de temporada y sus respectivas actividades.
     *
     * @return void
     */
    public function test_crear_visualizacion_capitulo_y_visualizacion_temporada()
    {
        $this->actingAs($this->usuario);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo2->id,
            'persona_id' => $this->usuario->id,
        ]);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->usuario->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                    ->where('capitulo_id', $this->capitulo->id)
                                    ->exists());

        $this->assertTrue(VisualizacionTemporada::where('persona_id', $this->usuario->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                    ->where('temporada_id', $this->temporada->id)
                                    ->exists());
    }

    /**
     * Crear visualización de capítulo y su respectiva actividad.
     *
     * @return void
     */
    public function test_crear_visualizacion_capitulo()
    {
        $this->actingAs($this->usuario);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertTrue(VisualizacionCapitulo::where('persona_id', $this->usuario->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertTrue(Actividad::where('persona_id', $this->usuario->id)
                                    ->where('capitulo_id', $this->capitulo->id)
                                    ->exists());

        $this->assertFalse(VisualizacionTemporada::where('persona_id', $this->usuario->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());

        $this->assertFalse(Actividad::where('persona_id', $this->usuario->id)
                                    ->where('temporada_id', $this->temporada->id)
                                    ->exists());
    }

    /**
     * Borrar visualización de capítulo y visualización de temporada.
     *
     * @return void
     */
    public function test_borrar_visualizacion_capitulo_y_visualizacion_temporada()
    {
        $this->actingAs($this->usuario);

        $capitulo2 = Capitulo::create([
            'temporada_id' => $this->temporada->id,
            'numero' => 2,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->usuario->id,
        ]);

        VisualizacionCapitulo::create([
            'capitulo_id' => $capitulo2->id,
            'persona_id' => $this->usuario->id,
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $this->temporada->id,
            'persona_id' => $this->usuario->id,
        ]);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->usuario->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());

        $this->assertFalse(VisualizacionTemporada::where('persona_id', $this->usuario->id)
                                                ->where('temporada_id', $this->temporada->id)
                                                ->exists());
    }

    /**
     * Borrar visualización de capítulo.
     *
     * @return void
     */
    public function test_borrar_visualizacion_capitulo()
    {
        $this->actingAs($this->usuario);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->usuario->id,
        ]);

        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertFalse(VisualizacionCapitulo::where('persona_id', $this->usuario->id)
                                                ->where('capitulo_id', $this->capitulo->id)
                                                ->exists());
    }

    /**
     * Intenta crear la visualización de capítulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_visualizacion_capitulo_usuario_sin_sesion_iniciada()
    {
        $response = $this->call('POST', '/api/visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene el capítulo anterior y el capítulo siguiente de un capítulo.
     *
     * @return void
     */
    public function test_anterior_siguiente_capitulo()
    {
        $this->actingAs($this->usuario);

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
     * Intenta obtener el capítulo anterior y el capítulo siguiente de un capítulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_anterior_siguiente_capitulo_usuario_sin_sesion_iniciada()
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
     * Comprueba que la visualización de capítulo existe para el usuario actual.
     *
     * @return void
     */
    public function test_visualizacion_capitulo_existe()
    {
        $this->actingAs($this->usuario);

        VisualizacionCapitulo::create([
            'capitulo_id' => $this->capitulo->id,
            'persona_id' => $this->usuario->id,
        ]);

        $response = $this->getJson('/api/saber-visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Comprueba que la visualización de capítulo no existe para el usuario actual.
     *
     * @return void
     */
    public function test_visualizacion_capitulo_no_existe()
    {
        $this->actingAs($this->usuario);

        $response = $this->getJson('/api/saber-visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Intenta comprobar visualización de capítulo sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_visualizacion_capitulo_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/saber-visualizacion-capitulo/'.$this->capitulo->id);

        $response->assertUnauthorized();
    }
}
