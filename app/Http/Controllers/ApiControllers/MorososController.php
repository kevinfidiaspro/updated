<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MorososResource;
use Illuminate\Http\Request;
use App\Models\Deuda;

class MorososController extends Controller
{
  public function getMorosos(Request $request, $user_id){
    $morosos = Deuda::where('user_id', '=', $user_id)->with('deuda.recibo.cliente')->whereColumn('total', '>', 'pagado')->orderBy('created_at', 'DESC')->get();
    $morosos_resource = MorososResource::collection($morosos);
    return response()->json($morosos_resource, 200);
  }
}
