<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioCapitulo extends Model
{
    use HasFactory;

    protected $table = 'comentario_capitulo';

    protected $fillable = [
        'capitulo_id',
        'persona_id',
        'texto',
        'votosPositivos'
    ];
}
