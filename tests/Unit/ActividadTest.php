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
     * User activity with logged in user.
     *
     * @return void
     */
    public function test_user_activity_logged_in_user()
    {
        $this->actingAs($this->user);
        
        Actividad::create([
            'persona_id' => $this->user->id,
            'tipo' => 1,
        ]);
        
        $response = $this->getJson('/api/actividad-usuario/'.$this->user->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                         ->first(fn ($json) =>
                            $json->where('usuario_id', strval($this->user->id))
                                 ->etc()
                         )
                 );
    }

    /**
     * User activity without logged in user.
     *
     * @return void
     */
    public function test_user_activity_not_logged_in_user()
    {
        $response = $this->getJson('/api/actividad-usuario/'.$this->user->id);

        $response->assertUnauthorized();
    }

    /**
     * Friends activity with logged in user.
     *
     * @return void
     */
    public function test_friends_activity_logged_in_user()
    {
        $this->actingAs($this->user);

        $friend = Persona::factory()->create();

        SeguimientoPersona::create([
            'personaActual_id' => $this->user->id,
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
     * Friends activity without logged in user.
     *
     * @return void
     */
    public function test_friends_activity_not_logged_in_user()
    {
        $response = $this->getJson('/api/actividad-amigos/');

        $response->assertUnauthorized();
    }

    /**
     * Delete activity with logged in user.
     *
     * @return void
     */
    public function test_delete_activity_logged_in_user()
    {
        $this->actingAs($this->user);

        $activity = Actividad::create([
            'persona_id' => $this->user->id,
            'tipo' => 1,
        ]);

        $response = $this->postJson('/api/borrar-actividad/'.$activity->id);

        $response->assertOk();
        $this->assertTrue(!Actividad::where('persona_id', $this->user->id)->exists());
    }

    /**
     * Delete activity without logged in user.
     *
     * @return void
     */
    public function test_delete_activity_not_logged_in_user()
    {
        $activity = Actividad::create([
            'persona_id' => $this->user->id,
            'tipo' => 1,
        ]);

        $response = $this->postJson('/api/borrar-actividad/'.$activity->id);

        $response->assertUnauthorized();
    }

    /**
     * Delete activity that doesn't exist.
     *
     * @return void
     */
    public function test_delete_activity_that_not_exist()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/borrar-actividad/1');

        $response->assertOk();
    }
}
