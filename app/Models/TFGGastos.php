<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TFGGastos extends Model
{
    use HasFactory;
    protected $table = 'tfg_gastos';
    protected $fillable = ['descripcion','id_tipo','mes','euros'];
    public function Tipo(){
        return $this->hasOne(TiposTFGGastos::class,'id','id_tipo');
    }
}
