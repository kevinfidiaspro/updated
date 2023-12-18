<?php

namespace App\Http\Controllers\ApiControllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use App\Http\Requests\IngresoRequest;
use App\Http\Resources\IngresoResource;
use App\Models\Ingreso;
use App\Models\Factura;
use App\Models\Proyecto;
use App\Models\Deuda;
use App\Models\NroFactura;
use App\Models\NroNota;
use DB;

class IngresoController extends Controller
{
  public function getAllIngresos(){
    $allIngresos = Ingreso::with('proyecto:id,nombre','facturas')->orderBy('created_at', 'DESC')->get();
    
    return response()->json(IngresoResource::collection($allIngresos), 200);
  } 

  public function getIngresos(Request $request, $user_id){
    $user = $request->user();
    
    if ($request->has(['desde', 'hasta'])) {
        $ingresos = Ingreso::with('proyecto:id,nombre','facturas')->orderBy('created_at', 'DESC')->whereBetween('created_at', [$request->desde, $request->hasta])->orderBy('created_at','DESC')->get();
      
        return response()->json($ingresos, 200);
    }

    $ingreso = Ingreso::with('proyecto:id,nombre','facturas')->orderBy('created_at', 'DESC')->get();
    return response()->json(IngresoResource::collection($ingreso), 200);
  }

  public function getIngresoById($ingreso_id){
    $ingreso = Ingreso::find($ingreso_id);
    $fecha = \Carbon\Carbon::parse($ingreso->created_at);
    $ingreso = $ingreso->toArray();
  $ingreso['created_at'] = $fecha->toDateString();
    return response()->json($ingreso, 200);
  }

  public function getIngresoByProyectoId($proyecto_id){  
    $total_pagos_ingresos = 0;
    $allIngresosProyectoId = Ingreso::with('proyecto:id,nombre,pvp','facturas')->where('proyecto_id',$proyecto_id)->orderBy('created_at', 'DESC')->get();

    foreach ($allIngresosProyectoId as $allIngreso) {
      $total_pagos_ingresos = $total_pagos_ingresos + $allIngreso->importe;
    }
    $allIngresosProyectoId['total_pagos_ingresos'] = $total_pagos_ingresos;

    return response()->json($allIngresosProyectoId, 200);
  }

  /**
   * Metodo para guardar un ingreso.
   *
   * @param  \App\Http\Requests\IngresoRequest  $request
   * @return \App\Models\Ingreso;
   */
  public function saveIngreso(IngresoRequest $request){
    $error = null;
    if($request->factura_id){
      $ingresos = Ingreso::where('factura_id',$request->factura_id)->where('id','!=',$request->id)->get();
      $total = $request->importe;
      foreach($ingresos as $ingreso){
        $total+= $ingreso->importe;
      }
      $factura = Factura::find($request->factura_id);
      if($total > $factura->total){
       $error = 'el importe supera el valor de la factura por: '.($total-$factura->total);
      }
      if($total < $factura->total){
        $error = 'el importe es inferior al valor de la factura por: '.($factura->total-$total);
       }
      if($total >= $factura->total){
        $factura->pagada = 1;
        $factura->update();
      }
      else{
        $factura->pagada = 0;
        $factura->update();
      }
    }
    $ingreso = Ingreso::updateOrCreate(['id' => $request->id], $request->all());

    return response()->json(['ingreso'=>$ingreso,'error'=>$error], 200);
  }

  /**
   * Metodo para asociar ingreso a una factura.
   *
   * @param string $codigo_to_upper
   * @param string $model_name
   * @param string $column_name
   * @param string $tipo
   * @return void;
   */
  public function asociarFactura(String $codigo_to_upper, String $model_name, String $column_name, String $tipo){
    $extraer_cod = str_replace($tipo, '', $codigo_to_upper);

    $numero_factura = ltrim($extraer_cod, '0');

    $nro_factura = $this->getFacturaOrNota($model_name, $column_name, $numero_factura);

    $current_deuda = $nro_factura->deuda;

    if(!$current_deuda){
       return null;
    }

    $suma_ingresos = $this->sumaIngresos($tipo, $numero_factura);

    return $this->updateDeuda($current_deuda, $suma_ingresos[0]->total);
  }

  private function sumaIngresos(String $tipo, $numero_factura){
    return DB::select("SELECT COALESCE(sum(importe), 0) as total FROM ingresos WHERE TRIM(LEADING '0' FROM REPLACE(codigo, '{$tipo}', '')) = ?", [$numero_factura]);
  }

  private function updateDeuda(Deuda $current_deuda, $suma_ingresos){
    $current_deuda->pagado = $suma_ingresos;
    $current_deuda->save();
    return $current_deuda;
  }

  private function getFacturaOrNota($model_name, $column_name, $numero_factura){
    if($model_name == 'nro_factura'){
       return NroFactura::where($column_name, $numero_factura)->get()->first();
    }

    if($model_name == 'nro_nota'){
       return NroNota::where($column_name, $numero_factura)->get()->first();
    }
    return null;
  }

  public function deleteIngreso($ingreso_id){
    $ingreso = Ingreso::find($ingreso_id);

    $codigo = $ingreso->codigo;

    $ingreso->delete();

    $codigo_to_upper = mb_strtoupper($codigo);

    $codigo_inicio = substr($codigo_to_upper, 0, 3);

    if($codigo_inicio == mb_strtoupper('FAC')){
       $this->asociarFactura($codigo_to_upper, 'nro_factura', 'nro_factura', 'FAC');
    }

    if($codigo_inicio == mb_strtoupper('NOT')){
       $this->asociarFactura($codigo_to_upper, 'nro_nota', 'nro_nota', 'NOT');
    }

    return response()->json('Ingreso eliminado', 200);
  }
}
