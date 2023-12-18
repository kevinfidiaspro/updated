<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsFactura extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items_factura';

    protected $fillable = [
        'id_factura',
        'descripcion',
        'cantidad',
        'importe',
        'precio'
      ];
}
