<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TFGPotenciales extends Model
{
    use HasFactory;
    protected $table="potenciales_tfg";
    protected $fillable = ['dia','id_empresa','id_tipo','cantidad'	];
    public function Tipo(){
        return $this->hasOne(TiposTFGPotenciales::class,'id','id_tipo');
    }
    public function Empresa(){
        return $this->hasOne(EmpresaTFG::class,'id','id_empresa');
    }
}
