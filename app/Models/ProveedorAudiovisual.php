<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedorAudiovisual extends Model
{
    use HasFactory;

    protected $table = 'proveedor_audiovisual';

    protected $fillable = [
        'proveedor_id',
        'audiovisual_id',
        'disponibilidad',
    ];
}
