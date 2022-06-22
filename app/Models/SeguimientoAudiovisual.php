<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoAudiovisual extends Model
{
    use HasFactory;

    protected $table = 'seguimiento_audiovisual';

    protected $fillable = [
        'audiovisual_id',
        'persona_id',
        'estado',
    ];
}
