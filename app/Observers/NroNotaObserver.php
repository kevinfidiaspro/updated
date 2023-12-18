<?php

namespace App\Observers;

use Auth;
use App\Models\NroNota;

class NroNotaObserver
{
  /*
    esto es solo para incrementar el numero de la nota
    al crear el registro -> al actualizar permanecera igual
  */
  public function creating(NroNota $nroNota)
  {
    // ANTIGUO
    // $contador = NroNota::withTrashed()->get()->count();
    // $nroNota->nro_nota = $contador + 1;
    //
    // # START asigna numero de nota correlativo segun usuario id
    // #  cambiado Oscar para numeracion correcta
    $nota =  NroNota::where(['user_id' => Auth::user()->id])->get();
    $valorNota = (1*count($nota) + 1*1);  
    $nroNota->nro_nota = $valorNota;
    // # END asigna numero de nota correlativo segun usuario id
  }
}
