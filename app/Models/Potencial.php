<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Potencial extends Model
{
    use SoftDeletes;

    protected $table = 'proyecto';

    protected $fillable = [
      'usuario_id',
      'servicio_id',
      'fecha_alta',
      'detalle_servicio',
      'estado_id',
      'pvp',
      'pvp_gasto',
      'detalles_gasto',
      'potencial',
      'activo',
      'proyecto',
    ];

    protected $casts = [
      'potencial' => 'boolean',
      'proyecto'  => 'boolean',
    ];

    public function servicio(){
      return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function estado(){
      return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function user(){
      return $this->belongsTo(User::class);
    }

    public function archivos(){
      return $this->morphMany(Archivo::class, 'archivos');
    }
}
