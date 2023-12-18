<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\VentasDiarias;
use App\Models\Factura;
use App\Http\Controllers\ApiControllers\GastosController;
use Carbon\Carbon;
class VentasDiariasController extends Controller
{
    public function saveVentaDiaria(Request $request){
        return VentasDiarias::updateOrCreate(['id'=>$request->id],$request->all());
    }
    public function deleteVentaDiaria(Request $request ){
        VentasDiarias::find($request->id)->delete();
    }
    public function getFacturas(){
        $factura = Factura::with(['items_factura','proyecto.usuario','ingresos'])->orderBy('fecha', 'desc')->whereDoesntHave('VentasDiarias')->where('tipo',1)->get();
        
        return response()->json($factura, 200);
    }
    public function Dashboard(){
        $year = date('Y');
        $month = date('m');
        $query = $this->ResumenVentas();
      
        $previous_year = $year-1;
        $previous_month = $month;
        $previous_date = $previous_year.'-'.$previous_month.'-01';
        $query->whereDate('facturas.fecha','>=',$previous_date)->orderBy(DB::raw('Year(facturas.fecha)'),'DESC')->orderBy(DB::raw('Month(facturas.fecha)'),'DESC');
        
      
        return $query->get();

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
    public function getResumenVentas($year){
        return $this->ResumenVentas()->whereYear('facturas.fecha',$year)->orderBy(DB::raw('Year(facturas.fecha)'),'DESC')->orderBy(DB::raw('Month(facturas.fecha)'),'DESC')->get();
    }
    public function ResumenVentas(){
    
        $ventas = VentasDiarias::
        join('facturas','ventas_diarias.id_factura','=','facturas.id')
        ->selectRaw('(IFNULL(sum(facturas.total),0) + IFNULL(sum(ventas_diarias.total_sf),0)) as total, (IFNULL(sum(facturas.total),0) + IFNULL(sum(ventas_diarias.total_sf),0)) / 1.21 as total_sin_iva, Month(facturas.fecha) as mes, Year(facturas.fecha) as year')
        ->groupBy(DB::raw('Month(facturas.fecha), Year(facturas.fecha)'));
        return $ventas;
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

        $query = $this->ResumenVentas();
        $query->whereDate('facturas.fecha', '>=', $start_date->toIso8601String());
        $query->whereDate('facturas.fecha', '<=', $end_date->toIso8601String());
        $query->orderBy(DB::raw('Year(facturas.fecha)'),'ASC')->orderBy(DB::raw('Month(facturas.fecha)'),'ASC');;

        // Create an instance of TFGGastosController
        $gastosController = new GastosController();
        // Call a method from TFGGastosController
        $gastos = $gastosController->ResumenGastos();
        $gastos->whereDate('fecha', '>=', $start_date->toIso8601String());
        $gastos->whereDate('fecha', '<=', $end_date->toIso8601String());
        $gastos = $gastos->orderBy(DB::raw('Year(fecha)'),'ASC')->orderBy(DB::raw('Month(fecha)'),'ASC')->get()->all();

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
    public function VentasAnuales(Request $request){
        $ventas = $this->getVentasDiarias($request);
        
    }

    public function getVentasDiarias(Request $request){
        $ventas = VentasDiarias::with(['Factura.cliente','Cliente'])->withAggregate('Factura','fecha');
        if($request->search){
            $ventas->where('importe','LIKE','%'.$request->search.'%');
        }
        if($request->fecha_inicio){
            $ventas->where(function($ventas) use($request){
                $ventas->whereDate('dia','>=',$request->fecha_inicio);
                $ventas->whereDoesntHave('Factura');
                $ventas->orWhereHas('Factura',function ($query) use ($request){
                    $query->whereDate('fecha','>=',$request->fecha_inicio);
                });
            });
        }
        if($request->fecha_fin){
            $ventas->where(function($ventas) use($request){
                $ventas->whereDate('dia','<=',$request->fecha_fin);
                $ventas->whereDoesntHave('Factura');
                $ventas->orWhereHas('Factura',function ($query) use ($request){
                    $query->whereDate('fecha','<=',$request->fecha_fin);
                });
            });
        }
        if($request->id_factura){
            $ventas->where('id_factura',$request->id_factura);
        }
        $ventas->orderByRaw('COALESCE(factura_fecha, dia) DESC');
       // $ventas->orderBy('factura_nro_factura','DESC');
        if($request->amount < 0){
            return ['data'=>$ventas->get(),'total'=>$ventas->count()];
        }
       
        return $ventas->paginate($request->amount);
    }
}
