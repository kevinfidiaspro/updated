<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPotencial extends Model
{
    use HasFactory;
    protected $table = 'estado_potenciales';
    protected $fillable = [
        'nombre'
    ];
}
