<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoParticipante;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusquedaTest extends TestCase
{
    use RefreshDatabase;

    protected $usuario, $tipoPersona, $tipoActor, $actor;
    
    /**
     * Inicializa el test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->usuario = Persona::factory()->create();

        $this->tipoPersona = TipoPersona::create([
            'nombre' => 'Participante'
        ]);

        $this->tipoActor = TipoParticipante::create([
            'nombre' => 'Actor'
        ]);

        $this->actor = Persona::create([
            'tipoPersona_id' => $this->tipoPersona->id,
            'tipoParticipante_id' => $this->tipoActor->id,
            'nombre' => 'Aaron Paul'
        ]);
    }
    
    /**
     * Búsqueda de usuario por su nombre.
     *
     * @return void
     */
    public function test_busqueda()
    {
        $this->actingAs($this->usuario);
        
        $response = $this->getJson('/api/busqueda/'.$this->usuario->nombre);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('usuarios', 1, fn ($json) =>
                        $json->where('nombre', $this->usuario->nombre)
                             ->etc()
                    )->etc()
                 );
    }

    /**
     * Búsqueda con texto vacío.
     *
     * @return void
     */
    public function test_busqueda_texto_vacio()
    {
        $this->actingAs($this->usuario);

        $response = $this->getJson('/api/busqueda/');

        $response->assertNotFound();
    }

    /**
     * Intenta realizar una búsqueda sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_busqueda_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/busqueda/prueba');

        $response->assertUnauthorized();
    }
}
