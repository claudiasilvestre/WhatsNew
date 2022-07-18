<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'idioma';

    public $incrementing = false;

    public function audiovisuales()
    {
        return $this->hasMany(Audiovisual::class);
    }
}
