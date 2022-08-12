<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
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
        
        $response = $this->get('/api/actividad-usuario/'.$user->id);

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

        $response = $this->get('/api/actividad-usuario/'.$user->id);

        $response->assertStatus(401);
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
        
        $response = $this->get('/api/actividad-amigos/');

        $response->assertOk();
    }

    /**
     * Friends activity without logged in user.
     *
     * @return void
     */
    public function test_friends_activity_not_logged_in_user()
    {
        TipoPersona::factory()->create();

        $user = Persona::factory()->create();

        $response = $this->get('/api/actividad-amigos/');

        $response->assertStatus(401);
    }
}
