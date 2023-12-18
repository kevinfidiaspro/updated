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

class EstadisticasContabilidadController extends Controller
{
  public function getIngresoBruto($user_id, $desde, $hasta){
    if ($desde && $hasta) {
        $suma_gastos = Gasto::whereBetween('created_at', [$desde, $hasta])->where('user_id', '=', $user_id)->sum('importe');

        $tiposGasto = TiposGasto::where('user_id', '=', $user_id)->orderBy('id', 'DESC')->get();
        $suma_gastos_desglosado = array();
        foreach ($tiposGasto as $tipoGasto) {
          $suma_gastos_desglosado[$tipoGasto->nombre] = Gasto::where('tipo_gasto_id', $tipoGasto->id)->where('user_id', '=', $user_id)->whereBetween('created_at', [$desde, $hasta])->sum('importe');
        }
        $suma_gastos_proyecto = Gasto::whereBetween('created_at', [$desde, $hasta])->where('user_id', '=', $user_id)->where('proyecto_id', '>=', 0)->sum('importe');
        $suma_gastos_adicional = Gasto::whereBetween('created_at', [$desde, $hasta])->where('user_id', '=', $user_id)->where('proyecto_id', null)->sum('importe');
        $suma_ingresos_proyecto = Ingreso::whereBetween('created_at', [$desde, $hasta])->where('user_id', '=', $user_id)->where('proyecto_id', '>=', 0)->sum('importe');
        $suma_ingresos_adicional = Ingreso::whereBetween('created_at', [$desde, $hasta])->where('user_id', '=', $user_id)->where('proyecto_id', null)->sum('importe');

        return response()->json([
          'gasto_desglosado' => $suma_gastos_desglosado,
          'gasto' => $suma_gastos,
          'gasto_proyecto' => $suma_gastos_proyecto,
          'gasto_adicional' => $suma_gastos_adicional,
          'ingreso_proyecto' => $suma_ingresos_proyecto,
          'ingreso_adicional' => $suma_ingresos_adicional,
        ], 200);
    }

    return response()->json('error', 301);
  }
}
