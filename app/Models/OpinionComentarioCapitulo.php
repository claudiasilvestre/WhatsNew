<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpinionComentarioCapitulo extends Model
{
    use HasFactory;

    protected $table = 'opinion_comentario_capitulo';

    protected $fillable = [
        'persona_id',
        'comentarioCapitulo_id',
        'opinion',
    ];
}
