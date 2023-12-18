<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Provincia;

class ProvinciaController extends Controller
{
  public function getProvincias(Request $request, $user_id= null){
    return response()->json(Provincia::orderBy('nombre')->get(['id', 'nombre']), 200);
  }
}
