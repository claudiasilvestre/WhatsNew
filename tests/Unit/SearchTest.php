<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Search with logged in user.
     *
     * @return void
     */
    public function test_search_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);
        
        $response = $this->getJson('/api/search/'.$user->nombre);

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('usuarios', 1, fn ($json) =>
                $json->where('nombre', $user->nombre)
                        ->etc()
            )->etc()
        );
    }

    /**
     * Search empty text.
     *
     * @return void
     */
    public function test_search_empty_text()
    {
        $response = $this->getJson('/api/search');

        $response->assertNotFound();
    }

    /**
     * Search without logged in user.
     *
     * @return void
     */
    public function test_search_not_logged_in_user()
    {
        $response = $this->getJson('/api/search/prueba');

        $response->assertUnauthorized();
    }
}
