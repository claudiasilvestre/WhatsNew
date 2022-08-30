<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\Actividad;
use App\Models\SeguimientoPersona;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActividadTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario;
    
    /**
     * Inicializa el test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->usuario = Persona::factory()->create();
    }

    /**
     * Obtiene la actividad de un usuario por su ID.
     *
     * @return void
     */
    public function test_obtener_actividad_usuario()
    {
        $this->actingAs($this->usuario);
        
        Actividad::create([
            'persona_id' => $this->usuario->id,
            'tipo' => 1,
        ]);
        
        $response = $this->getJson('/api/actividad-usuario/'.$this->usuario->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('usuario_id', strval($this->usuario->id))
                                 ->etc()
                         )
                 );
    }

    /**
     * Obtiene la actividad de un usuario por su ID sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_actividad_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/actividad-usuario/'.$this->usuario->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene la actividad de los amigos del usuario actual.
     *
     * @return void
     */
    public function test_obtener_actividad_amigos_usuario_actual()
    {
        $this->actingAs($this->usuario);

        $friend = Persona::factory()->create();

        SeguimientoPersona::create([
            'personaActual_id' => $this->usuario->id,
            'persona_id' => $friend->id,
        ]);

        $activity = Actividad::create([
            'persona_id' => $friend->id,
            'tipo' => 1,
        ]);
    
        $response = $this->getJson('/api/actividad-amigos/');

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('usuario_id', strval($friend->id))
                                 ->etc()
                         )
                 );
    }

    /**
     * Obtiene la actividad de los amigos del usuario actual sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_actividad_amigos_usuario_actual_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/actividad-amigos/');

        $response->assertUnauthorized();
    }

    /**
     * Borra una actividad del usuario actual por su ID.
     *
     * @return void
     */
    public function test_borrar_actividad()
    {
        $this->actingAs($this->usuario);

        $activity = Actividad::create([
            'persona_id' => $this->usuario->id,
            'tipo' => 1,
        ]);

        $response = $this->postJson('/api/borrar-actividad/'.$activity->id);

        $response->assertOk();
        $this->assertTrue(!Actividad::where('persona_id', $this->usuario->id)->exists());
    }

    /**
     * Borra una actividad que no existe.
     *
     * @return void
     */
    public function test_borrar_actividad_inexistente()
    {
        $this->actingAs($this->usuario);

        $response = $this->postJson('/api/borrar-actividad/1');

        $response->assertOk();
    }

    /**
     * Borra una actividad por su ID sin que el usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_borrar_actividad_usuario_sin_sesion_iniciada()
    {
        $activity = Actividad::create([
            'persona_id' => $this->usuario->id,
            'tipo' => 1,
        ]);

        $response = $this->postJson('/api/borrar-actividad/'.$activity->id);

        $response->assertUnauthorized();
    }
}
