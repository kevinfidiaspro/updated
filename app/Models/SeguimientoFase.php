<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoFase extends Model
{
    use HasFactory;

    protected $table = 'fase_seguimiento';
    protected $fillable = [
      'fase',
      'objetivo',
      'criterio',
      'tasa_obj',
      'tasa_obj_per',
      'tasa_med',
      'tasa_precio',
      'id_seguimiento'
    ];
    protected $appends = ['total'];
    public function allSeguimientos(){
      return $this->hasMany(SeguimientoFase::class,'id_seguimiento','id_seguimiento');
    }
    public function  getCalcAttribute(){
      $fases = $this->allSeguimientos;
      $anterior = null;
      for($i = 0 ; $i < count($fases) ; $i++ ){
        if($fases[$i]['id']==$this->id){
          if($i != 0)
          $anterior = $fases[$i-1];
        }
      }
      return $anterior;
    }
    public function  getTotalAttribute(){
      return number_format(floatval( $this->tasa_precio??0)*floatval($this->tasa_med??0),2);
    }
}
