<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facturas';
    protected $appends = ['anio','nro_anio_factura','esta_pagada','cliente_real','tipo_nro'];
    protected $fillable = [
        'id_proyecto',
        'id_cliente',
        'id_factura',
        'total',
        'descuento',
        'fecha',
        'nro_factura',
        'id_anio',
        'file_name',
        'url',
        'status_iva',
        'id_cuenta',
        'pagada',
        'tipo',
        'notas'
      ];
      public function VentasDiarias(){
        return $this->hasOne(VentasDiarias::class,'id_factura','id');
      }
      public function getClienteRealAttribute(){
        if($this->cliente ==null){
          $this->id_cliente = $this->proyecto->usuario->id;
       
          return $this->proyecto->usuario;
        }
        return $this->cliente;
      }
      public function getEstaPagadaAttribute(){
        $sum = 0 ;
        $count = count($this->ingresos);
        if($this->ingresos){
          if(count($this->ingresos)){
            foreach($this->ingresos as $ingreso){
              $sum += $ingreso->importe;
            }
          }
        }
        if( $sum == 0){
          return 0;
        }else if ( floatval($this->total) <= $sum){
          return 1;
        } else{
          if($count == 1){
            return 2;
          }else{
            return 3;
          }
        }
        
      }
      public function getNroAnioFacturaAttribute(){
         $result = substr(strval($this->anio),'2')."-".$this->nro_factura;
         return $result;
      }
      public function getTipoNroAttribute(){
        $result = substr(strval($this->anio),'2')."-".$this->nro_factura . ' | ' . ($this->tipo == 1 ? 'F':'P');
        return $result;
     }
      public function getAnioAttribute(){
        $anio = $this->AnioFiscal;
        if($anio == null){
          return explode('-',$this->created_at)[0];
        }
        else{
          return $anio->year;
        }
      }
      public function AnioFiscal(){
        return $this->hasOne(AnioFiscal::class, 'id','id_anio');
      }
      public function items_factura(){
        return $this->hasMany(ItemsFactura::class, 'id_factura');
      }

      public function proyecto(){
        return $this->belongsTo(Proyecto::class, 'id_proyecto');
      }
      public function cliente(){
        return $this->hasOne(User::class, 'id','id_cliente');
      }
      // Start Oscar 03/11/22 
      public function cuenta(){ // una sola cuenta por Factura
        return $this->hasOne(Cuenta::class);
      }
      // End Oscar 03/11/22
      
      public function ingresos(){
        return $this->hasMany(Ingreso::class, 'factura_id','id');
      }
}
