<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Resources\AppResources\AppPromocionResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promocion;

class AppPromocionController extends Controller
{
  public function getActivePromo(){
    $promociones = Promocion::orderBy('created_at', 'DESC')
      ->where('active', 1)->get(['id', 'nombre', 'url']);
    $promociones_resource = AppPromocionResource::collection($promociones);
    return response()->json($promociones_resource, 200);
  }
}
