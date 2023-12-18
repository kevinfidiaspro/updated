<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Cuenta;

class CuentasController extends Controller
{
  public function getCuentas(){
    $cuentas = Cuenta::orderBy('id')->get(['id', 'cuenta','nombre_banco']);
    return response()->json($cuentas, 200);
  } 
}
