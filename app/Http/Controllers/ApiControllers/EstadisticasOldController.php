<?php

namespace App\Http\Controllers\ApiControllers;

use DB;
use App\Models\Proyecto;
use App\Models\TiposGasto;
use App\Models\Gasto;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EstadisticasController extends Controller
{
  public function getIngresoBruto(Request $request, $user_id){
    if ($request->has(['desde', 'hasta'])) {
        $suma_gastos = Gasto::whereBetween('created_at', [$request->desde, $request->hasta])->where('user_id', '=', $user_id)->sum('importe');

        $tiposGasto = TiposGasto::where('user_id', '=', $user_id)->orderBy('id', 'DESC')->get();
        $suma_gastos_desglosado = array();
        foreach ($tiposGasto as $tipoGasto) {
          $suma_gastos_desglosado[$tipoGasto->nombre] =Gasto::where('tipo', $tipoGasto->nombre)->where('user_id', '=', $user_id)->whereBetween('created_at', [$request->desde, $request->hasta])->sum('importe');
        }

        $suma_ingresos = Ingreso::whereBetween('created_at', [$request->desde, $request->hasta])->where('user_id', '=', $user_id)->sum('importe');

        $suma_deuda = Deuda::whereBetween('created_at', [$request->desde, $request->hasta])->where('user_id', '=', $user_id)->select(DB::raw('sum(total - pagado) as total'))->get();

        $deuda = $suma_deuda[0]['total'] ? $suma_deuda[0]['total'] : 0;

        return response()->json([
          'gasto_desglosado' => $suma_gastos_desglosado,
          //'gasto_interno' => $suma_gastos_internos,
          'gasto'         => $suma_gastos,
          'ingreso'       => $suma_ingresos,
          'suma_deuda'    => $deuda
        ], 200);
    }

    return response()->json('error', 301);
  }
}
