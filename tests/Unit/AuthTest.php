<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Insert user in the database.
     *
     * @return void
     */
    public function test_insert_user()
    {
        TipoPersona::factory()->create();

        $user = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->post('/api/register', $user);

        $response->assertStatus(200);
    }

    /**
     * Insert duplicate user in the database.
     *
     * @return void
     */
    public function test_insert_duplicate_user()
    {
        TipoPersona::factory()->create();

        $user = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->post('/api/register', $user);

        $response = $this->post('/api/register', $user);

        $response->assertStatus(302);
    }
}
