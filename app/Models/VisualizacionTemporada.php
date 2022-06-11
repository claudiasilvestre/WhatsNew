<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualizacionTemporada extends Model
{
    use HasFactory;

    protected $table = 'visualizacion_temporada';

    protected $fillable = [
        'temporada_id',
        'persona_id',
    ];
}
