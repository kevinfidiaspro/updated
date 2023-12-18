<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use App\Models\EmpresaTFG;
use App\Models\TFGPotenciales;
use App\Models\VentasDiariasTFG;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VentasDiariasTFGController extends Controller
{
    public function Dashboard(){
        $year = date('Y');
        $month = date('m');
        $query = $this->ResumenVentas();
    
            $previous_year = $year-1;
            $previous_month = $month;
            $previous_date = $previous_year.'-'.$previous_month.'-01';
            $query->whereDate('dia','>=',$previous_date)->whereDate('dia','<=', date('Y-m-d'));
        

        return $query->get();

    }
    public function getResumenVentas($year){
        return $this->ResumenVentas()->whereYear('dia',$year)->get();
    }
    public function ResumenVentas(){
        return $this->ResumenQueryBase() ->orderBy(DB::raw('Year(dia)'),'DESC')->orderBy(DB::raw('Month(dia)'),'DESC');
    }
    public function ResumenQueryBase(){
        $ventas = VentasDiariasTFG::selectRaw('sum(precio) as total, sum(precio) / 1.21 as total_sin_iva, Month(dia) as mes,Year(dia) as year,sum(gasto) as gasto,  (sum(precio) / 1.21) - sum(gasto) as beneficio')
        ->groupBy(DB::raw('Month(dia),Year(dia)'));
       
        return $ventas;
    }
    public function getEstadisticas(Request $request){
        $year = date('Y');
        $ventas = [];
        $gastos = [];
        $label = [];
        $ventas = [];
        for($i = $year -2;$i<=$year;$i++){
            $temp = $this->EstadisticasController($i.'-01-01',$i.'-12-31');
            $results[] = $temp['data'];
            $gastos[] = $temp['gastos'];
        }
        return ['ventas'=>$results,'gastos'=>$gastos];
    }
    public function getEstadisticaVentas(Request $request){
        return $this->EstadisticasController($request->start,$request->end);
    }
    public function EstadisticasController($start = null,$end=null){
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sept','Oct','Nov','Dec'];
        $start_date = Carbon::now()->subYear()->firstOfMonth();
        $end_date  = Carbon::now()->endOfMonth();

        if($start){
            $start_date = Carbon::parse($start);
         
        }
        if($end){
            $end_date = Carbon::parse($end);
        }

        $query = $this->ResumenQueryBase();
        $query->whereDate('dia', '>=', $start_date->toIso8601String());
        $query->whereDate('dia', '<=', $end_date->toIso8601String());
        $query->orderBy(DB::raw('Year(dia)'),'ASC')->orderBy(DB::raw('Month(dia)'),'ASC');;

        // Create an instance of TFGGastosController
        $gastosController = new TFGGastosController();
        // Call a method from TFGGastosController
        $gastos = $gastosController->ResumenGastosQuery();
        $gastos->whereDate('mes', '>=', $start_date->toIso8601String());
        $gastos->whereDate('mes', '<=', $end_date->toIso8601String());
        $gastos = $gastos->orderBy(DB::raw('Year(mes)'),'ASC')->orderBy(DB::raw('Month(mes)'),'ASC')->get()->all();

        $ventas = $query->get()->all();
        $result = [];
        $result_gastos = [];
        $result_gastos_sin_iva = [];
        $labels = [];
        $index = 0;
        $index_gastos = 0;
    
        while (($start_date->year < $end_date->year || $start_date->month <= $end_date->month)&&$start_date->year <= $end_date->year) {
            $labels[] = $meses[$start_date->month-1].'-'.$start_date->format('y');
            if(isset($ventas[$index])){
                if($ventas[$index]['mes'] == $start_date->month && $ventas[$index]['year'] ==  $start_date->year){
                    $result [] = $ventas[$index]['total'];
                    $result_gastos_sin_iva [] = floatval( number_format( $ventas[$index]['total']/1.21,2,'.',''));
                    $index++;
                }else{
                    $result []= 0;
                    $result_gastos_sin_iva []= 0;
                }
            }else{
                $result []= 0;
            }
            if(isset($gastos[$index_gastos])){
                if($gastos[$index_gastos]['mes'] == $start_date->month && $gastos[$index_gastos]['year'] ==  $start_date->year){
                    $result_gastos [] = $gastos[$index_gastos]['total'];
                    $index_gastos++;
                }else{
                    $result_gastos []= 0;
                }
            }else{
                $result_gastos []= 0;
            }
            $start_date->addMonth();
        }
        return ['data'=>$result,'gastos'=>$result_gastos,'sin_iva'=>$result_gastos_sin_iva,'labels'=>$labels,'ventas'=>$gastos];
    }
    public function ResumenVentasByYear(){
        $meses = [ 
            "filler",
            "enero",    // January
        "febrero",  // February
        "marzo",    // March
        "abril",    // April
        "mayo",     // May
        "junio",    // June
        "julio",    // July
        "agosto",   // August
        "septiembre", // September
        "octubre",  // October
        "noviembre", // November
        "diciembre" ];
        $query =   $this->ResumenQueryBase() ->orderBy(DB::raw('Year(dia)'),'ASC')->orderBy(DB::raw('Month(dia)'),'DESC');
        $year = date('Y');
        $month = date('m');
        $previous_year = $year-4;
        $previous_month = $month;
        $previous_date = $previous_year.'-'.$previous_month.'-01';
        $query->whereDate('dia','>=',$previous_date)->whereDate('dia','<=', date('Y-m-d'));
        $ventas = $query->get();

        $result = [];
        $previous_year = null;
        foreach($ventas as $venta){
            if(!isset($result[$venta->year])) $result[$venta->year] = ['year'=>$venta->year];
            $result[$venta->year] [$meses[$venta->mes]] = number_format($venta->total,2,'.','') ;

        }
        $new_result = [];
        foreach ($result as $value) {
            $new_result[] = $value;
        }
        
        return $new_result;
    }
    public function deleteVentaDiaria(Request $request){
        VentasDiariasTFG::find($request->id)->delete();
    }
    public function saveVentaDiaria(Request $request){
        if($request->id != null){
            VentasDiariasTFG::updateOrCreate(['id'=>$request->id],$request->all());
        }
        else{
            VentasDiariasTFG::updateOrCreate(['codigo'=>$request->codigo],$request->all());
        }
    }
    public function getVentasDiarias(Request $request){
        $ventas = VentasDiariasTFG::with(['Empresa']);
        if($request->search){
            $ventas->where('codigo','LIKE','%'.$request->search.'%');
        }
        if($request->fecha_inicio){
            $ventas->whereDate('dia','>=',$request->fecha_inicio);
        }
        if($request->fecha_fin){
            $ventas->whereDate('dia','<=',$request->fecha_fin);
        }
        if($request->id_empresa){
            $ventas->where('id_empresa',$request->id_empresa);
        }
        $ventas->orderBy('dia','Desc');
        if($request->amount == -1){
            return ['data'=>$ventas->get()];
        }
        $totales = [
            'total_precio'=>number_format($ventas->sum('precio'),2,'.',''),
            'total_precio_no_iva'=>number_format($ventas->sum('precio')/1.21,2,'.',''),
            'total_presupuesto'=>number_format($ventas->sum('presupuesto'),2,'.',''),
            'total_gasto'=>number_format($ventas->sum('gasto'),2,'.',''),
            '               total_pagado'=>number_format($ventas->sum('pagado'),2,'.',''),

            ];
        return ['data'=>$ventas->simplePaginate($request->amount)->items(),
                'total'=>$ventas->count(),
                'totales'=>$totales
                ];
    }
}
