<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualizacionCapitulo extends Model
{
    use HasFactory;

    protected $table = 'visualizacion_capitulo';

    protected $fillable = [
        'capitulo_id',
        'persona_id',
    ];
}
