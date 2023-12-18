<?php

namespace App\Observers;

use Auth;
use App\Models\NroPresupuesto;

class NroPresupuestoObserver
{
  /*
    esto es solo para incrementar el numero del presupuesto
    al crear el registro -> al actualizar permanecera igual
  */
  public function creating(NroPresupuesto $nroPresupuesto)
  {
    // ANTIGUO
    // $contador = NroPresupuesto::withTrashed()->get()->count();
    // $nroPresupuesto->nro_presupuesto = $contador + 1;
    //
    // # START asigna numero de presupuesto correlativo segun usuario id
    // #  cambiado Oscar para numeracion correcta
    $presupuesto =  NroPresupuesto::where(['user_id' => Auth::user()->id])->get();
    $valorPresupuesto = (1*count($presupuesto) + 1*1);  
    $nroPresupuesto->nro_presupuesto = $valorPresupuesto;
    // # END asigna numero de presupuesto correlativo segun usuario id
  }
}
