<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use Tests\TestCase;
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
        
        $response = $this->get('/api/search/prueba');

        $response->assertOk();
    }

    /**
     * Search empty text.
     *
     * @return void
     */
    public function test_search_empty_text()
    {
        $response = $this->get('/api/search');

        $response->assertNotFound();
    }

    /**
     * Search without logged in user.
     *
     * @return void
     */
    public function test_search_not_logged_in_user()
    {
        $response = $this->get('/api/search/prueba');

        $response->assertUnauthorized();
    }
}
