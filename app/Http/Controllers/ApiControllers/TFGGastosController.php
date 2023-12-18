<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TiposTFGGastos;
use App\Models\TFGGastos;
use App\Http\Resources\GastoTfgResource;
use Illuminate\Support\Facades\DB;

class TFGGastosController extends Controller
{
    public function Dashboard(){
        $year = date('Y');
        $month = date('m');
        $query = $this->ResumenGastos();
      
        $previous_year = $year-1;
        $previous_month = $month;
        $previous_date = $previous_year.'-'.$previous_month.'-01';
        $query->whereDate('mes','>=',$previous_date)->whereDate('mes','<=', date('Y-m-d'));
        
        return $query->get();
    }
    public function DashboardNetos(){
        return['gastos_netos'=> $this->DashboardYear(1),
        'gastos_caja'=> $this->DashboardYear(0)];
    }

    public function DashboardYear($netos){
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
        $year = date('Y');
        $month = date('m');
        $query = $this->ResumenGastos();
      
            $previous_year = $year-2;
            $previous_month = $month;
            $previous_date = $previous_year.'-'.$previous_month.'-01';
            $query->whereDate('mes','>=',$previous_date)->whereDate('mes','<=', date('Y-m-d'));
        
        if($netos){
            $query->where('id_tipo','!=',8);
        }
        $ventas = $query->get();
        $result = [];
        foreach($ventas as $venta){
            if(!isset($result[$venta->year])) $result[$venta->year] = ['year'=>$venta->year];
            $result[$venta->year] [$meses[$venta->mes]] = number_format($venta->total,2) ;
        }
        $new_result = [];
        foreach ($result as $value) {
            $new_result[] = $value;
        }
        
        return $new_result;
    }
    public function ResumenGastosQuery(){
    
        $ventas = TFGGastos::selectRaw('(sum(euros)) as total, (sum(euros) ) / 1.21 as total_sin_iva, Month(mes) as mes, Year(mes) as year')
        ->groupBy(DB::raw('Month(mes), Year(mes)'));
        return $ventas;
    }
    public function ResumenGastos(){
        return $this->ResumenGastosQuery()->orderBy(DB::raw('Year(mes)'),'DESC')->orderBy(DB::raw('Month(mes)'),'DESC');
    }
    public function getTipos(){
        return TiposTFGGastos::all();
    }
    public function deleteTipo(Request $request){
        TiposTFGGastos::find($request->id)->delete();
    }
    public function saveTipo(Request $request){
        TiposTFGGastos::updateOrCreate(['id'=>$request->id],$request->all());
    }
    public function getGastos(Request $request){
        $gastos = TFGGastos::with(['Tipo']);
        $year = date('Y');
        $mes = date('m');
        if($request->inicio){
            $date = explode('-',$request->inicio);
            $year = $date[0];
            $mes = $date[1];
        }
    
        if($request->fin){
            /*$date = explode('-',$request->mes);
            $year = $date[0];*/
            //$gastos->whereDate('mes','<=',$request->fin);
        }
        if($request->id_tipo){
            $gastos->where('id_tipo','=',$request->id_tipo);
        }
        $gastos->whereYear('mes','=',$year);
        $total_anual='€'.number_format($gastos->sum('euros'),2,',','.') ;
        $gastos->whereMonth('mes','=',$mes);
        $total = '€'.number_format($gastos->sum('euros'),2,',','.') ;
        return ['total'=>$total,'total_anual'=>$total_anual,'data'=>GastoTfgResource::collection($gastos->get())];
    }
    public function deleteGasto(Request $request){
        TFGGastos::find($request->id)->delete();
    }
    public function getGasto($id){
        return TFGGastos::find($id);
    }
    public function saveGasto(Request $request){
        $resp = $request->all();
        /*$month_separated = explode('-',($resp['mes'].'-'));
        $resp['mes']= $month_separated[0].'-'.$month_separated[1].'-01';*/
        TFGGastos::updateOrCreate(['id'=>$request->id],$resp );
    }
}
