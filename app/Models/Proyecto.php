<?php

namespace App\Models;

use App\Mail\Presupesto;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use SoftDeletes;

    protected $table = 'proyecto';

    protected $fillable = [
      'no_mail',
      'usuario_id',
      'servicio_id',
      'fecha_alta',
      'id_lead_form',
      'detalle_servicio',
      'estado_id',
      'es_kit',
      'pvp',
      'pvp_gasto',
      'porc_realizado',
      'detalles_gasto',
      'nombre',
      'activo',
      'id_estado_potencial',
      'minutos_estimados',
      'semanal',
      'tipo_proyecto'
    ];
    protected $casts = [
      'potencial' => 'boolean',
      'proyecto' => 'boolean',
    ];
    public function ProyectoTareas(){
      return $this->hasMany(ProyectoTarea::class,'id_proyecto','id');
    }
    public function Tareas(){
      return $this->hasMany(Tareas::class,'id_proyecto','id');
    }
    public function EstadoPotencial(){
      return $this->hasOne(EstadoPotencial::class,'id','id_estado_potencial');
    }
    public function LeadForm(){
      return $this->hasOne(FacebookForm::class,'id','id_lead_form');
    }
    public function servicio(){
      return $this->belongsTo(Servicio::class, 'servicio_id');
    }
    public function estado(){
      return $this->belongsTo(Estado::class, 'estado_id');
    }
    public function usuarios(){
      return $this->hasMany(ProyectoUsuarios::class,'id_proyecto','id');
    }
    public function usuario(){
      return $this->belongsTo(User::class, 'usuario_id');
    }
    public function presupuesto(){
      return $this->hasOne(Presupuesto::class, 'id_proyecto');
    }
    public function archivos(){
      return $this->morphMany(Archivo::class, 'archivos');
    }
    public function estadosProyecto(){
      return $this->hasMany(EstadoProyecto::class, 'id_proyecto');
    }
    public function lastActiveState(){
      return $this->hasOne(EstadoProyecto::class, 'id_proyecto','id')->where('finalizado',0);
    }
    public function ingresos(){
      return $this->belongsTo(Ingreso::class, 'proyecto_id');
    }
    public function facturas(){
      return $this->belongsTo(Factura::class, 'id_proyecto');
    }
}
