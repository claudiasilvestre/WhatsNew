<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpinionComentarioAudiovisual extends Model
{
    use HasFactory;

    protected $table = 'opinion_comentario_audiovisual';

    protected $fillable = [
        'persona_id',
        'comentarioAudiovisual_id',
        'opinion',
    ];
}
