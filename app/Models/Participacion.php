<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participacion extends Model
{
    use HasFactory;

    protected $table = 'participacion';

    protected $fillable = [
        'audiovisual_id',
        'persona_id',
    ];
}
