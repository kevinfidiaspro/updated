<?php

namespace App\Observers;

use Auth;
use App\Models\NroFactura;

class NroFacturaObserver
{
  /*
    esto es solo para incrementar el numero de la nota
    al crear el registro -> al actualizar permanecera igual
  */
  public function creating(NroFactura $nroFactura)
  {
    // ANTIGUO
    // $contador = NroFactura::get()->count();
    // $nroFactura->nro_factura = $contador + 1;
    //
    // # START asigna numero de factura correlativo segun usuario id
    // #  cambiado Oscar para numeracion correcta
    $factura =  NroFactura::where(['user_id' => Auth::user()->id])->get();
    $valorFactura = (1*count($factura) + 1*1);  
    $nroFactura->nro_factura = $valorFactura;
    // # END asigna numero de factura correlativo segun usuario id    
  }
}
