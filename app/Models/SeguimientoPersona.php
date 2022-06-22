<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoPersona extends Model
{
    use HasFactory;

    protected $table = 'seguimiento_persona';

    protected $fillable = [
        'personaActual_id',
        'persona_id',
    ];
}
