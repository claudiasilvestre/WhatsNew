<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoParticipante;
use App\Models\Participacion;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
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
}
