<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;

    protected $table = 'cuentas';
    protected $fillable = ['cuenta','nombre_banco'];
    protected $dates = ['created_at'];
    protected $casts = ['created_at' => 'date:d-m-Y', 'updated_at'=> 'date:d-m-Y', 'deleted_at'=> 'date:d-m-Y'];
    
    // Start Oscar 03/11/22 
    public function factura(){ // muchas facturas para una cuenta
      return $this->belongsTo(Factura::class, 'id_factura');
    }
    // End Oscar 03/11/22 
}
