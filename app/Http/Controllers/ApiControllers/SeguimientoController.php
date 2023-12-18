<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Seguimiento;
use App\Models\SeguimientoFase;
use App\Models\VentasSeguimiento;
use App\Models\VentasDiarias;
class SeguimientoController extends Controller 
{
  public function saveSeguimiento(Request $request){
      $seguimiento = Seguimiento::updateOrCreate(['id'=>$request->id],[
        'hasta'=>$request->hasta,
        'desde'=>$request->desde,
        'producto'=>$request->producto,
        'nombre'=>$request->nombre,
        'gastos_totales'=>$request->gastos_totales??0,
      ]);
      $fases = [
        [
          'fase'=>1,
          'objetivo'=>'Alcance',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>2,
          'objetivo'=>'Interés',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>3,
          'objetivo'=>'Contacto',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>'4a',
          'objetivo'=>'Propuesta Comercial',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>5,
          'objetivo'=>'Producto Estrella',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>6,
          'objetivo'=>'Negociación',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>7,
          'objetivo'=>'Sistema P10-15-15',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>8,
          'objetivo'=>'Resolución: € o motivo',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>9,
          'objetivo'=>'Entrega',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
        [
          'fase'=>10,
          'objetivo'=>'Post-venta',
          'criterio'=>'',
          'tasa_obj'=>0,
          'tasa_obj_per'=>0,
          'tasa_med'=>0,
          'tasa_precio'=>0,
          'id_seguimiento'=> $seguimiento->id,
        ],
       

      ];
      if($request->id == null){
        foreach($fases as $fase){
          SeguimientoFase::create($fase);
        }
      }
      
    
      return $fases;
  }
  public function getSeguimiento($id){
    $seguimiento = Seguimiento::with(['Ventas.Factura.cliente','Ventas.Cliente'])->find($id);
    return $seguimiento;
  }
  public function getSeguimientos(){
    $seguimientos = Seguimiento::all();
    return $seguimientos;
  }
  public function deleteSeguimiento($id){
    $seguimiento = Seguimiento::find($id);
    foreach($seguimiento->Fase as $fase){
      $fase->delete();
    }
    $seguimiento->delete();
  }
  public function saveSeguimientoFase(Request $request){
    $fase = $request->fase;
    if($request->id == null){
      $seguimientoFase  = SeguimientoFase::where('id_seguimiento',$request->id_seguimiento)->where('fase','like','%4%')->orderby('fase','desc')->first();
      $letter = explode('4',$seguimientoFase->fase)[1];
      $letters = 'abcdefghijklmnñopqrstuvxyz';

      for($i = 0 ; $i < strlen($letters); $i++){
        if($letter == $letters[$i]){
          $fase = '4'.$letters[$i+1];
        }
      }

    }
    $seguimiento = SeguimientoFase::updateOrCreate(['id'=>$request->id],[
      'fase'=>$fase,
      'objetivo'=>$request->objetivo,
      'criterio'=>$request->criterio,
      'tasa_obj'=>$request->tasa_obj,
      'tasa_obj_per'=>$request->tasa_obj_per,
      'tasa_med'=>$request->tasa_med,
      'tasa_precio'=>$request->tasa_precio,
      'id_seguimiento'=>$request->id_seguimiento,
    ]);
    return $seguimiento;

  }
  public function getSeguimientoFase($id){
    $seguimiento = SeguimientoFase::find($id);
    return $seguimiento;
  }
  public function getSeguimientoFases($id){
    $seguimientos = SeguimientoFase::where('id_seguimiento',$id)->orderByRaw('CONVERT(fase,SIGNED) ASC')->get();
    if(count($seguimientos)>0){
     $seguimientos[0]->tasa_obj = ceil(floatval( $seguimientos[0]->tasa_obj??0));
     $seguimientos[0]->tasa_per_med = 100;
    }
    for($i = 1 ; $i < count($seguimientos) ; $i++){
      $seguimientos[$i]->tasa_obj = ceil(floatval($seguimientos[$i-1]->tasa_obj??0)*floatval($seguimientos[$i-1]->tasa_obj_per??0)/100);
      if($i <=2){
        $dividend = $seguimientos[$i-1]->tasa_med??1;
        if($dividend == 0) $dividend = 1;
        $seguimientos[$i]->tasa_per_med = ceil(floatval($seguimientos[$i]->tasa_med??0)/floatval(($dividend))*100);
      }
      else{
        $seguimientos[$i]->tasa_per_med = ($seguimientos[2]->tasa_med == 0)?0: ceil(floatval($seguimientos[$i]->tasa_med??0)/floatval(($seguimientos[2]->tasa_med??1))*100);
      }

    }
    return $seguimientos;
  }
  public function deleteSeguimientoFase($id){
    $seguimiento = SeguimientoFase::find($id);
    $seguimiento->delete();
  }
  public function getVentas(){
    return VentasDiarias::whereDoesntHave('VentaSeguimiento')->get();
  }
}
