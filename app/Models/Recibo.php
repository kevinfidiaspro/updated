<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;


class Recibo extends Model
{
    use HasFactory;

    protected $table = 'recibo';

    protected $fillable = [
        'cliente_id',
        'fecha',
        'sub_total',
        'iva',
        'porcentaje_descuento',
        'total_descuento',
        'total',
        'presupuesto_url',
        'factura_url',
        'nota_url',
        'has_iva',
        'user_id'
    ];

    protected $casts = [
      'has_iva' => 'boolean'
    ];

    protected $dates = [
      'fecha'
    ];

    public function setActiveAttribute($iva){
       $this->attributes['has_iva'] = ($iva == 'true') ? 1 : 0;
    }

    public function cliente(){
      return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function servicios(){
      return $this->hasMany(ReciboServicio::class);
    }

    public function nro_factura(){
      return $this->hasOne(NroFactura::class);
    }

    public function nro_nota(){
      return $this->hasOne(NroNota::class);
    }

    public function nro_presupuesto(){
      return $this->hasOne(NroPresupuesto::class);
    }

    public function nro_parte_trabajo(){
      return $this->hasOne(NroParteTrabajo::class);
    }

    public function setFechaAttribute($fecha){
      $this->attributes['fecha'] = Carbon::createFromDate($fecha)->format('Y-m-d');
    }

    public function getFechaAttribute(){
      return Carbon::createFromDate($this->attributes['fecha'])->format('Y-m-d');
    }

     public function user(){
      return $this->belongsTo(User::class, 'id');
    }
}
