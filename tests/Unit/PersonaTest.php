<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoParticipante;
use App\Models\Participacion;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\SeguimientoPersona;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonaTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    
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
     * Crea un usuario.
     *
     * @return void
     */
    public function test_crear_usuario()
    {
        $user = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/registro', $user);

        $response->assertOk();
        $this->assertTrue(Persona::where('usuario', $user['usuario'])->exists());
    }

    /**
     * Crea un usuario duplicado.
     *
     * @return void
     */
    public function test_crear_usuario_duplicado()
    {
        $user = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/registro', $user);

        $response = $this->postJson('/api/registro', $user);

        $response->assertUnprocessable();
    }

    /**
     * Obtiene una persona por su ID.
     *
     * @return void
     */
    public function test_obtener_persona()
    {
        $this->actingAs($this->usuario);
        
        $response = $this->getJson('/api/personas/'.$this->usuario->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('id', $this->usuario->id)
                                 ->etc()
                         )
                 );
    }

    /**
     * Obtiene una persona por su ID sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_persona_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/personas/'.$this->usuario->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene los participantes de un audiovisual por el ID del audiovisual.
     *
     * @return void
     */
    public function test_obtener_participantes_audiovisual()
    {
        $this->actingAs($this->usuario);

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $tipoPersona = TipoPersona::create([
            'nombre' => 'Participante'
        ]);

        $tipoActor = TipoParticipante::create([
            'nombre' => 'Actor'
        ]);

        $tipoDirector = TipoParticipante::create([
            'nombre' => 'Director'
        ]);

        $actor = Persona::create([
            'tipoPersona_id' => $tipoPersona->id,
            'tipoParticipante_id' => $tipoActor->id,
            'nombre' => 'Aaron Paul'
        ]);

        $director = Persona::create([
            'tipoPersona_id' => $tipoPersona->id,
            'tipoParticipante_id' => $tipoDirector->id,
            'nombre' => 'Vince Gilligan'
        ]);

        Participacion::create([
            'audiovisual_id' => $serie->id,
            'persona_id' => $actor->id,
        ]);

        Participacion::create([
            'audiovisual_id' => $serie->id,
            'persona_id' => $director->id,
        ]);
        
        $response = $this->getJson('/api/personas-participacion/'.$serie->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('personas_reparto', 1, fn ($json) =>
                        $json->where('persona_id', strval($actor->id))
                            ->etc()
                        )->has('personas_equipo', 1, fn ($json) =>
                            $json->where('persona_id', strval($director->id))
                                ->etc()
                        )
                 );
    }

    /**
     * Obtiene los participantes de un audiovisual por el ID del audiovisual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_participantes_audiovisual_usuario_sin_sesion_iniciada()
    {
        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $response = $this->getJson('/api/personas-participacion/'.$serie->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene la información de un usuario
     *
     * @return void
     */
    public function test_obtener_informacion_usuario()
    {
        $this->actingAs($this->usuario);
        
        $response = $this->getJson('/api/info-usuario/'.$this->usuario->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(4)
                         ->where('usuario', $this->usuario->usuario)
                         ->etc()
                 );
    }

    /**
     * Obtiene la información de un usuario sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_informacion_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/info-usuario/'.$this->usuario->id);

        $response->assertUnauthorized();
    }

    /**
     * Cambia información del usuario actual sin cambiar la foto de perfil.
     *
     * @return void
     */
    public function test_cambiar_informacion_usuario_actual_sin_cambiar_foto_perfil()
    {
        $this->actingAs($this->usuario);

        $request = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => $this->usuario->email,
        ];
        
        $response = $this->postJson('/api/guardar-informacion', $request);

        $response->assertOk();
        $this->assertTrue(Persona::where('nombre', 'Claudia')
                                 ->where('usuario', 'claudia')
                                 ->where('email', $this->usuario->email)
                                 ->exists());
    }

    /**
     * Cambia información del usuario actual.
     *
     * @return void
     */
    public function test_cambiar_informacion_usuario_actual()
    {
        $this->actingAs($this->usuario);

        $request = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => $this->usuario->email,
            'imagen' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        
        $response = $this->postJson('/api/guardar-informacion', $request);

        $response->assertOk();
        $this->assertTrue(Persona::where('nombre', 'Claudia')
                                 ->where('usuario', 'claudia')
                                 ->where('email', $this->usuario->email)
                                 ->exists());
    }

    /**
     * Cambia información del usuario actual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_cambiar_informacion_usuario_actual_sin_sesion_iniciada()
    {
        $request = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => $this->usuario->email,
        ];
        
        $response = $this->postJson('/api/guardar-informacion', $request);

        $response->assertUnauthorized();
    }

    /**
     * Cambia la contraseña del usuario actual.
     *
     * @return void
     */
    public function test_cambiar_contraseña_usuario_actual()
    {
        $this->actingAs($this->usuario);

        $request = [
            'current_password' => '12345678',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];
        
        $response = $this->putJson('/api/guardar-password', $request);

        $response->assertOk();
    }

    /**
     * Cambia la contraseña del usuario actual por una no válida.
     *
     * @return void
     */
    public function test_cambiar_contraseña_usuario_actual_por_una_no_valida()
    {
        $this->actingAs($this->usuario);

        $request = [
            'current_password' => '12345679',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];
        
        $response = $this->putJson('/api/guardar-password', $request);

        $response->assertUnprocessable();
    }

    /**
     * Cambia la contraseña del usuario actual sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_cambiar_contraseña_usuario_actual_sin_sesion_iniciada()
    {
        $request = [
            'current_password' => '12345678',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];
        
        $response = $this->putJson('/api/guardar-password', $request);

        $response->assertUnauthorized();
    }

    /**
     * Comprueba que el usuario actual sigue al usuario proporcionado.
     *
     * @return void
     */
    public function test_usuario_actual_sigue_al_usuario()
    {
        $this->actingAs($this->usuario);

        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->usuario->id,
            'persona_id' => $usuario2->id,
        ]);

        $request = [
            'usuarioActual_id' => $this->usuario->id,
            'usuario_id' => $usuario2->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Comprueba que el usuario actual no sigue al usuario proporcionado.
     *
     * @return void
     */
    public function test_usuario_actual_no_sigue_al_usuario()
    {
        $this->actingAs($this->usuario);

        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->usuario->id,
            'usuario_id' => $usuario2->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Comprueba si el usuario actual sigue al usuario proporcionado sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_saber_seguimiento_usuario_sin_sesion_iniciada()
    {
        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->usuario->id,
            'usuario_id' => $usuario2->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-usuario', $request);

        $response->assertUnauthorized();
    }

    /**
     * Borra un seguimiento de usuario porque el seguimiento ya existe.
     *
     * @return void
     */
    public function test_borrar_seguimiento_usuario()
    {
        $this->actingAs($this->usuario);

        Persona::where('id', $this->usuario->id)->update(['seguidos' => 1]);

        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
            'seguidores' => 1,
            'puntos' => 5,
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->usuario->id,
            'persona_id' => $usuario2->id,
        ]);

        $request = [
            'usuarioActual_id' => $this->usuario->id,
            'usuario_id' => $usuario2->id,
        ];

        $response = $this->postJson('/api/seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertFalse(SeguimientoPersona::where('personaActual_id', $this->usuario->id)->where('persona_id', $usuario2->id)->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)->where('seguidos', 0)->exists());
        $this->assertTrue(Persona::where('id', $usuario2->id)->where('seguidores', 0)->where('puntos', 0)->exists());
    }

    /**
     * Crea un seguimiento de usuario.
     *
     * @return void
     */
    public function test_crear_seguimiento_usuario()
    {
        $this->actingAs($this->usuario);

        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->usuario->id,
            'usuario_id' => $usuario2->id,
        ];

        $response = $this->postJson('/api/seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertTrue(SeguimientoPersona::where('personaActual_id', $this->usuario->id)->where('persona_id', $usuario2->id)->exists());
        $this->assertTrue(Persona::where('id', $this->usuario->id)->where('seguidos', 1)->exists());
        $this->assertTrue(Persona::where('id', $usuario2->id)->where('seguidores', 1)->where('puntos', 5)->exists());
    }

    /**
     * Crea o borra un seguimiento de usuario sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_crear_o_borrar_seguimiento_usuario_sin_sesion_iniciada()
    {
        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->usuario->id,
            'usuario_id' => $usuario2->id,
        ];

        $response = $this->postJson('/api/seguimiento-usuario', $request);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene los usuarios que sigue el usuario.
     *
     * @return void
     */
    public function test_obtener_usuarios_seguidos()
    {
        $this->actingAs($this->usuario);

        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->usuario->id,
            'persona_id' => $usuario2->id,
        ]);

        $response = $this->getJson('/api/siguiendo/'.$this->usuario->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('siguiendo', 1, fn ($json) =>
                        $json->where('id', strval($usuario2->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Obtiene los usuarios que sigue el usuario sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_usuarios_seguidos_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/siguiendo/'.$this->usuario->id);

        $response->assertUnauthorized();
    }

    /**
     * Obtiene los seguidores de un usuario.
     *
     * @return void
     */
    public function test_obtener_seguidores_usuario()
    {
        $this->actingAs($this->usuario);

        $usuario2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->usuario->id,
            'persona_id' => $usuario2->id,
        ]);

        $response = $this->getJson('/api/seguidores/'.$usuario2->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('seguidores', 1, fn ($json) =>
                        $json->where('id', strval($this->usuario->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Obtiene los seguidores de un usuario sin que un usuario tenga iniciada la sesión.
     *
     * @return void
     */
    public function test_obtener_seguidores_usuario_sin_sesion_iniciada()
    {
        $response = $this->getJson('/api/seguidores/'.$this->usuario->id);

        $response->assertUnauthorized();
    }
}
