<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioAudiovisual extends Model
{
    use HasFactory;

    protected $table = 'comentario_audiovisual';

    protected $fillable = [
        'audiovisual_id',
        'persona_id',
        'texto',
        'votosPositivos',
        'votosNegativos',
    ];
}
