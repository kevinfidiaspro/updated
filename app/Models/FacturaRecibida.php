<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proveedor;



class FacturaRecibida extends Model
{
    use HasFactory;


    public function proveedor(){
      return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

}
