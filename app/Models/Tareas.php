<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Tareas extends Model
{
    use HasFactory, SoftDeletes; 

    protected $table = 'tareas';

    protected $fillable = [
      'id_proyecto',
      'descripcion',
      'tiempo',
      'fecha',
      'id_usuario'
    ];

    protected $dates = ['created_at'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];

    protected $appends =['nombre_proyecto','nombre_usuario'];
    public function Proyecto(){
      return $this->hasOne(Proyecto::class,'id','id_proyecto');
    }
    public function Usuario(){
      return $this->hasOne(User::class,'id','id_usuario');
    }
    public function getNombreProyectoAttribute(){
      $proyecto = $this->Proyecto()->withTrashed()->first();
      if($proyecto == null){
        return "Proyecto Eliminado";
      }
      
      return $proyecto->nombre??"Proyecto Eliminado";
    }
    public function getNombreUsuarioAttribute(){
      $usuario = $this->Usuario()->first();
      if($usuario == null){
        return 'Usuario No Existe';
      }
      return  $usuario->nombre??'Usuario No Existe';
    }
    public function Tareas(){
      return $this->hasMany(Tareas::class,'id_proyecto','id_proyecto');
    }
}