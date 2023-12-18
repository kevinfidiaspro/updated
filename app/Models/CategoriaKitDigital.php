<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaKitDigital extends Model
{
    use HasFactory;
    protected $table = 'categoria_kit_digital';
    protected $fillable = ['nombre'];
}
