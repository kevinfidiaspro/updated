<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;
    protected $table = 'reunion';
    protected $fillable = [
        'fecha',
        'comentario',
        'id_organizador',
        'hora_desde',
        'hora_hasta'
    ];
    protected $casts = [
        'hora_desde' => 'double',
        'hora_hasta' => 'double',

    ];
    public function Asistentes(){
        return $this->hasMany(ReunionAsistente::class,'id_reunion','id');
    }
    public function Organizador(){
        return $this->hasOne(User::class,'id','id_organizador');
    }
}

