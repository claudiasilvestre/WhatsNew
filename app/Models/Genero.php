<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = 'genero';

    public $incrementing = false;

    public function audiovisuales()
    {
        return $this->hasMany(Audiovisual::class);
    }
}
