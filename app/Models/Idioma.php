<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idioma';

    protected $fillable = [
        'nombre',
    ];

    public function audiovisuales()
    {
        return $this->hasMany(Audiovisual::class);
    }
}
