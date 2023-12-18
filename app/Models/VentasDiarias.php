<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDiarias extends Model
{
    use HasFactory;
    protected $table = 'ventas_diarias';
    protected $fillable = ['id_factura','dia','sin_factura','total_sf','id_cliente','id_seguimiento'];
    public function Cliente(){
        return $this->hasOne(User::class,'id','id_cliente');
    }
    public function Factura(){
        return $this->hasOne(Factura::class,'id','id_factura');
    }
    public function Seguimiento(){
        return $this->hasOne(Seguimiento::class,'id','id_seguimiento');
    }
    public function VentaSeguimiento(){
        return $this->hasOne(VentaSeguimiento::class,'id_venta','id');
    }
}
