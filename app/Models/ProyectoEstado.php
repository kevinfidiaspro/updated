<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoEstado extends Model
{
    use HasFactory;
    protected $table='proyecto_estados';
    protected $fillable=['nombre'];
}
