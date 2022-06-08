<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimiento';

    protected $fillable = [
        'audiovisual_id',
        'persona_id',
        'estado',
    ];
}
