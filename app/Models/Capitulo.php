<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capitulo extends Model
{
    use HasFactory;

    protected $table = 'capitulo';

    protected $fillable = [
        'temporada_id',
        'numero',
    ];

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }
}
