<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmpresaTFG;
use App\Models\TFGPotenciales;
use App\Models\VentasDiariasTFG;
class VentasWebhookController extends Controller
{
    public function SaveVentaTFG(Request $request){
        $empresa = EmpresaTFG::where('nombre','like','%'.$request['web'].'%')->first();
        if($empresa == null){
            $empresa= EmpresaTFG::create(['nombre'=>$request['web']]);
        }
        VentasDiariasTFG::updateOrCreate(['codigo'=>$request['codigo']],
        [
            'dia'=>$request['fecha'],
            'codigo'=>$request['codigo'],
            'id_empresa'=>$empresa->id,
            'precio'=>$request['precio'],
            'pagado'=>$request['pagado'],
            'presupuesto'=>$request['presupuesto'],
            'gasto'=>$request['pagado_docente'],
        ]
        );

    }
    public function importfromjson(Request $request){
        foreach($request->all() as $venta){
            $empresa = EmpresaTFG::where('nombre','like','%'.$venta['web'].'%')->first();
            if($empresa == null){
                $empresa= EmpresaTFG::create(['nombre'=>$venta['web']]);
            }
            VentasDiariasTFG::updateOrCreate(['codigo'=>$venta['codigo']],
            [
                'dia'=>$venta['fecha'],
                'codigo'=>$venta['codigo'],
                'id_empresa'=>$empresa->id,
                'precio'=>$venta['precio']??0,
                'pagado'=>$venta['pagado']??0,
                'presupuesto'=>$venta['presupuesto']??0,
                'gasto'=>$venta['pagado_docente']??0,
            ]);
        }
    }
}
