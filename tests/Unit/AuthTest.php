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

        $response->assertOk();
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

    /**
     * Success login user.
     *
     * @return void
     */
    public function test_success_login()
    {
        TipoPersona::factory()->create();

        Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $user = [
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
        ];

        $response = $this->post('/api/login', $user);

        $response->assertOk();
        $response->assertCookie('jwt');
    }

    /**
     * Login user fail.
     *
     * @return void
     */
    public function test_login_fail()
    {
        TipoPersona::factory()->create();

        Persona::factory()->create([
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '$2y$10$6kNsORbwNXD1SyN8E6uHK.zITd80IYwFj1vikDr6zR1szG1uot6OG',
        ]);

        $user = [
            'email' => 'claudiasilvestre9@gmail.com',
            'password' => '12345678',
        ];

        $response = $this->post('/api/login', $user);

        $response->assertStatus(302);
    }

    /**
     * Success logout user.
     *
     * @return void
     */
    public function test_success_logout()
    {
        TipoPersona::factory()->create();

        $user = Persona::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/api/logout');

        $response->assertCookieExpired('jwt');
    }
}
