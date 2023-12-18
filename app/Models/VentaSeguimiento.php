<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaSeguimiento extends Model
{
    use HasFactory;
    protected $table = 'venta_seguimiento';
    protected $fillable = ['id_venta','id_seguimiento'];
    public function Seguimiento(){
        return $this->hasOne(Sseguimiento::class,'id','id_seguimiento');
    }
    public function Venta(){
        return $this->hasOne(Ventas::class,'id','id_venta');
    }
}
