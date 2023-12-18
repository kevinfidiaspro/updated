<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoModulo extends Model
{
    use HasFactory;
    protected $table = 'producto_modulo';
    protected $fillable = ['nombre'];
}
