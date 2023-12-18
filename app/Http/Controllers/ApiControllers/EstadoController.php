<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Estado;

class EstadoController extends Controller 
{
  public function getEstados(Request $request, $user_id= null){
    return response()->json(Estado::orderBy('id')->get(['id', 'nombre']), 200);
  }
}
