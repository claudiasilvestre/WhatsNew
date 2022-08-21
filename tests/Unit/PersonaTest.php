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
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonaTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    
    /**
     * Set up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->user = Persona::factory()->create();
    }

    /**
     * Get person with logged in user.
     *
     * @return void
     */
    public function test_get_person_logged_in_user()
    {
        $this->actingAs($this->user);
        
        $response = $this->getJson('/api/personas/'.$this->user->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('id', $this->user->id)
                                 ->etc()
                         )
                 );
    }

    /**
     * Get person without logged in user.
     *
     * @return void
     */
    public function test_get_person_not_logged_in_user()
    {
        $response = $this->getJson('/api/personas/'.$this->user->id);

        $response->assertUnauthorized();
    }

    /**
     * Get participation with logged in user.
     *
     * @return void
     */
    public function test_get_participation_logged_in_user()
    {
        $this->actingAs($this->user);

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
     * Get participation without logged in user.
     *
     * @return void
     */
    public function test_get_participation_not_logged_in_user()
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
     * Get person info with logged in user.
     *
     * @return void
     */
    public function test_get_person_info_logged_in_user()
    {
        $this->actingAs($this->user);
        
        $response = $this->getJson('/api/info-usuario/'.$this->user->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(4)
                         ->where('usuario', $this->user->usuario)
                         ->etc()
                 );
    }

    /**
     * Get person info without logged in user.
     *
     * @return void
     */
    public function test_get_person_info_not_logged_in_user()
    {
        $response = $this->getJson('/api/info-usuario/'.$this->user->id);

        $response->assertUnauthorized();
    }

    /**
     * Change information user profile with logged in user.
     *
     * @return void
     */
    public function test_change_information_user_profile_logged_in_user()
    {
        $this->actingAs($this->user);

        $request = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => $this->user->email,
        ];
        
        $response = $this->postJson('/api/guardar-informacion', $request);

        $response->assertOk();
        $this->assertTrue(Persona::where('nombre', 'Claudia')->where('usuario', 'claudia')->where('email', $this->user->email)->exists());
    }

    /**
     * Change information user profile without logged in user.
     *
     * @return void
     */
    public function test_change_information_user_profile_not_logged_in_user()
    {
        $request = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => $this->user->email,
        ];
        
        $response = $this->postJson('/api/guardar-informacion', $request);

        $response->assertUnauthorized();
    }

    /**
     * Change password with logged in user.
     *
     * @return void
     */
    public function test_change_password_logged_in_user()
    {
        $this->actingAs($this->user);

        $request = [
            'current_password' => '12345678',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];
        
        $response = $this->putJson('/api/guardar-password', $request);

        $response->assertOk();
    }

    /**
     * Change password with wrong current password.
     *
     * @return void
     */
    public function test_change_password_with_wrong_current_password()
    {
        $this->actingAs($this->user);

        $request = [
            'current_password' => '12345679',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];
        
        $response = $this->putJson('/api/guardar-password', $request);

        $response->assertUnprocessable();
    }

    /**
     * Change password without logged in user.
     *
     * @return void
     */
    public function test_change_password_not_logged_in_user()
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
     * Get user following with logged in user.
     *
     * @return void
     */
    public function test_get_user_following_logged_in_user()
    {
        $this->actingAs($this->user);

        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->user->id,
            'persona_id' => $user2->id,
        ]);

        $request = [
            'usuarioActual_id' => $this->user->id,
            'usuario_id' => $user2->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertTrue($response->original);
    }

    /**
     * Get user following without user following existing.
     *
     * @return void
     */
    public function test_get_user_following_and_user_following_not_existing()
    {
        $this->actingAs($this->user);

        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->user->id,
            'usuario_id' => $user2->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertFalse($response->original);
    }

    /**
     * Get user following without logged in user.
     *
     * @return void
     */
    public function test_get_user_following_not_logged_in_user()
    {
        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->user->id,
            'usuario_id' => $user2->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-usuario', $request);

        $response->assertUnauthorized();
    }

    /**
     * Create user following.
     *
     * @return void
     */
    public function test_user_following()
    {
        $this->actingAs($this->user);

        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->user->id,
            'usuario_id' => $user2->id,
        ];

        $response = $this->postJson('/api/seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertTrue(SeguimientoPersona::where('personaActual_id', $this->user->id)->where('persona_id', $user2->id)->exists());
    }

    /**
     * Delete user following because user following exists.
     *
     * @return void
     */
    public function test_user_following_existing()
    {
        $this->actingAs($this->user);

        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->user->id,
            'persona_id' => $user2->id,
        ]);

        $request = [
            'usuarioActual_id' => $this->user->id,
            'usuario_id' => $user2->id,
        ];

        $response = $this->postJson('/api/seguimiento-usuario', $request);

        $response->assertOk();
        $this->assertFalse(SeguimientoPersona::where('personaActual_id', $this->user->id)->where('persona_id', $user2->id)->exists());
    }

    /**
     * Create or delete user following without logged in user.
     *
     * @return void
     */
    public function test_create_or_delete_user_following_not_logged_in_user()
    {
        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $request = [
            'usuarioActual_id' => $this->user->id,
            'usuario_id' => $user2->id,
        ];

        $response = $this->postJson('/api/seguimiento-usuario', $request);

        $response->assertUnauthorized();
    }

    /**
     * Get following.
     *
     * @return void
     */
    public function test_get_following()
    {
        $this->actingAs($this->user);

        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->user->id,
            'persona_id' => $user2->id,
        ]);

        $response = $this->getJson('/api/siguiendo/'.$this->user->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('siguiendo', 1, fn ($json) =>
                        $json->where('id', strval($user2->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Get following without logged in user.
     *
     * @return void
     */
    public function test_get_following_not_logged_in_user()
    {
        $response = $this->getJson('/api/siguiendo/'.$this->user->id);

        $response->assertUnauthorized();
    }

    /**
     * Get followers.
     *
     * @return void
     */
    public function test_get_followers()
    {
        $this->actingAs($this->user);

        $user2 = Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        SeguimientoPersona::create([
            'personaActual_id' => $this->user->id,
            'persona_id' => $user2->id,
        ]);

        $response = $this->getJson('/api/seguidores/'.$user2->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('seguidores', 1, fn ($json) =>
                        $json->where('id', strval($this->user->id))
                            ->etc()
                        )->etc()
                 );
    }

    /**
     * Get followers without logged in user.
     *
     * @return void
     */
    public function test_get_followers_not_logged_in_user()
    {
        $response = $this->getJson('/api/seguidores/'.$this->user->id);

        $response->assertUnauthorized();
    }
}
