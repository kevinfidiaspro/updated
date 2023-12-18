<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoVentasKit extends Model
{
    use HasFactory;
    protected $table = 'estado_ventas_kit';
    protected $fillable = ['nombre'];
}
