<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoParticipante extends Model
{
    use HasFactory;

    protected $table = 'tipo_participante';

    protected $fillable = [
        'nombre',
    ];
}
