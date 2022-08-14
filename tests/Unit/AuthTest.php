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
     * Set up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
    }

    /**
     * Insert user in the database.
     *
     * @return void
     */
    public function test_insert_user()
    {
        $user = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/register', $user);

        $response->assertOk();
        $this->assertTrue(Persona::where('usuario', $user['usuario'])->exists());
    }

    /**
     * Insert duplicate user in the database.
     *
     * @return void
     */
    public function test_insert_duplicate_user()
    {
        $user = [
            'nombre' => 'Claudia',
            'usuario' => 'claudia',
            'email' => 'claudiasilvestre98@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->postJson('/api/register', $user);

        $response = $this->postJson('/api/register', $user);

        $response->assertUnprocessable();
    }

    /**
     * Success login user.
     *
     * @return void
     */
    public function test_success_login()
    {
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

        $response = $this->postJson('/api/login', $user);

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

        $response = $this->postJson('/api/login', $user);

        $response->assertUnprocessable();
    }

    /**
     * Success logout user.
     *
     * @return void
     */
    public function test_success_logout()
    {
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/api/logout');

        $response->assertOk();
        $response->assertCookieExpired('jwt');
    }
}
