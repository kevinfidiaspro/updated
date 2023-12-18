<?php

namespace App\Http\Controllers\ApiControllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CategoriaKitDigital;
use App\Models\VentasKitDigital;
use App\Models\EstadosVentasKit;
use App\Models\User;
use Carbon\Carbon;
class VentasKitDigitalController extends Controller
{
    public function geEstadoVenta(){
        return EstadosVentasKit::all();
    }
    public function getCategorias(){
        return CategoriaKitDigital::all();
    }
    public function importVentasDiarias(Request $request){
        $array = $request->all();
        $clientes = [];
        foreach($array as  $factura){
            $fecha  = null;
            $fecha_iva = null;
            if(isset($factura['fecha_firma'])){
                $fecha = Carbon::createFromFormat('m/d/Y', $factura['fecha_firma']);
                $fecha_iva = Carbon::createFromFormat('m/d/Y', $factura['fecha_firma'])->addDays(90);
            }
            $cliente = User::where( 'nombre','like','%'.$factura['razon_social'].'%')->orWhere('nombre_fiscal','like','%'.$factura['razon_social'].'%')->orWhere('nombre_comercial','like','%'.$factura['razon_social'].'%')->first();
            if($cliente == null){
                $clientes[]= $factura['razon_social'];
            }
            else{
                VentasKitDigital::updateOrCreate(['num_acuerdo'=>$factura['num']],[
                    'id_estado'=>$factura['estado'],
                    'num_acuerdo'=>$factura['num'],
                    'importe'=>$factura['importe'],
                    'id_cliente'=>$cliente->id,
                    'id_categoria'=>$factura['categoria'],
                    'fecha_firma'=>$fecha,
                    'fecha_iva'=>$fecha_iva,
    
                ]);
            }
        }
        return $clientes;
        
    }
    public function pagada($id){
        $venta = VentasKitDigital::find($id);
        if($venta != null){
            $venta->update(['pagado'=>$venta->pagado == 1 ? 0:1]);
        }
    }
    public function deleteVentaKitDigital(Request $request){
        VentasKitDigital::find($request->id)->delete();
    }
    public function saveVentaKitDigital(Request $request){
        $factura = $request->all();
        if(isset($factura['fecha_firma']) && ($factura['fecha_iva']??null) == null){
            $fecha = Carbon::parse($factura['fecha_firma']);
            $fecha_iva = Carbon::parse( $factura['fecha_firma'])->addDays(89);
            $factura['fecha_iva'] = $fecha_iva;
        }
        return VentasKitDigital::updateOrCreate(['id'=>$request->id],$factura);
    }
    public function getVentasKitDigital(Request $request){
        $ventas =  VentasKitDigital::with(['Cliente','Categoria']);
        //filtros
        if($request->id_cliente){
            $ventas->where('id_cliente',$request->id_cliente);
        }
        if($request->fecha_inicio){
            $ventas->whereDate('fecha_firma','>=',$request->fecha_inicio);
        }
        if($request->fecha_fin){
            $ventas->whereDate('fecha_firma','<=',$request->fecha_fin);
        }
        if($request->amount < 0){
            return ['data'=>$ventas->get()];
        }
        $ventas = $ventas->orderBy('fecha_firma','Desc');
        if($request->amount == -1){
            $ventas = ['data'=>$ventas->get(),'total'=>$ventas->count()];

        }else{
            $ventas = $ventas->paginate($request->amount??15);

        }
        return $ventas;
    }
    public function getResumenVentas($year){
        $ventas = VentasKitDigital::whereYear('fecha_firma',$year)
        ->selectRaw('sum(importe * (100 + iva) / 100 ) as total, sum(importe)  as total_sin_iva, Month(fecha_firma) as mes')
        ->groupBy(DB::raw('Month(fecha_firma)'))->get();
        return $ventas;
    }
}
