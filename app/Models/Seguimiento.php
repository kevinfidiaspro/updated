<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimiento';
  protected $fillable = [
    'hasta','desde','producto','nombre','gastos_totales'
  ];
  protected $appends=['total_venta','tasa_med'];
  public function getTasaMedAttribute(){
    $fases = $this->Fase;
    if(count($fases) == 0){
      return 0;
    }else{
      return $fases[0]->tasa_med;
    }
  }
  public function getTotalVentaAttribute(){
    $total = 0;
    foreach($this->Fase as $fase){
      $total += $fase->total;
    }
    return $total;
  }
  public function Ventas(){
    return $this->hasMany(VentasDiarias::class,'id_seguimiento','id');
  }
  public function Fase(){
    return $this->hasMany(SeguimientoFase::class,'id_seguimiento','id');
  }
}
