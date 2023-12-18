<?php

namespace App\Http\Controllers\ApiControllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProveedorRequest;

class ProveedorController extends Controller
{
  public function getProveedores($user_id){
    $proveedores = Proveedor::where('user_id', '=', $user_id)->orderBy('created_at', 'DESC')->get();
    return response()->json($proveedores, 200);
  }

  public function getProveedorByid($proveedor_id){
    $proveedor = Proveedor::find($proveedor_id);
    return response()->json($proveedor, 200);
  }

  public function saveProveedor(ProveedorRequest $request){
    $proveedor = Proveedor::updateOrCreate(['id' => $request->id], $request->all());
    return response()->json($proveedor, 200);
  }

  public function deleteProveedor($proveedor_id){
    $proveedor = Proveedor::find($proveedor_id);
    $proveedor->delete();
    return response()->json($proveedor, 200);
  }
}
