<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoTarea extends Model
{
    use HasFactory;
    protected $table = 'proyecto_tarea';
    protected $fillable = [
        'id_proyecto',
        'fecha',
        'comentario',
        'hora',
        'alarma',
    ];
    public function Proyecto(){
        return $this->hasOne(Proyecto::class,'id','id_proyecto');
    }
}

