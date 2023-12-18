<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Resources\AppResources\AppFacturaResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Factura;

class AppFacturaController extends Controller
{
  public function getFacturasByUserId($user_id){
    $user = User::where('id', $user_id)->get()->first();
    $factura_resource = AppFacturaResource::collection($user->facturas);
    return response()->json($factura_resource, 200);
  }

  public function getFacturasByProyecto($proyecto_id){
    $facturas = Factura::where('id_proyecto', $proyecto_id)->get();
    $factura_resource = AppFacturaResource::collection($facturas);
    return response()->json($factura_resource, 200);
  }
}
