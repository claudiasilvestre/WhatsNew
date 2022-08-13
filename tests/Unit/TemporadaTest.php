<?php

namespace Tests\Unit;

use App\Models\Persona;
use App\Models\TipoPersona;
use App\Models\TipoAudiovisual;
use App\Models\Audiovisual;
use App\Models\Temporada;
use App\Models\Capitulo;
use App\Models\VisualizacionTemporada;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TemporadaTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Get season with logged in user.
     *
     * @return void
     */
    public function test_get_season_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);
        
        $response = $this->getJson('/api/temporadas/'.$serie->id);

        $response->assertOk();
    }

    /**
     * Get season without logged in user.
     *
     * @return void
     */
    public function test_get_season_not_logged_in_user()
    {
        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);
        
        $response = $this->getJson('/api/temporadas/'.$serie->id);

        $response->assertUnauthorized();
    }

    /**
     * Viewed season and viewed season exists.
     *
     * @return void
     */
    public function test_viewed_season_exists()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $temporada->id,
            'persona_id' => $user->id,
        ]);

        $request = [
            'usuario_id' => $user->id,
            'temporada_id' => $temporada->id,
        ];
        
        $response = $this->getJson('/api/saber-visualizacion-temporada', $request);

        $response->assertOk();
    }

    /**
     * Viewed season and viewed season doesn't exist.
     *
     * @return void
     */
    public function test_viewed_season_not_existing()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $request = [
            'usuario_id' => $user->id,
            'temporada_id' => $temporada->id,
        ];
        
        $response = $this->getJson('/api/saber-visualizacion-temporada', $request);

        $response->assertOk();
    }

    /**
     * Viewed season without logged in user.
     *
     * @return void
     */
    public function test_viewed_season_not_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $request = [
            'usuario_id' => $user->id,
            'temporada_id' => $temporada->id,
        ];

        $response = $this->getJson('/api/saber-visualizacion-temporada', $request);

        $response->assertUnauthorized();
    }

    /**
     * Viewing and viewed season exists.
     *
     * @return void
     */
    public function test_viewing_and_season_exists()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        VisualizacionTemporada::create([
            'temporada_id' => $temporada->id,
            'persona_id' => $user->id,
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $user->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];
        
        $response = $this->postJson('/api/visualizacion-temporada', $request);

        $response->assertOk();
    }

    /**
     * Viewing and viewed season doesn't exist.
     *
     * @return void
     */
    public function test_viewing_and_season_not_existing()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();
        $this->actingAs($user);

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $user->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];
        
        $response = $this->postJson('/api/visualizacion-temporada', $request);

        $response->assertOk();
    }

    /**
     * Viewing without logged in user.
     *
     * @return void
     */
    public function test_viewing_and_not_logged_in_user()
    {
        TipoPersona::factory()->create();
        $user = Persona::factory()->create();

        $tipoAudiovisual = TipoAudiovisual::create([
            'nombre' => 'Serie',
        ]);

        $serie = Audiovisual::create([
            'id' => 1,
            'tipoAudiovisual_id' => $tipoAudiovisual->id,
            'titulo' => 'Breaking Bad',
        ]);

        $temporada = Temporada::create([
            'audiovisual_id' => $serie->id,
            'numero' => 1,
            'nombre' => 'Temporada 1',
        ]);

        $capitulo = Capitulo::create([
            'temporada_id' => $temporada->id,
            'numero' => 1,
        ]);

        $capitulos = [];
        array_push($capitulos, $capitulo);

        $request = [
            'usuario_id' => $user->id,
            'temporada_id' => $temporada->id,
            'capitulos' => $capitulos,
        ];

        $response = $this->postJson('/api/visualizacion-temporada', $request);

        $response->assertUnauthorized();
    }
}
