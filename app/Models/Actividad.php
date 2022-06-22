<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividad';

    protected $fillable = [
        'persona_id',
        'tipo',
        'audiovisual_id',
        'capitulo_id',
        'temporada_id',
    ];
}
