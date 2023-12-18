<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CicloCreacionPotenciales extends Model
{
    use HasFactory;
    protected $table ='ciclo_creacion_potenciales';
    protected $fillable = ['id_empleado','cantidad'];
    public function Empleado(){
        return $this->hasOne(User::class,'id','id_empleado');
    }
}
