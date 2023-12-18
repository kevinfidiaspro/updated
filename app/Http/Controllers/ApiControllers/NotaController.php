<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Resources\NotaResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Recibo;
use Storage;

class NotaController extends Controller
{
  public function getNotas($user_id){
    $notas = NotaResource::collection(Recibo::where('user_id', '=', $user_id)->whereHas('nro_nota')->orderBy('id', 'DESC')->get());
    return response()->json($notas, 200);
  }

  public function deleteNota($recibo_id){
    $recibo = Recibo::find($recibo_id);

    if(Storage::disk('recibos')->exists($recibo->nota_url)){
       Storage::disk('recibos')->delete($recibo->nota_url);
    }

    $recibo->nro_nota->deuda->delete();

    $recibo->nro_nota()->delete();

    $recibo->nota_url = null;

    $recibo->save();

    return response()->json('nota eliminada', 200);
  }
}
