<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsPresupuesto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items_presupuesto';

    protected $fillable = [
        'id_presupuesto',
        'descripcion',
        'cantidad',
        'importe',
        'precio'
      ];
}
