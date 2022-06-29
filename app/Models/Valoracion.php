<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    use HasFactory;

    protected $table = 'valoracion';

    protected $fillable = [
        'audiovisual_id',
        'persona_id',
        'puntuacion',
    ];
}
