<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\Actividad;
use App\Models\SeguimientoPersona;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActividadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User activity with logged in user.
     *
     * @return void
     */
    public function test_user_activity_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);
        
        Actividad::create([
            'persona_id' => $user->id,
            'tipo' => 1,
        ]);
        
        $response = $this->getJson('/api/actividad-usuario/'.$user->id);

        $response->assertOk();
    }

    /**
     * User activity without logged in user.
     *
     * @return void
     */
    public function test_user_activity_not_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();

        $response = $this->getJson('/api/actividad-usuario/'.$user->id);

        $response->assertUnauthorized();
    }

    /**
     * Friends activity with logged in user.
     *
     * @return void
     */
    public function test_friends_activity_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $friend = Persona::factory()->create();

        SeguimientoPersona::create([
            'personaActual_id' => $user->id,
            'persona_id' => $friend->id,
        ]);

        $activity = Actividad::create([
            'persona_id' => $friend->id,
            'tipo' => 1,
        ]);
    
        $response = $this->getJson('/api/actividad-amigos/');

        $response->assertOk();
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
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $activity = Actividad::create([
            'persona_id' => $user->id,
            'tipo' => 1,
        ]);

        $response = $this->postJson('/api/borrar-actividad/'.$activity->id);

        $response->assertOk();
    }

    /**
     * Delete activity without logged in user.
     *
     * @return void
     */
    public function test_delete_activity_not_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();

        $activity = Actividad::create([
            'persona_id' => $user->id,
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
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/borrar-actividad/1');

        $response->assertOk();
    }
}
