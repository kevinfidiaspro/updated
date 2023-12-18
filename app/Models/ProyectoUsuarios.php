<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProyectoUsuarios extends Model
{
    use HasFactory; 

    protected $table = 'proyecto_usuarios';

    protected $fillable = [
      'id_proyecto',
      'id_usuario',

    ];

    protected $dates = ['created_at'];

    protected $casts = [
      'created_at' => 'date:d-m-Y'
    ];
    public function usuario(){
      return $this->hasOne(User::class,'id', 'id_usuario');
    }
}