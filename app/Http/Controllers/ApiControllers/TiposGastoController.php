<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\TiposGasto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProveedorRequest;

class TiposGastoController extends Controller
{
  public function getTiposGasto($user_id){
    $tiposGasto = TiposGasto::orderBy('id', 'DESC')->get();
    return response()->json($tiposGasto, 200);
  }

  /*public function getProveedorByid($proveedor_id){
    $proveedor = Proveedor::find($proveedor_id);
    return response()->json($proveedor, 200);
  }*/

  public function saveTiposGasto(ProveedorRequest $request){
    $tiposGasto = TiposGasto::updateOrCreate(['id' => $request->id], $request->all());
    return response()->json($tiposGasto, 200);
  }

  public function deleteTiposGasto($tipos_gasto_id){
    $tiposGasto = TiposGasto::find($tipos_gasto_id);
    $tiposGasto->delete();
    return response()->json($tiposGasto, 200);
  }
}
