<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Inicializa el test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
    }

    /**
     * Inicio de sesión con credenciales correctas.
     *
     * @return void
     */
    public function test_inicio_sesion_correcto()
    {
        Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $usuario = [
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
        ];

        $response = $this->postJson('/api/inicio-sesion', $usuario);

        $response->assertOk()
                 ->assertCookie('jwt');
    }

    /**
     * Inicio de sesión con credenciales incorrectas.
     *
     * @return void
     */
    public function test_inicio_sesion_incorrecto()
    {
        Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $usuario = [
            'email' => 'claudiasilvestre9@gmail.com',
            'password' => '12345678',
        ];

        $response = $this->postJson('/api/inicio-sesion', $usuario);

        $response->assertUnprocessable();
    }

    /**
     * Devuelve el usuario actual.
     *
     * @return void
     */
    public function test_obtener_usuario_actual()
    {
        $usuario = Persona::factory()->create();
        $this->actingAs($usuario);

        $response = $this->getJson('/api/usuario');

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(7)
                         ->where('id', $usuario->id)
                         ->etc()
                 );
    }

    /**
     * Intenta devolver el usuario actual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_usuario_actual_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/usuario');

        $response->assertUnauthorized();
    }

    /**
     * Cierra la sesión del usuario actual.
     *
     * @return void
     */
    public function test_cierre_sesion()
    {
        $usuario = Persona::factory()->create();
        $this->actingAs($usuario);

        $response = $this->post('/api/cierre-sesion');

        $response->assertOk()
                 ->assertCookieExpired('jwt');
    }
}
