<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audiovisual extends Model
{
    use HasFactory;

    protected $table = 'audiovisual';

    protected $fillable = [
        'id',
        'tipoAudiovisual_id',
        'titulo',
        'genero_id',
        'idioma_id',
        'puntuacion',
    ];

    public $incrementing = false;

    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }

    public function idioma()
    {
        return $this->belongsTo(Idioma::class);
    }
}
