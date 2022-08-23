<?php

namespace Tests\Unit;

use App\Models\TipoPersona;
use App\Models\TipoParticipante;
use App\Models\Persona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Participacion;
use App\Models\SeguimientoAudiovisual;
use App\Models\Proveedor;
use App\Models\ProveedorAudiovisual;
use App\Models\Valoracion;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AudiovisualTest extends TestCase
{
    use RefreshDatabase;

    protected $user, $tipoAudiovisual, $pelicula;
    
    /**
     * Set up the test
     */
    public function setUp(): void
    {
        parent::setUp();

        TipoPersona::factory()->create();
        $this->user = Persona::factory()->create();

        $this->tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Pelicula',
        ]);

        $this->pelicula = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $this->tipoAudiovisual->id,
            'titulo' => 'Kill Bill',
        ]);
    }

    /**
     * Get tv shows and movies with logged in user.
     *
     * @return void
     */
    public function test_get_tv_shows_and_movies_logged_in_user()
    {
        $this->actingAs($this->user);

        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Breaking Bad',
        ]);

        $response = $this->getJson('/api/audiovisuales');

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('peliculas', 1, fn ($json) =>
                        $json->where('id', strval($this->pelicula->id))
                             ->etc()
                        )->has('series', 1, fn ($json) =>
                            $json->where('id', strval($serie->id))
                                ->etc()
                        )
                 );
    }

    /**
     * Get tv shows and movies without logged in user.
     *
     * @return void
     */
    public function test_get_tv_shows_and_movies_not_logged_in_user()
    {
        $response = $this->getJson('/api/audiovisuales');

        $response->assertUnauthorized();
    }

    /**
     * Get audiovisual with logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_logged_in_user()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/audiovisuales/'.$this->pelicula->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                    ->first(fn ($json) =>
                        $json->where('id', strval($this->pelicula->id))
                            ->etc()
                    )
                 );
    }

    /**
     * Get audiovisual without logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_not_logged_in_user()
    {
        $response = $this->getJson('/api/audiovisuales/'.$this->pelicula->id);

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

        $tipoPersona = TipoPersona::create([
            'nombre' => 'Participante'
        ]);

        $tipoActor = TipoParticipante::create([
            'nombre' => 'Actor'
        ]);

        $actor = Persona::create([
            'tipoPersona_id' => $tipoPersona->id,
            'tipoParticipante_id' => $tipoActor->id,
            'nombre' => 'Uma Thurman'
        ]);

        Participacion::create([
            'persona_id' => $actor->id,
            'audiovisual_id' => $this->pelicula->id,
        ]);

        $response = $this->getJson('/api/audiovisuales-participacion/'.$actor->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has(1)
                    ->first(fn ($json) =>
                        $json->where('audiovisual_id', strval($this->pelicula->id))
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
        $tipoPersona = TipoPersona::create([
            'nombre' => 'Participante'
        ]);

        $tipoActor = TipoParticipante::create([
            'nombre' => 'Actor'
        ]);

        $actor = Persona::create([
            'tipoPersona_id' => $tipoPersona->id,
            'tipoParticipante_id' => $tipoActor->id,
            'nombre' => 'Uma Thurman'
        ]);

        $response = $this->getJson('/api/audiovisuales-participacion/'.$actor->id);

        $response->assertUnauthorized();
    }

    /**
     * Get audiovisual tracking with audiovisual tracking existing.
     *
     * @return void
     */
    public function test_get_audiovisual_tracking_with_audiovisual_tracking_existing()
    {
        $this->actingAs($this->user);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
            'estado' => 1,
        ]);

        $request = [
            'usuario_id' => $this->user->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(1, $response->getContent());
    }

    /**
     * Get audiovisual tracking without audiovisual tracking existing.
     *
     * @return void
     */
    public function test_get_audiovisual_tracking_without_audiovisual_tracking_existing()
    {
        $this->actingAs($this->user);

        $request = [
            'usuario_id' => $this->user->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(0, $response->getContent());
    }

    /**
     * Get audiovisual tracking without logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_tracking_not_logged_in_user()
    {
        $request = [
            'usuario_id' => $this->user->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-seguimiento-audiovisual', $request);

        $response->assertUnauthorized();
    }

    /**
     * Get providers with logged in user.
     *
     * @return void
     */
    public function test_get_providers_logged_in_user()
    {
        $this->actingAs($this->user);

        $proveedor = Proveedor::create([
            'nombre' => 'Amazon Prime Video'
        ]);

        ProveedorAudiovisual::create([
            'proveedor_id' => $proveedor->id,
            'audiovisual_id' => $this->pelicula->id,
            'disponibilidad' => 1,
        ]);

        ProveedorAudiovisual::create([
            'proveedor_id' => $proveedor->id,
            'audiovisual_id' => $this->pelicula->id,
            'disponibilidad' => 2,
        ]);

        ProveedorAudiovisual::create([
            'proveedor_id' => $proveedor->id,
            'audiovisual_id' => $this->pelicula->id,
            'disponibilidad' => 3,
        ]);

        $response = $this->getJson('/api/proveedores/'.$this->pelicula->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('stream', 1, fn ($json) =>
                        $json->where('proveedor_id', strval($proveedor->id))
                            ->etc()
                        )->has('alquilar', 1, fn ($json) =>
                        $json->where('proveedor_id', strval($proveedor->id))
                            ->etc()
                        )->has('comprar', 1, fn ($json) =>
                        $json->where('proveedor_id', strval($proveedor->id))
                            ->etc()
                        )
                 );
    }

    /**
     * Get providers without logged in user.
     *
     * @return void
     */
    public function test_get_providers_not_logged_in_user()
    {
        $response = $this->getJson('/api/proveedores/'.$this->pelicula->id);

        $response->assertUnauthorized();
    }

    /**
     * Get user colection with logged in user.
     *
     * @return void
     */
    public function test_get_user_colection_logged_in_user()
    {
        $this->actingAs($this->user);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
            'estado' => 3,
        ]);

        $tipoSerie = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 2,
            'tipoAudiovisual_id' => $tipoSerie->id,
            'titulo' => 'Breaking Bad',
        ]);

        SeguimientoAudiovisual::create([
            'audiovisual_id' => $serie->id,
            'persona_id' => $this->user->id,
            'estado' => 2,
        ]);

        $response = $this->getJson('/api/coleccion-usuario/'.$this->user->id);

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                    $json->has('todo', 2)
                         ->has('series', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($serie->id))
                                ->etc()
                         )->has('peliculas', 1, fn ($json) =>
                            $json->where('audiovisual_id', strval($this->pelicula->id))
                                ->etc()
                         )
                 );
    }

    /**
     * Get user colection without logged in user.
     *
     * @return void
     */
    public function test_get_user_colection_not_logged_in_user()
    {
        $response = $this->getJson('/api/coleccion-usuario/'.$this->user->id);

        $response->assertUnauthorized();
    }

    /**
     * Get audiovisual rating with audiovisual rating existing.
     *
     * @return void
     */
    public function test_get_audiovisual_rating_with_audiovisual_rating_existing()
    {
        $this->actingAs($this->user);

        Valoracion::create([
            'audiovisual_id' => $this->pelicula->id,
            'persona_id' => $this->user->id,
            'puntuacion' => 5,
        ]);

        $request = [
            'usuario_id' => $this->user->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-valoracion-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(5, $response->getContent());
    }

    /**
     * Get audiovisual rating without audiovisual rating existing.
     *
     * @return void
     */
    public function test_get_audiovisual_rating_without_audiovisual_rating_existing()
    {
        $this->actingAs($this->user);

        $request = [
            'usuario_id' => $this->user->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-valoracion-audiovisual', $request);

        $response->assertOk();
        $this->assertEquals(0, $response->getContent());
    }

    /**
     * Get audiovisual rating without logged in user.
     *
     * @return void
     */
    public function test_get_audiovisual_rating_not_logged_in_user()
    {
        $request = [
            'usuario_id' => $this->user->id,
            'audiovisual_id' => $this->pelicula->id,
        ];

        $response = $this->call('GET', '/api/saber-valoracion-audiovisual', $request);

        $response->assertUnauthorized();
    }
}
