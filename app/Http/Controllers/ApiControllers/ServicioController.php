<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
  public function getServicios(Request $request, $user_id= null){
    return response()->json(Servicio::orderBy('id')->get(['id', 'nombre'])->where('id_lead_form',null), 200);
  }
}
